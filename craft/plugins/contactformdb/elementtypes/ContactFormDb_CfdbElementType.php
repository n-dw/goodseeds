<?php
namespace Craft;

class ContactFormDb_CfdbElementType extends BaseElementType
{
    // Public Methods
    // =========================================================================

    public function getName()
    {
        return Craft::t('Contact Form Submission');
    }

    public function hasContent()
    {
        return true;
    }

    public function hasTitles()
    {
        return false;
    }

    public function hasStatuses()
    {
        return true;
    }

    public function getStatuses()
    {
        return array(
            ContactFormDb_CfdbModel::READ => Craft::t('Read'),
            ContactFormDb_CfdbModel::UNREAD => Craft::t('Unread'),
            ContactFormDb_CfdbModel::RESPONDED => Craft::t('Responded'),
            ContactFormDb_CfdbModel::RESOLVED => Craft::t('Resolved'),
            ContactFormDb_CfdbModel::SPAM => Craft::t('Spam'),
            ContactFormDb_CfdbModel::ARCHIVED => Craft::t('Archived'),
            ContactFormDb_CfdbModel::TRASHED => Craft::t('Trashed')
        );
    }

    public function getSources($context = null)
    {
        $sources = array(
            '*' => array(
                'label' => Craft::t('All Contact form Submissions'),
            ),
        );

        return $sources;
    }

    public function populateElementModel($row)
    {
        return ContactFormDb_CfdbModel::populateModel($row);
    }

    public function defineTableAttributes($source = null)
    {
        return array(
            'elementId'     => Craft::t('Edit Link'),
            'email'         => Craft::t('Email'),
            'status'        => Craft::t('Status'),
            'name'          => Craft::t('name'),
            'message'       => Craft::t('message'),
            'dateCreated'   => Craft::t('Date'),
            'ipAddress'     => Craft::t('IP Address'),
        );
    }

    public function defineSortableAttributes()
    {
        return array(
            'status'        => Craft::t('Status'),
            'email'         => Craft::t('Email'),
            'name'          => Craft::t('name'),
            'dateCreated'   => Craft::t('Date'),
            'ipAddress'     => Craft::t('IP Address'),
        );
    }

    public function getTableAttributeHtml(BaseElementModel $element, $attribute)
    {
        switch ($attribute)
        {
            case 'status':
            {
                $status = $element->$attribute;

               return '<span class="status ' . strtolower($status) . '"></span> ' . $status;
            }
            case 'elementId':
            {

                return 's';
            }

            default:
            {
                return parent::getTableAttributeHtml($element, $attribute);
            }
        }
    }

    public function defineCriteriaAttributes()
    {
        return array(
            'status'            => array(AttributeType::String),
            'name'              => array(AttributeType::String),
            'email'             => array(AttributeType::Email),
            'inquiryType'       => array(AttributeType::String),
            'message'           => array(AttributeType::String),
            'answered'          => array(AttributeType::Bool),
            'answeredDate'      => array(AttributeType::DateTime),
            'archived'          => array(AttributeType::Bool),
            'archivedDate'      => array(AttributeType::DateTime),
            'ipAddress'         => array(AttributeType::String),
            'userAgent'         => array(AttributeType::String),
            'urlReferrer'       => array(AttributeType::String),
            'order'             => array(AttributeType::String, 'default' => 'submissionDate desc'),
        );
    }

    public function modifyElementsQuery(DbCommand $query, ElementCriteriaModel $criteria)
    {
        $query
            ->addSelect('submissions.status, submissions.name, submissions.email, submissions.inquiryType, submissions.message, submissions.answered, submissions.answeredDate, submissions.archived, submissions.archivedDate, submissions.urlReferrer, submissions.ipAddress, submissions.userAgent, submissions.dateCreated AS submissionDate')
            ->join('contactformdb_cfdb submissions', 'submissions.elementId = elements.id');

        if ($criteria->status) {
            $query->andWhere(DbHelper::parseParam('submissions.status', $criteria->status, $query->params));
        }

        if ($criteria->name) {
            $query->andWhere(DbHelper::parseParam('submissions.name', $criteria->name, $query->params));
        }

        if ($criteria->email) {
            $query->andWhere(DbHelper::parseParam('submissions.email', $criteria->email, $query->params));
        }

        if ($criteria->inquiryType) {
            $query->andWhere(DbHelper::parseParam('submissions.inquiryType', $criteria->inquiryType, $query->params));
        }

        if ($criteria->message) {
            $query->andWhere(DbHelper::parseParam('submissions.message', $criteria->message, $query->params));
        }

        if ($criteria->answered) {
            $query->andWhere(DbHelper::parseParam('submissions.email', $criteria->answered, $query->params));
        }

        if ($criteria->answeredDate) {
            $query->andWhere(DbHelper::parseParam('submissions.answeredDate', $criteria->answeredDate, $query->params));
        }

        if ($criteria->archived) {
            $query->andWhere(DbHelper::parseParam('submissions.archived', $criteria->archived, $query->params));
        }

        if ($criteria->archivedDate) {
            $query->andWhere(DbHelper::parseParam('submissions.archivedDate', $criteria->archivedDate, $query->params));
        }

        if ($criteria->urlReferrer) {
            $query->andWhere(DbHelper::parseParam('submissions.urlReferrer', $criteria->urlReferrer, $query->params));
        }

        if ($criteria->ipAddress) {
            $query->andWhere(DbHelper::parseParam('submissions.ipAddress', $criteria->ipAddress, $query->params));
        }

        if ($criteria->userAgent) {
            $query->andWhere(DbHelper::parseParam('submissions.userAgent', $criteria->userAgent, $query->params));
        }

    }

}