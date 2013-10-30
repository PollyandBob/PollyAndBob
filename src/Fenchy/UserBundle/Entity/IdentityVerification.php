<?php

namespace Fenchy\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Fenchy\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Table(name="identity__verification")
* @ORM\Entity(repositoryClass="Fenchy\UserBundle\Entity\IdentityVerificationRepository")
*  
*/
class IdentityVerification
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
	 * @ORM\OneToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="identity")
	 */
	private $user;
	
	/**
	 * @var string $username
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 */	
	private $username;
	
	/**
	 * @var string $status
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 */
	private $status;
	
	/**
	 * @var DateTime
	 *
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $created_at;

	public function __construct()
	{			
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
     * Set username
     *
     * @param string $username
     * @return IdentityVerification
     */
    public function setUsername($username)
    {
        $this->username = $username;
    
        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return IdentityVerification
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return IdentityVerification
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
     * Set user
     *
     * @param Fenchy\UserBundle\Entity\User $user
     * @return IdentityVerification
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