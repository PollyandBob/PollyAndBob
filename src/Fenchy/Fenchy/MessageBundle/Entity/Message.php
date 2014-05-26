<?php

namespace Fenchy\MessageBundle\Entity;

use Doctrine\Tests\DBAL\Types\IntegerTest;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\Common\Collections\ArrayCollection;
use Fenchy\UserBundle\Entity\User;
use Fenchy\NoticeBundle\Entity\Notice;

/**
 * Fenchy\MessageBundle\Entity\Message
 *
 * @ORM\Table(name="message__messages")
 * @ORM\Entity(repositoryClass="Fenchy\MessageBundle\Entity\MessageRepository")
 */
class Message
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank
     */
    private $title;

    /**
     * @var string $content
     *
     * @ORM\Column(name="content", type="text")
     * @Assert\NotBlank
     */
    private $content;

    /**
     * @var boolean $read
     *
     * @ORM\Column(name="read", type="boolean")
     */
    private $read;

    /**
     * @var \DateTime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created_at;

    /**
     * @var \DateTime $sender_deleted_at
     *
     * @ORM\Column(name="sender_deleted_at", type="datetime", nullable=true)
     */
    private $sender_deleted_at;
    
    /**
     * @var \DateTime $receiver_deleted_at
     *
     * @ORM\Column(name="receiver_deleted_at", type="datetime", nullable=true)
     */
    private $receiver_deleted_at;
    
    /**
     * @var boolean $system
     *
     * @ORM\Column(name="system", type="boolean")
     */
    private $system;
    
    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="messages")
     * @ORM\JoinColumn(name="sender_user_id", referencedColumnName="id", nullable=true)
     */
    private $sender;
    
    /**
     * @var UserGroup
     *
     * @ORM\ManyToOne(targetEntity="Fenchy\RegularUserBundle\Entity\UserGroup", inversedBy="messages")
     * @ORM\JoinColumn(name="usergroup_id", referencedColumnName="id", nullable=true)
     */
    private $usergroup;
    
    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="Fenchy\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="receiver_user_id", referencedColumnName="id", nullable=true)
     */
    private $receiver;
    
    /**
     * @var ArrayCollection()
     * 
     * (ManytoOne with JoinTable)
     * @ORM\ManyToMany(targetEntity="Fenchy\UserBundle\Entity\User")
     * @ORM\JoinTable(name="message__about_user",
     *      joinColumns={@ORM\JoinColumn(name="message_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="about_user_id", referencedColumnName="id")}
     *      )
     */
    private $about_user;
    
    /**
     * @var ArrayCollection()
     * 
     * (OnetoMany with JoinTable)
     * @ORM\ManyToMany(targetEntity="Fenchy\NoticeBundle\Entity\Notice")
     * @ORM\JoinTable(name="message__about_notice",
     *      joinColumns={@ORM\JoinColumn(name="message_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="about_notice_id", referencedColumnName="id")}
     *      )
     */
    private $about_notice;
    
    /**
     * @var Message
     * 
     * @ORM\ManyToOne(targetEntity="Message")
     * @ORM\JoinColumn(name="first_message_id", referencedColumnName="id")
     */
    private $first;

    /**
     * @var Message
     * 
     * @ORM\OneToOne(targetEntity="Message")
     * @ORM\JoinColumn(name="prev_message_id", referencedColumnName="id")
     */
    private $prev;
    
    /**
     * @var Message
     * 
     * @ORM\OneToOne(targetEntity="Message")
     * @ORM\JoinColumn(name="next_message_id", referencedColumnName="id")
     */
    private $next;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="fromgroup", type="integer", nullable=true)
     */
    private $fromgroup;
     
     /**
     * @var boolean $chat
     *
     * @ORM\Column(name="chat", type="boolean", nullable=true)
     */
    private $chat = false;

    
    /**
     * Constructor
     */
    public function __construct($receiver = null)
    {
        $this->about_user = new ArrayCollection();
        $this->about_notice = new ArrayCollection();
        $this->read = false;
        $this->receiver = $receiver;
        $this->first = $this;
        $this->system = false;
    }

    /**
     * Cloner
     */
    public function __clone()
    {
        $this->id = null;
        $this->content = null;
        $this->read = false;
        $this->sender = null;
        $this->created_at = null;
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
     * @return Message
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
     * Set content
     *
     * @param string $content
     * @return Message
     */
    public function setContent($content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Message
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
     * Set read
     *
     * @param boolean $read
     * @return Message
     */
    public function setRead($read)
    {
        $this->read = $read;
    
        return $this;
    }

    /**
     * Get read
     *
     * @return boolean 
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * Get read
     *
     * @return boolean 
     */
    public function isRead()
    {
        return $this->read;
    }
    
    /**
     * Get fromgroup
     *
     * @return integer
     */
    public function getFromgroup()
    {
    	return $this->fromgroup;
    }
    
    /**
     * Get fromgroup
     *
     * @return integer
     */
    public function setFromgroup($id)
    {
    	$this->fromgroup = $id;
    	
    	return $this->fromgroup;
    }
    
    /**
     * Get unread
     *
     * @return boolean 
     */
    public function isUnread()
    {
        return !$this->read;
    }
    
    /**
     * Set sender
     *
     * @param Fenchy\UserBundle\Entity\User $sender
     * @return Message
     */
    public function setSender(\Fenchy\UserBundle\Entity\User $sender)
    {
        $this->sender = $sender;
    
        return $this;
    }

    /**
     * Get sender
     *
     * @return Fenchy\UserBundle\Entity\User 
     */
    public function getSender()
    {
        return $this->sender;
    }
    
    /**
     * Set usergroup
     *
     * @param Fenchy\RegularUserBundle\Entity\UserGroup $usergroup
     * @return Message
     */
    public function setUsergroup(\Fenchy\RegularUserBundle\Entity\UserGroup $usergroup)
    {
    	$this->usergroup = $usergroup;
    
    	return $this;
    }
    
    /**
     * Get usergroup
     *
     * @return Fenchy\RegularUserBundle\Entity\UserGroup
     */
    public function getUsergroup()
    {
    	return $this->usergroup;
    }

    /**
     * @return Message This
     */
    public function unsetSender() {
        
        $this->sender = NULL;
        return $this;
    }
    
    /**
     * Set receiver
     *
     * @param Fenchy\UserBundle\Entity\User $receiver
     * @return Message
     */
    public function setReceiver(\Fenchy\UserBundle\Entity\User $receiver)
    {
        $this->receiver = $receiver;
    
        return $this;
    }

    /**
     * Get receiver
     *
     * @return Fenchy\UserBundle\Entity\User 
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @return Message This
     */
    public function unsetReceiver() {
        
        $this->receiver = NULL;
        return $this;
    }
    /**
     * @return Message This
     */
    public function unsetFirstMsg() {
        
        $this->first = NULL;
        return $this;
    }
    
    /**
     * @return Message This
     */
    public function unsetNextMsg() {
        
        $this->next = NULL;
        return $this;
    }
    
    /**
     * @return Message This
     */
    public function unsetPrevMsg() {
        
        $this->prev = NULL;
        return $this;
    }
    
    /**
     * Add about_user
     *
     * @param Fenchy\UserBundle\Entity\User $aboutUser
     * @return Message
     */
    public function addAboutUser(\Fenchy\UserBundle\Entity\User $aboutUser)
    {
        $this->about_user[] = $aboutUser;
    
        return $this;
    }

    /**
     * Remove about_user
     *
     * @param Fenchy\UserBundle\Entity\User $aboutUser
     */
    public function removeAboutUser(\Fenchy\UserBundle\Entity\User $aboutUser)
    {
        $this->about_user->removeElement($aboutUser);
    }

    /**
     * Get about_user
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getAboutUser()
    {
        return $this->about_user;
    }

    /**
     * Add about_notice - unused
     *
     * @param Fenchy\NoticeBundle\Entity\Notice $aboutNotice
     * @return Message
     */
    public function addAboutNotice(\Fenchy\NoticeBundle\Entity\Notice $aboutNotice)
    {
        $this->about_notice[] = $aboutNotice;
    
        return $this;
    }

    /**
     * Remove about_notice - unused
     *
     * @param Fenchy\NoticeBundle\Entity\Notice $aboutNotice
     */
    public function removeAboutNotice(\Fenchy\NoticeBundle\Entity\Notice $aboutNotice)
    {
        $this->about_notice->removeElement($aboutNotice);
    }

    /**
     * Get about_notice
     *
     * @return Fenchy\NoticeBundle\Entity\Notice 
     */
    public function getAboutNotice()
    {
        return $this->about_notice->first();
    }

    /**
     * Get about_notice
     *
     * @return Fenchy\NoticeBundle\Entity\Notice 
     */
    public function getNotice()
    {
        return $this->getAboutNotice();
    }

    /**
     * Sets Notice
     * @param Notice $notice
     * @return Message
     */
    public function setNotice(Notice $aboutNotice)
    {
        $this->about_notice->clear();
        $this->addAboutNotice($aboutNotice);
    
        return $this;
    }
    
    /**
     * Unsets Notice(s)
     * 
     * @return Message This
     * 
     * @author Michał Nowak <mnowak@pgs-soft.com>
     */
    public function unsetNotice() {
        
        $this->about_notice->clear();
        
        return $this;
    }

    public function isRequest()
    {
        return false === $this->about_notice->isEmpty() && $this->getNotice()->getUser() == $this->receiver;
    }

    /**
     * Set first
     *
     * @param Fenchy\MessageBundle\Entity\Message $first
     * @return Message
     */
    public function setFirst(\Fenchy\MessageBundle\Entity\Message $first)
    {
        $this->first = $first->getFirst();
    
        return $this;
    }

    /**
     * Get first
     *
     * @return Fenchy\MessageBundle\Entity\Message 
     */
    public function getFirst()
    {
        return null === $this->first ? $this : $this->first;
    }

    /**
     * Set prev
     *
     * @param Fenchy\MessageBundle\Entity\Message $prev
     * @return Message
     */
    public function setPrev(\Fenchy\MessageBundle\Entity\Message $prev = null)
    {
        $this->prev = $prev;
        if (null === $prev->getNext()) {
            $prev->setNext($this);
        }
        
        return $this;
    }

    /**
     * Get prev
     *
     * @return Fenchy\MessageBundle\Entity\Message 
     */
    public function getPrev()
    {
        return $this->prev;
    }
    
    /**
     * Set next
     *
     * @param Fenchy\MessageBundle\Entity\Message $next
     * @return Message
     */
    public function setNext(\Fenchy\MessageBundle\Entity\Message $next = null)
    {
        $this->next = $next;
        if (null === $next->getPrev()) {
            $next->setPrev($this);
        }
    
        return $this;
    }

    /**
     * Get next
     *
     * @return Fenchy\MessageBundle\Entity\Message 
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * Checks if message is reply or not
     *
     * @return bool
     */
    public function isReply()
    {
        return $this->first !== $this;
    }

    /**
     * Set sender_deleted_at
     *
     * @param \DateTime $senderDeletedAt
     * @return Message
     */
    public function setSenderDeletedAt(\DateTime $senderDeletedAt = null)
    {
        if (null === $senderDeletedAt) {
            $senderDeletedAt = new \DateTime();
        }
        $this->sender_deleted_at = $senderDeletedAt;
    
        return $this;
    }

    /**
     * Set sender_deleted_at to null
     *
     * @return Message
     */
    public function unsetSenderDeletedAt()
    {
        $this->sender_deleted_at = null;
    
        return $this;
    }

    /**
     * Get sender_deleted_at
     *
     * @return \DateTime 
     */
    public function getSenderDeletedAt()
    {
        return $this->sender_deleted_at;
    }

    /**
     * Set receiver_deleted_at
     *
     * @param \DateTime $receiverDeletedAt
     * @return Message
     */
    public function setReceiverDeletedAt(\DateTime $receiverDeletedAt = null)
    {
        if (null === $receiverDeletedAt) {
            $receiverDeletedAt = new \DateTime();
        }
        $this->receiver_deleted_at = $receiverDeletedAt;
    
        return $this;
    }

    /**
     * Set receiver_deleted_at to null
     *
     * @return Message
     */
    public function unsetReceiverDeletedAt()
    {
        $this->receiver_deleted_at = null;
    
        return $this;
    }

    /**
     * Get receiver_deleted_at
     *
     * @return \DateTime 
     */
    public function getReceiverDeletedAt()
    {
        return $this->receiver_deleted_at;
    }

    /**
     * Set system
     *
     * @param boolean $system
     * @return Message
     */
    public function setSystem($system = true)
    {
        $this->system = $system;
    
        return $this;
    }

    /**
     * Get system
     *
     * @return boolean 
     */
    public function getSystem()
    {
        return $this->system;
    }
    
    public function isReplyable() {
        
        return !($this->system || $this->receiver === NULL || $this->sender === NULL || $this->receiver->getId() === NULL || $this->sender->getId() === NULL);
    }
    
    
    /**
     * Set chat
     *
     * @param boolean $chat
     * @return Message
     */
    public function setChat($chat)
    {
        $this->chat = $chat;
    
        return $this;
    }

    /**
     * Get chat
     *
     * @return boolean 
     */
    public function getChat()
    {
        return $this->chat;
    }
}