<?php
/**
 * In Stock Notifier plugin for Craft CMS
 *
 * InStockNotifier_Notification Controller
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
 * @author    NdW
 * @copyright Copyright (c) 2017 NdW
 * @link      natedewaard.com
 * @package   InStockNotifier
 * @since     1.0.0
 */

namespace Craft;

class InStockNotifier_NotificationController extends BaseController
{
    /**
     * Handle a request going to our plugin's index action URL, e.g.: actions/inStockNotifier
     */
    public function actionRequestRestockNotification()
    {
        $this->requirePostRequest();

        $customerEmail = craft()->request->getPost('customerEmail');
        $productId =  craft()->request->getPost('productId');

        if($customerEmail == '' ||  $productId == '' || !is_numeric($productId))
        {
            return false;
        }

        //check is product exists and is actually out of stock
        $product = craft()->commerce_products->getProductById($productId);
        if(!$product || $product->getTotalStock() > 0)
        {
            return false;
        }

        $model = new InStockNotifier_NotificationModel();
        $model->productId = $productId;
        $model->customerEmail = $customerEmail;

        if(craft()->inStockNotifier_notification->saveNotificationRequest($model)){
            craft()->userSession->setNotice(Craft::t($customerEmail . ' has been added and you will be notified when ' . $product->getName() . ' is restocked.'));
        }
        else{
           craft()->userSession->setError(Craft::t('Sorry you couldn\'t be added to the notifications list'));
        }

    }

    public function actionSendNotifications()
    {
        if (craft()->inStockNotifier_notification->processNotifications())
            return true;

        return false;

    }
}