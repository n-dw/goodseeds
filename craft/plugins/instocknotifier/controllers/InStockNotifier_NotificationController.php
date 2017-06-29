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
        $productName =  craft()->request->getPost('productName');

        //add record of user
        //first check to see if its already inplace if so sent a message sayting you have already requested for this product
        //then add record
        //on error through error
        craft()->userSession->setNotice(Craft::t($customerEmail . ' has been added and you will be notified when ' . $productName . ' is restocked.'));
    }
}