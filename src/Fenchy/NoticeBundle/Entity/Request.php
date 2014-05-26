<?php

namespace Fenchy\NoticeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

use Fenchy\UserBundle\Entity\User,
    Fenchy\RegularUserBundle\Entity\UserGroup,
    Fenchy\UtilBundle\Entity\Sticker,
    Fenchy\NoticeBundle\Entity\RequestMessages;

/**
 * @ORM\Table(name="notice__requests")
 * @ORM\Entity(repositoryClass="Fenchy\NoticeBundle\Entity\RequestRepository")
 */
class Request
{
    const STATUS_RUNNING    = 'running';
    const STATUS_PENDING    = 'pending';
    const STATUS_AGREED    	= 'agreed';    
    const STATUS_REJECTED   = 'rejected';
    const STATUS_ACCEPTED   = 'accepted';
    const STATUS_DONE    	= 'done';
    
    
    public static $statusMap = array(
        self::STATUS_RUNNING => 'running',
        self::STATUS_PENDING => 'pending',
    	self::STATUS_AGREED  => 'agreed',    	
    	self::STATUS_REJECTED => 'rejected',
    	self::STATUS_ACCEPTED => 'accepted',
    	self::STATUS_DONE => 'done',
    );
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank
     */
    private $title;
    
    /**
     * @var string $text
     * 
     * @ORM\Column(type="string", length=1000, nullable=true)
     * @Assert\NotBlank
     */
    private $text;
    
    /**
     * 1 - running; 2 - padding; 3 - agreed; 4 - rejected; 5- accepted; 6 - done
     * @var integer
     * @ORM\Column(type="string", nullable=false, options={"default":"running"})
     */
    private $status;
    
    /**
     * 1 - running; 2 - padding; 3 - agreed; 4 - rejected; 5- accepted; 6 - done
     * @var integer
     * @ORM\Column(type="string", nullable=false, options={"default":"pending"})
     */
    private $requeststatus;
    
