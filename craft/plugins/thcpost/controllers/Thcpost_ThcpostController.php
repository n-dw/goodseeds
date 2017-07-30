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
    protected $allowAnonymous = array('actionSearchSite', 'actionShopPage');

    private static function resultSort($a, $b){
        return ($a['ranking'] < $b['ranking']) ? 1 : -1;
    }

   /* public function actionShopPage()
    {
        $params = ['with' => 'productImages'];
        $formParams = ['productParam' => ''];
        $categoriesArray = ['or'];

        if(craft()->request->getPost())
        {
            $formResults = craft()->request->getPost();
            unset($formResults['CRAFT_CSRF_TOKEN']);
            $formParams = array_merge($formParams, $formResults);

            foreach ($formResults as $postVarName=>$postVarValue){
                switch ($postVarName){
                    case "sale":
                        $params =  array_merge($params, array('hasSales' => true));
                        break;
                    case "sativa" || "indica" || "hybrid":
                        $category = craft()->categories->slug($postVarName)->ids();
                        $categoriesArray = array_merge($categoriesArray, array('targetElement' => $category));
                        $params = array_merge($params, array('relatedTo' => $categoriesArray));
                        break;
                    case "oil":
                        $params = array_merge($params, array('type' => 'Oil'));
                        break;
                    case "flower":
                        $params = array_merge($params, array('type' => 'Flower'));
                        break;
                    case "edibles":
                        $params = array_merge($params, array('type' => 'Edibles'));
                        break;
                    case "stock":
                        $params = array_merge($params, array('hasVariant' => array('hasStock' => true)));
                        break;
                    case "sortBy":
                        if($postVarName == 'new')
                        {
                            $params = array_merge($params, array('order' => 'postDate DESC'));
                        }
                        elseif($postVarName == 'htl')
                        {
                            $params = array_merge($params, array('order' => 'defaultPrice DESC'));
                        }
                        elseif($postVarName == 'lth')
                        {
                            $params = array_merge($params, array('order' => 'defaultPrice ASC'));
                        }
                        elseif($postVarName == 'reviewHtl')
                        {
                            $params = array_merge($params, array('order' => 'averageRating DESC'));
                        }
                        break;
                }
            }
        }
        elseif($queryString = craft()->request->getParam('cat'))
        {
            $productParam = craft()->request->getParam('cat');
            $formParams = array('productParam' => $productParam);

            switch ($productParam){
                case "sale":
                    $params =  array_merge($params, array('hasSales' => true));
                    break;
                case "sativa" || "indica" || "hybrid":
                    $category = craft()->elements->getCriteria(ElementType::Category , ['slug' => $productParam]);
                    $category = $category->id;
                    $categoriesArray = array_merge($categoriesArray, array('targetElement' => $category));
                    $params = array_merge($params, array('relatedTo' => $categoriesArray));
                    break;
                case "oil":
                    $params = array_merge($params, array('type' => 'Oil'));
                    break;
                case "flower":
                    $params = array_merge($params, array('type' => 'Flower'));
                    break;
                case "edibles":
                    $params = array_merge($params, array('type' => 'Edibles'));
                    break;
            }

        }

        $products = craft()->elements->getCriteria('Commerce_Product', $params);

        //craft()->urlManager->setRouteVariables(array('products' => $products, 'formParams' => $formParams));
       //craft()->templates->render('shop/products/index', array('products' => $products, 'formParams' => $formParams));
        $this->renderTemplate('shop/products/index', array(
            'products' => $products,
            'formParams' => $formParams
        ));
    }*/

    public function actionSearchSite()
    {
        $queryString = craft()->request->getParam('q');

        $productSearch = craft()->elements->getCriteria('Commerce_Product')->search('title:' . '*' . $queryString . '*')->order('score');
        $contentSearch = craft()->elements->getCriteria(ElementType::Entry)->search('title:' . '*' . $queryString . '*' . ' OR ' . 'answer:' . '*' . $queryString . '*')->order('score');

        $results = [];

        if(count($productSearch) < 1 && count($contentSearch) < 1)
        {
            $result['title'] = 'Sorry no results found for ' . $queryString;
            array_push($results, $result);
            $results = array('data' => $results);
            $this->returnJson($results);
        }

        foreach($productSearch as $product){
            $result['title'] = str_ireplace($queryString, '<span class="search-string-match">' . $queryString . '</span>' , $product->title);
            $result['url'] = $product->url;
            $result['ranking'] = $product->searchScore;

            array_push($results, $result);
        }

        foreach($contentSearch as $content){
            $result['title'] = str_ireplace($queryString, '<span class="search-string-match">' . $queryString . '</span>' , $content->title);
            if($content->type->handle == 'faqs')
            {
                $result['url'] = 'frequently-asked-questions' . '?aq=' . $content->id . '#faq_' . $content->id;
            }
            else{
                $result['url'] = $content->url;
            }
            $result['ranking'] = $content->searchScore;
            array_push($results, $result);
        }

        usort($results, 'self::resultSort');

        $results = array('data' => $results);
        $this->returnJson($results);

    }
}