<?php
/**
 * thcpost plugin for Craft CMS
 *
 * Thcpost_Thcpost Controller
 *
 * --snip--
 * Generally speaking, controllers are the middlemen between the front end of the CP/website and your plugin’s
 * services. They contain action methods which handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering post data, saving it on a model,
 * passing the model off to a service, and then responding to the request appropriately depending on the service
 * method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what the method does (for example,
 * actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 * --snip--
 *
 * @author    NdW
 * @copyright Copyright (c) 2017 NdW
 * @link      natedewaard.com
 * @package   Thcpost
 * @since     1.0.0
 */

namespace Craft;

class Thcpost_ThcpostController extends BaseController
{

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     * @access protected
     */
    protected $allowAnonymous = array('actionSearchSite');

    private static function resultSort($a, $b){
        return ($a['ranking'] < $b['ranking']) ? -1 : 1;
    }

    public function actionSearchSite()
    {
        /* if(!craft()->request->isAjaxRequest())
             return false;*/

        $queryString = craft()->request->getParam('q');

        $productSearch = craft()->elements->getCriteria('Commerce_Product')->search('title:' . '*' . $queryString . '*')->order('score');
        $contentSearch = craft()->elements->getCriteria(ElementType::Entry)->search('title:' . '*' . $queryString . '*' . ' OR ' . 'answer:' . '*' . $queryString . '*')->order('score');

        $results = [];

        if(count($productSearch) < 1 && count($contentSearch) < 1)
        {
            $result['title'] = 'Sorry no results found for ' . $queryString;
            array_push($results, $result);
            $this->returnJson($results);
        }

        foreach($productSearch as $product){
            $result['title'] = $product->title;
            $result['url'] = $product->url;
            $result['ranking'] = $product->searchScore;
            array_push($results, $result);
        }

        foreach($contentSearch as $content){
            $result['title'] = $content->title;
            $result['url'] = $content->url;
            $result['ranking'] = $content->searchScore;
            array_push($results, $result);
        }

        usort($results, 'self::resultSort');
        $results = array('data' => $results);
        $this->returnJson($results);


        //search faqs and page titles, products as well ans merge
    }
}