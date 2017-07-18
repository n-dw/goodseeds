<?php
/**
 * Comments Rating plugin for Craft CMS
 *
 * @author    	Jason Mayo
 * @twitter 	@madebymayo
 * @package   	CommentsRating
 */

namespace Craft;

class CommentsRatingModel extends BaseModel
{
    /**
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'id' => AttributeType::Number,
            'commentId' => array(AttributeType::Number),
            'elementId' => array(AttributeType::Number),
            'userId' => array(AttributeType::Number),
            'rating' => array(AttributeType::Number),
            'comment_approved' => array(AttributeType::Bool)
        ));
    }

}