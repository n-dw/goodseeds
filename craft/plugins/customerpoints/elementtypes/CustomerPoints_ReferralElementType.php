<?php
/**
 * Customer Referral Program plugin for Craft CMS
 *
 * CustomerReferralProgram_Referral ElementType
 *
 * --snip--
 * Element Types are the classes used to identify each of these types of elements in Craft. There’s a
 * “UserElementType”, there’s an “AssetElementType”, and so on. If you’ve ever developed a custom Field Type class
 * before, this should sound familiar. The relationship between an element and an Element Type is the same as that
 * between a field and a Field Type.
 *
 * http://pixelandtonic.com/blog/craft-element-types
 * --snip--
 *
 * @author    NdW
 * @copyright Copyright (c) 2017 NdW
 * @link      natedewaard.com
 * @package   CustomerReferralProgram
 * @since     1.0.0
 */

namespace Craft;

require_once(__DIR__ . '/CustomerPoints_BaseElementType.php');

class CustomerPoints_ReferralElementType extends CustomerPoints_BaseElementType
{
    /**
     * Returns this element type's name.
     *
     * @return mixed
     */
    public function getName()
    {
        return Craft::t('Referral');
    }

    /**
     * Returns whether this element type has content.
     *
     * @return bool
     */
    public function hasContent()
    {
        return true;
    }

    /**
     * Returns whether this element type has titles.
     *
     * @return bool
     */
    public function hasTitles()
    {
        return true;
    }

    /**
     * Returns whether this element type can have statuses.
     *
     * @return bool
     */
    public function hasStatuses()
    {
        return  false;
    }

    /**
     * Returns whether this element type is localized.
     *
     * @return bool
     */
    public function isLocalized()
    {
        return true;
    }

    /**
     * Returns this element type's sources.
     *
     * @param string|null $context
     * @return array|false
     */
    public function getSources($context = null)
    {
        $sources = array(
            '*' => array(
                'label'    => Craft::t('All referrals'),
            )
        );

        return $sources;
    }

    /**
     * Returns the attributes that can be shown/sorted by in table views.
     *
     * @param string|null $source
     * @return array
     */
    public function defineTableAttributes($source = null)
    {
        return [
            'customerPointsId'      => Craft::t('Customer Email'),
            'referralEmail'         => Craft::t('Referral Email'),
            'hasSignedUp'           => Craft::t('Registered'),
            'hasPurchased'          => Craft::t('Has Purchased'),
        ];
    }

    public function defineSortableAttributes()
    {
        return [
            'customerPointsId'      => Craft::t('Customer Email'),
            'referralEmail'         => Craft::t('Referral Email'),
            'hasSignedUp'           => Craft::t('Registered'),
            'hasPurchased'          => Craft::t('Has Purchased'),
        ];
    }

    /**
     * Returns the table view HTML for a given attribute.
     *
     * @param BaseElementModel $element
     * @param string $attribute
     * @return string
     */
    public function getTableAttributeHtml(BaseElementModel $element, $attribute)
    {
        switch ($attribute)
        {
            case 'customerPointsId':
            {
                $customerPointsId = $element->$attribute;

                $customerEmail = craft()->customerPoints_user->getUserCustomerEmail($customerPointsId);

                return $customerEmail;
            }

            default:
            {
                return parent::getTableAttributeHtml($element, $attribute);
            }
        }
    }

    /**
     * Defines any custom element criteria attributes for this element type.
     *
     * @return array
     */
    public function defineCriteriaAttributes()
    {
        return [
            'referralEmail'             => array(AttributeType::String),
            'emailSendFail'             => array(AttributeType::Bool),
            'hasSignedUp'               => array(AttributeType::Bool),
            'hasPurchased'              => array(AttributeType::Bool),
            'referrerIpAddress'         => array(AttributeType::String),
            'referrerUserAgent'         => array(AttributeType::String),
            'referreeIpAddress'         => array(AttributeType::String),
            'referreeUserAgent'         => array(AttributeType::String),
        ];
    }

    /**
     * Modifies an element query targeting elements of this type.
     *
     * @param DbCommand $query
     * @param ElementCriteriaModel $criteria
     * @return mixed
     */
    public function modifyElementsQuery(DbCommand $query, ElementCriteriaModel $criteria)
    {
        $query
            ->addSelect('cp_referral.id, cp_referral.customerPointsId, cp_referral.referralEmail, cp_referral.emailSendFail, cp_referral.hasSignedUp, cp_referral.hasPurchased, cp_referral.referrerIpAddress, cp_referral.referrerUserAgent, cp_referral.referreeIpAddress, cp_referral.referreeUserAgent')
            ->join('customerpoints_referrals cp_referral', 'cp_referral.id = elements.id');

        if ($criteria->referralEmail) {
            $query->andWhere(DbHelper::parseParam('cp_referral.referralEmail', $criteria->referralEmail, $query->params));
        }

        if ($criteria->emailSendFail) {
            $query->andWhere(DbHelper::parseParam('cp_referral.emailSendFail', $criteria->emailSendFail, $query->params));
        }

        if ($criteria->hasSignedUp)
        {
            $query->andWhere(DbHelper::parseParam('cp_referral.hasSignedUp', $criteria->hasSignedUp, $query->params));
        }

        if ($criteria->hasPurchased) {
            $query->andWhere(DbHelper::parseParam('cp_referral.hasPurchased', $criteria->hasPurchased, $query->params));
        }

        if ($criteria->referrerIpAddress) {
            $query->andWhere(DbHelper::parseParam('cp_referral.referrerIpAddress', $criteria->referrerIpAddress, $query->params));
        }

        if ($criteria->referrerUserAgent) {
            $query->andWhere(DbHelper::parseParam('cp_referral.referrerUserAgent', $criteria->referrerUserAgent, $query->params));
        }

        if ($criteria->referreeIpAddress) {
            $query->andWhere(DbHelper::parseParam('cp_referral.referreeIpAddress', $criteria->referreeIpAddress, $query->params));
        }

        if ($criteria->referreeUserAgent) {
            $query->andWhere(DbHelper::parseParam('cp_referral.referreeUserAgent', $criteria->referreeUserAgent, $query->params));
        }

    }

    /**
     * Populates an element model based on a query result.
     *
     * @param array $row
     * @return array
     */
    public function populateElementModel($row)
    {
        return CustomerPoints_ReferralModel::populateModel($row);
    }
}