<?php
/**
 * thcpost plugin for Craft CMS
 *
 * Thcpost_Thcpost Service
 *
 * --snip--
 * All of your plugin’s business logic should go in services, including saving data, retrieving data, etc. They
 * provide APIs that your controllers, template variables, and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 * --snip--
 *
 * @author    NdW
 * @copyright Copyright (c) 2017 NdW
 * @link      natedewaard.com
 * @package   Thcpost
 * @since     1.0.0
 */

namespace Craft;

class Thcpost_ThcpostService extends BaseApplicationComponent
{
    /**
     * This function can literally be anything you want, and you can have as many service functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     craft()->thcpost_thcpost->exampleService()
     */
    public function hashValue($arr)
    {
        if(is_array($arr))
        {
            if(array_key_exists('CRAFT_CSRF_TOKEN', $arr))
            {
                unset($arr['CRAFT_CSRF_TOKEN']);
            }

            $jsonArr = json_encode($arr);

            $hashVal = hash('crc32', $jsonArr);

            return $hashVal;

        }
        return false;
    }
//was doing product save from the product service and setpost but wanst saving for some rerasons so i edit the fields oops
    public function saveProductRatings($elementId, $avgRating, $totalRatings)
    {
        $values = array(
            'field_averageRating' => $avgRating,
            'field_totalratings' => $totalRatings,
        );

        return craft()->db->createCommand()->update('content', $values, array('elementId' => $elementId));
    }

    public function adjustVariantStock($variantId, $qty)
    {
        $values = array(
            'stock' => $qty,
        );

        return craft()->db->createCommand()->update('commerce_variants', $values, array('id' => $variantId));
    }

}