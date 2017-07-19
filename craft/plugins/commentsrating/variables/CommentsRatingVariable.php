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
     * @return models
     */
    public function elementAvgRatings($elementId)
    {
        $cRRModels = $this->elementRatings($elementId);
        $avg = 0;
        $ratingTotal = 0;
        $ratingsCount = 0;

        if(count($cRRModels == 0))
          return $avg;

        foreach ($cRRModels as $cRRModel)
        {
            if(!is_null($cRRModel->rating))
            {
                $ratingTotal += $cRRModel->rating;
                $ratingsCount++;
            }

        }

        $avg = (float)($ratingTotal / $ratingsCount) / 2;

        return $avg;
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