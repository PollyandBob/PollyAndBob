<?php

namespace Fenchy\RegularUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Fenchy\UserBundle\Entity\User;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\GalleryBundle\Entity\Gallery;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="user__regular")
 * @ORM\Entity(repositoryClass="Fenchy\RegularUserBundle\Entity\UserRegularRepository")
 */
class UserRegular 
{
    const GENDER_MALE   = 1;
    const GENDER_FEMALE = 2;
    const MALE          = 'male';
    const FEMALE        = 'female';
    
    public static $emptyImagePath = '/images/default_profile_picture.png';
    
    static public $genderMap = array(
        self::GENDER_MALE   => self::MALE,
        self::GENDER_FEMALE => self::FEMALE,
    );
    
    /**
     * @var integer $id
     * 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    
    /**
     * @var string $firstname
     * 
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $firstname;
    
    /**
     * @var string $lastname
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     */
    private $lastname;
    
    /**
     * @var integer $gender
     * 
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $gender;
    
    /**
     * @var integer $birthday
     * 
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthday;
    
    /**
     * @var string aboutMe
     * @ORM\Column(name="about_me", type="text", nullable=true)
     * @Assert\MaxLength(1000)
     */
    private $aboutMe;
    
    /**
     * @var string link
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $link;
    
    /**
     * @var User $user; 
     * @ORM\OneToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="user_regular")
     */
    private $user;
    
    /**
     * @ORM\OneToOne(targetEntity="Fenchy\GalleryBundle\Entity\Gallery", mappedBy="regular_user", cascade={"remove", "persist"})
     */
    private $gallery;
    
    /**
     * @ORM\ManyToMany(targetEntity="Fenchy\RegularUserBundle\Entity\UserRegular", inversedBy="friendsWithMe")
     * @ORM\JoinTable(name="user__friends",
     *      joinColumns={@ORM\JoinColumn(name="user_regular_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="friend_user_regular_id", referencedColumnName="id")}
     *      )
     */
    private $myFriends;

    /**
     * @ORM\ManyToMany(targetEntity="Fenchy\RegularUserBundle\Entity\UserRegular", mappedBy="myFriends")
     */
    private $friendsWithMe;

    /**
     * @ORM\OneToMany(targetEntity="Invitation", mappedBy="invitation_by", cascade="remove")
     */
    private $myInvitations;

    /**
     * @ORM\OneToMany(targetEntity="Invitation", mappedBy="invitation_for", cascade="remove")
     */
    private $invitationsForMe;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $facebook_publish;
    
    
    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $pref_locale;
    
    /**
     * @var String
     * @ORM\Column(type="text", name="languages", nullable=true).
     */
    private $languages;
    
    /**
     * @todo add interests and activities to DB. It was just Notice without type.
     *      Now it should be tagged string.
     * @var String 
     */
    private $interestsAndActivities;
	
    //New Values v2.1
    /**
     * @var String
     * @ORM\Column(type="text", name="mylike", nullable=true).
     */
    private $mylike;
    
    /**
     * @var String
     * @ORM\Column(type="text", name="hotplaces", nullable=true).
     */
    private $hotplaces;
    
   	/**
     * @var String
     * @ORM\Column(type="text", name="promotedinitiatives", nullable=true).
     */
    private $promotedinitiatives;
    
    /**
     * @var String
     * @ORM\Column(type="text", name="notshowninfo", nullable=true).
     */
    private $notshowninfo;
    
    // Previous values
    
    private $prevAboutMe = FALSE;
    
    private $prevGender = FALSE;
    
    private $prevLink = FALSE;
    
    private $prevLanguages = FALSE;
    
    private $prevInterestsAndActivities = FALSE;
    
    private $prevMyFriendsQuantity = FALSE;
    
    private $prevMylike = FALSE;
    
    private $prevHotplaces = FALSE;
    
    private $prevPromotedinitiatives = FALSE;
    
