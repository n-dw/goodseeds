<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 9/11/17
 * Time: 9:52 AM
 */
namespace Craft;

class CustomerPoints_ReferralsController extends CustomerPoints_BaseAdminController
{

    /**
     * Customer Points Settings Index
     */
    public function actionIndex()
    {
        $this->renderTemplate('customerpoints/referrals/_index');
    }
}
