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

class CustomerPoints_UserModel extends BaseElementModel
{
    /**
     * @var string
     */
    protected $elementType = 'CustomerPoints_User';

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
        return UrlHelper::getCpUrl('customerpoints/customers/edit/' . $this->id);
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
            'id'                  => array(AttributeType::Number),
            'customerId'          => array(AttributeType::Number),
            'email'               => array(AttributeType::String),
            'points'              => array(AttributeType::Number),
            'pointsUsed'          => array(AttributeType::Number),
            'totalPointsAcquired' => array(AttributeType::Number),
            'referrerHash'        => array(AttributeType::String)
        ));
    }
}