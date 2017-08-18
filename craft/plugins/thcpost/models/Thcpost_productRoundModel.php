<?php
/**
 * Thcpost plugin for Craft CMS
 *
 * Thcpost_productRoundModel Model
 *
 * --snip--
 * Models are containers for data. Just about every time information is passed between services, controllers, and
 * templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 * --snip--
 *
 * @author    NdW
 * @copyright Copyright (c) 2017 NdW
 * @link      natedewaard.com
 * @package   InStockNotifier
 * @since     1.0.0
 */

namespace Craft;
/**
 * Thcpost model.
 *
 * @property int $id
 * @property int $productId
 * @property bool $lastRoundUp
 *
 * @author    Nathan de Waard
 */

class Thcpost_productRoundModel extends BaseModel
{
    /**
     * Defines this model's attributes.
     *
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'id'                => AttributeType::Number,
            'productId'         => array(AttributeType::Number),
            'lastRoundUp'       => array(AttributeType::Bool, 'default' => false)
        ));
    }
}