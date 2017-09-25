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

class CustomerPoints_UserElementType extends CustomerPoints_BaseElementType
{
    /**
     * Returns this element type's name.
     *
     * @return mixed
     */
    public function getName()
    {
        return Craft::t('Customer Points Info');
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
                'label'    => Craft::t('All Customers'),
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
            'email'               => Craft::t('Email'),
            'points'              => Craft::t('Points'),
            'pointsUsed'          => Craft::t('Points Used'),
            'totalPointsAcquired' => Craft::t('Total Points Earned'),
            'referrerHash'        => Craft::t('Referrer Code')
        ];
    }

    public function defineSortableAttributes()
    {
        return [
            'email'               => Craft::t('Email'),
            'points'              => Craft::t('Points'),
            'pointsUsed'          => Craft::t('Points Used'),
            'totalPointsAcquired' => Craft::t('Total Points Earned'),
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
        return parent::getTableAttributeHtml($element, $attribute);
    }

    /**
     * Defines any custom element criteria attributes for this element type.
     *
     * @return array
     */
    public function defineCriteriaAttributes()
    {
        return [
            'customerId'          => array(AttributeType::Number),
            'email'               => array(AttributeType::String),
            'points'              => array(AttributeType::Number),
            'pointsUsed'          => array(AttributeType::Number),
            'totalPointsAcquired' => array(AttributeType::Number),
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
            ->addSelect('cp_user.id, cp_user.customerId, cp_user.email, cp_user.points, cp_user.pointsUsed, cp_user.totalPointsAcquired, cp_user.pointsUsed, cp_user.referrerHash, cp_user.dateCreated, cp_user.dateUpdated')
            ->join('customerpoints_user cp_user', 'cp_user.id = elements.id');

        if ($criteria->customerId) {
            $query->andWhere(DbHelper::parseParam('cp_user.customerId', $criteria->customerId, $query->params));
        }

        if ($criteria->email) {
            $query->andWhere(DbHelper::parseParam('cp_user.email', $criteria->email, $query->params));
        }

        if ($criteria->points)
        {
            $query->andWhere(DbHelper::parseParam('cp_user.points', $criteria->points, $query->params));
        }

        if ($criteria->pointsUsed) {
            $query->andWhere(DbHelper::parseParam('cp_user.pointsUsed', $criteria->pointsUsed, $query->params));
        }

        if ($criteria->totalPointsAcquired) {
            $query->andWhere(DbHelper::parseParam('cp_user.totalPointsAcquired', $criteria->totalPointsAcquired, $query->params));
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
        return CustomerPoints_UserModel::populateModel($row);
    }
}