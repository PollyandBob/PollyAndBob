<?php

namespace Fenchy\NoticeBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Fenchy\NoticeBundle\Entity\Notice,
    Doctrine\Common\Collections\ArrayCollection;
    
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="notice__dictionary")
 * @ORM\Entity(repositoryClass="Fenchy\NoticeBundle\Entity\DictionaryRepository")
 */
class Dictionary
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var string $text
     * 
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     * @Assert\NotBlank
     */
    private $text;
    
    /**
     * @var integer
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $tag;
    
    /**
     * @var integer $quantitySearch
     * 
     * @ORM\Column(name="quantity_search", type="integer", length=255, nullable=false)
     */
    private $quantitySearch;
    
    /**
     * @var integer $quantityTag
     * 
     * @ORM\Column(name="quantity_tag", type="integer", length=255, nullable=false)
     */
    private $quantityTag;
            
    /**
     * @var integer
     * @ORM\Column(name="search_activated", type="boolean", nullable=false)
     */
    private $searchActivated;
    
    /**
     * @var integer
     * @ORM\Column(name="tag_activated", type="boolean", nullable=false)
     */
    private $tagActivated;
    
    /**
     * @var bool $disabled
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $disabled;
    
    /**
     * @var ArrayCollection $notices
     * 
     * @ORM\ManyToMany(targetEntity="Fenchy\NoticeBundle\Entity\Notice", inversedBy="dictionaries")
     * @ORM\JoinTable(name="notice__notices__dictionaries")
     */
    protected $notices;
	
    public function __construct() {
        
        $this->quantitySearch = 0;
        $this->quantityTag = 0;
        $this->searchActivated = FALSE;
        $this->tagActivated = FALSE;
        $this->disabled = FALSE;
        $this->tag = FALSE;
        $this->notices = new ArrayCollection();
    }
    
    public function __toString() {
        return $this->text;
    }
    
    /**
     * @return integer
     */
    public function getId() {
        
        return $this->id;
    }
    
    /**
     * @param integer $id
     * @return Notice 
     */
    public function setId($id) {
        
        $this->id = $id;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getText() {
        
        return $this->text;
    }
    
    /**
     * @param string $text
     * @return Dictionary
     */
    public function setText($text) {
        
        $this->text = $text;
        
        return $this;
    }
    
    public function setSearchActivated($activated) {
        
        $this->searchActivated = $activated;
        
        return $this;
    }
    
    public function getSearchActivated() {
        
        return $this->searchActivated;
    }
    
    public function setTagActivated($activated) {
        
        $this->tagActivated = $activated;
        
        return $this;
    }
    
    public function getTagActivated() {
        
        return $this->tagActivated;
    }
    
    public function setDisabled($disabled) {
        
        $this->disabled = $disabled;
        
        return $this;
    }
    
    public function getDisabled() {
        
        return $this->disabled;
    }
    
    public function setQuantitySearch($quantity) {
        
        $this->quantitySearch = $quantity;
        
        return $this;
    }
    
    public function getQuantitySearch() {
        
        return $this->quantitySearch;
    }
    
    public function incQuantitySearch($amount = 1) {
        
        $this->quantitySearch += $amount;
        
        return $this;
    }
    
    public function setQuantityTag($quantity) {
        
        $this->quantityTag = $quantity;
        
        return $this;
    }
    
    public function getQuantityTag() {
        
        return $this->quantityTag;
    }
    
    public function incQuantityTag($amount = 1) {
        
        $this->quantityTag += $amount;
        
        return $this;
    }
    
    public function setTag($bool) {
        
        $this->tag = (bool) $bool;
        
        return $this;
    }
    
    public function getTag() {
        
        return $this->tag;
    }
    
    public function getNotices11() {
        
        return $this->notices;
    }
    
    public function setNotices($notices) {
        
        $this->notices = $notices;
        
        return $this;
    }
    
    public function addNotice($notice) {
        
        $this->notices->add($notice);
        
        return $this;
    }
    
    public function removeNotice($notice) {

        $this->notices->removeElement($notice);
        
        return $this;
    }
}
