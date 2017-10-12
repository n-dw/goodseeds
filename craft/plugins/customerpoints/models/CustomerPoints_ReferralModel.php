<?php
/**
 * Customer Referral Program plugin for Craft CMS
 *
 * CustomerReferralProgram_Referral Model
 *
 * --snip--
 * Models are containers for data. Just about every time information is passed between services, controllers, and
 * templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * https://craftcms.com/docs/plugins/working-with-elements
 * --snip--
 *
 * @author    NdW
 * @copyright Copyright (c) 2017 NdW
 * @link      natedewaard.com
 * @package   CustomerReferralProgram
 * @since     1.0.0
 */

namespace Craft;

class CustomerPoints_ReferralModel extends BaseElementModel
{
    /**
     * @var string
     */
    protected $elementType = 'CustomerPoints_Referral';

    public function isEditable()
    {
        return true;
    }

    public function hasTitles()
    {
        return false;
    }

    public function isLocalized()
    {
        return true;
    }

    public function getCpEditUrl()
    {
        return UrlHelper::getCpUrl('customerpoints/referrals/edit/' . $this->id);
    }

    public function getElement()
    {
        return craft()->elements->getElementById($this->elementId);
    }


    public function canEdit()
    {
        $user = craft()->userSession->getUser();

        // Only logged in users can edit a comment
        if (!$user) {
            return false;
        }

        // Check that user is trying to edit their own comment
        if ($user->can('customerpoints-managePoints')) {
            return true;
        }

        return false;
    }

    /**
     * Defines this model's attributes.
     *
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'id'                        => array(AttributeType::Number),
            'customerPointsId'          => array(AttributeType::Number),
            'referralEmail'             => array(AttributeType::String),
            'emailSendFail'             => array(AttributeType::Bool),
            'hasSignedUp'               => array(AttributeType::Bool),
            'hasPurchased'              => array(AttributeType::Bool),
            'referrerIpAddress'         => array(AttributeType::String),
            'referrerUserAgent'         => array(AttributeType::String),
            'referreeIpAddress'         => array(AttributeType::String),
            'referreeUserAgent'         => array(AttributeType::String),
        ));
    }
}