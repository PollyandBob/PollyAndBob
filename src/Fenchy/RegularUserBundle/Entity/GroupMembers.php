<?php

namespace Fenchy\RegularUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Fenchy\UserBundle\Entity\User;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\GalleryBundle\Entity\Gallery;

use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Table(name="usergroup__members")
 * @ORM\Entity(repositoryClass="Fenchy\RegularUserBundle\Entity\GroupMembersRepository")
 */
class GroupMembers
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;


    /**
     * @var UserGroup
     *
     * @ORM\ManyToOne(targetEntity="Fenchy\RegularUserBundle\Entity\UserGroup", inversedBy="usergroup")
     * @ORM\JoinColumn(name="usergroup_id", referencedColumnName="id", nullable=false)
     */
    private $current;
    
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="members")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id", nullable=true)
     */
    private $neighbor;
    
    /**
     * @ORM\Column(name="admin" , type="integer", nullable=true)
     * 
     */
    public $admin;
    
    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;
    
    /**
     * Constructor
     */
    public function __construct()
    {
    	$this->admin = false;
    	$this->created_at   = new \DateTime();
    }
    
    /**
     * @return integer
     */
    public function getId() {
    
    	return $this->id;
    }
    
    /**
     * @param integer $id
     * @return Neighbors
     */
    public function setId($id) {
    
    	$this->id = $id;
    
    	return $this;
    }
    
    public function setAdmin($true)
    {
    	return $this->admin = $true;	
    }
    
    public function getAdmin()
    {
    	return $this->admin;
    }
    
    /**
     * Set Current.
     * @param \Fenchy\RegularUserBundle\Entity\UserGroup $usergroup
     * @return \Fenchy\RegularUserBundle\Entity\GroupMembers
     */
    public function setCurrent(UserGroup $usergroup) {
    
    	$this->current = $usergroup;
    
    	return $this;
    }
    
    /**
     * Get Current.
     * @return \Fenchy\RegularUserBundle\Entity\UserGroup
     */
    public function getCurrent() {
    
    	return $this->current;
    }
    
    /**
     * Get Neighbor
     * @return User
     */
    public function getNeighbor() {
    
    	return $this->neighbor;
    }
    
    /**
     * Set Neighbor.
     * @param \Fenchy\UserBundle\Entity\User $user
     * @return \Fenchy\RegularUserBundle\Entity\GroupMembers
     */
    public function setNeighbor(User $user) {
    
    	$this->neighbor = $user;
    
    	return $this;
    }
    

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return GroupMembers
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
}