     /**
     * @var ArrayCollection $requestmessage
     *
     * @ORM\OneToMany(targetEntity="Fenchy\NoticeBundle\Entity\RequestMessages", mappedBy="request")
     * @ORM\OrderBy({"created_at"="DESC"})
     */
    private $requestmessage;
    
    
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;
    
    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="ownRequests")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
     */
    private $author;
    
    /**
     * @var Notice
     * 
     * @ORM\ManyToOne(targetEntity="Fenchy\NoticeBundle\Entity\Notice", inversedBy="requests")
     * @ORM\JoinColumn(name="notice_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $aboutNotice;
    
    
    /**
     * @var NeighborhoodMsg
     * 
     * @ORM\ManyToOne(targetEntity="Fenchy\RegularUserBundle\Entity\NeighborhoodMsg", inversedBy="requests")
     * @ORM\JoinColumn(name="neighbormsg_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $aboutNeighborhoodMsg;
    
    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="requests")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $aboutUser;
    
    /**
     * @var UserGruop
     * 
     * @ORM\ManyToOne(targetEntity="Fenchy\RegularUserBundle\Entity\UserGroup", inversedBy="request")
     * @ORM\JoinColumn(name="usergroup_id", referencedColumnName="id", nullable=true)
     */
    private $aboutUserGroup;
    
    /**
     * @var piece_spot
     * @ORM\Column(type="integer", nullable=true)
     */
    private $piece_spot;
    
    /**
     * @var decimal
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    private $price;
    
    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $free = false;
    
    /**
     * @var decimal
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    private $proposeprice;
    
    /**
     * @var decimal
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    private $totalprice;
    
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $currency;
    
    
    /**
     *
     * @var boolean $is_read
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $is_read = false;
    
    /**
     *
     * @var boolean $is_read
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_read_status = false;
	
    /**
     *
     * @var boolean $blue
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $blue = false;
    
    /**
     *
     * @var boolean $request_blue
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $request_blue = false;
    
    /**
     *
     * @var boolean $other_review
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $other_review = false;
    
    /**
     *
     * @var boolean $my_review
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $my_review = false;
    
    /**
     * @var string $swapMsg
     *
     * @ORM\Column(type="string", length=1000, nullable=true)
     * @Assert\NotBlank
     */
    private $swapMsg;
    
    /**
     * @var string $proposedLocation
     *
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank
     */
    private $proposedLocation;
    
    /**
     * @var Date
     *
     * @ORM\Column(type="date", nullable=true)
     *
     */
    private $start_date;
    
    /**
     * @var Time
     *
     * @ORM\Column(type="time", nullable=true)
     *
     */
    private $start_time;
    
    /**
     * @var Date
     *
     * @ORM\Column(type="date", nullable=true)
     *
     */
    private $end_date;
    
    /**
     * @var Time
     *
     * @ORM\Column(type="time", nullable=true)
     *
     */
    private $end_time;
    
    public function __construct() {
        
        $this->status = self::STATUS_PENDING;
        $this->requeststatus = self::STATUS_PENDING;
        $this->created_at = new \DateTime();
        $this->blue = FALSE;
        $this->request_blue = FALSE;
    }
    
    public function __toString() {
        return $this->text;
    }
    
    /**
     * @return integer
     */
    public function getId() {
        
        return $this->id;
    }
    
    /**
     * @param integer $id
     * @return Notice 
     */
    public function setId($id) {
        
        $this->id = $id;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getText() {
        
        return $this->text;
    }
    
    /**
     * @param string $text
     * @return Dictionary
     */
    public function setText($text) {
        
        $this->text = $text;
        
        return $this;
    }
    
    /**
     * Set Status. Throws exception if $status is not valid type.
     * @param Integer $status
     * @return \Fenchy\NoticeBundle\Entity\Request
     * @throws Exception
     */
    public function setStatus($status = self::STATUS_RUNNING) {
  
        $this->status = $status;
        
        return $this;
    }
    
    /**
     * Get Status.
     * @return Integer
     */
    public function getStatus() {
        
        return $this->status;
    }
    
    /**
     * Set Status. Throws exception if $status is not valid type.
     * @param Integer $status
     * @return \Fenchy\NoticeBundle\Entity\Request
     * @throws Exception
     */
    public function setRequeststatus($requeststatus = self::STATUS_PENDING) {
    
    	$this->requeststatus = $requeststatus;
    
    	return $this;
    }
    
    /**
     * Get Status.
     * @return Integer
     */
    public function getRequeststatus() {
    
    	return $this->requeststatus;
    }
    
    /**
     * Returns string representation of status.
     * If $status param has not been set then method returns name of self status.
     * @param Integer $status
     * @return string
     */
    public function getStatusName($status = NULL) {
        
        !$status && $status = $this->status;
        
        if(!array_key_exists($status, self::$statusMap)) {
            return '-';
        }
        
        return self::$statusMap[$status];
    }
    
    /**
     * Set Author.
     * @param \Fenchy\NoticeBundle\Entity\User $user
     * @return \Fenchy\NoticeBundle\Entity\Request
     */
    public function setAuthor(User $user) {
        
        $this->author = $user;
        
        return $this;
    }
    
    /**
     * Get Author.
     * @return \Fenchy\NoticeBundle\Entity\User
     */
    public function getAuthor() {
        
        return $this->author;
    }
    
    /**
     * Set Created at.
     * @param \DateTime $datetime
     * @return \Fenchy\NoticeBundle\Entity\Request
     */
    public function setCreatedAt($datetime = NULL) {
        
        !($datetime instanceof \DateTime) && $datetime = new \DateTime();
        
        $this->created_at = $datetime;
        
        return $this;
    }
    
    /**
     * Get Created at.
     * @return \DateTime
     */
    public function getCreatedAt() {
        
        return $this->created_at;
    }
    
    /**
     * Get About Notice.
     * @return Notice
     */
    public function getAboutNotice() {
        
        return $this->aboutNotice;
    }
    
    /**
     * Set About Notice.
     * @param \Fenchy\NoticeBundle\Entity\Notice $notice
     * @return \Fenchy\NoticeBundle\Entity\Request
     */
    public function setAboutNotice(Notice $notice) {
        
        $this->aboutNotice = $notice;
        
        return $this;
    }
    
    /**
     * Unsets assigned notice. It is needed to remove notice.
     * @return \Fenchy\NoticeBundle\Entity\Request
     */
    public function unsetAboutNotice() {
        
        $this->aboutNotice = NULL;
        
        return $this;
    }
    
    /**
     * Get About User
     * @return User
     */
    public function getAboutUser() {
        
        return $this->aboutUser;
    }
    
    /**
     * Set About User.
     * @param \Fenchy\UserBundle\Entity\User $user
     * @return \Fenchy\NoticeBundle\Entity\Request
     */
    public function setAboutUser(User $user) {
        
        $this->aboutUser = $user;
        
        return $this;
    }
    
    /**
     * Get About UserGroup
     * @return UserGroup
     */
    public function getAboutUserGroup() {
        
        return $this->aboutUserGroup;
    }
    
    /**
     * Set About UserGroup.
     * @param \Fenchy\RegularUserBundle\Entity\UserGroup $usergroup
     * @return \Fenchy\NoticeBundle\Entity\Request
     */
    public function setAboutUserGroup(UserGroup $usergroup) {
        
        $this->aboutUserGroup = $usergroup;
        
        return $this;
    }
    
    /**
     * Get about (user or notice).
     * @return User|Notice|null
     */
    public function getAbout() {
        
        if(NULL !== $this->aboutNotice) return $this->aboutNotice;
        
        if(NULL !== $this->aboutUser) return $this->aboutUser;
        
        return NULL;
    }
    
    /**
     * Returns target user. If $aboutUser is set then it will be returned, 
     * otherwise $aboutNotice owner will be returned.
     * 
     * @return User
     */
    public function findTargetUser() {
        
        if($this->aboutUser) return $this->aboutUser;
        
        return $this->aboutNotice->getUser();
    }
    
    /**
     * Checks whether request is read
     * @return boolean
     */
    public function getIsRead() {
        return $this->is_read;
    }

    /**
     * Sets read state of request
     * @param boolean $is_read
     */
    public function setIsRead($is_read) {
        $this->is_read = $is_read;
    }
    
    /**
     * Checks whether status request is read
     * @return boolean
     */
    public function getIsReadStatus() {
    	return $this->is_read_status;
    }
    
    /**
     * Sets read state of status request
     * @param boolean $is_read_status
     */
    public function setIsReadStatus($is_read_status) {
    	$this->is_read_status = $is_read_status;
    }
    
    /**
     * Gets the title of the listing which is request about
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Sets the title for listing which is request about
     * @param type $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Set piece_spot
     *
     * @param integer $pieceSpot
     * @return Request
     */
    public function setPieceSpot($pieceSpot)
    {
        $this->piece_spot = $pieceSpot;
    
        return $this;
    }

    /**
     * Get piece_spot
     *
     * @return integer 
     */
    public function getPieceSpot()
    {
        return $this->piece_spot;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Request
     */
    public function setPrice($price)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set free
     *
     * @param boolean $free
     * @return Request
     */
    public function setFree($free)
    {
        $this->free = $free;
    
        return $this;
    }

    /**
     * Get free
     *
     * @return boolean 
     */
    public function getFree()
    {
        return $this->free;
    }

    /**
     * Set proposeprice
     *
     * @param float $proposeprice
     * @return Request
     */
    public function setProposeprice($proposeprice)
    {
        $this->proposeprice = $proposeprice;
    
        return $this;
    }

    /**
     * Get proposeprice
     *
     * @return float 
     */
    public function getProposeprice()
    {
        return $this->proposeprice;
    }

    /**
     * Set totalprice
     *
     * @param float $totalprice
     * @return Request
     */
    public function setTotalprice($totalprice)
    {
        $this->totalprice = $totalprice;
    
        return $this;
    }

    /**
     * Get totalprice
     *
     * @return float 
     */
    public function getTotalprice()
    {
        return $this->totalprice;
    }

    /**
     * Set currency
     *
     * @param string $currency
     * @return Request
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    
        return $this;
    }

    /**
     * Get currency
     *
     * @return string 
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set swapMsg
     *
     * @param string $swapMsg
     * @return Request
     */
    public function setSwapMsg($swapMsg)
    {
        $this->swapMsg = $swapMsg;
    
        return $this;
    }

    /**
     * Get swapMsg
     *
     * @return string 
     */
    public function getSwapMsg()
    {
        return $this->swapMsg;
    }    

    /**
     * Set start_time
     *
     * @param \DateTime $startTime
     * @return Request
     */
    public function setStartTime($startTime)
    {
        $this->start_time = $startTime;
    
        return $this;
    }

    /**
     * Get start_time
     *
     * @return \DateTime 
     */
    public function getStartTime()
    {
        return $this->start_time;
    }
    
    /**
     * Set end_time
     *
     * @param \DateTime $endTime
     * @return Request
     */
    public function setEndTime($endTime)
    {
        $this->end_time = $endTime;
    
        return $this;
    }

    /**
     * Get end_time
     *
     * @return \DateTime 
     */
    public function getEndTime()
    {
        return $this->end_time;
    }

    /**
     * Set start_date
     *
     * @param \DateTime $startDate
     * @return Request
     */
    public function setStartDate($startDate)
    {
        $this->start_date = $startDate;
    
        return $this;
    }

    /**
     * Get start_date
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Set end_date
     *
     * @param \DateTime $endDate
     * @return Request
     */
    public function setEndDate($endDate)
    {
        $this->end_date = $endDate;
    
        return $this;
    }

    /**
     * Get end_date
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * Set blue
     *
     * @param boolean $blue
     * @return Request
     */
    public function setBlue($blue)
    {
        $this->blue = $blue;
    
        return $this;
    }

    /**
     * Get blue
     *
     * @return boolean 
     */
    public function getBlue()
    {
        return $this->blue;
    }

    /**
     * Set request_blue
     *
     * @param boolean $requestBlue
     * @return Request
     */
    public function setRequestBlue($requestBlue)
    {
        $this->request_blue = $requestBlue;
    
        return $this;
    }

    /**
     * Get request_blue
     *
     * @return boolean 
     */
    public function getRequestBlue()
    {
        return $this->request_blue;
    }

    /**
     * Set other_review
     *
     * @param boolean $otherReview
     * @return Request
     */
    public function setOtherReview($otherReview)
    {
        $this->other_review = $otherReview;
    
        return $this;
    }

    /**
     * Get other_review
     *
     * @return boolean 
     */
    public function getOtherReview()
    {
        return $this->other_review;
    }

    /**
     * Set my_review
     *
     * @param boolean $myReview
     * @return Request
     */
    public function setMyReview($myReview)
    {
        $this->my_review = $myReview;
    
        return $this;
    }

    /**
     * Get my_review
     *
     * @return boolean 
     */
    public function getMyReview()
    {
        return $this->my_review;
    }

    /**
     * Set aboutNeighborhoodMsg
     *
     * @param Fenchy\RegularUserBundle\Entity\NeighborhoodMsg $aboutNeighborhoodMsg
     * @return Request
     */
    public function setAboutNeighborhoodMsg(\Fenchy\RegularUserBundle\Entity\NeighborhoodMsg $aboutNeighborhoodMsg = null)
    {
        $this->aboutNeighborhoodMsg = $aboutNeighborhoodMsg;
    
        return $this;
    }

    /**
     * Get aboutNeighborhoodMsg
     *
     * @return Fenchy\RegularUserBundle\Entity\NeighborhoodMsg 
     */
    public function getAboutNeighborhoodMsg()
    {
        return $this->aboutNeighborhoodMsg;
    }


    /**
     * Add requestmessage
     *
     * @param Fenchy\NoticeBundle\Entity\RequestMessages $requestmessage
     * @return Request
     */
    public function addRequestmessage(\Fenchy\NoticeBundle\Entity\RequestMessages $requestmessage)
    {
        $this->requestmessage[] = $requestmessage;
    
        return $this;
    }

    /**
     * Remove requestmessage
     *
     * @param Fenchy\NoticeBundle\Entity\RequestMessages $requestmessage
     */
    public function removeRequestmessage(\Fenchy\NoticeBundle\Entity\RequestMessages $requestmessage)
    {
        $this->requestmessage->removeElement($requestmessage);
    }

    /**
     * Get requestmessage
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getRequestmessage()
    {
        return $this->requestmessage;
    }


    /**
     * Set proposedLocation
     *
     * @param string $proposedLocation
     * @return Request
     */
    public function setProposedLocation($proposedLocation)
    {
        $this->proposedLocation = $proposedLocation;
    
        return $this;
    }

    /**
     * Get proposedLocation
     *
     * @return string 
     */
    public function getProposedLocation()
    {
        return $this->proposedLocation;
    }
}