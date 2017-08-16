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
        craft()->on('commerce_orders.onOrderComplete', function($event){
            $order = $event->params['order'];
            $lineItems = $order->lineItems;
            $settings = craft()->plugins->getPlugin('customerpoints')->getSettings();


            //if there are discounts we dont want to give out extra points
            $itemTotal = $order->itemTotal;



            $variantsToLessStock = [];

            foreach ($lineItems as $lineitem){
                $purchasable = $lineitem->getPurchasable();
                $quantity =  $lineitem->qty;

                $productStock = $purchasable->product->getTotalStock();
                $variant = craft()->commerce_variants->getVariantById($purchasable->id);
                $vWeight = $variant->variantWeight->value;

                $field = $fieldNames[$vWeight -1];
                $settings = craft()->globals->getSetByHandle('gramWeights');
                $multiplier = $settings->$field;
                $totalGramAmount = $quantity * $multiplier;

                if($totalGramAmount > $productStock)
                {
                    $cart = craft()->commerce_cart->getCart();
                    craft()->userSession->setError(Craft::t($purchasable->product->getName() . ' Could not be added to your cart. There is '. $productStock . 'g' .' of stock left.'));
                    $cart->addError( 'stock', Craft::t($purchasable->product->getName() . ' Could not be added to your cart. There is '. $productStock . 'g' .' of stock left.'));
                    $event->performAction = false;
                    break;
                }
            }
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
        return false;
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
     * Defines the attributes that model your plugin’s available settings.
     *
     * @return array
     */
    protected function defineSettings()
    {
        return array(
            'pointsPerDollerSpent'        => array(AttributeType::Number, 'required' => true),
            'pointsPerDollerEarned'       => array(AttributeType::Number, 'required' => true),
            'maxAmountRedeemable'         => array(AttributeType::Number, 'required' => true),
            'allowWithDiscounts'          => AttributeType::Bool,
            'earnWithRedemption'          => AttributeType::Bool,
            'successFlashMessage'         => array(AttributeType::String, 'default' => Craft::t('Your Points have been applied'), 'required' => true),
        );
    }

    /**
     * Returns the HTML that displays your plugin’s settings.
     *
     * @return mixed
     */
    public function getSettingsHtml()
    {
       return craft()->templates->render('customerpoints/CustomerPoints_Settings', array(
           'settings' => $this->getSettings()
       ));
    }

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