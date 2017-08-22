<?php
/**
 * Contact Form DB plugin for Craft CMS
 *
 * ContactFormDb_Cfdb Controller
 *
 * --snip--
 * Generally speaking, controllers are the middlemen between the front end of the CP/website and your plugin’s
 * services. They contain action methods which handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering post data, saving it on a model,
 * passing the model off to a service, and then responding to the request appropriately depending on the service
 * method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what the method does (for example,
 * actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 * --snip--
 *
 * @author    Nathan de Waard
 * @copyright Copyright (c) 2017 Nathan de Waard
 * @link      natedewaard.com
 * @package   ContactFormDb
 * @since     1.0.0
 */

namespace Craft;

class ContactFormDb_CfdbController extends BaseController
{
    public function actionEditSubmission(array $variables = array())
    {
        $submissionId = $variables['cfdbId'];
        $submission = craft()->contactFormDb_cfdb->getContactFormSubmissionById($submissionId);

        $variables['submission'] = $submission;

        $this->renderTemplate('contactFormDb/edit', $variables);
    }

    public function actionSaveSubmission()
    {
        $this->requirePostRequest();

        $submissionId = craft()->request->getRequiredPost('submissionId');
        $submission = craft()->contactFormDb_cfdb->getContactFormSubmissionById($submissionId);

        if($submission)
        {
            $submission->elementId = $submissionId;
            $statusChange = craft()->request->getRequiredPost('status');

            if($statusChange)
            {
                $submission->status = $statusChange;

                if($statusChange == 'Read'){
                    $submission->read = true;
                    $submission->readDate = date('Y-m-d H:i:s');
                }
                elseif($statusChange == 'Responded'){
                    $submission->answered = true;
                    $submission->answeredDate = date('Y-m-d H:i:s');
                }
                elseif($statusChange == 'Resolved')
                {
                    $submission->resolved = true;
                    $submission->resolvedDate = date('Y-m-d H:i:s');
                }
                elseif($statusChange == 'Archived')
                {
                    $submission->archived = true;
                    $submission->archivedDate = date('Y-m-d H:i:s');
                }
            }
            if ($result = craft()->contactFormDb_cfdb->saveContactFormSubmission($submission)) {
                craft()->userSession->setNotice(Craft::t('Submission Saved.'));
                $this->redirectToPostedUrl($submission);
            } else {
                craft()->userSession->setError($result);
            }
        }
        else
        {
            craft()->userSession->setError('There is no Submission of id: ' . $submissionId);
        }

    }

    public function actionDeleteSubmission()
    {
        $this->requirePostRequest();

        $submissionId = craft()->request->getRequiredPost('submissionId');
        $submission = craft()->contactFormDb_cfdb->getContactFormSubmissionById($submissionId);

        if ($result = craft()->contactFormDb_cfdb->deleteContactFormSubmission($submission)) {
            craft()->userSession->setNotice(Craft::t('Submission deleted.'));
            $this->redirectToPostedUrl($submission);
        } else {
            craft()->userSession->setError($result);
        }
    }

}