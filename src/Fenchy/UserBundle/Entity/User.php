<?php

namespace Fenchy\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

use Fenchy\RegularUserBundle\Entity\UserRegular;
use Doctrine\Common\Collections\ArrayCollection;
use Fenchy\UserBundle\Entity\NotificationGroupInterval;
use Fenchy\NoticeBundle\Entity\Notice,
    Fenchy\GalleryBundle\Entity\Gallery,
    Fenchy\UserBundle\Entity\Reference,
    Fenchy\UtilBundle\Entity\Location,
    Fenchy\UtilBundle\Entity\Sticker,
    Fenchy\NoticeBundle\Entity\Review;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="user__users")
 * @ORM\Entity(repositoryClass="Fenchy\UserBundle\Entity\UserRepository")
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 */
class User extends BaseUser
{
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    
    /**
     * @var string
     */
    protected $username;

    /**
     * @Assert\NotBlank
     * @Assert\Email
     * @var string
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="facebookId", type="string", length=255, nullable=true)
     */
    protected $facebookId;

    /** 
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $twitterID;

    /** 
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $twitter_username;

    /**
     * @var UserRegular $user_regular
     * 
     * @ORM\OneToOne(targetEntity="Fenchy\RegularUserBundle\Entity\UserRegular", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $user_regular;
    
    /**
     * @var Location $location
     * 
     * @ORM\OneToOne(targetEntity="Fenchy\UtilBundle\Entity\Location", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $location;
    
    /**
     * @var ArrayCollection $notification_group_intervals
     * 
     * @ORM\OneToMany(targetEntity="Fenchy\UserBundle\Entity\NotificationGroupInterval", mappedBy="user", cascade={"persist", "remove"})
     */
    private $notification_group_intervals;

    /**
     * @var ArrayCollection $notifications
     * 
     * @ORM\ManyToMany(targetEntity="Fenchy\UserBundle\Entity\Notification", inversedBy="users")
     * @ORM\JoinTable(name="user__users__notifications")
     */
    private $notifications;
    
    /**
     * @var ArrayCollection $notices
     * 
     * @ORM\OneToMany(targetEntity="Fenchy\NoticeBundle\Entity\Notice", mappedBy="user", cascade={"remove", "persist"})
     * @ORM\OrderBy({"created_at"="DESC"})
     */
    private $notices;
    
    /**
     * @var ArrayCollection $messages
     * 
     * @ORM\OneToMany(targetEntity="Fenchy\MessageBundle\Entity\Message", mappedBy="sender")
     */
    private $messages;
    
    /**
     * @ORM\OneToOne(targetEntity="Fenchy\GalleryBundle\Entity\Gallery", mappedBy="user", cascade={"persist", "remove"})
     */
    private $gallery;

    /**
     * @var boolean $justEnabled
     *  
     */      
    private $justEnabled;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $activity;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ask_again;
    
	/**
     * @var string
     *
     * @ORM\Column(name="registeredWith", type="string", length=8, nullable=false)
     */
    protected $registeredWith;

	/**
     * @var string
     *
     * @ORM\Column(name="registeredWithId", type="string", length=100, nullable=false)
     */
    protected $registeredWithId;	
	
    /**
     * @var ArrayCollection $got_reference
     * 
     * @ORM\OneToMany(targetEntity="Fenchy\UserBundle\Entity\Reference", mappedBy="new_user", cascade={"persist", "remove"})
     */
    private $got_references;
    
    /**
     * @var ArrayCollection $sent_references
     * 
     * @ORM\OneToMany(targetEntity="Fenchy\UserBundle\Entity\Reference", mappedBy="ref_user", cascade={"persist", "remove"})
     */
    private $sent_references;
      
