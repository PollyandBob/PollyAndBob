<?php

namespace Fenchy\NoticeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Table(name="notice__values")
 * @ORM\Entity(repositoryClass="Fenchy\NoticeBundle\Entity\ValueRepository")
 */
class Value {
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    
    /**
     * Should contain string value or serialized array of values depends on
     * related PropertyType.
     * 
     * @var string $value
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $value;
    
    /**
     * @var Notice $notice
     * 
     * @ORM\ManyToOne(targetEntity="Notice", inversedBy="values")
     * @ORM\JoinColumn(name="notice_id", referencedColumnName="id", nullable=true)
     */
    private $notice;
    
    /**
     * @var PropertyType $property
     * 
     * @ORM\ManyToOne(targetEntity="PropertyType", inversedBy="values")
     * @ORM\JoinColumn(name="property_id", referencedColumnName="id", nullable=false)
     */
    private $property;
    
    /**
     * @return integer
     */
    public function getId() {
        
        return $this->id;
    }
    
    /**
     * @param integer $id
     * @return Value
     */
    public function setId($id) {
        
        $this->id = $id;
        
        return $this;
    }
    
    /**
     * @return Notice
     */
    public function getNotice() {
        
        return $this->notice;
    }
    
    /**
     * @param Notice $notice
     * @return Value 
     */
    public function setNotice(Notice $notice) {
        
        $this->notice = $notice;
        
        return $this;
    }
    
    /**
     * @return PropertyType
     */
    public function getProperty() {
        
        return $this->property;
    }
    
    /**
     * @param PropertyType $property
     * @return Value 
     */
    public function setProperty(PropertyType $property) {
        
        $this->property = $property;
        
        return $this;
    }
    
    /**
     * @param bool $raw
     * @return mixed 
     */
    public function getValue($raw = TRUE) {

        if(!$raw || $this->property->getElement() == PropertyType::ELEMENT_TEXT || $this->property->getElement() == PropertyType::ELEMENT_STRING) {
            try {
                return unserialize($this->value);
            } catch (\Exception $exc) {
                return $this->value;
            }
        }
        
        return $this->value;
    }
    
    /**
     * @param mixed $value
     * @return Value 
     */
    public function setValue($value) {

        if(is_string($value) || is_numeric($value)) {
            
            $this->value = $value;
            
        } else {
            
            $this->value = serialize($value);
        }
        
        return $this;
    }
    
    public function hasValue($val) {
        
        if($this->property->getMultiple()) {
            
            return in_array($val, $this->getValue());
            
        } else {
            
            return $val == $this->value;
        }
    }
    
    public function getValueAsString() {
        
        if (!is_numeric($this->value)) {
            
            return $this->value;
        }
        return $this->property->getOptionName($this->value);
    }
    
    /**
     * Search for valid value by given name in related Property options.
     * 
     * @param String $name
     * @return MIX FALSE if value not found or value otherwise.
     */
    public function setValueFromOptionByString($name) {
        
        if($this->property->isSelectable()) {
            
            $val = $this->property->getOptionValueByName($name);
            
            if(FALSE === $val) return FALSE;
            
            $this->value = $val;
            
            return $val;
        }
    }
}