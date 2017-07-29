<?php
/**
 * Variant Discount plugin for Craft CMS
 *
 * Discount is limited to specific variant SKU&#39;s by searching the discount description field for the keyword &#39;ONLY&#39; and the variant&#39;s SKU. e.g. Discount ONLY applies to SKU78439 and SKU94300
 *
 * @author    Luke Holder
 * @copyright Copyright (c) 2017 Luke Holder
 * @link      http://craftcms.stackexchange.com/users/91/luke-holder
 * @package   VariantDiscount
 * @since     1.0.0
 */

namespace Craft;

class VariantDiscountPlugin extends BasePlugin
{
    /**
     * @return mixed
     */
    public function init()
    {
        parent::init();


    }

    /**
     * @return mixed
     */
    public function getName()
    {
         return Craft::t('Variant Discount');
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return Craft::t("Discount is limited to specific variant SKU's by searching the discount description field for the keyword ONLY and the variants SKU. e.g. Discount ONLY applies to SKU78439 and SKU94300");
    }

    /**
     * @return string
     */
    public function getDocumentationUrl()
    {
        return '???';
    }

    /**
     * @return string
     */
    public function getReleaseFeedUrl()
    {
        return '???';
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return '1.0.0';
    }

    /**
     * @return string
     */
    public function getSchemaVersion()
    {
        return '1.0.0';
    }

    /**
     * @return string
     */
    public function getDeveloper()
    {
        return 'Luke Holder';
    }

    /**
     * @return string
     */
    public function getDeveloperUrl()
    {
        return 'http://craftcms.stackexchange.com/users/91/luke-holder';
    }

    /**
     * @return bool
     */
    public function hasCpSection()
    {
        return false;
    }
}