    private $prevNotshowninfo = FALSE;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->myFriends        = new ArrayCollection();
        $this->friendsWithMe    = new ArrayCollection();
        $this->myInvitations    = new ArrayCollection();
        $this->invitationsForMe = new ArrayCollection();
        $this->gallery          = new \Fenchy\GalleryBundle\Entity\Gallery();
        $this->gallery->setRegularUser($this);
    }
    
    public function __toString() {
        return $this->getName();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getFirstname()
    {
        return $this->firstname;
    }
    
    public function setFirstname($name) 
    {
        $this->firstname = $name;    
    }
    
    public function getLastname()
    {
        return $this->lastname;
    }
    
    public function setLastname($name)
    {
        $this->lastname = $name;
    }
    
    public function getName()
    {
        return $this->firstname;
    }
    
    public function getGender()
    {
        return $this->gender;
    }
    
    public function getPrevGender() {
        
        return $this->prevGender;
    }
    
    public function setGender($gender)
    {
        $this->prevGender = $this->gender;
        $this->gender = $gender;
    }
    
    public function getBirthday()
    {
        return $this->birthday;
    }
    
    public function setBirthday(\DateTime $birthday)
    {
        $this->birthday = $birthday;
    }
    
    public function getAge()
    {
        if(empty($this->birthday)) {
            
            return null;
        }
        
        return $this->birthday->diff(new \DateTime('this year'))->format('%y');
    }
    
    public function setAge($age)
    {
        $birthday = new \DateTime('first day of January this year 00:00:00');
        $age && $birthday->modify("-$age years");
        $this->birthday = $birthday;
    }
    
    public function getAboutMe() {
        
        return $this->aboutMe;
    }
    
    public function getPrevAboutMe() {
        
        return $this->prevAboutMe;
    }
    
    public function setAboutMe($text) {
        $this->prevAboutMe = $this->aboutMe;
        $this->aboutMe = $text;
        
        return $this;
    }
    
    public function getLink() {
        
        return $this->link;
    }
    
    public function getPrevLink() {
        
        return $this->prevLink;
    }
    
    public function setLink($link) {
        
        $this->prevLink = $this->link;
        $this->link = $link;
        
        return $this;
    }
    
    /**
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    public function setUser(User $user)
    {
        $this->user = $user;
    }
    
    /**
     * Return Gallery object, If UR have not gallery then it will be created.
     * 
     * @return Gallery 
     */
    public function getGallery($createIfEmpty = TRUE)
    {
        if($createIfEmpty && is_null($this->gallery)) {
            $this->gallery = new Gallery();
        }
        
        return $this->gallery;
            
    }
	
    public function setGallery(Gallery $gallery)
    {
        $this->gallery = $gallery->setRegularUser($this);
    }
	
    /**
     * Set facebook_publish
     *
     * @param boolean $facebookPublish
     * @return UserRegular
     */
    public function setFacebookPublish($facebookPublish)
    {
        $this->facebook_publish = $facebookPublish;
    
        return $this;
    }

    /**
     * Get facebook_publish
     *
     * @return boolean 
     */
    public function getFacebookPublish()
    {
        return $this->facebook_publish;
    }
    
    /**
     * Add myFriends
     *
     * @param Fenchy\RegularUserBundle\Entity\UserRegular $friend
     * @return UserRegular
     */
    public function addMyFriend(\Fenchy\RegularUserBundle\Entity\UserRegular $friend, $revert = TRUE)
    {
        $this->prevMyFriendsQuantity = $this->myFriends->count();
        $this->myFriends[] = $friend;
        if ($revert) $friend->addMyFriend($this, FALSE);
        
        return $this;
    }

    /**
     * Remove myFriends
     *
     * @param Fenchy\RegularUserBundle\Entity\UserRegular $myFriends
     */
    public function removeMyFriend(\Fenchy\RegularUserBundle\Entity\UserRegular $myFriends, $revert = TRUE)
    {   
        $this->prevMyFriendsQuantity = $this->myFriends->count();
        $this->myFriends->removeElement($myFriends);
        if ($revert) $myFriends->removeMyFriend($this, FALSE);
    }

    /**
     * Get myFriends
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getMyFriends()
    {
        return $this->myFriends;
    }
    
    public function getPrevMyFriendsQuantity() {
        
        return $this->prevMyFriendsQuantity;
    }
    
    /**
     * Get myFriendsIds
     *
     * @return array 
     */
    public function getMyFriendsIds()
    {
        foreach ($this->getMyFriends() as $friend) {
            $ids[] = $friend->getId(); 
        }
        
        return $ids;
    }    

    /**
     * Checks if user already exists in friend list
     *
     * @param Fenchy\RegularUserBundle\Entity\UserRegular $myFriend
     * @return bool 
     */
    public function existsFriend(\Fenchy\RegularUserBundle\Entity\UserRegular $myFriend)
    {
        return $this->myFriends->contains($myFriend);
    }

    /**
     * Alias for existsFriend
     *
     * @param Fenchy\RegularUserBundle\Entity\UserRegular $myFriend
     * @return bool 
     */
    public function isMyFriend(\Fenchy\RegularUserBundle\Entity\UserRegular $myFriend)
    {
        return $this->existsFriend($myFriend);
    }

    /**
     * Returns number of friends
     * 
     * @return integer
     */
    public function countMyFriends()
    {
        return $this->myFriends->count();
    }
    
    /**
     * Add friendsWithMe
     *
     * @param Fenchy\RegularUserBundle\Entity\UserRegular $friendsWithMe
     * @return UserRegular
     */
    public function addFriendsWithMe(\Fenchy\RegularUserBundle\Entity\UserRegular $friendsWithMe)
    {
        $this->friendsWithMe[] = $friendsWithMe;
    
        return $this;
    }

    /**
     * Remove friendsWithMe
     *
     * @param Fenchy\RegularUserBundle\Entity\UserRegular $friendsWithMe
     */
    public function removeFriendsWithMe(\Fenchy\RegularUserBundle\Entity\UserRegular $friendsWithMe)
    {
        $this->friendsWithMe->removeElement($friendsWithMe);
    }

    /**
     * Get friendsWithMe
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getFriendsWithMe()
    {
        return $this->friendsWithMe;
    }
    
    /**
     * Add myInvitations
     *
     * @param Fenchy\RegularUserBundle\Entity\Invitation $myInvitations
     * @return UserRegular
     */
    public function addMyInvitation(\Fenchy\RegularUserBundle\Entity\Invitation $myInvitations)
    {
        $this->myInvitations[] = $myInvitations;
    
        return $this;
    }

    /**
     * Alias for addMyInvitation
     * 
     * @param \Fenchy\RegularUserBundle\Entity\Invitation $invitation
     * @return type 
     */
    public function addInvitation(\Fenchy\RegularUserBundle\Entity\Invitation $invitation)
    {
        return $this->addMyInvitation($invitation);
    }
    
    /**
     * Remove myInvitations
     *
     * @param Fenchy\RegularUserBundle\Entity\Invitation $myInvitations
     */
    public function removeMyInvitation(\Fenchy\RegularUserBundle\Entity\Invitation $myInvitations)
    {
        $this->myInvitations->removeElement($myInvitations);
    }

    /**
     * Get myInvitations
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getMyInvitations()
    {
        return $this->myInvitations;
    }
    
    /**
     * Checks if friend was invited - has not responded invitation
     * 
     * @param \Fenchy\RegularUserBundle\Entity\UserRegular $regularUser
     * @return type 
     */
    public function isInvited(\Fenchy\RegularUserBundle\Entity\UserRegular $regularUser)
    {
        return $this->myInvitations->exists(
                    function($k, $invitation) use ($regularUser)
                    {
                        return !$invitation->isRespond() && $regularUser === $invitation->getInvitationFor();
                    });
    }

    /**
     * Add invitationsForMe
     *
     * @param Fenchy\RegularUserBundle\Entity\Invitation $invitationsForMe
     * @return UserRegular
     */
    public function addInvitationsForMe(\Fenchy\RegularUserBundle\Entity\Invitation $invitationsForMe)
    {
        $this->invitationsForMe[] = $invitationsForMe;
    
        return $this;
    }

    /**
     * Remove invitationsForMe
     *
     * @param Fenchy\RegularUserBundle\Entity\Invitation $invitationsForMe
     */
    public function removeInvitationsForMe(\Fenchy\RegularUserBundle\Entity\Invitation $invitationsForMe)
    {
        $this->invitationsForMe->removeElement($invitationsForMe);
    }

    /**
     * Get invitationsForMe
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getInvitationsForMe()
    {
        return $this->invitationsForMe;
    }
	
	
	public function setPrefLocale($prefLocale)
    {
        $this->pref_locale = $prefLocale;
        return $this;
    }

    public function getPrefLocale()
    {
        return $this->pref_locale;
    }
    
    /**
     * Return TRUE if all required location data for user is set. False otherwise.
     * @return bool
     */
    public function hasRequiredLocation() {
        
        return $this->user->hasRequiredLocation();

    }
    
    /**
     * Gets main image of notice
     * @return \Fenchy\GalleryBundle\Entity\Image
     */
    public function getMainImage() {
        
        if( $this->gallery != NULL && $image = $this->gallery->getMainImage()) {
            return $image;
        }
        return self::$emptyImagePath;
    }
    
    public function getAvatar($small = TRUE) {
        
        $url = $this->gallery->getAvatar($small);
        return $url ? $url : self::$emptyImagePath;
    }
    
    /**
     * Set Languages.
     * 
     * @todo Set parameter when it is created in DB. Remmember to set prev value!
     * 
     * @param String $languages
     * @return \Fenchy\RegularUserBundle\Entity\UserRegular
     */
    public function setLanguages($languages) {
        
        $this->prevLanguages = $this->languages;
        $this->languages = $languages;
        
        return $this;
    }
    
    /**
     * Get Languages.
     * @return String
     */
    public function getLanguages() {
        
        return $this->languages;
    }
    
    /**
     * Get previous Languages.
     * @return String
     */
    public function getPrevLanguages() {
        
        return $this->prevLanguages;
    }    
    
    /**
     * Set My Like.
     *
     * @todo Set parameter when it is created in DB. Remmember to set prev value!
     *
     * @param String $mylike
     * @return \Fenchy\RegularUserBundle\Entity\UserRegular
     */
    public function setMylike($mylike) {
    
    	$this->prevMylike = $this->mylike;
    	$this->mylike = $mylike;
    
    	return $this;
    }
    
    /**
     * Get Mylike.
     * @return String
     */
    public function getMylike() {
    
    	return $this->mylike;
    }
    
    /**
     * Get previous Mylike.
     * @return String
     */
    public function getPrevMylike() {
    
    	return $this->prevMylike;
    }
    
    /**
     * Set Hotplaces.
     *
     * @todo Set parameter when it is created in DB. Remmember to set prev value!
     *
     * @param String $hotplaces
     * @return \Fenchy\RegularUserBundle\Entity\UserRegular
     */
    public function setHotplaces($hotplaces) {
    
    	$this->prevHotplaces = $this->hotplaces;
    	$this->hotplaces = $hotplaces;
    
    	return $this;
    }
    
    /**
     * Get Hotplaces.
     * @return String
     */
    public function getHotplaces() {
    
    	return $this->hotplaces;
    }
    
    /**
     * Get previous Hotplaces.
     * @return String
     */
    public function getPrevHotplaces() {
    
    	return $this->prevHotplaces;
    }
    
    /**
     * Set Promotedinitiatives.
     *
     * @todo Set parameter when it is created in DB. Remmember to set prev value!
     *
     * @param String $promotedinitiatives
     * @return \Fenchy\RegularUserBundle\Entity\UserRegular
     */
    public function setPromotedinitiatives($promotedinitiatives) {
    
    	$this->prevPromotedinitiatives = $this->promotedinitiatives;
    	$this->promotedinitiatives = $promotedinitiatives;
    
    	return $this;
    }
    
    /**
     * Get Promotedinitiatives.
     * @return String
     */
    public function getPromotedinitiatives() {
    
    	return $this->promotedinitiatives;
    }
    
    /**
     * Get previous promotedinitiatives.
     * @return String
     */
    public function getPrevPromotedinitiatives() {
    
    	return $this->prevPromotedinitiatives;
    }
    
    /**
     * Set Notshowninfo.
     *
     * @todo Set parameter when it is created in DB. Remmember to set prev value!
     *
     * @param String $notshowninfo
     * @return \Fenchy\RegularUserBundle\Entity\UserRegular
     */
    public function setNotshowninfo($notshowninfo) {
    
    	$this->prevNotshowninfo = $this->notshowninfo;
    	$this->notshowninfo = $notshowninfo;
    
    	return $this;
    }
    
    /**
     * Get Notshowninfo.
     * @return String
     */
    public function getNotshowninfo() {
    
    	return $this->notshowninfo;
    }
    
    /**
     * Get previous Notshowninfo.
     * @return String
     */
    public function getPrevNotshowninfo() {
    
    	return $this->prevNotshowninfo;
    }
    
    /**
     * Set Interests And Activities.
     * 
     * @todo Set this parameter in DB.
     * 
     * @param String $str
     * @return \Fenchy\RegularUserBundle\Entity\UserRegular
     */
    public function setInterestsAndActivities($str) {
        
        $this->prevInterestsAndActivities = $this->interestsAndActivities;
        $this->interestsAndActivities = $str;
        
        return $this;
    }
    
    /**
     * Get previous value of Interests And Activities.
     * @return String
     */
    public function getPrevInterestsAndActivities() {
        
        return $this->prevInterestsAndActivities;
    }
    
    /**
     * This is fake geter. I&A will be changed from notice to string.
     * We need that method for now to get points counter works.
     * @return null
     */
    public function getInterestsAndActivities() {
        
        return NULL;
    }
}