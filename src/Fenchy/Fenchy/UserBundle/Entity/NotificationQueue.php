<?php

namespace Fenchy\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fenchy\UserBundle\Entity\NotificationQueue
 *
 * Represenst single queue entry in notification queue. When user set notifications to daily
 * they are not being sent on applicable events but put to queue. Queue is processed
 * by separate command run from cron.
 * 
 * @ORM\Table(name="user__notification_queue")
 * @ORM\Entity(repositoryClass="Fenchy\UserBundle\Entity\NotificationQueueRepository")
 */
class NotificationQueue
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
     * @var DateTime determines the moment after which notification can be sent
     * @ORM\Column(name="send_after", type="datetime", nullable=false)
     */
    protected $send_after;
    
    /**
     * @var string
     * @ORM\Column(name="from_address", type="string", length=90, nullable=false)
     */
    protected $from_address;
    
    /**
     * @var string
     * @ORM\Column(name="from_name", type="string", length=60, nullable=false)
     */
    protected $from_name;
    
    /**
     * @var string
     * @ORM\Column(name="to_address", type="string", length=90, nullable=false)
     */
    protected $to_address;
    
    /**
     * @var string
     * @ORM\Column(name="subject", type="string", length=90, nullable=false)
     */
    protected $subject;
    
    /**
     * @var string
     * @ORM\Column(name="body_html", type="text", nullable=false)
     */
    protected $body_html;
    
    /**
     * @var string
     * @ORM\Column(name="body_plain", type="text", nullable=false) 
     */
    protected $body_plain;

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
     * Set send_after
     *
     * @param \DateTime $sendAfter
     * @return NotificationQueue
     */
    public function setSendAfter($sendAfter)
    {
        $this->send_after = $sendAfter;
    
        return $this;
    }

    /**
     * Get send_after
     *
     * @return \DateTime 
     */
    public function getSendAfter()
    {
        return $this->send_after;
    }

    /**
     * Set from_address
     *
     * @param string $fromAddress
     * @return NotificationQueue
     */
    public function setFromAddress($fromAddress)
    {
        $this->from_address = $fromAddress;
    
        return $this;
    }

    /**
     * Get from_address
     *
     * @return string 
     */
    public function getFromAddress()
    {
        return $this->from_address;
    }

    /**
     * Set from_name
     *
     * @param string $fromName
     * @return NotificationQueue
     */
    public function setFromName($fromName)
    {
        $this->from_name = $fromName;
    
        return $this;
    }

    /**
     * Get from_name
     *
     * @return string 
     */
    public function getFromName()
    {
        return $this->from_name;
    }

    /**
     * Set to_address
     *
     * @param string $toAddress
     * @return NotificationQueue
     */
    public function setToAddress($toAddress)
    {
        $this->to_address = $toAddress;
    
        return $this;
    }

    /**
     * Get to_address
     *
     * @return string 
     */
    public function getToAddress()
    {
        return $this->to_address;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return NotificationQueue
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    
        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set body_html
     *
     * @param string $bodyHtml
     * @return NotificationQueue
     */
    public function setBodyHtml($bodyHtml)
    {
        $this->body_html = $bodyHtml;
    
        return $this;
    }

    /**
     * Get body_html
     *
     * @return string 
     */
    public function getBodyHtml()
    {
        return $this->body_html;
    }

    /**
     * Set body_plain
     *
     * @param string $bodyPlain
     * @return NotificationQueue
     */
    public function setBodyPlain($bodyPlain)
    {
        $this->body_plain = $bodyPlain;
    
        return $this;
    }

    /**
     * Get body_plain
     *
     * @return string 
     */
    public function getBodyPlain()
    {
        return $this->body_plain;
    }
}