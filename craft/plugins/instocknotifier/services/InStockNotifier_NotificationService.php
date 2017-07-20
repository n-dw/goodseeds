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
    public function createNotificationRequest(InStockNotifier_NotificationModel $model)
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

        return $this->saveNotificationRequest($model, $record);
    }

    private function saveNotificationRequest(InStockNotifier_NotificationModel $model, InStockNotifier_NotificationRecord $record)
    {
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
        $notificationsForSending = $this->getNotificationRequestsToSend();
        if(count($notificationsForSending) > 0)
        {
            $this->sendNotificationEmails($notificationsForSending);
        }

        return true;
    }

    //returns a model array of notification requests where the product is in stock
    private function getNotificationRequestsToSend()
    {
        $records = InStockNotifier_NotificationRecord::model()->findAllByAttributes(array('dateNotified' => null));
        $models = [];

        $notifications = $records;
        foreach ($notifications as $key => $notification)
        {
            if ($notification->productId == '' || !is_numeric($notification->productId))
            {
                // throw new Exception('No product in notification model');
                unset($records[$key]);
                continue;
            }

            $product = craft()->commerce_products->getProductById($notification->productId);
            //still no stock take out the model as we don't need to send anything
            if (!$product || $product->getTotalStock() == 0)
            {
                unset($records[$key]);
            }

        }

        $fields = ['id', 'productId', 'customerEmail', 'dateNotified', 'sendFail'];
        foreach ($records as $record)
        {
            $model = new InStockNotifier_NotificationModel();
            foreach ($fields as $field)
            {
                $model->$field = $record->$field;
            }
            array_push($models, $model);
        }

        return array('records' => $records, 'models' => $models);
    }

    /*
     * deletes sent ones
     */
    private function cleanNotificationRequests()
    {
        return InStockNotifier_NotificationRecord::model()->deleteAllByAttributes(array('sendFail' => 0), 'dateNotified IS NOT NULL');
    }

    private function sendNotificationEmails($notifications)
    {
        if (!array_key_exists('records', $notifications) || count($notifications['records']) <= 0)
            return false;

        foreach ($notifications['models'] as $key => $notification)
        {
            if ($this->sendNotificationMessage($notification))
            {
                //change record to date notified
                $notifications['records'][$key]->dateNotified = time();
                $notifications['records'][$key]->sendFail = false;
                // sendfailed false
            }
            else
            {
                //change send failed true
                $notifications['records'][$key]->sendFail = true;
            }

            $this->saveNotificationRequest($notification, $notifications['records'][$key]);
        }
    }

    public function sendNotificationMessage(InStockNotifier_NotificationModel $model)
    {

        if (!filter_var($model->customerEmail, FILTER_VALIDATE_EMAIL) || $model->productId == '' || !is_numeric($model->productId))
            return false;

        //check is product exists and has stock
        $product = craft()->commerce_products->getProductById($model->productId);
        if (!$product || $product->getTotalStock() < 0)
            return false;


        $customerEmail = $model->customerEmail;

        $email = new EmailModel();
        $emailSettings = craft()->email->getSettings();

        $email->fromEmail = $emailSettings['emailAddress'];
        $email->replyTo = $emailSettings['emailAddress'];
        $email->sender = $emailSettings['senderName'];
        $email->fromName = craft()->getSiteName();
        $email->toEmail = $customerEmail;
        $email->subject = $product->getName() . ' is back in stock';
        $email->body = 'Dear ' . $customerEmail . ' <a href="' . $product->getUrl() . '">' . $product->getName() . '</a> is in stock. We hope you enjoy it!';

        if (craft()->email->sendEmail($email))
            return true;

        return false;
    }

}