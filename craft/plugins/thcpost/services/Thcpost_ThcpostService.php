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

    public function getVariantStock($variantId)
    {
        $query = craft()->db->createCommand()
            ->select('stock')
            ->from('commerce_variants')
            ->where('id=' . $variantId)
            ->queryAll();

        return (count($query) == 0) ? false : $query[0]['stock'];

    }

    public function getProductRound($productId)
    {
        $record =  Thcpost_productRoundRecord::model()->findByAttributes(array('productId' => $productId));
        $fields = ['productId', 'lastRoundUp'];
        //we already have a record
        if ($record)
        {
            $model = new Thcpost_productRoundModel();

            $model->id = $record->id;
            $model->productId = $record->productId;
            $model->lastRoundUp = ! $record->lastRoundUp;

            foreach ($fields as $field)
            {
                $record->$field = $model->$field;
            }
        }
        else
        {
            $record = new Thcpost_productRoundRecord();
            $model =  new Thcpost_productRoundModel();

            $model->productId = $productId;
            $model->lastRoundUp = true;

            foreach ($fields as $field)
            {
                $record->$field = $model->$field;
            }
        }


        if($this->saveproductRoundRecord($model, $record))
        {
            return $model->lastRoundUp;
        }
        else
        {
            return false;
        }
    }
    private function saveproductRoundRecord(Thcpost_productRoundModel $model, Thcpost_productRoundRecord$record)
    {
        $record->validate();
        $model->addErrors($record->getErrors());

        $transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
        try
        {
            if (!$model->hasErrors())
            {
                $record->save(false);
                $model->id = $record->id;

                if ($transaction !== null)
                {
                    $transaction->commit();
                }

                return true;
            }
        } catch (\Exception $e)
        {
            if ($transaction !== null)
            {
                $transaction->rollback();
            }
            throw $e;
        }

        if ($transaction !== null)
        {
            $transaction->rollback();
        }

        return false;
    }

}