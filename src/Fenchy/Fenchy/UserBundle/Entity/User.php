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
    Fenchy\NoticeBundle\Entity\Review,
    Fenchy\RegularUserBundle\Entity\Neighbors,
	Fenchy\NoticeBundle\Entity\Request;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Fenchy\NoticeBundle\Entity\Comment;
use Cunningsoft\ChatBundle\Entity\AuthorInterface;


/**
 * @ORM\Table(name="user__users")
 * @ORM\Entity(repositoryClass="Fenchy\UserBundle\Entity\UserRepository")
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 */
class User extends BaseUser implements AuthorInterface
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
     * @var boolean
     *
     * @ORM\Column(name="managertype", type="boolean", length=255, nullable=true)
     */
    protected $managertype;

    /**
     * @var boolean
     *
     * @ORM\Column(name="member", type="boolean", nullable=true)
     */
    protected $member;
    
    /** 
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $twitterID;
    
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $typesID;

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
     * @var UserGroup $user_group
     *
     * @ORM\OneToOne(targetEntity="Fenchy\RegularUserBundle\Entity\UserGroup", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $user_group;

    
    /**
     * @var LocationVerification $location_id
     *
     * @ORM\OneToOne(targetEntity="Fenchy\UserBundle\Entity\LocationVerification", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $location_id;
    
    /**
     * @var LocationVerification $identity
     *
     * @ORM\OneToOne(targetEntity="Fenchy\UserBundle\Entity\IdentityVerification", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $identity;
    
    /**
     * @var Payment $payment_id
     *
     * @ORM\OneToOne(targetEntity="Fenchy\UserBundle\Entity\Payment", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $payment_id;
    
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
     * Logged users Neighbor
     * @var ArrayCollection $members
     *
     * @ORM\OneToMany(targetEntity="Fenchy\RegularUserBundle\Entity\GroupMembers", mappedBy="neighbor", cascade={"remove", "persist"})
     * @ORM\OrderBy({"created_at"="DESC"})
     */
    private $members;
    
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
     * @var ArrayCollection $reviews
     * 
     * @ORM\OneToMany(targetEntity="Fenchy\NoticeBundle\Entity\Review", mappedBy="aboutUser", cascade={"remove"})
     * @ORM\OrderBy({"created_at"="DESC"})
     */
    private $reviews;
    
    /**
     * Requests about this user
     * @var ArrayCollection $request
     *
     * @ORM\OneToMany(targetEntity="Fenchy\NoticeBundle\Entity\Request", mappedBy="aboutUser", cascade={"remove"})
     * @ORM\OrderBy({"created_at"="DESC"})
     */
    private $requests;
    
    /**
     * Comment about this user
     * @var ArrayCollection $comments
     *
     * @ORM\OneToMany(targetEntity="Fenchy\NoticeBundle\Entity\Comment", mappedBy="aboutUser", cascade={"remove"})
     * @ORM\OrderBy({"created_at"="DESC"})
     */
    private $comments;
    
    /**
     * Reviews created by this user
     * @var ArrayCollection $ownReviews
     * 
     * @ORM\OneToMany(targetEntity="Fenchy\NoticeBundle\Entity\Review", mappedBy="author", cascade={"remove"})
     * @ORM\OrderBy({"created_at"="DESC"})
     */
    private $ownReviews;
    
    /**
     * Logged User
     * @var ArrayCollection $logged
     *
     * @ORM\OneToMany(targetEntity="Fenchy\RegularUserBundle\Entity\Neighbors", mappedBy="current", cascade={"remove"})
     * @ORM\OrderBy({"created_at"="DESC"})
     */
    private $logged;
    
    /**
     * Logged users Neighbor
     * @var ArrayCollection $myNeighbor
     *
     * @ORM\OneToMany(targetEntity="Fenchy\RegularUserBundle\Entity\Neighbors", mappedBy="neighbor", cascade={"remove"})
     * @ORM\OrderBy({"created_at"="DESC"})
     */
    private $myNeighbor;
    
    /**
     * Requests created by this user
     * @var ArrayCollection $ownRequests
     *
     * @ORM\OneToMany(targetEntity="Fenchy\NoticeBundle\Entity\Request", mappedBy="author", cascade={"remove"})
     * @ORM\OrderBy({"created_at"="DESC"})
     */
    private $ownRequests;
    
    /**
     * Comment created by this user
     * @var ArrayCollection $ownComments
     *
     * @ORM\OneToMany(targetEntity="Fenchy\NoticeBundle\Entity\Comment", mappedBy="author", cascade={"remove"})
     * @ORM\OrderBy({"created_at"="DESC"})
     */
    private $ownComments;
    
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
     * Indicates whether user is online
     * @var boolean
     *
     * @ORM\Column(name="is_login", type="boolean", nullable=true)
     */
    private $isLogin = false;
    
    /**
     * @var DateTime
     * 
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;
    
    protected $prevNoticesQuantity = FALSE;
    protected $prevOwnReviewsQuantity = FALSE;
    protected $prevReviewsQuantity = FALSE;
    protected $prevOwnRequestsQuantity = FALSE;
    protected $prevRequestsQuantity = FALSE;
    protected $prevOwnCommentsQuantity = FALSE;
    protected $prevCommentsQuantity = FALSE;
    
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
        $this->myNeighbor                   = new ArrayCollection();
        $this->logged                   	= new ArrayCollection();
        $this->requests                     = new ArrayCollection();
        $this->ownRequests                  = new ArrayCollection();
        $this->comment                      = new ArrayCollection();
        $this->ownComments                   = new ArrayCollection();
        $this->created_at                   = new \DateTime(); 
       	$this->user 						= new ArrayCollection();
       	$this->members 						= new ArrayCollection();
     
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
     * Set typesID
     *
     * @param string $typesID
     */
    public function setTypesID($typesID)
    {
    	$this->typesID = $typesID;
    }
    
    /**
     * Get typesID
     *
     * @return string
     */
    public function getTypesID()
    {
    	return $this->typesID;
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

    /**
     * @param boolean $managertype
     * @return void
     */
    
//     public function setManagerType($managertype)
//     {
//     	$this->managertype = $managertype;
//     }
    
    /**
     * @return boolean
     */
//     public function getManagerType()
//     {
//     	return $this->managertype;
//     }
    
    
    
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
     * Set Logged
     * @param \Doctrine\Common\Collections\ArrayCollection $logged
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function setLogged(ArrayCollection $logged) {
    	 
    	$this->logged = $logged;
    
    	return $this;
    }
    
    /**
     * Get Logged
     * @return ArrayCollection
     */
    public function getLogged() {
    	 
    	return $this->logged;
    }
    
    
    /**
     * Set MyNeighbor
     * @param \Doctrine\Common\Collections\ArrayCollection $myneighbor
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function setMyNeighbor(ArrayCollection $myneighbor) {
    	
    	$this->myneighbor = $myneighbor;
    
    	return $this;
    }
    
    /**
     * Get MyNeighbor
     * @return ArrayCollection
     */
    public function getMyNeighbor() {
    	
    	return $this->myneighbor;
    }
    
    
    /**
     * Set Requests
     * @param \Doctrine\Common\Collections\ArrayCollection $request
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function setRequests(ArrayCollection $request) {
    	$this->prevRequestsQuantity = $this->requests->count();
    	$this->requests = $request;
    
    	return $this;
    }
    
    /**
     * Set Comments
     * @param \Doctrine\Common\Collections\ArrayCollection $comments
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function setComments(ArrayCollection $comments) {
    	$this->prevCommentsQuantity = $this->comments->count();
    	$this->comments = $comments;
    
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
     * Add Request
     * @param \Fenchy\NoticeBundle\Entity\Request $request
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function addRequest(Request $request) {
    	$this->prevRequestsQuantity = $this->requests->count();
    	$this->requests->add($request);
    
    	return $this;
    }
    
    /**
     * Add Comment
     * @param \Fenchy\NoticeBundle\Entity\Comment $comment
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function addComment(Comment $comment) {
    	$this->prevCommentsQuantity = $this->comments->count();
    	$this->comments->add($comment);
    
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
     * Remove Request
     * @param \Fenchy\NoticeBundle\Entity\Request $request
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function removeRequest(Request $request) {
    	$this->prevRequestsQuantity = $this->requests->count();
    	$this->requests->removeElement($request);
    
    	return $this;
    }
    
    /**
     * Remove Comments
     * @param \Fenchy\NoticeBundle\Entity\Comment $comment
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function removeComment(Comment $comment) {
    	$this->prevCommentsQuantity = $this->comments->count();
    	$this->comments->removeElement($comment);
    
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
    
    public function getRequests($status = NULL) {
    
    	if(NULL === $status)
    		return $this->requests;
    
    	if($status == 'running') {
    		$filtered = new ArrayCollection();
    		foreach($this->requests as $request) {
    			if($request->getStatus() === Request::STATUS_RUNNING) {
    				$filtered[] = $request;
    			}
    		}
    		return $filtered;
    	} else if($status == 'accepted'){
    		$filtered = new ArrayCollection();
    		foreach($this->requests as $request) {
    			if($request->getStatus() === Request::STATUS_ACCEPTED) {
    				$filtered[] = $request;
    			}
    		}
    		return $filtered;
    	} else if($status == 'pendding'){
    		$filtered = new ArrayCollection();
    		foreach($this->requests as $request) {
    			if($request->getStatus() === Request::STATUS_PENDING) {
    				$filtered[] = $request;
    			}
    		}
    		return $filtered;
    	} else if($status == 'rejected'){
    		$filtered = new ArrayCollection();
    		foreach($this->requests as $request) {
    			if($request->getStatus() === Request::STATUS_REJECTED) {
    				$filtered[] = $request;
    			}
    		}
    		return $filtered;
    	} else if($status == 'agreed'){
    		$filtered = new ArrayCollection();
    		foreach($this->requests as $request) {
    			if($request->getStatus() === Request::STATUS_AGREED) {
    				$filtered[] = $request;
    			}
    		}
    		return $filtered;
    	} else if($status == 'done'){
    		$filtered = new ArrayCollection();
    		foreach($this->requests as $request) {
    			if($request->getStatus() === Request::STATUS_DONE) {
    				$filtered[] = $request;
    			}
    		}
    		return $filtered;
    	}
    }
    
    
    /**
     * Get Comments
     * @return ArrayCollection
     */
    public function getComments($type = NULL) {
    
    	if(NULL === $type)
    		return $this->comments;
    
    	if($type) {
    		$filtered = new ArrayCollection();
    		foreach($this->comments as $comment) {
    			if($comment->getType() === Comment::TYPE_POSITIVE) {
    				$filtered[] = $comment;
    			}
    		}
    		return $filtered;
    	} else {
    		$filtered = new ArrayCollection();
    		foreach($this->comments as $comment) {
    			if($comment->getType() === Comment::TYPE_NEGATIVE) {
    				$filtered[] = $comment;
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
     * Set Own Requests
     * @param \Doctrine\Common\Collections\ArrayCollection $requests
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function setOwnRequests(ArrayCollection $requests) {
    	$this->prevOwnRequestsQuantity = $this->ownRequests->count();
    	$this->ownRequests = $requests;
    
    	return $this;
    }
    
    /**
     * Set Own Comments
     * @param \Doctrine\Common\Collections\ArrayCollection $comments
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function setOwnComments(ArrayCollection $comments) {
    	$this->prevOwnCommentsQuantity = $this->ownComments->count();
    	$this->ownComments = $comments;
    
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
     * Add Own Request
     * @param \Fenchy\NoticeBundle\Entity\Request $request
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function addOwnRequest(Request $request) {
    	$this->prevOwnRequestsQuantity = $this->ownRequests->count();
    	$this->ownRequests->add($request);
    
    	return $this;
    }
    
    /**
     * Add Own Comment
     * @param \Fenchy\NoticeBundle\Entity\Comment $comment
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function addOwnComment(Comment $comment) {
    	$this->prevOwnCommentsQuantity = $this->ownComments->count();
    	$this->ownComments->add($comment);
    
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
     * Remove Own Request
     * @param \Fenchy\NoticeBundle\Entity\Request $request
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function removeOwnRequest(Request $request) {
    	$this->prevOwnRequestsQuantity = $this->ownRequests->count();
    	$this->ownRequests->removeElement($request);
    
    	return $this;
    }
    
    /**
     * Remove Own Comment
     * @param \Fenchy\NoticeBundle\Entity\Comment $comment
     * @return \Fenchy\UserBundle\Entity\User
     */
    public function removeOwnComment(Comment $comment) {
    	$this->prevOwnCommentsQuantity = $this->ownComments->count();
    	$this->ownComments->removeElement($comment);
    
    	return $this;
    }
    
    /**
     * Get Own Reviews
     * @return Review
     */
    public function getOwnReviews() {
        
        return $this->ownReviews;
    }
    
    /**
     * Get Own Requests
     * @return Request
     */
    public function getOwnRequests() {
    
    	return $this->ownRequests;
    }
    
    /**
     * Get Own Comments
     * @return Comment
     */
    public function getOwnComments() {
    
    	return $this->ownComments;
    }
    
    public function getPrevReviewsQuantity() {
        
        return $this->prevReviewsQuantity;
    }
    
    public function getPrevRequestsQuantity() {
    
    	return $this->prevRequestsQuantity;
    }
    
    public function getPrevCommentsQuantity() {
    
    	return $this->prevCommentsQuantity;
    }
    
    public function getPrevOwnReviewsQuantity() {
        
        return $this->prevOwnReviewsQuantity;
    }
    
    public function getPrevOwnRequestsQuantity() {
    
    	return $this->prevOwnRequestsQuantity;
    }
    
    public function getPrevOwnCommentsQuantity() {
    
    	return $this->prevOwnCommentsQuantity;
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
    
    /**
     * Returns all requests assigned to this user and his notices.
     * @return Array
     */
    public function getAllRequests() {
    
    	$result = $this->requests->toArray();
    	foreach($this->notices as $notice) {
    		$result = array_merge($result, $notice->getRequests()->toArray());
    	}
    
    	return $result;
    }
    
    
    /**
     * Returns all comments assigned to this user and his notices.
     * @return Array
     */
    public function getAllComments() {
    
    	$result = $this->comments->toArray();
    	foreach($this->notices as $notice) {
    		$result = array_merge($result, $notice->getComments()->toArray());
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

    /**
     * Set location_id
     *
     * @param Fenchy\UserBundle\Entity\LocationVerification $locationId
     * @return User
     */
    public function setLocationId(\Fenchy\UserBundle\Entity\LocationVerification $locationId = null)
    {
        $this->location_id = $locationId;
    
        return $this;
    }

    /**
     * Get location_id
     *
     * @return Fenchy\UserBundle\Entity\LocationVerification 
     */
    public function getLocationId()
    {
        return $this->location_id;
    }

    /**
     * Set identity
     *
     * @param Fenchy\UserBundle\Entity\IdentityVerification $identity
     * @return User
     */
    public function setIdentity(\Fenchy\UserBundle\Entity\IdentityVerification $identity = null)
    {
        $this->identity = $identity;
    
        return $this;
    }

    /**
     * Get identity
     *
     * @return Fenchy\UserBundle\Entity\IdentityVerification 
     */
    public function getIdentity()
    {
        return $this->identity;
    }
    
    /**
     * Set payment_id
     *
     * @param Fenchy\UserBundle\Entity\Payment $paymentId
     * @return User
     */
    public function setPaymentId(\Fenchy\UserBundle\Entity\Payment $paymentId = null)
    {
    	$this->payment_id = $paymentId;
    
    	return $this;
    }
    
    /**
     * Get payment_id
     *
     * @return Fenchy\UserBundle\Entity\Payment
     */
    public function getPaymentId()
    {
    	return $this->payment_id;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getUser()
    {
    	return $this->user;
    }
    
    /**
     * @param ArrayCollection $user
     */
    public function setUsers(ArrayCollection $user)
    {
    	$this->user = $user;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getMembers()
    {
    	return $this->members;
    }
    
    /**
     * @param ArrayCollection $members
     */
    public function setMembers(ArrayCollection $members)
    {
    	$this->members = $members;
    }

    /**
     * Set managertype
     *
     * @param boolean $managertype
     * @return User
     */
    public function setManagertype($managertype)
    {
        $this->managertype = $managertype;
    
        return $this;
    }

    /**
     * Get managertype
     *
     * @return boolean 
     */
    public function getManagertype()
    {
        return $this->managertype;
    }

    /**
     * Set user_group
     *
     * @param Fenchy\RegularUserBundle\Entity\UserGroup $userGroup
     * @return User
     */
    public function setUserGroup(\Fenchy\RegularUserBundle\Entity\UserGroup $userGroup = null)
    {
        $this->user_group = $userGroup;
    
        return $this;
    }

    /**
     * Get user_group
     *
     * @return Fenchy\RegularUserBundle\Entity\UserGroup 
     */
    public function getUserGroup()
    {
        return $this->user_group;
    }

    /**
     * Add user
     *
     * @param Fenchy\RegularUserBundle\Entity\UserGroup $user
     * @return User
     */
    public function addUser(\Fenchy\RegularUserBundle\Entity\UserGroup $user)
    {
        $this->user[] = $user;
    
        return $this;
    }

    /**
     * Remove user
     *
     * @param Fenchy\RegularUserBundle\Entity\UserGroup $user
     */
    public function removeUser(\Fenchy\RegularUserBundle\Entity\UserGroup $user)
    {
        $this->user->removeElement($user);
    }

    /**
     * Add logged
     *
     * @param Fenchy\RegularUserBundle\Entity\Neighbors $logged
     * @return User
     */
    public function addLogged(\Fenchy\RegularUserBundle\Entity\Neighbors $logged)
    {
        $this->logged[] = $logged;
    
        return $this;
    }

    /**
     * Remove logged
     *
     * @param Fenchy\RegularUserBundle\Entity\Neighbors $logged
     */
    public function removeLogged(\Fenchy\RegularUserBundle\Entity\Neighbors $logged)
    {
        $this->logged->removeElement($logged);
    }

    /**
     * Add myNeighbor
     *
     * @param Fenchy\RegularUserBundle\Entity\Neighbors $myNeighbor
     * @return User
     */
    public function addMyNeighbor(\Fenchy\RegularUserBundle\Entity\Neighbors $myNeighbor)
    {
        $this->myNeighbor[] = $myNeighbor;
    
        return $this;
    }

    /**
     * Remove myNeighbor
     *
     * @param Fenchy\RegularUserBundle\Entity\Neighbors $myNeighbor
     */
    public function removeMyNeighbor(\Fenchy\RegularUserBundle\Entity\Neighbors $myNeighbor)
    {
        $this->myNeighbor->removeElement($myNeighbor);
    }

    /**
     * Set member
     *
     * @param boolean $member
     * @return User
     */
    public function setMember($member)
    {
        $this->member = $member;
    
        return $this;
    }

    /**
     * Get member
     *
     * @return boolean 
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * Add members
     *
     * @param Fenchy\RegularUserBundle\Entity\GroupMembers $members
     * @return User
     */
    public function addMember(\Fenchy\RegularUserBundle\Entity\GroupMembers $members)
    {
        $this->members[] = $members;
    
        return $this;
    }

    /**
     * Remove members
     *
     * @param Fenchy\RegularUserBundle\Entity\GroupMembers $members
     */
    public function removeMember(\Fenchy\RegularUserBundle\Entity\GroupMembers $members)
    {
        $this->members->removeElement($members);
    }

    /**
     * Set isLogin
     *
     * @param boolean $isLogin
     * @return User
     */
    public function setIsLogin($isLogin)
    {
        $this->isLogin = $isLogin;
    
        return $this;
    }

    /**
     * Get isLogin
     *
     * @return boolean 
     */
    public function getIsLogin()
    {
        return $this->isLogin;
    }
}