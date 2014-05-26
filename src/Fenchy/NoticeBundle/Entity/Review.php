<?php

namespace Fenchy\NoticeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

use Fenchy\UserBundle\Entity\User,
    Fenchy\UtilBundle\Entity\Sticker;

/**
 * @ORM\Table(name="notice__reviews")
 * @ORM\Entity(repositoryClass="Fenchy\NoticeBundle\Entity\ReviewRepository")
 */
class Review
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
     * @ORM\Column(type="integer", nullable=false, options={"default":"1"})
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
     * @ORM\ManyToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="ownReviews")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
     */
    private $author;
    
    /**
     * @var Notice
     * 
     * @ORM\ManyToOne(targetEntity="Fenchy\NoticeBundle\Entity\Notice", inversedBy="reviews")
     * @ORM\JoinColumn(name="notice_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $aboutNotice;
    
    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="reviews")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $aboutUser;
    
    /**
     * @var ArrayCollection $stickers
     * 
     * @ORM\OneToMany(targetEntity="Fenchy\UtilBundle\Entity\Sticker", mappedBy="review", cascade={"remove"})
     * @ORM\OrderBy({"created_at"="DESC"})
     */
    private $stickers;
    
    /**
     *
     * @var boolean $is_read
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $is_read = false;
    
    /**
     *
     * @var boolean $is_read_status
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_read_status = false;
    
	
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
     * @return \Fenchy\NoticeBundle\Entity\Review
     * @throws Exception
     */
    public function setType($type = self::TYPE_POSITIVE) {
        
//         if(!array_key_exists($type, self::$typeMap)) {
//             throw new \Exception('Invalid Review type: '.$type);
//         }
        
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
     * @return \Fenchy\NoticeBundle\Entity\Review
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
     * @return \Fenchy\NoticeBundle\Entity\Review
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
     * @return \Fenchy\NoticeBundle\Entity\Review
     */
    public function setAboutNotice(Notice $notice) {
        
        $this->aboutNotice = $notice;
        
        return $this;
    }
    
    /**
     * Unsets assigned notice. It is needed to remove notice.
     * @return \Fenchy\NoticeBundle\Entity\Review
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
     * @return \Fenchy\NoticeBundle\Entity\Review
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
     * @return \Fenchy\NoticeBundle\Entity\Review
     */
    public function setStickers(ArrayCollection $stickers) {
        
        $this->stickers = $stickers;
        
        return $this;
    }
    
    /**
     * Add Sticker
     * @param \Fenchy\UtilBundle\Entity\Sticker $sticker
     * @return \Fenchy\NoticeBundle\Entity\Review
     */
    public function addSticker(Sticker $sticker) {
        
        $this->stickers->add($sticker);
        
        return $this;
    }
    
    /**
     * Remove Sticker
     * @param \Fenchy\UtilBundle\Entity\Sticker $sticker
     * @return \Fenchy\NoticeBundle\Entity\Review
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
     * Checks whether review is read
     * @return boolean
     */
    public function getIsRead() {
        return $this->is_read;
    }

    /**
     * Sets read state of review
     * @param boolean $is_read
     */
    public function setIsRead($is_read) {
        $this->is_read = $is_read;
    }
    
    /**
     * Gets the title of the listing which is review about
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Sets the title for listing which is review about
     * @param type $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }





    /**
     * Set is_read_status
     *
     * @param boolean $isReadStatus
     * @return Review
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
}