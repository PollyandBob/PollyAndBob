<?php

namespace Fenchy\NoticeBundle\Entity;

use Fenchy\RegularUserBundle\Entity\UserGroup;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

use Fenchy\UserBundle\Entity\User,
    Fenchy\UtilBundle\Entity\Sticker;

/**
 * @ORM\Table(name="notice__comments")
 * @ORM\Entity(repositoryClass="Fenchy\NoticeBundle\Entity\CommentRepository")
 */
class Comment
{
    const TYPE_POSITIVE      = 1;
    const TYPE_NEGATIVE      = 0;
    
    public static $typeMap = array(
        self::TYPE_POSITIVE => 'positive',
        self::TYPE_NEGATIVE => 'negative',
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
     * @ORM\Column(type="string", length=1000, nullable=false)
     * @Assert\NotBlank
     */
    private $text;
    
    /**
     * 0 - negative; 1 - positive
     * @var integer
     * @ORM\Column(type="integer", nullable=false)
     */
    private $type;
    
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;
    
    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="ownComments")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
     */
    private $author;
    
    /**
     * @var Notice
     * 
     * @ORM\ManyToOne(targetEntity="Fenchy\NoticeBundle\Entity\Notice", inversedBy="comments")
     * @ORM\JoinColumn(name="notice_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $aboutNotice;
    
    /**
     * @var UserGroup
     *
     * @ORM\ManyToOne(targetEntity="Fenchy\RegularUserBundle\Entity\UserGroup", inversedBy="comments")
     * @ORM\JoinColumn(name="usergroup_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $aboutUserGroup;
    
    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $aboutUser;
    
    /**
     * @var ArrayCollection $stickers
     * 
     * @ORM\OneToMany(targetEntity="Fenchy\UtilBundle\Entity\Sticker", mappedBy="comment", cascade={"remove"})
     * @ORM\OrderBy({"created_at"="DESC"})
     */
    private $stickers;
    
    /**
     *
     * @var boolean $is_read
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_read = false;
	
    /**
     *
     * @var boolean $is_read_status
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
    
    public function __construct() {
        
        $this->type = self::TYPE_POSITIVE;
        $this->created_at = new \DateTime();
        $this->stickers = new ArrayCollection();
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
     * Set Type. Throws exception if $type is not valid type.
     * @param Integer $type
     * @return \Fenchy\NoticeBundle\Entity\Comment
     * @throws Exception
     */
    public function setType($type) {
        
        if(!array_key_exists($type, self::$typeMap)) {
            throw new \Exception('Invalid Comment type: '.$type);
        }
        
        $this->type = $type;
        
        return $this;
    }
    
    /**
     * Get Type.
     * @return Integer
     */
    public function getType() {
        
        return $this->type;
    }
    
    /**
     * Returns string representation of type.
     * If $type param has not been set then method returns name of self type.
     * @param Integer $type
     * @return string
     */
    public function getTypeName($type = NULL) {
        
        !$type && $type = $this->type;
        
        if(!array_key_exists($type, self::$typeMap)) {
            return '-';
        }
        
        return self::$typeMap[$type];
    }
    
    /**
     * Set Author.
     * @param \Fenchy\NoticeBundle\Entity\User $user
     * @return \Fenchy\NoticeBundle\Entity\Comment
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
     * @return \Fenchy\NoticeBundle\Entity\Comment
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
     * @return \Fenchy\NoticeBundle\Entity\Comment
     */
    public function setAboutNotice(Notice $notice) {
        
        $this->aboutNotice = $notice;
        
        return $this;
    }
    
    /**
     * Get About UserGroup.
     * @return UserGroup
     */
    public function getAboutUserGroup() {
    
    	return $this->aboutUserGroup;
    }
    
    /**
     * Set About UserGroup.
     * @param \Fenchy\RegularUserBundle\Entity\UserGroup $usergroup
     * @return \Fenchy\NoticeBundle\Entity\Comment
     */
    public function setAboutUserGroup(UserGroup $usergroup) {
    
    	$this->aboutUserGroup = $usergroup;
    
    	return $this;
    }
    
    /**
     * Unsets assigned notice. It is needed to remove notice.
     * @return \Fenchy\NoticeBundle\Entity\Comment
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
     * @return \Fenchy\NoticeBundle\Entity\Comment
     */
    public function setAboutUser(User $user) {
        
        $this->aboutUser = $user;
        
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
     * Set Stickers
     * @param \Fenchy\NoticeBundle\Entity\ArrayCollection $stickers
     * @return \Fenchy\NoticeBundle\Entity\Comment
     */
    public function setStickers(ArrayCollection $stickers) {
        
        $this->stickers = $stickers;
        
        return $this;
    }
    
    /**
     * Add Sticker
     * @param \Fenchy\UtilBundle\Entity\Sticker $sticker
     * @return \Fenchy\NoticeBundle\Entity\Comment
     */
    public function addSticker(Sticker $sticker) {
        
        $this->stickers->add($sticker);
        
        return $this;
    }
    
    /**
     * Remove Sticker
     * @param \Fenchy\UtilBundle\Entity\Sticker $sticker
     * @return \Fenchy\NoticeBundle\Entity\Comment
     */
    public function removeSticker(Sticker $sticker) {
        
        $this->stickers->removeElement($sticker);
        
        return $this;
    }
    
    /**
     * Get Stickers
     * @return ArrayCollection
     */
    public function getStickers() {
        
        return $this->stickers;
    }
    
    /**
     * Checks whether comment is read
     * @return boolean
     */
    public function getIsRead() {
        return $this->is_read;
    }

    /**
     * Sets read state of comment
     * @param boolean $is_read
     */
    public function setIsRead($is_read) {
        $this->is_read = $is_read;
    }
    
    /**
     * Gets the title of the listing which is comment about
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Sets the title for listing which is comment about
     * @param type $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }





    /**
     * Set is_read_status
     *
     * @param boolean $isReadStatus
     * @return Comment
     */
    public function setIsReadStatus($isReadStatus)
    {
        $this->is_read_status = $isReadStatus;
    
        return $this;
    }

    /**
     * Get is_read_status
     *
     * @return boolean 
     */
    public function getIsReadStatus()
    {
        return $this->is_read_status;
    }

    /**
     * Set blue
     *
     * @param boolean $blue
     * @return Comment
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
     * @return Comment
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
}