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

class CustomerPointsEventRecord extends BaseRecord
{
    /**
     * Returns the name of the database table the model is associated with (sans table prefix). By convention,
     * tables created by plugins should be prefixed with the plugin name and an underscore.
     *
     * @return string
     */
    public function getTableName()
    {
        return 'customer_points_events';
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
                'values' => [self::Review, self::Referral, self::Order, self::Potential],
                'required' => true
            ],
            'action' => [
                AttributeType::Enum,
                'values' => [self::Earn, self::Redeem, self::FailRedeem, self::PotentialEarn],
                'required' => true
            ],
            'points' => [ AttributeType::Number, 'required' => true ],
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
        return array(
            'customerPoints' => array(static::BELONGS_TO, 'CustomerPointsRecord', 'onDelete' => static::CASCADE),
            'CustomerPointsEventsType' => array(static::HAS_MANY, 'CustomerPointsEventsTypeRecord')
        );
    }
}