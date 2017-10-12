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
    /**
     * gets points if the user has them
     *
     * @return integer
     */
    public function getUserPoints($customerEmail)
    {
        return craft()->customerPoints->getUserPoints($customerEmail);
    }
    /**
     * gets points if the user has them
     *
     * @return integer
     */
    public function getUserReferralHash($userId)
    {
        return craft()->customerPoints_User->getUserReferralHash($userId);
    }

    /**
     * Rating - Elements
     *
     * @return models
     */
    public function elementRatings($elementId)
    {
        return craft()->commentsRating->elementRatings($elementId);
    }

    /**
     * Rating - Elements
     *
     * @return avg rating out of 10
     */
    public function elementAvgRatings($elementId)
    {
        return craft()->commentsRating->elementAvgRatings($elementId);
    }

    /**
     * Rating - Comment
     *
     * @return integer
     */
    public function commentRating($commentId)
    {
        return craft()->commentsRating->commentRating($commentId);
    }
}