<?php
namespace Fenchy\UserBundle\Services;

use Fenchy\NoticeBundle\Entity\Notice,
    Fenchy\NoticeBundle\Entity\Review,
    Fenchy\UserBundle\Entity\User,
    Fenchy\RegularUserBundle\Entity\UserRegular,
    Fenchy\GalleryBundle\Entity\Gallery;

class ReputationCounter
{
    protected $noticePoints         = 2;//
    protected $gotReviewPoints      = 2;//
    protected $reviewPoints         = 1;//
    protected $facebookPoints       = 5;//
    protected $contactPoints        = 1;//
    protected $aboutMePercent       = 0.25;//
    protected $hotplacesPercent		= 0.15;
    protected $myLikePercent			= 0.25;
    protected $firstPicturePercent  = 0.25;//
    protected $nextPicturePercent   = 0.0;//
    protected $profilePicturePercent  = 0.25;//
    protected $coverPicturePercent   = 0.10;//
    protected $nextPictureLimit     = 0;//
    protected $genderPercent        = 0.25;//
    protected $linkPercent          = 0.00;
    protected $languagesPercent     = 0.10;
    protected $profilePoints        = 20;
    
    const TYPE_NOTICE   = 'notice';
    const TYPE_REVIEW   = 'review';
    const TYPE_OWN_REVIEW = 'own_review';
    const TYPE_PROFILE  = 'profile';
    const TYPE_FB       = 'FB';
    const TYPE_FRIEND   = 'friend';
    
    public function __construct(
           	$notice, 
            $gotReview, 
            $review, 
            $fb, 
            $contact,
            $aboutMe,
            $firstPicture,
            $nextPicture,
            $nextPictureLimit,
            $gender,
            $link,
            $languages,
            $profile
            )
    {
        //$this->noticePoints     = $notice;
        //$this->gotReviewPoints  = $gotReview;
        $this->reviewPoints     = $review;
        $this->facebookPoints   = $fb;
        $this->contactPoints    = $contact;
        $this->aboutMePercent   = $aboutMe;
        $this->firstPicturePercent = $firstPicture;
        $this->nextPicturePercent  = $nextPicture;
        $this->nextPictureLimit = $nextPictureLimit;
        $this->genderPercent    = $gender;
        $this->linkPercent      = $link;
        $this->languagesPercent = $languages;
        $this->profilePoints    = $profile;
    }

    /**
     * Pre Update trust points counter.
     * Should be called before each user or regular user persist
     * 
     * @todo add event for contacts/friends
     * 
     * @param User $entity
     */
    public function update(User $entity, $type = NULL) {
        
        if($type === NULL || $type === self::TYPE_FB) {
            if(NULL === $entity->getPrevFacebookId() && $entity->getFacebookId()) {
                $entity->addActivity($this->facebookPoints);
            }

            elseif($entity->getPrevFacebookId() && NULL === $entity->getFacebookId()) {
                $entity->addActivity(-$this->facebookPoints);
            }
        }
        
        if($type === NULL || $type === self::TYPE_PROFILE) {
        	echo $this->countProfilePoint($entity->getRegularUser());
            $entity->addActivity($this->countProfilePoint($entity->getRegularUser()));
        echo "dd";
            if (NULL !== $entity->getRegularUser()->getGallery()) {
                //$entity->addActivity($this->getGalleryPointsChange($entity->getRegularUser()->getGallery()));
            }
        }
        
        if($type === NULL || $type === self::TYPE_NOTICE) {
            $entity->addActivity($this->getNoticesPointsChange($entity));
        }
        
        if($type === NULL || $type === self::TYPE_REVIEW) {
            $entity->addActivity($this->getReviewsPointsChange($entity));
        }
        
        if($type === NULL || $type === self::TYPE_OWN_REVIEW) {
            $entity->addActivity($this->getOwnReviewsPointsChange($entity));
        }
        
        if($type === NULL || $type == self::TYPE_FRIEND) {
            $entity->addActivity($this->getFriendsPointsChange($entity));
        }
    }
    
    private function getNoticesPointsChange(User $user) {
        
        $prev = $user->getPrevNoticesQuantity();
        if(FALSE === $prev) return 0;
        $curr = $user->getNonDraftNoticesCount();
        
        return ($curr - $prev) * $this->noticePoints;
    }
    
    private function getFriendsPointsChange(User $user) {
        
        $prev = $user->getRegularUser()->getPrevMyFriendsQuantity();
        if(FALSE === $prev) return 0;
        $curr = $user->getRegularUser()->getMyFriends()->count();
        
        return ($curr - $prev) * $this->contactPoints;
    }
    
