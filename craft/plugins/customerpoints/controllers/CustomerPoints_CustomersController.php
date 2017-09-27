<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 9/11/17
 * Time: 9:52 AM
 */
namespace Craft;

class CustomerPoints_CustomersController extends CustomerPoints_BaseAdminController
{

    /**
     * Customer Points Settings Index
     */
    public function actionIndex()
    {

        $settings = craft()->customerPoints->getSettings();

        $this->renderTemplate('customerpoints/customers');

    }

    public function actionEditCustomerPointsInfo(array $variables = array())
    {
        if (empty($variables['cpUser'])) {
            if (!empty($variables['customerPointsUserId'])) {
                $id = $variables['customerPointsUserId'];
                $variables['cpUser'] = craft()->customerPoints_user->getCustomerPointsUserById($id);

                if (!$variables['cpUser']) {
                    throw new HttpException(404);
                }
            } else {
                $variables['cpUser'] = new CustomerPoints_UserModel();
            }
        }

        $this->renderTemplate('customerpoints/customers/_edit', $variables);
    }

    public function actionSaveCustomerPointsInfo()
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
}