    /**
     *@var Fenchy\UserBundle\Entity\EmailChangeRequest 
     *
     * Request to change user's e-mail addres ( if user has issued one)
     * @ORM\OneToOne(targetEntity="Fenchy\UserBundle\Entity\EmailChangeRequest", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $email_change_request;

    /**
     * @var ArrayCollection $stickers
     * 
     * @ORM\OneToMany(targetEntity="Fenchy\UtilBundle\Entity\Sticker", mappedBy="user", cascade={"remove"})
     * @ORM\OrderBy({"created_at"="DESC"})
     */
    private $stickers;
    
    /**
     * @var ArrayCollection $reported_stickers;
     * 
     * @ORM\OneToMany(targetEntity="Fenchy\UtilBundle\Entity\Sticker", mappedBy="reported_by", cascade={"remove"})
     * @ORM\OrderBy({"created_at"="DESC"})
     */
    private $reported_stickers;
    
    /**
     * Reviews about this user
     * @var ArrayCollection $rviews
     * 
     * @ORM\OneToMany(targetEntity="Fenchy\NoticeBundle\Entity\Review", mappedBy="aboutUser", cascade={"remove"})
     * @ORM\OrderBy({"created_at"="DESC"})
     */
    private $reviews;
    
    /**
     * Reviews created by this user
     * @var ArrayCollection $ownRviews
     * 
     * @ORM\OneToMany(targetEntity="Fenchy\NoticeBundle\Entity\Review", mappedBy="author", cascade={"remove"})
     * @ORM\OrderBy({"created_at"="DESC"})
     */
    private $ownReviews;
    
    /**
     * Indicates whether user's account is business type
     * @var boolean
     *
     * @ORM\Column(name="is_business", type="boolean", nullable=false)
     */
    private $isBusiness = false;
    
    // user prev attributes needet for activity points calculation
    
    /**
     * Previous fecebookId value
     * @var String $prevFacebookId
     */
    private $prevFacebookId = FALSE;
    
    /**
     * @var DateTime
     * 
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;
    
    protected $prevNoticesQuantity = FALSE;
    protected $prevOwnReviewsQuantity = FALSE;
    protected $prevReviewsQuantity = FALSE;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->notification_group_intervals = new ArrayCollection();
        $this->notifications                = new ArrayCollection();
        $this->notices                      = new ArrayCollection();
        $this->messages                     = new ArrayCollection();
        $this->got_references               = new ArrayCollection();
        $this->sent_references              = new ArrayCollection();
        $this->stickers                     = new ArrayCollection();
        $this->activity                     = 0;
        $this->ask_again                    = true;
        $this->reviews                      = new ArrayCollection();
        $this->ownReviews                   = new ArrayCollection();
        $this->created_at                   = new \DateTime(); 
    }
    
    public function __toString() {
        return '#'.$this->id.' '.$this->email;
    }
    
    public function getAskAgain() {
        
        return $this->ask_again;
    }

    public function setAskAgain($ask_again) {
        
        if($ask_again == 1)
            $this->ask_again = true;
        else
            $this->ask_again = false;
    }
    
    public function getName()
    {
        return $this->user_regular && $this->user_regular->getName() ? $this->user_regular->getName() : $this->username;
    }
    
    public function getMainImage()
    {
        if (NULL == $this->gallery) {
            return NULL;
        }
        return $this->gallery->getMainImage();
        
    }
    
    /**
     * Set twitterID
     *
     * @param string $twitterID
     */
    public function setTwitterID($twitterID)
    {
        $this->twitterID = $twitterID;
    }

    /**
     * Get twitterID
     *
     * @return string 
     */
    public function getTwitterID()
    {
        return $this->twitterID;
    }

    /**
     * Set twitter_username
     *
     * @param string $twitterUsername
     */
    public function setTwitterUsername($twitterUsername)
    {
        $this->twitter_username = $twitterUsername;
    }

    /**
     * Get twitter_username
     *
     * @return string 
     */
    public function getTwitterUsername()
    {
        return $this->twitter_username;
    }
    
    /**
     * @return UserRegular
     */
    public function getRegularUser()
    {
        return $this->user_regular;
    }
    
