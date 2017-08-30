<?php
/**
 * Customer Points plugin for Craft CMS
 *
 * Rewards points for customer
 *
 * --snip--
 * Craft plugins are very much like little applications in and of themselves. We’ve made it as simple as we can,
 * but the training wheels are off. A little prior knowledge is going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL, as well as some semi-
 * advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://craftcms.com/docs/plugins/introduction
 * --snip--
 *
 * @author    Nathan de Waard
 * @copyright Copyright (c) 2017 Nathan de Waard
 * @link      https://github.com/n-dw
 * @package   CustomerPoints
 * @since     1.0.0
 */

namespace Craft;
Craft::import('plugins.customerpoints.adjusters.CustomerPointsAdjuster');

class CustomerPointsPlugin extends BasePlugin
{
    /**
     * Called after the plugin class is instantiated; do any one-time initialization here such as hooks and events:
     *
     * craft()->on('entries.saveEntry', function(Event $event) {
     *    // ...
     * });
     *
     * or loading any third party Composer packages via:
     *
     * require_once __DIR__ . '/vendor/autoload.php';
     *
     * @return mixed
     */
    public function init()
    {
        parent::init();

       //since on order complete we have a customer add points to there account after payment recieved
        //since we use e transfer watch out for fuckers finishing orders and not paing and using the points - could look for status change on order to paid as well..
        // use for from payment pending commerce_orderHistories.onStatusChange
        craft()->on('commerce_orders.onOrderComplete', function($event){
            $order = $event->params['order'];
            $lineItems = $order->lineItems;
            $settings = craft()->plugins->getPlugin('customerpoints')->getSettings();
            //if there are discounts we dont want to give out extra points
            $itemTotal = $order->itemTotal;
        });
    }

    /**
     * Returns the user-facing name.
     *
     * @return mixed
     */
    public function getName()
    {
         return Craft::t('Customer Points');
    }

    /**
     * Plugins can have descriptions of themselves displayed on the Plugins page by adding a getDescription() method
     * on the primary plugin class:
     *
     * @return mixed
     */
    public function getDescription()
    {
        return Craft::t('Rewards points for customer');
    }

    /**
     * Plugins can have links to their documentation on the Plugins page by adding a getDocumentationUrl() method on
     * the primary plugin class:
     *
     * @return string
     */
    public function getDocumentationUrl()
    {
        return 'https://github.com/n-dw/customerpoints/blob/master/README.md';
    }

    /**
     * Plugins can now take part in Craft’s update notifications, and display release notes on the Updates page, by
     * providing a JSON feed that describes new releases, and adding a getReleaseFeedUrl() method on the primary
     * plugin class.
     *
     * @return string
     */
    public function getReleaseFeedUrl()
    {
        return 'https://raw.githubusercontent.com/n-dw/customerpoints/master/releases.json';
    }

    /**
     * Returns the version number.
     *
     * @return string
     */
    public function getVersion()
    {
        return '1.0.0';
    }

    /**
     * As of Craft 2.5, Craft no longer takes the whole site down every time a plugin’s version number changes, in
     * case there are any new migrations that need to be run. Instead plugins must explicitly tell Craft that they
     * have new migrations by returning a new (higher) schema version number with a getSchemaVersion() method on
     * their primary plugin class:
     *
     * @return string
     */
    public function getSchemaVersion()
    {
        return '1.0.0';
    }

    /**
     * Returns the developer’s name.
     *
     * @return string
     */
    public function getDeveloper()
    {
        return 'Nathan de Waard';
    }

    /**
     * Returns the developer’s website URL.
     *
     * @return string
     */
    public function getDeveloperUrl()
    {
        return 'https://github.com/n-dw';
    }

    /**
     * Returns whether the plugin should get its own tab in the CP header.
     *
     * @return bool
     */
    public function hasCpSection()
    {
        return true;
    }

    /**
     * Called right before your plugin’s row gets stored in the plugins database table, and tables have been created
     * for it based on its records.
     */
    public function onBeforeInstall()
    {
    }

    /**
     * Called right after your plugin’s row has been stored in the plugins database table, and tables have been
     * created for it based on its records.
     */
    public function onAfterInstall()
    {
    }

    /**
     * Called right before your plugin’s record-based tables have been deleted, and its row in the plugins table
     * has been deleted.
     */
    public function onBeforeUninstall()
    {
    }

