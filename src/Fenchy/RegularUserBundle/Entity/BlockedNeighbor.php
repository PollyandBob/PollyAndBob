<?php

namespace Fenchy\RegularUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Fenchy\UserBundle\Entity\User;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\GalleryBundle\Entity\Gallery;

use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Table(name="blocked__neighbors")
 * @ORM\Entity(repositoryClass="Fenchy\RegularUserBundle\Entity\BlockedNeighborRepository")
 */
class BlockedNeighbor
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
     * @ORM\ManyToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="blockeruser")
     * @ORM\JoinColumn(name="current_id", referencedColumnName="id", nullable=false)
     */
    private $blocker;
    
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="blockeduser")
     * @ORM\JoinColumn(name="neighbor_id", referencedColumnName="id", nullable=true)
     */
    private $blocked;
    
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return BlockedNeighbor
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
     * Set blocker
     *
     * @param Fenchy\UserBundle\Entity\User $blocker
     * @return BlockedNeighbor
     */
    public function setBlocker(\Fenchy\UserBundle\Entity\User $blocker)
    {
        $this->blocker = $blocker;
    
        return $this;
    }

    /**
     * Get blocker
     *
     * @return Fenchy\UserBundle\Entity\User 
     */
    public function getBlocker()
    {
        return $this->blocker;
    }

    /**
     * Set blocked
     *
     * @param Fenchy\UserBundle\Entity\User $blocked
     * @return BlockedNeighbor
     */
    public function setBlocked(\Fenchy\UserBundle\Entity\User $blocked = null)
    {
        $this->blocked = $blocked;
    
        return $this;
    }

    /**
     * Get blocked
     *
     * @return Fenchy\UserBundle\Entity\User 
     */
    public function getBlocked()
    {
        return $this->blocked;
    }
}