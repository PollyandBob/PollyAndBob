<?php

namespace Fenchy\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Fenchy\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="payment__setting")
 * @ORM\Entity(repositoryClass="Fenchy\UserBundle\Entity\PaymentRepository")
 *
 */
class Payment
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
	 * @ORM\OneToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="payment_id")
	 */
	private $user;

	/**
	 * @var string $account_holder
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 */
	private $account_holder;

	/**
	 * @var string $account_no
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 */
	private $account_no;

	/**
	 * @var string $bank_code
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 */
	private $bank_code;

	/**
	 * @var string $paypal_email
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 */
	private $paypal_email;
	
	/**
	 * @var string $card_type
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 */
	private $card_type;
	
	/**
	 * @var string $card_no
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 */
	private $card_no;
	
	/**
	 * @var string $cvv_code
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 */
	private $cvv_code;
	
	/**
	 * @var string $card_holder
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 */
	private $card_holder;
	
	/**
	 * @var string $end_month
	 *
	 * @ORM\Column(type="string", length=4, nullable=true)
	 *
	 */
	private $end_month;
	
	/**
	 * @var string $end_year
	 *
	 * @ORM\Column(type="string", length=6, nullable=true)
	 *
	 */
	private $end_year;
	
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
	 * @var boolean
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $agreed = false;
	

	/**
	 * @var string $type
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $type;
	
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
	 * @return LocationVerification
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
	 * @return Payment
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

    /**
     * Set account_holder
     *
     * @param string $accountHolder
     * @return Payment
     */
    public function setAccountHolder($accountHolder)
    {
        $this->account_holder = $accountHolder;
    
        return $this;
    }

    /**
     * Get account_holder
     *
     * @return string 
     */
    public function getAccountHolder()
    {
        return $this->account_holder;
    }

    /**
     * Set account_no
     *
     * @param string $accountNo
     * @return Payment
     */
    public function setAccountNo($accountNo)
    {
        $this->account_no = $accountNo;
    
        return $this;
    }

    /**
     * Get account_no
     *
     * @return string 
     */
    public function getAccountNo()
    {
        return $this->account_no;
    }

    /**
     * Set bank_code
     *
     * @param string $bankCode
     * @return Payment
     */
    public function setBankCode($bankCode)
    {
        $this->bank_code = $bankCode;
    
        return $this;
    }

    /**
     * Get bank_code
     *
     * @return string 
     */
    public function getBankCode()
    {
        return $this->bank_code;
    }

    /**
     * Set paypal_email
     *
     * @param string $paypalEmail
     * @return Payment
     */
    public function setPaypalEmail($paypalEmail)
    {
        $this->paypal_email = $paypalEmail;
    
        return $this;
    }

    /**
     * Get paypal_email
     *
     * @return string 
     */
    public function getPaypalEmail()
    {
        return $this->paypal_email;
    }

    /**
     * Set card_type
     *
     * @param string $cardType
     * @return Payment
     */
    public function setCardType($cardType)
    {
        $this->card_type = $cardType;
    
        return $this;
    }

    /**
     * Get card_type
     *
     * @return string 
     */
    public function getCardType()
    {
        return $this->card_type;
    }

    /**
     * Set card_no
     *
     * @param string $cardNo
     * @return Payment
     */
    public function setCardNo($cardNo)
    {
        $this->card_no = $cardNo;
    
        return $this;
    }

    /**
     * Get card_no
     *
     * @return string 
     */
    public function getCardNo()
    {
        return $this->card_no;
    }

    /**
     * Set card_holder
     *
     * @param string $cardHolder
     * @return Payment
     */
    public function setCardHolder($cardHolder)
    {
        $this->card_holder = $cardHolder;
    
        return $this;
    }

    /**
     * Get card_holder
     *
     * @return string 
     */
    public function getCardHolder()
    {
        return $this->card_holder;
    }

    /**
     * Set end_month
     *
     * @param string $endMonth
     * @return Payment
     */
    public function setEndMonth($endMonth)
    {
        $this->end_month = $endMonth;
    
        return $this;
    }

    /**
     * Get end_month
     *
     * @return string 
     */
    public function getEndMonth()
    {
        return $this->end_month;
    }

    /**
     * Set end_year
     *
     * @param string $endYear
     * @return Payment
     */
    public function setEndYear($endYear)
    {
        $this->end_year = $endYear;
    
        return $this;
    }

    /**
     * Get end_year
     *
     * @return string 
     */
    public function getEndYear()
    {
        return $this->end_year;
    }

    /**
     * Set agreed
     *
     * @param boolean $agreed
     * @return Payment
     */
    public function setAgreed($agreed)
    {
        $this->agreed = $agreed;
    
        return $this;
    }

    /**
     * Get agreed
     *
     * @return boolean 
     */
    public function getAgreed()
    {
        return $this->agreed;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Payment
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set cvv_code
     *
     * @param string $cvvCode
     * @return Payment
     */
    public function setCvvCode($cvvCode)
    {
        $this->cvv_code = $cvvCode;
    
        return $this;
    }

    /**
     * Get cvv_code
     *
     * @return string 
     */
    public function getCvvCode()
    {
        return $this->cvv_code;
    }
}