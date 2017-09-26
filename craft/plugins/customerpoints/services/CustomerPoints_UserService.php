<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 9/8/17
 * Time: 7:18 PM
 */

namespace Craft;

class  CustomerPoints_UserService extends BaseApplicationComponent
{
    public function getPlugin()
    {
        return craft()->plugins->getPlugin('customerpoints');
    }

    public function getSettings()
    {
        return $this->getPlugin()->getSettings();
    }

    /**
     * Returns an event by its ID.
     *
     * @param int $customerPointsUserId
     * @return Events_EventModel|null
     */
    public function getCustomerPointsUserById($customerPointsUserId)
    {
        return craft()->elements->getElementById($customerPointsUserId, 'CustomerPoints_User');
    }

    /**
     * Saves an event.
     *
     * @param CustomerPoints_UserModel $customerPointsUser
     * @throws Exception
     * @return bool
     */
    public function saveCustomerPointsUser(CustomerPoints_UserModel $customerPointsUser)
    {
        $isNewCustomer = !$customerPointsUser->id;

        // Event data
        if (!$isNewCustomer)
        {
            $customerPointsUserRecord = CustomerPoints_UserRecord::model()->findById($customerPointsUser->id);

            if (!$customerPointsUserRecord)
            {
                throw new Exception(Craft::t('No Customer exists with the ID “{id}”', array('id' => $customerPointsUser->id)));
            }
        }
        else
        {
            $customerPointsUserRecord = new CustomerPoints_UserRecord();
        }

        $customerPointsUserRecord->email        = $customerPointsUser->email;
        $customerPointsUserRecord->points       = $customerPointsUser->points;
        $customerPointsUserRecord->pointsUsed   = $customerPointsUser->pointsUsed;
        $customerPointsUserRecord->totalPointsAcquired   = $customerPointsUser->totalPointsAcquired;
        $customerPointsUserRecord->referrerHash          = $customerPointsUser->referrerHash;
        $customerPointsUserRecord->customerId            = $customerPointsUser->customerId;


        $customerPointsUserRecord->validate();
        $customerPointsUser->addErrors($customerPointsUserRecord->getErrors());

        if (!$customerPointsUser->hasErrors())
        {
            $transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
            try
            {
                // Fire an 'onBeforeSaveEvent' event
                $this->onBeforeSaveEvent(new Event($this, array(
                    'customerPointsUser'    => $customerPointsUser,
                    'isNewCustomer'         => $isNewCustomer
                )));

                if (craft()->elements->saveElement($customerPointsUser))
                {
                    // Now that we have an element ID, save it on the other stuff
                    if ($isNewCustomer)
                    {
                        $customerPointsUserRecord->id = $customerPointsUser->id;
                    }

                    $customerPointsUserRecord->save(false);

                    // Fire an 'onSaveEvent' event
                    $this->onSaveEvent(new Event($this, array(
                        'customerPointsUser'    => $customerPointsUser,
                        'isNewCustomer'         => $isNewCustomer
                    )));

                    if ($transaction !== null)
                    {
                        $transaction->commit();
                    }

                    return true;
                }
            }
            catch (\Exception $e)
            {
                if ($transaction !== null)
                {
                    $transaction->rollback();
                }

                throw $e;
            }
        }

        return false;
    }

    // Events

    /**
     * Fires an 'onBeforeSaveEvent' event.
     *
     * @param Event $event
     */
    public function onBeforeSaveEvent(Event $event)
    {
        $this->raiseEvent('onBeforeSaveEvent', $event);
    }

    /**
     * Fires an 'onSaveEvent' event.
     *
     * @param Event $event
     */
    public function onSaveEvent(Event $event)
    {
        $this->raiseEvent('onSaveEvent', $event);
    }


    //hash based upon email + id
    public function getUserReferralHash($email, $customerId)
    {
        return hash('crc32', ($email . $customerId), FALSE);
    }

    //hash based upon email + id
    public function getUserCustomerEmail($customerId)
    {
        $query = craft()->db->createCommand()
            ->select('email')
            ->from('customerpoints_user')
            ->where('id=' . $customerId)
            ->queryAll();

        return (count($query) == 0) ? 0 : $query[0]['email'];
    }

}