<?php
/**
 * Customer Points plugin for Craft CMS
 *
 * Customer Points Variable
 *
 * --snip--
 * Craft allows plugins to provide their own template variables, accessible from the {{ craft }} global variable
 * (e.g. {{ craft.pluginName }}).
 *
 * https://craftcms.com/docs/plugins/variables
 * --snip--
 *
 * @author    Nathan de Waard
 * @copyright Copyright (c) 2017 Nathan de Waard
 * @link      https://github.com/n-dw
 * @package   CustomerPoints
 * @since     1.0.0
 */

namespace Craft;

class CustomerPointsVariable
{
    /**
     * Whatever you want to output to a Twig template can go into a Variable method. You can have as many variable
     * functions as you want.  From any Twig template, call it like this:
     *
     *     {{ craft.customerPoints.exampleVariable }}
     *
     * Or, if your variable requires input from Twig:
     *
     *     {{ craft.customerPoints.exampleVariable(twigValue) }}
     */
    public function exampleVariable($optional = null)
    {
        return "And away we go to the Twig template...";
    }
}