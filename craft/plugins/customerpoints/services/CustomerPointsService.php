<?php
/**
 * Customer Points plugin for Craft CMS
 *
 * CustomerPoints Service
 *
 * --snip--
 * All of your plugin’s business logic should go in services, including saving data, retrieving data, etc. They
 * provide APIs that your controllers, template variables, and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 * --snip--
 *
 * @author    Nathan de Waard
 * @copyright Copyright (c) 2017 Nathan de Waard
 * @link      https://github.com/n-dw
 * @package   CustomerPoints
 * @since     1.0.0
 */

namespace Craft;

class  CustomerPointsService extends BaseApplicationComponent
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