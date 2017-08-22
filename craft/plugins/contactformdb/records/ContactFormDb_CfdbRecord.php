<?php
/**
 * Contact Form DB plugin for Craft CMS
 *
 * ContactFormDb_Cfdb Record
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
 * @link      natedewaard.com
 * @package   ContactFormDb
 * @since     1.0.0
 */

namespace Craft;

class ContactFormDb_CfdbRecord extends BaseRecord
{
    /**
     * Returns the name of the database table the model is associated with (sans table prefix). By convention,
     * tables created by plugins should be prefixed with the plugin name and an underscore.
     *
     * @return string
     */
    public function getTableName()
    {
        return 'contactformdb_cfdb';
    }

    public function scopes()
    {
        return array(
            'ordered' => array('order' => 'dateCreated'),
        );
    }

    /**
     * Returns an array of attributes which map back to columns in the database table.
     *
     * @access protected
     * @return array
     */
    protected function defineAttributes()
    {
        return array(
            'status'            => array(AttributeType::Enum, 'values' => array(
                                        ContactFormDb_CfdbModel::UNREAD,
                                        ContactFormDb_CfdbModel::READ,
                                        ContactFormDb_CfdbModel::ARCHIVED,
                                        ContactFormDb_CfdbModel::RESPONDED,
                                        ContactFormDb_CfdbModel::RESOLVED,
                                        ContactFormDb_CfdbModel::SPAM,
                                        ContactFormDb_CfdbModel::TRASHED
            )),
            'name'              => array(AttributeType::String, 'required' => true),
            'email'             => array(AttributeType::String, 'required' => true),
            'inquiryType'       => array(AttributeType::String, 'default' => null),
            'message'           => array(AttributeType::String, 'default' => false),
            'answered'          => array(AttributeType::Bool, 'default' => false),
            'answeredDate'      => array(AttributeType::DateTime, 'default' => null),
            'archived'          => array(AttributeType::Bool, 'default' => false),
            'archivedDate'      => array(AttributeType::DateTime, 'default' => null),
            'ipAddress'         => array(AttributeType::String, 'default' => null),
            'userAgent'         => array(AttributeType::String, 'default' => null),
            'urlReferrer'       => array(AttributeType::String, 'default' => null),
        );
    }

    public function defineRelations()
    {
        return array(
            'element'  => array(static::BELONGS_TO, 'ElementRecord', 'required' => true, 'onDelete' => static::CASCADE)
        );
    }
}