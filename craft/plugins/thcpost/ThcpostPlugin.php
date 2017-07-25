<?php
/**
 * thcpost plugin for Craft CMS
 *
 * Do various things we need to do such as a controller for search.
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
 * @package   Thcpost
 * @since     1.0.0
 */

namespace Craft;

class ThcpostPlugin extends BasePlugin
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
        /*
        * Password Confirmation field
        */
        craft()->on('users.onBeforeSaveUser', function(Event $event) {

            // Only do anything if it is a front end submission
            if(craft()->request->isSiteRequest())
            {
                $password = craft()->request->getPost('password');
                $passwordConfirm = craft()->request->getPost('passwordConfirm');
                if(isset($passwordConfirm) && strcmp($password, $passwordConfirm) !== 0)
                {
                    $event->params['user']->addErrors(array('passwordConfirm' => Craft::t('Passwords do not match')));
                    $event->performAction = false;
                }
            }
        });

        //change the field on the product for its average rating everytime a review is updated.
        craft()->on('commentsRating.onSaveCommentRating', function(Event $event) {

            $params = $event->params;
            $elementId = $params['commentRating']['elementId'];

            // Only do anything if it is a front end submission
            if(is_numeric($elementId))
            {
                $product = craft()->commerce_products->getProductById($elementId);

                if($product instanceof Commerce_ProductModel)
                {

                    $productAvgRating = craft()->commentsRating->elementAvgRatings($elementId);
                    $productNumberRatings = craft()->commentsRating->elementTotalRatings($elementId);

                    if(is_numeric($productAvgRating))
                    {
                         $product->setContentFromPost(array('averageRating' => $productAvgRating, 'totalRatings' => $productNumberRatings));
                      /* // $product->getContent()->setAttributes(array(
                                                                  'averageRating' => $productAvgRating,
                                                                  'totalRatings' => $productNumberRatings
                                                              ));*/

                        if (! craft()->commerce_products->saveProduct($product))
                        {
                            craft()->userSession->setError(Craft::t('Couldn’t save product.'));
                        }


                    }

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
        return Craft::t('thcpost');
    }

    /**
     * Plugins can have descriptions of themselves displayed on the Plugins page by adding a getDescription() method
     * on the primary plugin class:
     *
     * @return mixed
     */
    public function getDescription()
    {
        return Craft::t('Do various things we need to do such as a controller for search.');
    }

    /**
     * Plugins can have links to their documentation on the Plugins page by adding a getDocumentationUrl() method on
     * the primary plugin class:
     *
     * @return string
     */
    public function getDocumentationUrl()
    {
        return '???';
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
        return '???';
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
}