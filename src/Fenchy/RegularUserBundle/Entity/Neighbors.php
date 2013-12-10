<?php

namespace Fenchy\RegularUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Fenchy\UserBundle\Entity\User;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\GalleryBundle\Entity\Gallery;

use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Table(name="user__neighbors")
 * @ORM\Entity(repositoryClass="Fenchy\RegularUserBundle\Entity\NeighborsRepository")
 */
class Neighbors
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;


    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="logged")
     * @ORM\JoinColumn(name="current_id", referencedColumnName="id", nullable=false)
     */
    private $current;
    
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="myNeighbor")
     * @ORM\JoinColumn(name="neighbor_id", referencedColumnName="id", nullable=true)
     */
    private $neighbor;
    
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
    
    /**
     * Set Current.
     * @param \Fenchy\UserBundle\Entity\User $user
     * @return \Fenchy\RegularUserBundle\Entity\Neighbors
     */
    public function setCurrent(User $user) {
    
    	$this->current = $user;
    
    	return $this;
    }
    
    /**
     * Get Current.
     * @return \Fenchy\UserBundle\Entity\User
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
     * @return \Fenchy\RegularUserBundle\Entity\Neighbors
     */
    public function setNeighbor(User $user) {
    
    	$this->neighbor = $user;
    
    	return $this;
    }
    

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Neighbors
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