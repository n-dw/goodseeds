<?php
/**
 * In Stock Notifier plugin for Craft CMS
 *
 * InStockNotifier_Notification Service
 *
 * --snip--
 * All of your plugin’s business logic should go in services, including saving data, retrieving data, etc. They
 * provide APIs that your controllers, template variables, and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 * --snip--
 *
 * @author    NdW
 * @copyright Copyright (c) 2017 NdW
 * @link      natedewaard.com
 * @package   InStockNotifier
 * @since     1.0.0
 */

namespace Craft;

class InStockNotifier_NotificationService extends BaseApplicationComponent {

    /**
     * This function can literally be anything you want, and you can have as many service functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     craft()->inStockNotifier_notification->saveNotificationRequest()
     */
    public function saveNotificationRequest(InStockNotifier_NotificationModel $model)
    {
        if ($model->productId && $model->customerEmail)
        {
            $record = InStockNotifier_NotificationRecord::model()->findByAttributes(array('productId' => $model->productId, 'customerEmail' => $model->customerEmail));

            //we already have a record
            if ($record)
            {
                return true;
            }

        }

        $record = new InStockNotifier_NotificationRecord();

        $fields = ['productId', 'customerEmail'];
        foreach ($fields as $field)
        {
            $record->$field = $model->$field;
        }

        $record->validate();
        $model->addErrors($record->getErrors());

        $transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
        try
        {
            if (!$model->hasErrors())
            {
                $record->save(false);
                $model->id = $record->id;

                if ($transaction !== null)
                {
                    $transaction->commit();
                }

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

        return false;
    }

    public function processNotifications()
    {

        $products = [];
        $notificationsForSending = $this->getNotificationRequestsToSend();

        if(!$this->sendNotificationEmails())
        {
            throw new Exception('Error sending notificationdeleting Emails.');
            return false;
        }

        if (!$this->cleanNotificationRequests())
        {
            throw new Exception('Error deleting sent notifications.');
        }

        return true;
    }
    //returns a model array of notification requests where the product is in stock
    private function getNotificationRequestsToSend()
    {
        $records = InStockNotifier_NotificationRecord::model()->findByAttributes('dateNotified IS NULL');
        $models = InStockNotifier_NotificationModel::populateModel($records);
        $notifications = $models;

        foreach ($notifications as $key=>$notification)
        {
            if($notification->productId != '' && is_numeric($notification->productId))
            {
                throw new Exception('No product in notification model');
                return false;
            }

            $product  = craft()->commerce_products->getProductById($notification->productId);
            //still no stock take out the model as we don't need to send anything
            if(!$product || $product->getTotalStock() <= 0)
            {
                unset($models[$key]);
                continue;
            }

        }

        return $models;
    }
    /*
     * deletes sent ones
     */
    private function cleanNotificationRequests()
    {
        return InStockNotifier_NotificationRecord::model()->deleteAllByAttributes(array('sendFail' => 0), 'dateNotified IS NOT NULL');
    }


    public function sendNotificationMessage(InStockNotifier_NotificationModel $model)
    {

        if (!filter_var($model->customerEmail, FILTER_VALIDATE_EMAIL) || $model->productId == '' || !is_numeric($model->productId))
            return false;

        //check is product exists and has stock
        $product = craft()->commerce_products->getProductById($productId);
        if (!$product || $product->getTotalStock() <= 0)
            return false;


        $customerEmail = $model->customerEmail;

        $email = new EmailModel();
        $emailSettings = craft()->email->getSettings();

        $email->fromEmail = $emailSettings['emailAddress'];
        $email->replyTo = $emailSettings['emailAddress'];
        $email->sender = $emailSettings['emailAddress'];
        $email->fromName = craft()->getSiteName();
        $email->toEmail = $customerEmail;
        $email->subject = $product->getName() . ' is back in stock';
        $email->body = 'Dear ' . $customerEmail . ' <a href="' . $product->getUrl() . '">' . $product->getName() . '</a> is in stock. We hope you enjoy it!';

        if (craft()->email->sendEmail($email))
            return true;

        return false;
    }

}