    private function getOwnReviewsPointsChange(User $user) { 
        
        $sum = 0;
        
        $ownPrev = $user->getPrevOwnReviewsQuantity();
        if(FALSE != $ownPrev) {
            $ownCurr = $user->getOwnReviews()->count();
            $sum += ($ownCurr - $ownPrev) * $this->reviewPoints;
        }
        
        return $sum;
    }
    
    private function getReviewsPointsChange(User $user) { 
        
        $sum = 0;
        $prev = $user->getPrevReviewsQuantity();
        if(FALSE !== $prev) {
            $curr = $user->getReviews()->count();
            $sum = ($curr - $prev) * $this->gotReviewPoints;
        }
        
        return $sum;
    }
    
    /**
     * Counts quantity of points that should be added or removed from user in case
     * of changes in his profile.
     * @param \Fenchy\RegularUserBundle\Entity\UserRegular $ru
     * @return Integer
     */
    private function countProfilePoints(UserRegular $ru) {
        
        $points = 0;
        
        $params = array(
            'aboutMe',
            'gender',
            'link',
            'languages'
        );
        
        foreach ($params as $param) {
//            echo '* '.$param.': '.($this->getChange($param, $ru) * $this->getPoints($param)).'<br>';
            $points += $this->getChange($param, $ru) * $this->getPoints($param);
        }
//        exit;
        return $points;
    }
    
    /**
     * Counts quantity of points that should be added or removed from user in case
     * of changes in his profile.
     * @param \Fenchy\RegularUserBundle\Entity\UserRegular $ru
     * @return Integer
     */
    private function countProfilePoint(UserRegular $ru) {
    
    	$points = 0;
    
    	$params = array(
    			'aboutMe',
    			'myLike',
    			'hotplaces',    			
    	);
    
    	foreach ($params as $param) {
    		//            echo '* '.$param.': '.($this->getChange($param, $ru) * $this->getPoints($param)).'<br>';
    		$points += $this->getChange($param, $ru) * $this->getPoints($param);
    	}
    	//        exit;
    	return $points;
    	echo $points; exit;
    }
    
    /**
     * Checked if user changed parameter. Returns 1 if parameter has been set
     * from NULL, -1 if parameter has been set from NULL to some value
     * and 0 othervise (NULL to NULL or val to val).
     * @param String $param
     * @param \Fenchy\RegularUserBundle\Entity\UserRegular $ru
     * @return int 0|1|-1
     */
    protected function getChange($param, UserRegular $ru) {
        
        $param = ucfirst($param);

        if($ru->{'getPrev'.$param}() === FALSE) return 0;
        if($ru->{'get'.$param}() && !$ru->{'getPrev'.$param}())
            return 1;
        
        if(!$ru->{'get'.$param}() && $ru->{'getPrev'.$param}())
            return -1;
        
        return 0;
    }
    
    /**
     * Returns number of points that should be given for profile parameter.
     * @param String $param
     * @return Integer
     */
    protected function getPoints($param) {
        
        return (int) ($this->{$param.'Percent'} * $this->profilePoints);
    }
    
    protected function getGalleryPointsChange(Gallery $gallery) {
        
        $cur = $gallery->getImagesQuantity();
        $prev = $gallery->getPrevImagesQuantity();
        if(FALSE === $prev) return 0;
        return ($this->getGalleryPercent($cur) - $this->getGalleryPercent($prev)) * $this->profilePoints;
    }
    
    public function getPointsList(\Fenchy\UserBundle\Entity\User $user) {
        
        $points = array();
        $points['notice'] = $user->getNonDraftNoticesCount() * $this->noticePoints;
        $points['reviews'] = $user->getReviews(TRUE)->count() * $this->gotReviewPoints ;
        $points['ownReviews'] = $user->getOwnReviews()->count() * $this->reviewPoints;
        $points['facebook'] = $user->getFacebookId() ? $this->facebookPoints : 0;
        $points['contacts'] = $user->getRegularUser()->getMyFriends()->count() * $this->contactPoints;
        $points['profilePercent'] = $this->aboutMePercent * ($user->getRegularUser()->getAboutMe() ? 1 : 0) +
				        $this->myLikePercent * ($user->getRegularUser()->getMylike() ? 1 : 0) +
				        $this->hotplacesPercent * ($user->getRegularUser()->getHotplaces() ? 1 : 0);

        $points['profile'] = $this->profilePoints * $points['profilePercent'];
        //$points['profilePercent'] = ($points['profilePercent'] * 100).'%';
        
        return $points;
    }
    
    public function getGalleryPercent($q) {
        
        if($q == 0) return 0;
        if($q == 1) return $this->firstPicturePercent;
        if($q <= $this->nextPictureLimit + 1) return $this->firstPicturePercent + ($q - 1) * $this->nextPicturePercent;
        return $this->firstPicturePercent + $this->nextPictureLimit * $this->nextPicturePercent;
    }
}