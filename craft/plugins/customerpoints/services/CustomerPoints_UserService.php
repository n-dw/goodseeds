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

    //hash based upon email + id
    public function getUserReferralHash($userInfoToHash)
    {

        return hash('crc32', $userInfoToHash, FALSE);
    }

}