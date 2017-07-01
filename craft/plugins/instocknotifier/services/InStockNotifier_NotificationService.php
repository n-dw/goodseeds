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
            if($record){
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


    public function deleteNotificationRequest($id)
    {
        InStockNotifier_NotificationRecord::model()->deleteByPk($id);
    }

    /*
     * Cleans up the
     */
    public function cleanNotificationRequests()
    {
    }

}