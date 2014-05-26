<?php

namespace Fenchy\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Fenchy\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Table(name="tell__friend")
* @ORM\Entity(repositoryClass="Fenchy\UserBundle\Entity\TellFriendRepository")
*  
*/
class TellFriend
{	
	/**
	 * @var integer $id
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;
	
	/**
	 * @var User $user;
	 * @ORM\ManyToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="tellfriend")
         * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 */
	private $user;
	
	/**
	 * @var string $username
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 */	
        
        /**
	 * @var string $facebook_id
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 */	
	private $facebook_id;
        
        /**
	 * @var string $facebook_id
	 *
	 * @ORM\Column(type="boolean", nullable=true)
	 *
	 */	
	private $by_email = false;

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
     * Set facebook_id
     *
     * @param string $facebookId
     * @return TellFriend
     */
    public function setFacebookId($facebookId)
    {
        $this->facebook_id = $facebookId;
    
        return $this;
    }

    /**
     * Get facebook_id
     *
     * @return string 
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    /**
     * Set by_email
     *
     * @param boolean $byEmail
     * @return TellFriend
     */
    public function setByEmail($byEmail)
    {
        $this->by_email = $byEmail;
    
        return $this;
    }

    /**
     * Get by_email
     *
     * @return boolean 
     */
    public function getByEmail()
    {
        return $this->by_email;
    }

    /**
     * Set user
     *
     * @param Fenchy\UserBundle\Entity\User $user
     * @return TellFriend
     */
    public function setUser(\Fenchy\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return Fenchy\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
    
}