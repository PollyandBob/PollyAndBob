<?php

namespace Fenchy\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fenchy\UserBundle\Entity\Unsubscriber
 *
 * @ORM\Table(name="user__unsubscribers")
 * @ORM\Entity(repositoryClass="Fenchy\UserBundle\Entity\UnsubscriberRepository")
 */
class Unsubscriber
{
    /**
     * @var integer $id
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string $hashed_email
     *
     * @ORM\Column(type="string", length=40, unique=true)          
     */
    private $hashed_email;
    
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
     * Set hashed_email
     *
     * @param string $hashedEmail
     * @return Unsubscriber
     */
    public function setHashedEmail($hashedEmail)
    {
        $this->hashed_email = $hashedEmail;
    
        return $this;
    }

    /**
     * Get hashed_email
     *
     * @return string 
     */
    public function getHashedEmail()
    {
        return $this->hashed_email;
    }    

}