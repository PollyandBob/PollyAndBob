<?php

namespace Fenchy\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fenchy\UserBundle\Entity\EmailChangeRequest
 *
 * @ORM\Table(name="user__email_change_request")
 * @ORM\Entity(repositoryClass="Fenchy\UserBundle\Entity\EmailChangeRequestRepository")
 */
class EmailChangeRequest
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
     * @var Fenchy\UserBundle\Entity\User
     *
     * @ORM\OneToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="email_change_request")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="requested_email", type="string", length=90, nullable=false)
     */
    private $requested_email;
       
    /**
     * @var DateTime
     * 
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $supplied_at;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="confirmation_token", type="string", length=60, nullable=false)
     */
    private $confirmation_token;
    
    /**
     * Get id
     *
     * @return integer 
     */
     
     
    public function __construct() 
    {
        $this->supplied_at = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param Fenchy\UserBundle\Entity\User $user
     * @return EmailChangeRequest
     */
    public function setUser(\Fenchy\UserBundle\Entity\User $user)
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

    /**
     * Set requested_email
     *
     * @param string $requestedEmail
     * @return EmailChangeRequest
     */
    public function setRequestedEmail($requestedEmail)
    {
        $this->requested_email = $requestedEmail;
    
        return $this;
    }

    /**
     * Get requested_email
     *
     * @return string 
     */
    public function getRequestedEmail()
    {
        return $this->requested_email;
    }

    /**
     * Set supplied_at
     *
     * @param \DateTime $suppliedAt
     * @return EmailChangeRequest
     */
    public function setSuppliedAt($suppliedAt)
    {
        $this->supplied_at = $suppliedAt;
    
        return $this;
    }

    /**
     * Get supplied_at
     *
     * @return \DateTime 
     */
    public function getSuppliedAt()
    {
        return $this->supplied_at;
    }

    /**
     * Set confirmation_token
     *
     * @param string $confirmationToken
     * @return EmailChangeRequest
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmation_token = $confirmationToken;
    
        return $this;
    }

    /**
     * Get confirmation_token
     *
     * @return string 
     */
    public function getConfirmationToken()
    {
        return $this->confirmation_token;
    }
    
    
    public function isNonExpired($ttl) 
    {
        return $this->getSuppliedAt() instanceof \DateTime &&
            $this->getSuppliedAt()->getTimestamp() + $ttl > time();
    }
    
}