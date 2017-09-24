<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 9/11/17
 * Time: 9:52 AM
 */
namespace Craft;

class CustomerPoints_ReviewsController extends CustomerPoints_BaseAdminController
{

    /**
     * Customer Points Settings Index
     */
    public function actionIndex()
    {

        $this->renderTemplate('customerpoints/reviews/_index');

    }

    /**
     * @param array $variables
     *
     * @throws HttpException
     */
    public function actionEditReviews(array $variables = [])
    {
        $variables['orderSettings'] = craft()->commerce_orderSettings->getOrderSettingByHandle('order');

        if (!$variables['orderSettings'])
        {
            throw new HttpException(404, Craft::t('No order settings found.'));
        }

        if (empty($variables['order']))
        {
            if (!empty($variables['orderId']))
            {
                $variables['order'] = craft()->commerce_orders->getOrderById($variables['orderId']);

                if (!$variables['order'])
                {
                    throw new HttpException(404);
                }
            }
        }

        if (!empty($variables['orderId']))
        {
            $variables['title'] = "Order ".substr($variables['order']->number, 0, 7);
        }
        else
        {
            throw new HttpException(404);
        }

        craft()->templates->includeCssResource('commerce/order.css');

        $this->prepVariables($variables);


        if (empty($variables['paymentForm']))
        {
            $paymentMethod = $variables['order']->getPaymentMethod();

            if ($paymentMethod && $paymentMethod->getGatewayAdapter())
            {
                $variables['paymentForm'] = $variables['order']->paymentMethod->getPaymentFormModel();
            }
            else
            {
                $paymentMethod = ArrayHelper::getFirstValue(craft()->commerce_paymentMethods->getAllPaymentMethods());

                if($paymentMethod)
                {
                    $variables['paymentForm'] = $paymentMethod->getPaymentFormModel();
                }
                else
                {
                    $variables['paymentForm'] = null;
                }
            }
        }

        $variables['orderStatusesJson'] = JsonHelper::encode(craft()->commerce_orderStatuses->getAllOrderStatuses());

        $this->renderTemplate('commerce/orders/_edit', $variables);
    }
}
