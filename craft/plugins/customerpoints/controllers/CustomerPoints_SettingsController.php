<?php
namespace Craft;

/**
 * Class CustomerPoints_SettingsController
 *
 * @author    Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @copyright Copyright (c) 2015, Pixel & Tonic, Inc.
 * @license   https://craftcustomerpoints.com/license Craft Customer Points License Agreement
 * @see       https://craftcustomerpoints.com
 * @package   craft.plugins.customerpoints.controllers
 * @since     1.0
 */
class CustomerPoints_SettingsController extends CustomerPoints_BaseAdminController
{

    /**
     * Customer Points Settings Index
     */
    public function actionIndex()
    {

        $settings = craft()->customerPoints->getSettings();

        $this->renderTemplate('customerpoints/settings',
                              ['settings' => $settings]);

    }
}
