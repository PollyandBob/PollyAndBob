<?php

namespace Fenchy\RegularUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="share__holder")
 * @ORM\Entity(repositoryClass="Fenchy\RegularUserBundle\Entity\ShareholderRepository")
 */
class Shareholder
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
     * @var string $firstname
     * 
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $firstname;
    
    /**
     * @var string $surname
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $surname;
    
    /**
     * @var string $street
     *
     * @ORM\Column(type="string", length=500, nullable=true)
     * 
     */
    private $street;
    
    /**
     * @var string $city
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $city;
    
    /**
     * @var string $postalcode
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $postalcode;
    
    /**
     * @var string $country
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $country;    
       
    /**
     * @var $birthday
     * 
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthday;
    
    /**
     * @var integer $email
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    private $email;
    
    
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Min(1)
     * @Assert\Max(1000)
     * @Assert\Range( min = 1)
     * @Assert\Range( max = 1000)
     */
    private $share;
    
    /**
     * @var decimal
     * @ORM\Column(type="decimal", scale=2)
     * @Assert\NotBlank
     * @Assert\Range( min = 0)
     */
    private $price = 50;
    
    /**
     * @var decimal
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    private $totalamount;
    
    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $terms;
  

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
     * Set firstname
     *
     * @param string $firstname
     * @return Shareholder
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    
        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set surname
     *
     * @param string $surname
     * @return Shareholder
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    
        return $this;
    }

    /**
     * Get surname
     *
     * @return string 
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Shareholder
     */
    public function setCity($city)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set postalcode
     *
     * @param string $postalcode
     * @return Shareholder
     */
    public function setPostalcode($postalcode)
    {
        $this->postalcode = $postalcode;
    
        return $this;
    }

    /**
     * Get postalcode
     *
     * @return string 
     */
    public function getPostalcode()
    {
        return $this->postalcode;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Shareholder
     */
    public function setCountry($country)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }
   
    /**
     * Set email
     *
     * @param string $email
     * @return Shareholder
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }
   
    /**
     * Set price
     *
     * @param float $price
     * @return Shareholder
     */
    public function setPrice($price)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set totalamount
     *
     * @param float $totalamount
     * @return Shareholder
     */
    public function setTotalamount($totalamount)
    {
        $this->totalamount = $totalamount;
    
        return $this;
    }

    /**
     * Get totalamount
     *
     * @return float 
     */
    public function getTotalamount()
    {
        return $this->totalamount;
    }

    /**
     * Set terms
     *
     * @param boolean $terms
     * @return Shareholder
     */
    public function setTerms($terms)
    {
        $this->terms = $terms;
    
        return $this;
    }

    /**
     * Get terms
     *
     * @return boolean 
     */
    public function getTerms()
    {
        return $this->terms;
    }

    /**
     * Set share
     *
     * @param integer $share
     * @return Shareholder
     */
    public function setShare($share)
    {
        $this->share = $share;
    
        return $this;
    }

    /**
     * Get share
     *
     * @return integer 
     */
    public function getShare()
    {
        return $this->share;
    }

    /**
     * Set street
     *
     * @param string $street
     * @return Shareholder
     */
    public function setStreet($street)
    {
        $this->street = $street;
    
        return $this;
    }

    /**
     * Get street
     *
     * @return string 
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return Shareholder
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    
        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }
}