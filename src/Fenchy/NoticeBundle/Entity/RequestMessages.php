<?php

namespace Fenchy\NoticeBundle\Entity;

use Fenchy\RegularUserBundle\Entity\UserGroup;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

use Fenchy\UserBundle\Entity\User,
    Fenchy\UtilBundle\Entity\Sticker;

/**
 * @ORM\Table(name="request__messages")
 * @ORM\Entity(repositoryClass="Fenchy\NoticeBundle\Entity\RequestMessagesRepository")
 */
class RequestMessages
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
     * @ORM\Column(type="string", length=255, nullable=true)
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
     * @ORM\ManyToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="ownRequestMessage")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
     */
    private $author;
    
    /**
     * @var Notice
     * 
     * @ORM\ManyToOne(targetEntity="Fenchy\NoticeBundle\Entity\Notice", inversedBy="requestmessage")
     * @ORM\JoinColumn(name="notice_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $aboutNotice;
    
    /**
     * @var UserGroup
     *
     * @ORM\ManyToOne(targetEntity="Fenchy\RegularUserBundle\Entity\UserGroup", inversedBy="requestmessage")
     * @ORM\JoinColumn(name="usergroup_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $aboutUserGroup;
    
     /**
     * @var request
     *
     * @ORM\ManyToOne(targetEntity="Fenchy\NoticeBundle\Entity\Request", inversedBy="requestmessage")
     * @ORM\JoinColumn(name="request_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $request;
    
    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="requestmessage")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $aboutUser;
    
    
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
    }
    
    public function __toString() {
        return $this->text;
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
     * Set title
     *
     * @param string $title
     * @return RequestMessages
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return RequestMessages
     */
    public function setText($text)
    {
        $this->text = $text;
    
        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return RequestMessages
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return RequestMessages
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
     * Set is_read
     *
     * @param boolean $isRead
     * @return RequestMessages
     */
    public function setIsRead($isRead)
    {
        $this->is_read = $isRead;
    
        return $this;
    }

    /**
     * Get is_read
     *
     * @return boolean 
     */
    public function getIsRead()
    {
        return $this->is_read;
    }

    /**
     * Set author
     *
     * @param Fenchy\UserBundle\Entity\User $author
     * @return RequestMessages
     */
    public function setAuthor(\Fenchy\UserBundle\Entity\User $author)
    {
        $this->author = $author;
    
        return $this;
    }

    /**
     * Get author
     *
     * @return Fenchy\UserBundle\Entity\User 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set aboutNotice
     *
     * @param Fenchy\NoticeBundle\Entity\Notice $aboutNotice
     * @return RequestMessages
     */
    public function setAboutNotice(\Fenchy\NoticeBundle\Entity\Notice $aboutNotice = null)
    {
        $this->aboutNotice = $aboutNotice;
    
        return $this;
    }

    /**
     * Get aboutNotice
     *
     * @return Fenchy\NoticeBundle\Entity\Notice 
     */
    public function getAboutNotice()
    {
        return $this->aboutNotice;
    }

    /**
     * Set aboutUserGroup
     *
     * @param Fenchy\RegularUserBundle\Entity\UserGroup $aboutUserGroup
     * @return RequestMessages
     */
    public function setAboutUserGroup(\Fenchy\RegularUserBundle\Entity\UserGroup $aboutUserGroup = null)
    {
        $this->aboutUserGroup = $aboutUserGroup;
    
        return $this;
    }

    /**
     * Get aboutUserGroup
     *
     * @return Fenchy\RegularUserBundle\Entity\UserGroup 
     */
    public function getAboutUserGroup()
    {
        return $this->aboutUserGroup;
    }

    /**
     * Set aboutUser
     *
     * @param Fenchy\UserBundle\Entity\User $aboutUser
     * @return RequestMessages
     */
    public function setAboutUser(\Fenchy\UserBundle\Entity\User $aboutUser = null)
    {
        $this->aboutUser = $aboutUser;
    
        return $this;
    }

    /**
     * Get aboutUser
     *
     * @return Fenchy\UserBundle\Entity\User 
     */
    public function getAboutUser()
    {
        return $this->aboutUser;
    }

    /**
     * Set request
     *
     * @param Fenchy\NoticeBundle\Entity\Request $request
     * @return RequestMessages
     */
    public function setRequest(\Fenchy\NoticeBundle\Entity\Request $request = null)
    {
        $this->request = $request;
    
        return $this;
    }

    /**
     * Get request
     *
     * @return Fenchy\NoticeBundle\Entity\Request 
     */
    public function getRequest()
    {
        return $this->request;
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