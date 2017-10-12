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

class CustomerPoints_ReviewModel extends BaseElementModel
{
    /**
     * @var string
     */
    protected $elementType = 'CustomerPoints_Review';

    const PENDING       = 'Pending';
    const APPROVED      = 'Approved';
    const TRASHED       = 'Trashed';

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
        return UrlHelper::getCpUrl('customerpoints/reviews/edit/' . $this->id);
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
            'productId'     => array(AttributeType::Number),
            'status'        => array(AttributeType::Enum, 'values' => array(
                CustomerPoints_ReviewModel::APPROVED,
                CustomerPoints_ReviewModel::PENDING,
                CustomerPoints_ReviewModel::TRASHED,
            )),
            'name'          => array(AttributeType::String),
            'email'         => array(AttributeType::Email),
            'url'           => array(AttributeType::Url),
            'ipAddress'     => array(AttributeType::String),
            'userAgent'     => array(AttributeType::String),
            'review'        => array(AttributeType::Mixed),
            'rating'        => array(AttributeType::Number),
        ));
    }
}