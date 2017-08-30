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

class CustomerPointsModel extends BaseModel
{
    /**
     * Defines this model's attributes.
     *
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'id'             => array(AttributeType::Number),
            'email'          => array(AttributeType::String),
            'points'         => array(AttributeType::Number),
            'pointsUsed'     => array(AttributeType::Number),
            'totalPointsAcquired' => array(AttributeType::Number),
        ));
    }

    public function defineRelations()
    {
        return array(
            'customer'  => array(static::BELONGS_TO, 'Commerce_CustomerRecord', 'required' => true, 'onDelete' => static::CASCADE)
        );
    }

}