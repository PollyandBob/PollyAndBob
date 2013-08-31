<?php

namespace Fenchy\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Fenchy\UserBundle\Entity\User;
use Fenchy\UserBundle\Entity\Notification;

/**
 * @ORM\Table(name="user__notification_group_intervals")
 * @ORM\Entity(repositoryClass="Fenchy\UserBundle\Entity\NotificationGroupIntervalRepository")
 */
class NotificationGroupInterval
{
    const INTERVAL_NEVER = 0;
    const INTERVAL_IMMEDIATELY = 1;
    const INTERVAL_DAILY = 2;
    
    public static $intervalsMap = array(
        
        self::INTERVAL_NEVER        => 'never',
        self::INTERVAL_IMMEDIATELY  => 'immediately',
        self::INTERVAL_DAILY        => 'daily'
    );
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    
    /**
     * @var integer $interval
     * 
     * @ORM\Column(type="integer")
     */
    private $interval;
    
    
    /**
     *
     * @var type 
     *
     * @ORM\ManyToOne(targetEntity="NotificationGroup", inversedBy="notification_group_intervals", cascade={"persist"})
     * @ORM\JoinColumn(name="notification_group_id", referencedColumnName="id")
     */
    private $notification_group;
    
    /**
     * @var User $user
     * 
     * @ORM\ManyToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="notification_group_intervals")
     */
    private $user;
    
    
    public function __construct() { }
    
    public function __toString() 
    {
        return $this->notification_group->__toString();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }
    
    public function getInterval()
    {
        return $this->interval;
    }
    
    public function setInterval($interval)
    {
        $this->interval = $interval;
        
        return $this;
    }
    
    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    public function setUser(User $user)
    {
        $this->user = $user->addNotificationGroupInterval($this);
        
        return $this;
    }
    
    /**
     * @return NotificationGroup
     */
    public function getNotificationGroup()
    {
        return $this->notification_group;
    }
    
    /**
     * @param ArrayCollection $notifications 
     */
    public function setNotificationGroup(NotificationGroup $group)
    {
        $this->notification_group = $group->addNotificationGroupInterval($this);
        
        return $this;
    }
    
}