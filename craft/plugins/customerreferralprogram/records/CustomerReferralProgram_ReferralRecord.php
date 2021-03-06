<?php
/**
 * Customer Referral Program plugin for Craft CMS
 *
 * CustomerReferralProgram_Referral Record
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
 * @author    NdW
 * @copyright Copyright (c) 2017 NdW
 * @link      natedewaard.com
 * @package   CustomerReferralProgram
 * @since     1.0.0
 */

namespace Craft;

class CustomerReferralProgram_ReferralRecord extends BaseRecord
{
    /**
     * Returns the name of the database table the model is associated with (sans table prefix). By convention,
     * tables created by plugins should be prefixed with the plugin name and an underscore.
     *
     * @return string
     */
    public function getTableName()
    {
        return 'customerreferralprogram_Referral';
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
            'referralEmail'             => array(AttributeType::String, 'default' => ''),
            'emailSendFail'             => array(AttributeType::Bool, 'default' => false),
            'hasSignedUp'               => array(AttributeType::Bool, 'default' => false),
            'hasPurchased'              => array(AttributeType::Bool, 'default' => false),
            'referrerIpAddress'         => array(AttributeType::String, 'default' => null),
            'referrerUserAgent'         => array(AttributeType::String, 'default' => null),
            'referreeIpAddress'         => array(AttributeType::String, 'default' => null),
            'referreeUserAgent'         => array(AttributeType::String, 'default' => null),
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
            'element'  => [static::BELONGS_TO, 'ElementRecord', 'id', 'required' => true, 'onDelete' => static::CASCADE],
            'user'     => [static::BELONGS_TO, 'UserRecord', 'required' => true],
        );
    }

    /**
     * @inheritDoc BaseRecord::validate()
     *
     * @param null $attributes
     * @param bool $clearErrors
     *
     * @return bool|null
     */
    public function validate($attributes = null, $clearErrors = true)
    {
        //email is email
        if (!filter_var($this->referralEmail, FILTER_VALIDATE_EMAIL))
        {
            $this->addError('referralEmail', Craft::t('Please Enter a Valid Email Address.'));
        }

        return parent::validate($attributes, false);
    }
}