<?php
/**
 * Contact Form DB plugin for Craft CMS
 *
 * ContactFormDb_Cfdb Service
 *
 * --snip--
 * All of your plugin’s business logic should go in services, including saving data, retrieving data, etc. They
 * provide APIs that your controllers, template variables, and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 * --snip--
 *
 * @author    Nathan de Waard
 * @copyright Copyright (c) 2017 Nathan de Waard
 * @link      natedewaard.com
 * @package   ContactFormDb
 * @since     1.0.0
 */

namespace Craft;

class ContactFormDb_CfdbService extends BaseApplicationComponent
{
    public function getContactFormSubmissionById($submissionId)
    {
        return craft()->elements->getElementById($submissionId, 'ContactFormDb_Cfdb');
    }
    public function getSubmissionTitleTableElement(&$context)
    {
        if (!isset($context['element'])) {
            return;
        }

        // Only do this for a Comment ElementType
        if ($context['element']->getElementType() == 'ContactFormDb_Cfdb') {

            $html = '<div class="submission-title-element">';
            $html .= '<span class="status ' . $context['element']->status . '"></span>';
            $html .= '<a href="' . $context['element']->getCpEditUrl() . '">';
            $html .= '<span class="username">' . $context['element']->name . ' ' . $context['element']->email . '</span>';
            $html .= '<small>' . htmlspecialchars($context['element']->getExcerpt(0, 100)) . '</small></a>';
            $html .= '</div>';

            return $html;
        }
    }
    /**
     * This function can literally be anything you want, and you can have as many service functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     craft()->contactFormDb_cfdb->exampleService()
     */
    public function saveContactFormSubmission(ContactFormDb_CfdbModel $submission)
    {
        //check if we have the submission, in case the email is sent multiple times or form submitted.
        $isNewSubmission = !$submission->id;

        if($isNewSubmission)
        {
            $record = ContactFormDb_CfdbRecord::model()->findByAttributes(
                array(
                    'email' => $submission->email,
                    'name' => $submission->name,
                    'inquiryType' => $submission->inquiryType,
                    'message' => $submission->message,
                )
            );
            if($record != null)
            {
                return false;
            }
        }
        else
        {
            $record = ContactFormDb_CfdbRecord::model()->findById($submission->id);
        }

        if(!$record)
        {
            $record = new ContactFormDb_CfdbRecord();
        }

            $fields = ['name', 'email', 'inquiryType', 'message', 'status', 'ipAddress', 'userAgent', 'urlReferrer', 'answered', 'answeredDate', 'archived' , 'archivedDate'];

            foreach ($fields as $field)
            {
                $record->$field = $submission->$field;
            }

            $record->validate();
            $submission->addErrors($record->getErrors());

            if(!$submission->hasErrors())
            {
                $event = new Event($this, array(
                    'submission'      => $submission,
                    'isNewSubmission' => $isNewSubmission
                ));
                $this->onBeforeSaveSubmission($event);

                if (!$event->performAction)
                {
                    return false;
                }

                $transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
                try
                {
                    if (craft()->elements->saveElement($submission))
                    {
                        if ($isNewSubmission)
                        {
                            //$record->id = $submission->id;
                            $record->elementId = $submission->id;
                        }

                        $record->save(false);

                        if ($transaction !== null)
                        {
                            $transaction->commit();
                        }

                        $this->onSaveSubmission(new Event($this, array(
                            'submission'      => $submission,
                            'isNewSubmission' => $isNewSubmission
                        )));

                        return true;
                    }
                } catch (\Exception $e)
                {
                    if ($transaction !== null)
                    {
                        $transaction->rollback();
                    }
                    throw $e;
                }

                if ($transaction !== null)
                {
                    $transaction->rollback();
                }
            }

            return false;

    }

    // Event Handlers
    // =========================================================================

    public function onBeforeSaveSubmission(\CEvent $event)
    {
        $params = $event->params;

        if (empty($params['submission']) || !($params['submission'] instanceof ContactFormDb_CfdbModel)) {
            throw new Exception('onBeforeSaveSubmission event requires "submission" param with ContactFormDb_CfdbModel instance');
        }

        $this->raiseEvent('onBeforeSaveSubmission', $event);
    }
    // Event Handlers
    // =========================================================================

    public function onDeleteSubmission(\CEvent $event)
    {
        $params = $event->params;

        if (empty($params['submissionIds'])) {
            throw new Exception('onDeleteSubmission event requires "submissions" param with ContactFormDb_CfdbModel instance');
        }

        $this->raiseEvent('onDeleteSubmission', $event);
    }

    public function onSaveSubmission(\CEvent $event)
    {
        $params = $event->params;

        if (empty($params['submission']) || !($params['submission'] instanceof ContactFormDb_CfdbModel)) {
            throw new Exception('onSaveSubmission event requires "submission" param with ContactFormDb_CfdbModel instance');
        }

        $this->raiseEvent('onSaveSubmission', $event);
    }

    public function onBeforeTrashSubmission(\CEvent $event)
    {
        $params = $event->params;

        if (empty($params['submission']) || !($params['submission'] instanceof ContactFormDb_CfdbModel)) {
            throw new Exception('onBeforeTrashSubmission event requires "submission" param with ContactFormDb_CfdbModel instance');
        }

        $this->raiseEvent('onBeforeTrashSubmission', $event);
    }

    public function onTrashSubmission(\CEvent $event)
    {
        $params = $event->params;

        if (empty($params['submission']) || !($params['submission'] instanceof ContactFormDb_CfdbModel)) {
            throw new Exception('onTrashSubmission event requires "submission" param with ContactFormDb_CfdbModel instance');
        }

        $this->raiseEvent('onTrashSubmission', $event);
    }
}