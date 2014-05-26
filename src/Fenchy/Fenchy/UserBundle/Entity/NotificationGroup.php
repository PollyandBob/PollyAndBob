<?php

namespace Fenchy\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Fenchy\UserBundle\Entity\Notification;
use Fenchy\UserBundle\Entity\NotificationInterval;

/**
 * @ORM\Table(name="user__notification_groups")
 * @ORM\Entity(repositoryClass="Fenchy\UserBundle\Entity\NotificationGroupRepository")
 */
class NotificationGroup
{
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    
    /**
     * @var string $name
     * 
     * @ORM\Column(type="string", length=255)
     */
    private $name;
    /**
     *
     * @var ArrayCollection $notifications
     * 
     * @ORM\OneToMany(targetEntity="Notification", mappedBy="notification_group", cascade={"persist"})
     */
    private $notifications;
    
    /**
     *
     * @var ArrayCollection $notification_group_intervals
     * 
     * @ORM\OneToMany(targetEntity="NotificationGroupInterval", mappedBy="notification_group", cascade={"remove"})
     */
    private $notification_group_intervals;
    
    public function __construct()
    {
        $this->notifications = new ArrayCollection();
        $this->notification_group_intervals = new ArrayCollection();
    }
    
    public function __toString() 
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return ArrayCollection
     */
    public function getNotifications()
    {
        return $this->notifications;
    }
    
    /**
     * @param ArrayCollection $notifications 
     */
    public function setNotifications(ArrayCollection $notifications)
    {
        $this->notifications = $notifications;
    }
    
    /**
     * @param Notification $notification 
     */
    public function addNotification(Notification $notification)
    {
        $this->notifications->add($notification);
    }
    
    /**
     * @param Notification $notification 
     */
    public function removeNotification(Notification $notification)
    {
        $this->notifications->removeElement($notification);
    }
    
    public function getNotificationGroupIntervals()
    {
        return $this->notification_group_intervals;
    }
    
    public function setNotificationGroupIntervals(ArrayCollection $intervals)
    {
        $this->notification_group_intervals = $intervals;
    }
    
    public function addNotificationGroupInterval(NotificationGroupInterval $interval)
    {
        $this->notification_group_intervals->add($interval);
        
        return $this;
    }
    
    public function removeNotificationGroupInterval(NotificationGroupInterval $interval)
    {
        $this->notification_group_intervals->removeElement($interval);
    }
}