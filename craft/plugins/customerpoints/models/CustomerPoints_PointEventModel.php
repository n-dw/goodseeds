<?php
/**
 * Customer Points plugin for Craft CMS
 *
 * CustomerPoints Model
 *
 * --snip--
 * Models are containers for data. Just about every time information is passed between services, controllers, and
 * templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 * --snip--
 *
 * @author    Nathan de Waard
 * @copyright Copyright (c) 2017 Nathan de Waard
 * @link      https://github.com/n-dw
 * @package   CustomerPoints
 * @since     1.0.0
 */

namespace Craft;

class CustomerPoints_PointEventModel extends BaseElementModel
{
    /**
     * @var string
     */
    protected $elementType = 'CustomerPoints_PointEvent';

    const REVIEW   = 'Pending';
    const REFERRAL = 'Approved';
    const ORDER    = 'Trashed';
    const REGISTER = 'Register';

    const EARN    = 'Earn';
    const REDEEM  = 'Redeem';

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
        return UrlHelper::getCpUrl('customerpoints/points/edit/' . $this->id);
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
            'id'             => [AttributeType::Number],
            'customerPointsId' => [AttributeType::Number],
            'eventType' => [
                AttributeType::Enum,
                'values' => [CustomerPoints_PointEventModel::REVIEW, CustomerPoints_PointEventModel::REFERRAL, CustomerPoints_PointEventModel::ORDER, CustomerPoints_PointEventModel::REGISTER],
            ],
            'action' => [
                AttributeType::Enum,
                'values' => [CustomerPoints_PointEventModel::EARN, CustomerPoints_PointEventModel::REDEEM],
            ],
            'points' => [ AttributeType::Number],
            'eventId' => [ AttributeType::Number],
            'actionPending' =>  [ AttributeType::Boolean],
        ));
    }

}