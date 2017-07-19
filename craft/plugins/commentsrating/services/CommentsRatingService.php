<?php
/**
 * Comments Rating plugin for Craft CMS
 *
 * @author    	Jason Mayo
 * @twitter 	@madebymayo
 * @package   	CommentsRating
 */

namespace Craft;

class CommentsRatingService extends BaseApplicationComponent
{

	/**
	 * Rating - Create
	 *
	 * @return null
	*/
    public function createRating($comment)
    {

        if ($comment->id)
        {
            $record = CommentsRatingRecord::model()->findByAttributes(array('commentId' => $comment->id));

            //we already have a record
            if ($record)
            {
                $model = new CommentsRatingModel();

                $model->id = $record->id;
                $model->commentId = $comment->id;
                $model->elementId = $comment->elementId;
                $model->userId = $comment->userId;
                $model->comment_approved = ($comment->status == 'approved');
               //check if update comment from front end so them we havve the field if its the backend we just update status
                if(craft()->request->getPost('fields.commentsRating')){
                    $model->rating = craft()->request->getPost('fields.commentsRating');
                }
                else{
                    $model->rating = $record->rating;
                }
                $record->setAttributes($model->getAttributes());
                $record->save();

                return true;
            }
            else
            {
                if(craft()->request->getPost('fields.commentsRating')){
                    $model = new CommentsRatingModel();

                    $model->commentId = $comment->id;
                    $model->elementId = $comment->elementId;
                    $model->userId = $comment->userId;
                    $model->rating = craft()->request->getPost('fields.commentsRating');
                    $model->comment_approved = ($comment->status == 'approved');

                    $commentRatingRecord = new CommentsRatingRecord;

                    $commentRatingRecord->commentId = $model->commentId;
                    $commentRatingRecord->elementId = $model->elementId;
                    $commentRatingRecord->userId = $model->userId;
                    $commentRatingRecord->rating = $model->rating;
                    $commentRatingRecord->comment_approved = $model->comment_approved;

                    $commentRatingRecord->save();

                    return true;
                }
            }

        }
    }
    public function deleteRating($commentIds)
    {
        foreach ($commentIds as $commentid)
        {
            if ($commentid)
            {
                $record = CommentsRatingRecord::model()->findByAttributes(array('commentId' => $commentid));

               if(!is_null($record) && $record->delete()){
                   return true;
               }

                return false;
            }
        }
    }
    
	/**
	 * Rating - Element
	 *
	 * @return integer
	*/
    public function elementRating($elementId)
    {
	    
		$query = craft()->db->createCommand()
			->select('AVG(rating) as average')
			->from('comments_rating')
			->where('elementId=' . $elementId)
            ->andWhere('comment_approved = 1')
			->queryAll();
		
		return (count($query) == 0) ? 0 : $query[0]['average'];
	    
    }
    /**
     * Rating - Element
     *
     * @return integer'commentId' => array(AttributeType::Number),
    'elementId' => array(AttributeType::Number),
    'userId' => array(AttributeType::Number),
    'rating' => array(AttributeType::Number),
    'comment_approved' => array(AttributeType::Bool)
     */
    public function elementRatings($elementId)
    {
        $records = CommentsRatingRecord::model()->findAllByAttributes(array('elementId' => $elementId, 'comment_approved' => 1));

        if(count($records) == 0)
            return false;

        $models = [];

        foreach ($records as $record)
        {
            $cRRModel = new CommentsRatingModel();
            $cRRModel->commentId = $record->commentId;
            $cRRModel->elementId = $record->elementId;
            $cRRModel->userId = $record->userId;
            $cRRModel->rating = $record->rating;
            $cRRModel->comment_approved = $record->comment_approved;

            array_push($models, $cRRModel);
        }



        return (count($models) == 0) ? 0 : $models;

    }
    
	/**
	 * Rating - Comment
	 *
	 * @return integer
	*/
    public function commentRating($commentId)
    {
	    
		$query = craft()->db->createCommand()
			->select('rating')
			->from('comments_rating')
			->where('commentId=' . $commentId)
			->queryAll();
		
		return (count($query) == 0) ? 0 : $query[0]['rating'];
	    
    }

}