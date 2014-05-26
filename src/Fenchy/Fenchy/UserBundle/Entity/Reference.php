<?php

namespace Fenchy\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fenchy\UserBundle\Entity\Reference
 *
 * @ORM\Table(name="user__references")
 * @ORM\Entity(repositoryClass="Fenchy\UserBundle\Entity\ReferenceRepository")
 */
class Reference
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
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="sent_references")
     * @ORM\JoinColumn(name="ref_user_id", referencedColumnName="id", nullable=false)
     */
    private $ref_user;
    
    /**
     * @var User $new_user; 
     * 
     * @ORM\ManyToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="got_references")
     * @ORM\JoinColumn(name="new_user_id", referencedColumnName="id", nullable=true)
     */  
    private $new_user;
    
    /**
     * @var bigint $new_user_fb_id
     *
     * @ORM\Column(type="bigint", unique=true, nullable=true)          
     */    
    private $new_user_fb_id;
    
    /**
     * @var string $new_user_email
     *
     * @ORM\Column(type="string", nullable=true)          
     */    
    private $new_user_email;
        
    /**
     * @var boolean $active
     *
     * @ORM\Column(type="boolean")          
     */      
    private $active = false;
    
    /**
     * @var \DateTime $created_at
     * 
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;
    
    /**
     * @var \DateTime $activated_at
     * 
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $activated_at;
    
    /**
     * @var \DateTime $joined_at
     * 
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $joined_at;
    
    public function __construct() {
        
        $this->created_at = new \DateTime();
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
     * Set ref_user
     *
     * @param User $user
     * @return Reference
     */
    public function setRefUser(User $user)
    {
        $this->ref_user = $user;
    
        return $this;
    }

    /**
     * Get ref_user
     *
     * @return User 
     */
    public function getRefUser()
    {
        return $this->ref_user;
    }

    /**
     * Set new_user
     *
     * @param User $newUser
     * @return Reference
     */
    public function setNewUser(User $newUser)
    {
        $this->joined_at = new \DateTime();
        $this->new_user = $newUser;
    
        return $this;
    }

    /**
     * Get new_user
     *
     * @return User 
     */
    public function getNewUser()
    {
        return $this->new_user;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Reference
     */
    public function setActive($active)
    {
        if($active && !$this->active) {
            $this->activated_at = new \DateTime();
        }
        $this->active = $active;
    
        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set new_user_email
     *
     * @param string $newUserEmail
     * @return Reference
     */
    public function setNewUserEmail($newUserEmail)
    {
        $this->new_user_email = $newUserEmail;
    
        return $this;
    }

    /**
     * Get new_user_email
     *
     * @return string 
     */
    public function getNewUserEmail()
    {
        return $this->new_user_email;
    }

    /**
     * Set new_user_fb_id
     *
     * @param integer $newUserFbId
     * @return Reference
     */
    public function setNewUserFbId($newUserFbId)
    {
        $this->new_user_fb_id = $newUserFbId;
    
        return $this;
    }

    /**
     * Get new_user_fb_id
     *
     * @return integer 
     */
    public function getNewUserFbId()
    {
        return $this->new_user_fb_id;
    }
    
    public function getCreatedAt() {
        
        return $this->created_at;
    }
    
    public function setCreatedAt($date) {
        
        $this->created_at = $date;
        
        return $this;
    }
    
    public function getJoinedAt() {
        
        return $this->joined_at;
    }
    
    public function setJoinedAt($date) {
        
        $this->joined_at = $date;
        
        return $this;
    }
    
    public function getActivatedAt() {
        
        return $this->activated_at;
    }
    
    public function setActivatedAt($date) {
        
        $this->activated_at = $date;
        
        return $this;
    }
}