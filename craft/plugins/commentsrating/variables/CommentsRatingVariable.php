<?php
/**
 * Comments Rating plugin for Craft CMS
 *
 * @author    	Jason Mayo
 * @twitter 	@madebymayo
 * @package   	CommentsRating
 */

namespace Craft;

class CommentsRatingVariable
{
	
	/**
	 * Rating - Element
	 *
	 * @return integer
	*/
    public function elementRating($elementId)
    {
        return craft()->commentsRating->elementRating($elementId);
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