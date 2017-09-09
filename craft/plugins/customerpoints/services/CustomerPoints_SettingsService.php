<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 9/8/17
 * Time: 7:18 PM
 */

namespace Craft;

class  CustomerPoints_SettingsService extends BaseApplicationComponent
{
    public function getPlugin()
    {
        return craft()->plugins->getPlugin('customerpoints');
    }

    public function getSettings()
    {
        return $this->getPlugin()->getSettings();
    }

}