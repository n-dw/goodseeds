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
        $isNew = !$model->id;

        if (!$isNew)
        {
            $record = InStockNotifier_NotificationRecord::model()->findById($model->id);

            //we already have a record
            if($record){
                craft()->userSession->setError(Craft::t('You have already requested notification upon restock for this product.'));
                return false;
            }

        }
        else
        {
            $record = new InStockNotifier_NotificationRecord();

            $record->setAttributes($model->getAttributes(), false);
                
            $record->validate();
            $model->addErrors($record->getErrors());
            
             if (!$model->hasErrors())
             {
                 $transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
                 try
                 {
                     if (craft()->elements->saveElement($model))
                     {
                         if ($isNew)
                         {
                             $record->id = $model->id;
                         }
                         $record->save(false);

                         if ($transaction !== null)
                         {
                             $transaction->commit();
                         }

                         return true;
                     }
                 }
                 catch (\Exception $e)
                 {
                     if ($transaction !== null)
                     {
                         $transaction->rollback();
                     }

                     throw $e;
                 }
             }
             else{
                 return false;
             }
        }

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