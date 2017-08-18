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
                if(craft()->request->getPost('newPassword'))
                {
                    $password = craft()->request->getPost('newPassword');
                }
                elseif(craft()->request->getPost('password'))
                {
                    $password = craft()->request->getPost('password');
                }
                $passwordConfirm = craft()->request->getPost('passwordConfirm');
                if(isset($passwordConfirm) && strcmp($password, $passwordConfirm) !== 0)
                {
                    $event->params['user']->addErrors(array('passwordConfirm' => Craft::t('Passwords do not match')));
                    $event->performAction = false;
                }
            }
        });

        /*  So far this would only be called when the discount has matched with everything else about the product. */
        craft()->on('commerce_discounts.onBeforeMatchLineItem',
            function($event){

                $lineItem = $event->params['lineItem'];
                $discount = $event->params['discount'];

                if (stripos($discount->description, 'only') === false) {
                    return; /* do nothing, and let the discount match as it normally would, because the discount does not have 'only' in the description. */
                } else {
                    if (stripos($discount->description, $lineItem->sku) === false) {
                        $event->performAction = false; /* since this SKU is not in the description string, then don't apply this discount */
                    }
                }
            });

        //make sure we have enough stock
        craft()->on('commerce_cart.onBeforeAddToCart', function(Event $event) {
            //only do this with product with master stock
            $productTypesByHandle = ['flower', 'oil'];
            $purchasable = $event->params['lineItem']->getPurchasable();
            $productHandle = $purchasable->product->getType()->handle;

            if (in_array($productHandle, $productTypesByHandle))
            {
                $fieldNames = ['gramToGrams', 'eighthToGrams', 'quarterToGrams', 'halfToGrams', 'ounceToGrams'];


                $quantity = $event->params['lineItem']->qty;

                $productStock = $purchasable->product->getTotalStock();

                $variant = craft()->commerce_variants->getVariantById($purchasable->id);
                $vWeight = $variant->variantWeight->value;

                $field = $fieldNames[$vWeight - 1];
                $settings = craft()->globals->getSetByHandle('gramWeights');
                $multiplier = $settings->$field;
                $totalGramAmount = $quantity * $multiplier;

                if ($totalGramAmount > $productStock)
                {
                    $cart = craft()->commerce_cart->getCart();
                    $cart->addError('stock', Craft::t($purchasable->product->getName() . ' Could not be added to your cart. There is ' . $productStock . 'g' . ' of stock left'));
                    $event->performAction = false;
                }
            }

        });

        //make sure we have enough stock do this on save order as well as this is called before payment
      /*  craft()->on('commerce_orders.onBeforeSaveOrder', function(Event $event) {

            $fieldNames = ['gramToGrams', 'eighthToGrams', 'quarterToGrams', 'halfToGrams', 'ounceToGrams'];
            $order = $event->params['order'];
            $lineItems = $order->lineItems;

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
        });*/

        //make sure we have enough stock do this on save order as well as this is called before payment
        //we may want to leave this out if we want customers to pay and we
        // contact them after saying we are out of such and such would you take another
       /* craft()->on('commerce_orders.onBeforeOrderComplete', function(Event $event) {

           $fieldNames = ['gramToGrams', 'eighthToGrams', 'quarterToGrams', 'halfToGrams', 'ounceToGrams'];
            $order = $event->params['order'];
            $lineItems = $order->lineItems;

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
        });*/

        //change the field on the product for its average rating everytime a review is updated.
        craft()->on('commentsRating.onSaveCommentRating', function(Event $event) {

            $params = $event->params;
            $elementId = $params['commentRating']['elementId'];
            $approved = $params['commentRating']['approved'];
            $customerReview = $params['commentRating']['userReview'];

            if(is_numeric($elementId))
            {
                $product = craft()->commerce_products->getProductById($elementId);

                if($product instanceof Commerce_ProductModel)
                {
                    $message = 'Thank you for reviewing ' . $product->getName() . '. Your review will appear after moderation.';

                    $productAvgRating = craft()->commentsRating->elementAvgRatings($elementId);
                    $productNumberRatings = craft()->commentsRating->elementTotalRatings($elementId);

                    if(is_numeric($productAvgRating) && is_numeric($productNumberRatings))
                    {
                        if(! craft()->thcpost_thcpost->saveProductRatings($product->id, $productAvgRating, $productNumberRatings))
                        {
                            ThcpostPlugin::log('REVIEW PRODUCT SAVE ERROR: ' . $product->getName());
                        }
                    }
                    if($customerReview){
                        craft()->userSession->setNotice(Craft::t($message));
                    }
                }
            }
        });
        //we need to less the stock here this is called on each variant on order complete, only do this on
        //flowers and oil or any products that use grams
        craft()->on('commerce_orders.onOrderComplete', function($event){
            $order = $event->params['order'];
            $lineItems = $order->lineItems;

            //2 = flower, 3 = oil
            $productTypesByID = [2, 3];
            $products = [];
            $fieldNames = ['gramToGrams', 'eighthToGrams', 'quarterToGrams', 'halfToGrams', 'ounceToGrams'];

            foreach ($lineItems as $lineitem)
            {
                $product = $lineitem->purchasable->product;
                $variantId = $lineitem->purchasable->id;
                $productDefaultVariantID = $product->defaultVariant->id;
                $productDefaultVariantStock = $product->getTotalStock();

                //we have this product already and its one of the products that we do this too
                // and its not the default variant as it will less the stock for this on its own later
                // so we just want other variants and not less extra stock
                //if already in the products list update the qty
                if(in_array($product->typeId, $productTypesByID))
                {
                    $vWeight = $lineitem->purchasable->variantWeight->value;

                    $field = $fieldNames[$vWeight - 1];
                    $settings = craft()->globals->getSetByHandle('gramWeights');
                    $variantWeightMultiplier = $settings->$field;

                    if((!array_key_exists($product->id, $products)) && $productDefaultVariantID != $variantId)
                    {
                        $productInfo = array(
                                'productTitle' => $product->title,
                                'defaultVariantId' => $productDefaultVariantID,
                                'qty' => ($lineitem->qty * $variantWeightMultiplier),
                                'defaultVariantStock' => $productDefaultVariantStock
                        );
                        $products[$product->id ] = $productInfo;
                    }
                    else if(array_key_exists($product->id, $products))
                    {
                        $products[$product->id]['qty'] = $products[$product->id]['qty'] + ($lineitem->qty * $variantWeightMultiplier);
                    }

                }
            }
            foreach ($products as $k => $product)
            {
                $vStock = craft()->thcpost_thcpost->getVariantStock($product['defaultVariantId']);

                if($vStock)
                {

                    $productQuantity = $product['qty'];
                    $needRound = !is_int($productQuantity);
                    $roundUp = false;
                    //check if we need to round
                    if($needRound)
                    {
                        $roundUp = craft()->thcpost_thcpost->getProductRound($k);

                        if($roundUp)
                        {
                            $productQuantity = ceil($productQuantity);
                        }
                        else
                        {
                            $productQuantity = floor($productQuantity);
                        }
                    }

                    $stockToLess = $vStock - $productQuantity;
                    craft()->thcpost_thcpost->adjustVariantStock($product['defaultVariantId'], $stockToLess);
                    $newStock  = craft()->thcpost_thcpost->getVariantStock($product['defaultVariantId']);

                    $msg = '';

                    if($needRound){
                        $msg = '[STOCK-LESS-SUCCESS] We rounded , '. $roundUp .', lessed stock new calced qty:' . $stockToLess . ' ACTUAL NEW QTY IN DB: ' . $newStock  .' product: ' . $product['productTitle'] . ' variantId: ' . $product['defaultVariantId'] . ' qty: ' . $product['qty'] . ' totalStock: ' . $product['defaultVariantStock'] . 'stockdbBefore: ' . $vStock;
                    }
                    else{
                        $msg = '[STOCK-LESS-SUCCESS] lessed stock new calced qty:' . $stockToLess . ' ACTUAL NEW QTY IN DB: ' . $newStock  .' product: ' . $product['productTitle'] . ' variantId: ' . $product['defaultVariantId'] . ' qty: ' . $product['qty'] . ' totalStock: ' . $product['defaultVariantStock'] . 'stockdbBefore: ' . $vStock;
                    }

                    ThcpostPlugin::log($msg);
                }
                else{
                    ThcpostPlugin::log('[STOCK-LESS-ERR] Error getting variant stock and less stock ' . $product['productTitle'] . ' variantId: ' . $product['defaultVariantId'] . ' qty: ' . $product['qty'] . ' totalStock: ' . $product['defaultVariantStock']);
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

    public function addTwigExtension()
    {
       /* Craft::import('plugins.thcpost.twigextensions.Thcpost_TwigExtentions');
        return new Thcpost_TwigExtensions();*/
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