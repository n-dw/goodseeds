<?php
/**
 * Customer Points plugin for Craft CMS
 *
 * CustomerPoints Record
 *
 * --snip--
 * Active record models (or “records”) are like models, except with a database-facing layer built on top. On top of
 * all the things that models can do, records can:
 *
 * - Define database table schemas
 * - Represent rows in the database
 * - Find, alter, and delete rows
 *
 * Note: Records’ ability to modify the database means that they should never be used to transport data throughout
 * the system. Their instances should be contained to services only, so that services remain the one and only place
 * where system state changes ever occur.
 *
 * When a plugin is installed, Craft will look for any records provided by the plugin, and automatically create the
 * database tables for them.
 *
 * https://craftcms.com/docs/plugins/records
 * --snip--
 *
 * @author    Nathan de Waard
 * @copyright Copyright (c) 2017 Nathan de Waard
 * @link      https://github.com/n-dw
 * @package   CustomerPoints
 * @since     1.0.0
 */

namespace Craft;

class CustomerPoints_PointEventRecord extends BaseRecord
{
    /**
     * Returns the name of the database table the model is associated with (sans table prefix). By convention,
     * tables created by plugins should be prefixed with the plugin name and an underscore.
     *
     * @return string
     */
    public function getTableName()
    {
        return 'customerpoints_pointevent';
    }

    /**
     * Returns an array of attributes which map back to columns in the database table.
     *
     * eventType = self::Potential - user places order but has not paid yet.
     * @access protected
     * @return array
     */
    protected function defineAttributes()
    {
        return array(
            'eventType' => [
            AttributeType::Enum,
                'values' => [CustomerPoints_PointEventModel::REVIEW, CustomerPoints_PointEventModel::REFERRAL, CustomerPoints_PointEventModel::ORDER, CustomerPoints_PointEventModel::REGISTER],
                'required' => true
            ],
            'action' => [
                AttributeType::Enum,
                'values' => [CustomerPoints_PointEventModel::EARN, CustomerPoints_PointEventModel::REDEEM],
                'required' => true
            ],
            'points' => [ AttributeType::Number, 'required' => true ],
            'eventId' => [ AttributeType::Number, 'required' => true ],
            'actionPending' => [ AttributeType::Bool, 'default' => false ],
        );
    }

    /**
     * If your record should have any relationships with other tables, you can specify them with the
     * defineRelations() function
     *
     * @return array
     */
    public function defineRelations()
    {
        return [
            'customerPoints' => [static::BELONGS_TO, 'CustomerPoints_UserRecord', 'onDelete' => static::CASCADE],
            'element'  => [static::BELONGS_TO, 'ElementRecord', 'id', 'required' => true, 'onDelete' => static::CASCADE],
        ];
    }
}