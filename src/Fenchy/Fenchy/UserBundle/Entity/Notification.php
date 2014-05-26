<?php

namespace Fenchy\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Fenchy\UserBundle\Entity\User;

/**
 * @ORM\Table(name="user__notifications")
 * @ORM\Entity(repositoryClass="Fenchy\UserBundle\Entity\NotificationRepository")
 */
class Notification
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
     * @var string $description
     * 
     * @ORM\Column(type="string", length=255)
     */
    private $description;
    
    /**
     * @var type 
     *
     * @ORM\ManyToOne(targetEntity="NotificationGroup", inversedBy="notifications", cascade={"remove"})
     * @ORM\JoinColumn(name="notification_group_id", referencedColumnName="id")
     */
    private $notification_group;

    /**
     * @var ArrayCollection $users
     * 
     * @ORM\ManyToMany(targetEntity="Fenchy\UserBundle\Entity\User", mappedBy="notifications")
     */
    private $users;
    
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }
    
    public function __toString() {
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
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function setDescription($desc)
    {
        $this->description = $desc;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }
    
    /**
     * @param ArrayCollection $users
     */
    public function setUsers(ArrayCollection $users)
    {
        $this->users = $users;
    }
    
    /**
     * @param User $user
     */
    public function addUser(User $user)
    {
        $this->users->add($user);
    }
    
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
    }
    
    /**
     *
     * @return NotificationGroup
     */
    public function getNotificationGroup()
    {
        return $this->notification_group;
    }
    
    /**
     * @param NotificationGroup $group 
     */
    public function setNotificationGroup(NotificationGroup $group)
    {
        $this->notification_group = $group;
    }
    
    
}