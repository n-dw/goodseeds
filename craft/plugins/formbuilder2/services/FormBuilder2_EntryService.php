<?php
namespace Craft;


class FormBuilder2_EntryService extends BaseApplicationComponent
{
  
  private $_entriesById;
  private $_allEntryIds;
  private $_fetchedAllEntries = false;

  /**
   * Fires 'onBeforeSave' Form Entry
   *
   */
  public function onBeforeSave(Event $event)
  {
    $this->raiseEvent('onBeforeSave', $event);
  }

  /**
   * Get All Entry ID's
   *
   */
  public function getAllEntryIds()
  {
    if (!isset($this->_allEntryIds)) {
      if ($this->_fetchedAllEntries) {
        $this->_allEntryIds = array_keys($this->_entriesById);
      } else {
        $this->_allEntryIds = craft()->db->createCommand()
          ->select('id')
          ->from('formbuilder2_entries')
          ->queryColumn();
      }
    }
    return $this->_allEntryIds;
  }

  /**
   * Get All Entries
   *
   */
  public function getAllEntries()
  {
    $entries = FormBuilder2_EntryRecord::model()->findAll();
    return $entries;
  }

  public function getEntryById($entryId)
  {
    $entryRecord = FormBuilder2_EntryRecord::model()->findByAttributes(array(
      'id' => $entryId,
    ));
    $entry = FormBuilder2_EntryModel::populateModel($entryRecord);
    return $entry;
  }

  /**
   * Get Total Entries Count
   *
   */
  public function getTotalEntries()
  {
    return count($this->getAllEntryIds());
  }

  /**
   * Get Form By Handle
   *
   */
  public function getFormByHandle($handle)
  {
    $formRecord = FormBuilder2_FormRecord::model()->findByAttributes(array(
      'handle' => $handle,
    ));

    if (!$formRecord) { return false; }
    return FormBuilder2_FormModel::populateModel($formRecord);
  }

  /**
   * Get Form Entry By Id
   *
   */
  public function getFormEntryById($id)
  {
    return craft()->elements->getElementById($id, 'FormBuilder2');
  }

  /**
   * Get Submission By ID
   *
   */
  public function getSubmissionById($entryId)
  {
    return FormBuilder2_EntryRecord::model()->findById($entryId);
  }

  /**
   * Get All Entries From Form ID
   *
   */
  public function getAllEntriesFromFormID($formId)
  {
    $result = craft()->db->createCommand()
      ->select('*')
      ->from('formbuilder2_entries')
      ->where('formId = :formId', array(':formId' => $formId))
      ->queryAll();
    return $result;
  }

  /**
   * Validate values of a submitted form
   *
   */
  public function validateEntry($form, $submissionData) {
    $fieldLayoutFields = $form->getFieldLayout()->getFields();
    $errorMessage = array();

    foreach ($fieldLayoutFields as $key => $fieldLayoutField) {
      $requiredField = $fieldLayoutField->attributes['required'];
      $fieldId = $fieldLayoutField->fieldId;
      $field = craft()->fields->getFieldById($fieldId);

      $userValue = (array_key_exists($field->handle, $submissionData)) ? $submissionData[$field->handle] : false;          

      if ($requiredField == 1) {
        $field->required = true;
      }
      
      switch ($field->type) {
        case "PlainText":
          if ($field->required) {
            $text = craft()->request->getPost($field->handle);
            if ($text == '') {
              $errorMessage[$field->handle] = Craft::t('{fieldname} cannot be blank.', array(
                                                'fieldname' => $field->name
                                              ));
            }
          }
        break;
        case "RichField":
          if ($field->required) {
            $richField = craft()->request->getPost($field->handle);
            if ($richField == '') {
              $errorMessage[$field->handle] = Craft::t('{fieldname} cannot be blank.', array(
                                                'fieldname' => $field->name
                                              ));
            }
          }
        break;
        case "Number":
          $number = craft()->request->getPost($field->handle);
          if ($field->required) {
            if (!ctype_digit($number)) {
              $errorMessage[$field->handle] = Craft::t('{fieldname} cannot be blank and needs to contain only numbers.', array(
                                                'fieldname' => $field->name
                                              ));
            }
          } else {
            if (!ctype_digit($number) && (!empty($number))) {
              $errorMessage[$field->handle] = Craft::t('{fieldname} needs to contain only numbers.', array(
                                                'fieldname' => $field->name
                                              ));
            }
          }
        break;
        case "MultiSelect":
          $multiselect = craft()->request->getPost($field->handle);
          if ($field->required) {
            if ($multiselect == '') {
              $errorMessage[$field->handle] = Craft::t('{fieldname} needs at least one item selected.', array(
                                                'fieldname' => $field->name
                                              ));
            }
          }
        break;
        case "RadioButtons":
          $radiobuttons = craft()->request->getPost($field->handle);
          if ($field->required) {
            if ($radiobuttons == '') {
              $errorMessage[$field->handle] = Craft::t('{fieldname} needs at least one option selected.', array(
                                                'fieldname' => $field->name
                                              ));
            }
          }
        break;
        case "Dropdown":
          $dropdown = craft()->request->getPost($field->handle);
          if ($field->required) {
            if ($dropdown == '') {
              $errorMessage[$field->handle] = Craft::t('{fieldname} needs an item selected.', array(
                                                'fieldname' => $field->name
                                              ));
            }
          }
        break;
        case "Checkboxes":
          $checkbox = craft()->request->getPost($field->handle);
          if ($field->required) {
            if ($checkbox == '') {
              $errorMessage[$field->handle] = Craft::t('{fieldname} must be checked.', array(
                                                'fieldname' => $field->name
                                              ));
            }
          }
        break;
      }
    }
    return $errorMessage;
  }

