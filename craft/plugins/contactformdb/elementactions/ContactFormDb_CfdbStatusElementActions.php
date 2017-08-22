<?php
namespace Craft;

class ContactFormDb_CfdbStatusElementActions extends BaseElementAction
{
    // Public Methods
    // =========================================================================

    public function getTriggerHtml()
    {
        return craft()->templates->render('contactFormDb/_elementactions/status');
    }

    public function performAction(ElementCriteriaModel $criteria)
    {
        $status = $this->getParams()->status;

        // Figure out which element IDs we need to update
        $elementIds = $criteria->ids();

        // Update their statuses
        craft()->db->createCommand()->update(
            'contactformdb_submissions',
            array('status' => $status),
            array('in', 'id', $elementIds)
        );

        // Clear their template caches
        craft()->templateCache->deleteCachesByElementId($elementIds);

        // Fire an 'onSetStatus' event
        $this->onSetStatus(new Event($this, array(
            'criteria'   => $criteria,
            'elementIds' => $elementIds,
            'status'     => $status,
        )));


        $this->setMessage(Craft::t('Statuses updated.'));

        return true;
    }

    public function onSetStatus(Event $event)
    {
        $this->raiseEvent('onSetStatus', $event);
    }


    // Protected Methods
    // =========================================================================

    protected function defineParams()
    {
        return array(
            'status' => array(AttributeType::Enum, 'values' => array(
                ContactFormDb_CfdbModel::UNREAD,
                ContactFormDb_CfdbModel::READ,
                ContactFormDb_CfdbModel::ARCHIVED,
                ContactFormDb_CfdbModel::RESPONDED,
                ContactFormDb_CfdbModel::RESOLVED,
                ContactFormDb_CfdbModel::SPAM,
                ContactFormDb_CfdbModel::TRASHED
            ), 'required' => true)
        );
    }
}
