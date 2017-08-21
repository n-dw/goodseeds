<?php
namespace Craft;

class ContactFormDb_CfdbModel extends BaseElementModel
{
    // Properties
    // =========================================================================

    protected $elementType = 'ContactFormDb_Cfdb';

    const UNREAD        = 'Unread';
    const READ          = 'Read';
    const ARCHIVED      = 'Archived';
    const RESPONDED     = 'Responded';
    const RESOLVED      = 'Resolved';
    const SPAM          = 'Spam';
    const TRASHED       = 'Trashed';

    // Public Methods
    // =========================================================================

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
        return false;
    }

    public function getCpEditUrl()
    {
        return UrlHelper::getCpUrl('contactFormDb/cfdb/editSubmission/' . $this->id);
    }



    public function getExcerpt($startPos=0, $maxLength=100) {
        if (strlen($this->message) > $maxLength) {
            $excerpt   = substr($this->message, $startPos, $maxLength-3);
            $lastSpace = strrpos($excerpt, ' ');
            $excerpt   = substr($excerpt, 0, $lastSpace);
            $excerpt  .= '...';
        } else {
            $excerpt = $this->comment;
        }

        return $excerpt;
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
        if ($user->can('contactFormDbEdit')) {
            return true;
        }

        return false;
    }
    public function canStatus()
    {
        $user = craft()->userSession->getUser();

        // Only logged in users can edit a comment
        if (!$user) {
            return false;
        }

        if ($user->can('contactFormDbStatus')) {
            return true;
        }

        return false;
    }
    public function canArchive()
    {
        $user = craft()->userSession->getUser();

        // Only logged in users can edit a comment
        if (!$user) {
            return false;
        }

        if ($user->can('contactFormDbArchive')) {
            return true;
        }

        return false;
    }
    public function canDelete()
    {
        $user = craft()->userSession->getUser();

        // Only logged in users can edit a comment
        if (!$user) {
            return false;
        }

        if ($user->can('contactFormDbDelete')) {
          return true;
        }

        return false;
    }

    public function canTrash()
    {
        $user = craft()->userSession->getUser();

        // Only logged in users can trash a comment
        if (!$user) {
            return false;
        }

        // Check that user is trying to trash their own comment
        if ($user->can('contactFormDbTrash')) {
            return true;
        }

        return false;
    }



    // Protected Methods
    // =========================================================================

    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'id'            => AttributeType::Number,
            'status'        => array(AttributeType::Enum, 'values' => array(
                ContactFormDb_CfdbModel::UNREAD,
                ContactFormDb_CfdbModel::READ,
                ContactFormDb_CfdbModel::ARCHIVED,
                ContactFormDb_CfdbModel::RESPONDED,
                ContactFormDb_CfdbModel::RESOLVED,
                ContactFormDb_CfdbModel::SPAM,
                ContactFormDb_CfdbModel::TRASHED
            )),
            'ipAddress'         => array(AttributeType::String),
            'userAgent'         => array(AttributeType::String),
            'urlReferrer'       => array(AttributeType::String),
            'name'              => array(AttributeType::String),
            'email'             => array(AttributeType::String),
            'inquiryType'       => array(AttributeType::String),
            'message'           => array(AttributeType::String),
            'answered'          => array(AttributeType::Bool),
            'answeredDate'      => array(AttributeType::DateTime),
            'archived'          => array(AttributeType::Bool),
            'archivedDate'      => array(AttributeType::DateTime),
        ));
    }
}