    /**
     * @param UserRegular $ru 
     */
    public function setRegularUser(UserRegular $ru)
    {
        $this->user_regular = $ru;
        $this->user_regular->setUser($this);
    }


    /**
     * @return ArrayCollection
     */
    public function getNotificationGroupIntervals()
    {
        return $this->notification_group_intervals;
    }
    
    /**
     * @param ArrayCollection $intervals 
     */
    public function setNotificationGroupIntervals(ArrayCollection $intervals)
    {
        $this->notification_group_intervals = $intervals;
    }
    
    /**
     * @param NotificationGroupInterval $interval 
     */
    public function addNotificationGroupInterval(NotificationGroupInterval $interval)
    {
        $this->notification_group_intervals->add($interval);
        
        return $this;
    }
    
    /**
     *
     * @param NotificationGroupInterval $interval 
     */
    public function removeNotificationGroupInterval(NotificationGroupInterval $interval)
    {
        $this->notification_group_intervals->removeElement($interval);
    }

    /**
     * @return ArrayCollection
     */
    public function getNotifications()
    {
        return $this->notifications;
    }
    
    public function setNotifications(ArrayCollection $notifications)
    {
        $this->notifications = $notifications;
    }
    
    public function isRegular()
    {
        return null !== $this->user_regular;
    }

    public function createRegular()
    {
        $user_regular = new UserRegular();
        $user_regular->setUser($this);
        $this->user_regular = $user_regular;
        return $user_regular;
    }
    
    /*
     * facebook extension for FOS\UserBundle\Model\User.php serialize()
     */
    public function serialize()
    {
        return serialize(array($this->facebookId, parent::serialize()));
    }

    /*
     * facebook extension for FOS\UserBundle\Model\User.php unserialize()
     */
    public function unserialize($data)
    {
        list($this->facebookId, $parentData) = unserialize($data);
        parent::unserialize($parentData);
    }

    /**
     * @param string $facebookId
     * @return void
     */
    public function setFacebookId($facebookId)
    {
        $this->prevFacebookId = $this->facebookId;
        $this->facebookId = $facebookId;
            /* @TODO: for adding comments etc
            $this->addRole('ROLE_FACEBOOK');*/
    }

    /**
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    public function getUserRegular() {
        
        return $this->getRegularUser();
    }
    
    public function setUserRegular(UserRegular $ru) {
        
        $this->setRegularUser($ru);
    }
    
    /**
     * @return ArrayCollection
     */
    public function getNotices() {
        
        return $this->notices;
    }
    
