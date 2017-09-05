<?php
/**
 * Customer Referral Program plugin for Craft CMS
 *
 * Double Sided Customer Referral Program which integrates with the Customer Points Plugin
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
 * @author    NdW
 * @copyright Copyright (c) 2017 NdW
 * @link      natedewaard.com
 * @package   CustomerReferralProgram
 * @since     1.0.0
 */

namespace Craft;

class CustomerReferralProgramPlugin extends BasePlugin
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

        //user
        craft()->on('users.onActivateUser',
                    [
                        $this,
                        'onActivateUser'
                    ]
        );


        //order complete
        craft()->on('commerce_orders.onOrderComplete',
                    [
                        $this,
                        'onOrderComplete'
                    ]
        );


        //order.status - payment pending
        craft()->on('commerce_orderHistories.onStatusChange',
                    [
                        $this,
                        'onStatusChange'
                    ]
        );

        craft()->on('email.onSendEmailError',
                    [
                        $this,
                        'onSendEmailError'
                    ]
        );

        //email.onSendEmailError #
        //set sendfail
    }

    /**
     * @param Event $event
     * Params:
     * order – the Order Model after being saved.
     * orderHistory – the Order History Model that was created and which updated the order’s status.
     */
    public function onStatusChange(Event $event)
    {
        //here when we change the status to paid
        //referral
    }
    /**
     * @param Event $event
     * Params:
     * order – the cart Order Model that is about to be marked as complete and become an order.
     */
    public function onOrderComplete(Event $event)
    {

    }
    /**
     * @param Event $event
     * Params:

    user – A UserModel object representing the user that has just been activated.
     * check if they have a referral cookie and their name is in db
     */
    public function onActivateUser(Event $event)
    {

    }
    /**
     * @param Event $event
     * Params:

        user – A UserModel object representing the user that received the email.
        emailModel – The EmailModel defining the email that was sent.
        variables – Any variables that were available to the email template.
        error – The error defined by PHPMailer.
     */
    public function onSendEmailError(Event $event)
    {

    }
    /**
     * Returns the user-facing name.
     *
     * @return mixed
     */
    public function getName()
    {
         return Craft::t('Customer Referral Program');
    }

    /**
     * Plugins can have descriptions of themselves displayed on the Plugins page by adding a getDescription() method
     * on the primary plugin class:
     *
     * @return mixed
     */
    public function getDescription()
    {
        return Craft::t('Double Sided Customer Referral Program which integrates with the Customer Points Plugin');
    }

    /**
     * Plugins can have links to their documentation on the Plugins page by adding a getDocumentationUrl() method on
     * the primary plugin class:
     *
     * @return string
     */
    public function getDocumentationUrl()
    {
        return 'https://github.com/n-dw/customerreferralprogram/blob/master/README.md';
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
        return 'https://raw.githubusercontent.com/n-dw/customerreferralprogram/master/releases.json';
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
        return 'NdW';
    }

    /**
     * Returns the developer’s website URL.
     *
     * @return string
     */
    public function getDeveloperUrl()
    {
        return 'natedewaard.com';
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
     * Add any Twig extensions.
     *
     * @return mixed
     */
    public function addTwigExtension()
    {
        Craft::import('plugins.customerreferralprogram.twigextensions.CustomerReferralProgramTwigExtension');

        return new CustomerReferralProgramTwigExtension();
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
            'someSetting' => array(AttributeType::String, 'label' => 'Some Setting', 'default' => ''),
        );
    }

    /**
     * Returns the HTML that displays your plugin’s settings.
     *
     * @return mixed
     */
    public function getSettingsHtml()
    {
       return craft()->templates->render('customerreferralprogram/CustomerReferralProgram_Settings', array(
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