    /**
     * Called right after your plugin’s record-based tables have been deleted, and its row in the plugins table
     * has been deleted.
     */
    public function onAfterUninstall()
    {
    }

    /**
     * @param array|BaseModel $values
     */
    public function setSettings($values)
    {
        if (!$values)
        {
            $values = array();
        }

        if (is_array($values))
        {
            // Merge in any values that are stored in craft/config/customerpoints.php
            foreach ($this->getSettings() as $key => $value)
            {
                $configValue = craft()->config->get($key, 'customerpoints');

                if ($configValue !== null)
                {
                    $values[$key] = $configValue;
                }
            }
        }

        parent::setSettings($values);
    }

    /**
     * Defines the attributes that model your plugin’s available settings.
     *
     * @return array
     */
    protected function defineSettings()
    {
        return [
            //GENERAL
            'customerPointsName'          => [AttributeType::String, 'default' => Craft::t('Customer Points'), 'required' => true],
            'errorFlashMessage'           => [AttributeType::String, 'default' => Craft::t('There was an error redeeming your points'), 'required' => true],
            'successFlashMessage'         => [AttributeType::String, 'default' => Craft::t('Your Points have been applied'), 'required' => true],
            ///PURCHASES
            'earnPointsPurchases'         => [AttributeType::Bool, 'default' => true],
            'pointsPerDollarSpent'        => [AttributeType::Number, 'default' => 0, 'required' => true],
            'pointsPerDollarEarned'       => [AttributeType::Number, 'default' => 0, 'required' => true],
            'minimumCartSubTotal'         => [AttributeType::Number, 'default' => 0, 'required' => true],
            'minimumPointsRedemption'     => [AttributeType::Number, 'default' => 0, 'required' => true],
            'minimumPointsFirstRedemption' => [AttributeType::Number, 'default' => 0, 'required' => true],
            'maxAmountRedeemable'         => [AttributeType::Number, 'required' => true],
            'maxPointsRedeemable'         => [AttributeType::Number, 'required' => true],
            'allowWithDiscounts'          => [AttributeType::Bool, 'default' => false],
            'earnWithRedemption'          => [AttributeType::Bool, 'default' => false],
            'canRedeemFirstPurchase'      => [AttributeType::Bool, 'default' => false],
            //points for order if they want to give n points per order instead of for order subtotal
            'pointsForOrder'              => [AttributeType::Bool, 'default' => false],
            'pointsPerPurchase'             => [AttributeType::Number, 'default' => 0, 'required' => true],
            //REVIEWS
            'earnPointsReviews'           => [AttributeType::Bool, 'default' => false],
            'oneReviewPerProduct'         => [AttributeType::Bool, 'default' => false],
            'pointsPerReview'             => [AttributeType::Number, 'default' => 0, 'required' => true],
            //ACCOUNT CREATION
            'earnPointsAccountCreation'   => [AttributeType::Bool, 'default' => false],
            'pointsPerAccount'            => [AttributeType::Number, 'default' => 0, 'required' => true],
            //SOCIAL PROMOTION
            'earnPointsSocialPromotion'   => [AttributeType::Bool, 'default' => false],
            'pointsPerPromotion'          => [AttributeType::Number, 'default' => 0, 'required' => true],
            //REFERRALS
            'earnPointsReferrals'         => [AttributeType::Bool, 'default' => false],
            'pointsPerReferral'           => [AttributeType::Number, 'default' => 0, 'required' => true],
        ];
    }

    public function registerCpRoutes()
    {
        return [
            'customerPoints/edit/(?P<customerPointsEventId>\d+)' => ['action' => 'customerPoints/editPointsEvent'],
            'customerpoints/settings' => ['action' => 'customerPoints/settings'],
        ];
    }

    public function getSettingsUrl()
    {
        return 'customerpoints/settings';
    }

    /**
     * Returns the HTML that displays your plugin’s settings.
     *
     * @return mixed
     */
   /* public function getSettingsHtml()
    {
       return craft()->templates->render('customerpoints/CustomerPoints_Settings', array(
           'settings' => $this->getSettings()
       ));
    }*/

    /**
     * If you need to do any processing on your settings’ post data before they’re saved to the database, you can
     * do it with the prepSettings() method:
     *
     * @param mixed $settings  The plugin's settings
     *
     * @return mixed
     */
    public function prepSettings($settings)
    {
        // Modify $settings here...

        return $settings;
    }

}