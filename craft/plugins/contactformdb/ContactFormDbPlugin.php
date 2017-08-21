<?php
/**
 * Contact Form DB plugin for Craft CMS
 *
 * Saves contact submissions to the Database using the pixel and tonic contact form
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
 * @link      natedewaard.com
 * @package   ContactFormDb
 * @since     1.0.0
 */

namespace Craft;

class ContactFormDbPlugin extends BasePlugin
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
        craft()->on('contactForm.beforeSend', function(ContactFormEvent $event){
            $message = $event->params['message'];

            $submission = new ContactFormDb_CfdbModel();
            $submission->name = $message->fromName;
            $submission->email = $message->fromEmail;
            $submission->inquiryType = $message['messageFields']['inquiryType'];
            $submission->message = $message['messageFields']['body'];

            $submission->urlReferrer = craft()->request->urlReferrer;
            $submission->ipAddress = craft()->request->getUserHostAddress();
            $submission->userAgent = craft()->request->getUserAgent();

            $submission->status = ContactFormDb_CfdbModel::UNREAD;

            $settings = craft()->plugins->getPlugin('contactform')->getSettings();

            try{
               if($this->validateHoneypot($settings->honeypotField, $message))
               {
                   craft()->contactFormDb_cfdb->saveContactFormSubmission($submission);
               }
                return;
            }
            catch(\Exception $e){
                ContactFormDbPlugin::log('Error Saving Contact Submission: ' . $submission->email . ' ' . $submission->name . ' '. $submission->message );
                return;
            }
        });
    }

    /**
     * Checks that the 'honeypot' field has not been filled out (assuming one has been set).
     *
     * @param string $fieldName The honeypot field name.
     * @return bool
     */
    protected function validateHoneypot($fieldName, $message)
    {
        if (!$fieldName)
        {
            return true;
        }

        $honey = isset($message->$fieldName) ? $message->$fieldName : '';
        return $honey == '';
    }

    /**
     * Returns the user-facing name.
     *
     * @return mixed
     */
    public function getName()
    {
         return Craft::t('Contact Form DB');
    }

    /**
     * Plugins can have descriptions of themselves displayed on the Plugins page by adding a getDescription() method
     * on the primary plugin class:
     *
     * @return mixed
     */
    public function getDescription()
    {
        return Craft::t('Saves contact submissions to the Database using the pixel and tonic contact form');
    }

    /**
     * Plugins can have links to their documentation on the Plugins page by adding a getDocumentationUrl() method on
     * the primary plugin class:
     *
     * @return string
     */
    public function getDocumentationUrl()
    {
        return 'https://github.com/n-dW/contactformdb/blob/master/README.md';
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
        return 'https://raw.githubusercontent.com/n-dW/contactformdb/master/releases.json';
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
        return 'natedewaard.com';
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
     * Add any Twig extensions.
     *
     * @return mixed
     */
    public function addTwigExtension()
    {
        Craft::import('plugins.contactformdb.twigextensions.ContactFormDbTwigExtension');

        return new ContactFormDbTwigExtension();
    }

    public function registerCpRoutes()
    {
        return array(
            'contactFormDb/submissions/edit/(?P<cfdbId>\d+)'     => array('action' => 'contactFormDb/cfdb/editSubmission'),
        );
    }

    //if we want emails later on
    public function registerEmailMessages()
    {

    }

    public function registerUserPermissions()
    {
        return array(
            'contactFormDbEdit'     => array('label' => Craft::t('Edit other users\' contact form submissions')),
            'contactFormDbStatus'   => array('label' => Craft::t('Change the status of contact form submissions. (replied to customer) etc')),
            'contactFormDbArchive'  => array('label' => Craft::t('Archive other users\' contact form submissions')),
            'contactFormDbTrash'    => array('label' => Craft::t('Trash other users\' contact form submissions')),
            'contactFormDbDelete'   => array('label' => Craft::t('Delete contact form submissions')),
        );
    }
}