  /**
   * Process Submission Entry
   *
   */
  public function processSubmissionEntry(FormBuilder2_EntryModel $submission)
  { 
    // Fire Before Save Event
    $this->onBeforeSave(new Event($this, array(
      'entry' => $submission
    )));

    $form                       = craft()->formBuilder2_form->getFormById($submission->formId);
    $formFields                 = $form->fieldLayout->getFieldLayout()->getFields();
    $attributes                 = $form->getAttributes();
    $formSettings               = $attributes['formSettings'];

    $submissionRecord = new FormBuilder2_EntryRecord();

    // Build Entry Record
    $submissionRecord->formId       = $submission->formId;
    $submissionRecord->title        = $submission->title;
    $submissionRecord->files        = $submission->files;
    $submissionRecord->submission   = $submission->submission;

    $submissionRecord->validate();
    $submission->addErrors($submissionRecord->getErrors());

    // Save To Database
    if (!$submission->hasErrors()) {
      $transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
      try {
        if (craft()->elements->saveElement($submission)) {
          $submissionRecord->id = $submission->id;
          $submissionRecord->save(false);

          if ($transaction !== null) { 
            $transaction->commit(); 
          }
          return $submissionRecord->id;
        } else { 
          return false; 
        }
      } catch (\Exception $e) {
        if ($transaction !== null) { 
          $transaction->rollback(); 
        }
        throw $e;
      }
      return true;
    } else { 
      return false; 
    }
  }

  /**
   * Send Email Notification To Submitter
   *
   */
  public function sendEmailNotificationToSubmitter($form, $message, $html = true, $email = null)
  { 
    $errors = false;
    $attributes = $form->getAttributes();
    $notificationSettings = $attributes['notificationSettings'];
    $toEmail = $email;

    $adminEmail = craft()->systemSettings->getSetting('email', 'emailAddress');

    $email = new EmailModel();
    $emailSettings    = craft()->email->getSettings();

    $email->fromEmail = $adminEmail;
    $email->replyTo   = $adminEmail;
    $email->sender    = $adminEmail;
    $email->fromName  = $notificationSettings['publicFormName'] ? $notificationSettings['publicFormName'] : $form->name;
    $email->toEmail   = $toEmail;
    $email->subject   = $notificationSettings['submitterEmailSubject'] ? $notificationSettings['submitterEmailSubject'] : Craft::t('Thanks For Submission');
    $email->htmlBody      = $message;

    if (!craft()->email->sendEmail($email)) {
      $errors = true;
    }

    return $errors ? false : true;
  }

  /**
   * Send Email Notification
   *
   */
  public function sendEmailNotification($form, $files, $postData, $customEmail, $customSubject, $message, $html = true, $email = null)
  { 
    $errors = false;
    $attributes = $form->getAttributes();
    $notificationSettings = $attributes['notificationSettings'];
    $toEmails = ArrayHelper::stringToArray($notificationSettings['emailSettings']['notifyEmail']);
    $emailSettings    = craft()->email->getSettings();

    if (isset($notificationSettings['replyTo']) && ($notificationSettings['replyTo'] != '')) {
      $replyTo = $postData[$notificationSettings['replyTo']];
    } else {
      $replyTo = $emailSettings['emailAddress'];
    }

    // Process Subject Line
    if ($customSubject) {
      $subject = $customSubject;
    } else {
      $subject = $notificationSettings['emailSettings']['emailSubject'];
    }

    if ($customEmail != '') {
      $theEmailAddress = explode('|', $customEmail);
      ArrayHelper::prependOrAppend($toEmails, $theEmailAddress[0], true);
    }

    foreach ($toEmails as $toEmail) {
      $email = new EmailModel();

      $email->fromEmail = $emailSettings['emailAddress'];
      $email->replyTo   = $replyTo;
      $email->sender    = $emailSettings['emailAddress'];
      $email->fromName  = $form->name;
      $email->toEmail   = $toEmail;
      $email->subject   = $subject;
      $email->htmlBody  = $message;

      // Attach files to email
      if (!empty($files)) {
        foreach ($files as $attachment) {
          $email->addAttachment($attachment['tempPath'], $attachment['filename'], 'base64', $attachment['type']);
        }
      }

      if (!craft()->email->sendEmail($email)) {
        $errors = true;
      }
    }
    
    return $errors ? false : true;
  }
}