    /**
     * returns number of non draft user's notices
     * @return int
     */
    public function getNonDraftNoticesCount() {
        $count = 0;
        foreach($this->notices as $notice) {
            if(!$notice->getDraft()) {
                $count++;
            }
        }        
        return $count;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getInterestsAndActivities()
    {
        return $this->notices->filter(function($var){
            if (true !== $var->getDraft() && null === $var->getType()) {
                return true;
            }
            return false;
        });
    }
    
    /**
     * Sets prevNoticesQuantity value to current notices quantity.
     * This function should be called each time when one of user listings draft status must be changed (before it).
     * called in Fenchy\NoticeBundle\Entity\Notice::setDraft();
     */
    public function countPrevNoticesQuantity() {
        
        $this->prevNoticesQuantity = $this->getNonDraftNoticesCount();
    }
    /**
     * Set Notices. Clones user location into Notices which has no location.
     * @param ArrayCollection $notices
     * @return User 
     */
    public function setNotices(ArrayCollection $notices) {
        
        $this->prevNoticesQuantity = $this->getNonDraftNoticesCount();
        // set notices location
        foreach($notices as $notice) {
            
            if(!$notice->getLocation()) {
                $notice->setLocation(clone $this->getLocation());
            }
        }
        
        $this->notices = $notices;
        
        return $this;
    }
    
    /**
     * Add Notice. Clones user location into Notice if notice has no set location.
     * @param Notice $notice
     * @return User 
     */
    public function addNotice(Notice $notice) {
        
        if(!$notice->getLocation()) {
            $notice->setLocation(clone $this->getLocation());
        }   
        
        $this->prevNoticesQuantity = $this->getNonDraftNoticesCount();
        $this->notices->add($notice);
        
        return $this;
    }
    
    /**
     * @param Notice $notice
     * @return User 
     */
    public function removeNotice(Notice $notice) {
        
        $this->prevNoticesQuantity = $this->getNonDraftNoticesCount();
        $this->notices->removeElement($notice);
        
        return $this;
    }
    
    public function getPrevNoticesQuantity() {
        
        return $this->prevNoticesQuantity;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add notifications
     *
     * @param Fenchy\UserBundle\Entity\Notification $notifications
     * @return User
     */
    public function addNotification(\Fenchy\UserBundle\Entity\Notification $notifications)
    {
        $this->notifications[] = $notifications;
    
        return $this;
    }

    /**
     * Remove notifications
     *
     * @param Fenchy\UserBundle\Entity\Notification $notifications
     */
    public function removeNotification(\Fenchy\UserBundle\Entity\Notification $notifications)
    {
        $this->notifications->removeElement($notifications);
    }
    
    public function setEnabled($boolean)
    {
        $boolean && $this->justEnabled = TRUE;
        $this->enabled = (Boolean) $boolean;

        return $this;
    }
    
    public function getJustEnabled()
    {
        return $this->justEnabled;
    }

    /**
     * Set justEnabled
     *
     * @param boolean $justEnabled
     * @return User
     */
    public function setJustEnabled($justEnabled)
    {
        $this->justEnabled = $justEnabled;
    
        return $this;
    }
    
    /**
     * @param Gallery $gallery
     * 
     * @return User
     */
    public function setGallery(Gallery $gallery) {
        
        $this->gallery = $gallery->setUser($this);
        
        return $this;
    }
    
    /**
     * @return Gallery
     */
    public function getGallery() {
        
        return $this->gallery;
    }
    
    public function getActivity() {
        
        return $this->activity;
    }
    
    /**
     * @return User
     */
    public function setActivity($activity) {
        
        $this->activity = $activity;
        
        return $this;
    }
    
    public function addActivity($points) {

        $this->activity += $points;
    }
    
    public function setMessages(ArrayCollection $messages) {
        
        $this->messages = $messages;
        
        return $this;
    }
    
    public function getMessages() {
        
        return $this->messages();
    }
    
    public function addMessage($message) {
        
        $this->messages->add($message);
        
        return $this;
    }
    
    public function removeMessage($message) {
        
        $this->messages->removeEntity($message);
        
        return $this;
    }
    
    public function getGotReferences() {
        
        return $this->got_references;
    }
    
    public function setGotReferences(ArrayCollection $references) {
        
        $this->got_references = $references;
        
        return $this;
    }
    
    public function addGotReference(Reference $reference) {
        
        $this->got_references->add($reference->setNewUser($this));
        
        return $this;
    }
    
    public function removeGotReference(Reference $reference) {
        
        $this->got_reference->removeEntity($reference);
        
        return $this;
    }
    
    public function getSentReferences() {
        
        return $this->sent_references;
    }
    
    public function setSentReferences(ArrayCollection $references) {
        
        $this->sent_references = $references;
        
        return $this;
    }
    
    public function addSentReference(Reference $reference) {
        
        $this->sent_references->add($reference);
        
        return $this;
    }
    
    public function removeSentReference(Reference $reference) {
        
        $this->sent_references->removeEntity($reference);
        
        return $this;
    }
    
    public function enableReference() {

        if(!count($this->got_references)) {
            return;
        }
        
        $first = NULL;
        foreach($this->got_references as $ref) {

            if(NULL === $first || $ref->getCreatedAt() < $first->getCreatedAt()) {
                
                $first = $ref;
            }
        }
        $first->setActive(TRUE);
        $first->getRefUser()->referenceAccepted();

        return $first;
    }
    
    public function referenceAccepted() {
        
    }

    /**
     * Set registeredWith
     *
     * @param string $registeredWith
     * @return User
     */
    public function setRegisteredWith($registeredWith)
    {
        $this->registeredWith = $registeredWith;
    
        return $this;
    }

    /**
     * Get registeredWith
     *
     * @return string 
     */
    public function getRegisteredWith()
    {
        return $this->registeredWith;
    }

    /**
     * Set registeredWithId
     *
     * @param string $registeredWithId
     * @return User
     */
    public function setRegisteredWithId($registeredWithId)
    {
        $this->registeredWithId = $registeredWithId;
    
        return $this;
    }

    /**
     * Get registeredWithId
     *
     * @return string 
     */
    public function getRegisteredWithId()
    {
        return $this->registeredWithId;
    }
    

    /**
     * Set email_change_request
     *
     * @param Fenchy\UserBundle\Entity\EmailChangeRequest $emailChangeRequest
     * @return User
     */
    public function setEmailChangeRequest(\Fenchy\UserBundle\Entity\EmailChangeRequest $emailChangeRequest = null)
    {
        $this->email_change_request = $emailChangeRequest;
    
        return $this;
    }

    /**
     * Get email_change_request
     *
     * @return Fenchy\UserBundle\Entity\EmailChangeRequest 
     */
    public function getEmailChangeRequest()
    {
        return $this->email_change_request;
    }
    
    /**
     * Set Location
     * 
     * @param Location $location
     * @return User 
     */
    public function setLocation(Location $location) {
        
        $this->location = $location->setUser($this);
        
        return $this;
    }
    
    /**
     * Get Location
     * 
     * @return Location
     */
    public function getLocation() {
        
        !$this->location && $this->setLocation(new Location());
        
        return $this->location;
    }
    
    /**
     * Return TRUE if all required location data is set. False otherwise.
     * @return bool
     */
    public function hasRequiredLocation() {
        
        if(!$this->location) return FALSE;
        
        return $this->location->hasLocation();
    }
    
    /**
     * @return ArrayCollection
     */
    public function getStickers() {
        
        return $this->stickers;
    }
    
    /**
     * Set Stickers.
     * @param ArrayCollection $stickers
     * @return User 
     */
    public function setStickers(ArrayCollection $stickers) {

        $this->stickers = $stickers;
        
        return $this;
    }
    
    /**
     * Add Sticker.
     * @param Sticker $sticker
     * @return User 
     */
    public function addSticker(Sticker $sticker) {
        
        $this->stickers->add($sticker);

        return $this;
    }
    
    /**
     * @param Sticker $sticker
     * @return User 
     */
    public function removeSticker(Sticker $sticker) {
        
        $this->stickers->removeElement($sticker);
        
        return $this;
    }
    
    /**
     * Set ReportedStickers.
     * @param ArrayCollection $stickers
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function setReportedStickers(ArrayCollection $stickers) {
        
        $this->reported_stickers = $stickers;
        
        return $this;
    }
    
    /**
     * Add Reported Sticker.
     * @param \Fenchy\UtilBundle\Entity\Sticker $sticker
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function addReportedSticker(Sticker $sticker) {
        
        $this->reported_stickers->add($sticker);
        
        return $this;
    }
    
    /**
     * Remove Reported Sticker.
     * @param \Fenchy\UtilBundle\Entity\Sticker $sticker
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function removeReportedSticker(Sticker $sticker) {
        
        $this->reported_stickers->removeElement($sticker);
        
        return $this;
    }
    
    /**
     * Get Reported Stickers.
     * @return ArrayCollection
     */
    public function getReportedStickers() {
        
        return $this->reported_stickers;
    }
    
    /**
     * Set Reviews
     * @param \Doctrine\Common\Collections\ArrayCollection $reviews
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function setReviews(ArrayCollection $reviews) {
        $this->prevReviewsQuantity = $this->reviews->count();
        $this->reviews = $reviews;
        
        return $this;
    }
    
    /**
     * Add Review
     * @param \Fenchy\NoticeBundle\Entity\Review $review
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function addReview(Review $review) {
        $this->prevReviewsQuantity = $this->reviews->count();
        $this->reviews->add($review);
        
        return $this;
    }
    
    /**
     * Remove Review
     * @param \Fenchy\NoticeBundle\Entity\Review $review
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function removeReview(Review $review) {
        $this->prevReviewsQuantity = $this->reviews->count();
        $this->reviews->removeElement($review);
        
        return $this;
    }
    
    /**
     * Get Reviews
     * @return ArrayCollection
     */
    public function getReviews($type = NULL) {
        
        if(NULL === $type)
            return $this->reviews;
        
        if($type) {
            $filtered = new ArrayCollection();
            foreach($this->reviews as $review) {
                if($review->getType() === Review::TYPE_POSITIVE) {
                    $filtered[] = $review;
                }
            }
            return $filtered;
        } else {
            $filtered = new ArrayCollection();
            foreach($this->reviews as $review) {
                if($review->getType() === Review::TYPE_NEGATIVE) {
                    $filtered[] = $review;
                }
            }
            return $filtered;
        }
    }
    
    /**
     * Set Own Reviews
     * @param \Doctrine\Common\Collections\ArrayCollection $reviews
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function setOwnReviews(ArrayCollection $reviews) {
        $this->prevOwnReviewsQuantity = $this->ownReviews->count();
        $this->ownReviews = $reviews;
        
        return $this;
    }
    
    /**
     * Add Own Review
     * @param \Fenchy\NoticeBundle\Entity\Review $review
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function addOwnReview(Review $review) {
        $this->prevOwnReviewsQuantity = $this->ownReviews->count();
        $this->ownReviews->add($review);
        
        return $this;
    }
    
    /**
     * Remove Own Review
     * @param \Fenchy\NoticeBundle\Entity\Review $review
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function removeOwnReview(Review $review) {
        $this->prevOwnReviewsQuantity = $this->ownReviews->count();
        $this->ownReviews->removeElement($review);
        
        return $this;
    }
    
    /**
     * Get Own Reviews
     * @return Review
     */
    public function getOwnReviews() {
        
        return $this->ownReviews;
    }
    
    public function getPrevReviewsQuantity() {
        
        return $this->prevReviewsQuantity;
    }
    
    public function getPrevOwnReviewsQuantity() {
        
        return $this->prevOwnReviewsQuantity;
    }
    
    /**
     * Returns all reviews assigned to this user and his notices.
     * @return Array
     */
    public function getAllReviews() {
        
        $result = $this->reviews->toArray();
        foreach($this->notices as $notice) {
            $result = array_merge($result, $notice->getReviews()->toArray());
        }
        
        return $result;
    }

    public function toJsonArray() {
        
        return array(
            'title'     => $this->getRegularUser()->getFirstname(),
            'location'  => ''.$this->getLocation(),
            'content'   => $this->getRegularUser()->getAboutMe(),
            'image'     => ''.$this->getRegularUser()->getAvatar(),
            'direction' => '',
            'owner'     => '',
            'business'  => $this->getIsBusiness()?'business-user':''
        );
    }
    
    /**
     * Get PrevFacebookId
     * @return String
     */
    public function getPrevFacebookId() {
        
        return $this->prevFacebookId;
    }
    
    /**
     * returns whether user account is business type
     * @return boolean
     */
    public function getIsBusiness() {
        return $this->isBusiness;
    }

    /**
     * setter for business type flag
     * @param boolean $isBusiness
     */
    public function setIsBusiness($isBusiness) {
        $this->isBusiness = $isBusiness;
    }



    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    
        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }
}