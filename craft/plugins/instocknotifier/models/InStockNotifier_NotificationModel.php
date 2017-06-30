<?php
/**
 * In Stock Notifier plugin for Craft CMS
 *
 * InStockNotifier_Notification Model
 *
 * --snip--
 * Models are containers for data. Just about every time information is passed between services, controllers, and
 * templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 * --snip--
 *
 * @author    NdW
 * @copyright Copyright (c) 2017 NdW
 * @link      natedewaard.com
 * @package   InStockNotifier
 * @since     1.0.0
 */

namespace Craft;
/**
 * Notification model.
 *
 * @property int $id
 * @property int $productId
 * @property string $customerEmail
 * @property string $dateNotified
 * @property bool $sendFail
 *
 * @author    Nathan de Waard
 */

class InStockNotifier_NotificationModel extends BaseModel
{


    public function __get($name)
    {
        $getter = 'get'.$name;
        if (method_exists($this, $getter))
        {
            return $this->$getter();
        }

        return parent::__get($name);
    }

    /**
     * Defines this model's attributes.
     *
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'id'                => array(AttributeType::Number, 'default' => null),
            'productId'         => array(AttributeType::Number, 'default' => null),
            'customerEmail'     => array(AttributeType::String, 'default' => null),
            'dateNotified'      => array(AttributeType::DateTime, 'default' => null),
            'sendFail'          => array(AttributeType::Bool, 'default' => false),
        ));
    }

    public function getId()
    {
        return $this->id;
    }
    public function getProductId()
    {
        return $this->productId;
    }
    public function getCustomerEmail()
    {
        return $this->customerEmail;
    }
    public function getDateNotified()
    {
        return $this->dateNotified;
    }
    public function sendFailed()
    {
        return $this->sendFail;
    }

}