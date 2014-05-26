<?php

namespace Fenchy\NoticeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

/**
 * @ORM\Table(name="notice__property_types", uniqueConstraints={@UniqueConstraint(name="propertytypename_idx", columns={"name"})})
 * @ORM\Entity(repositoryClass="Fenchy\NoticeBundle\Entity\PropertyTypeRepository")
 * @Assert\Callback(methods={"isElementValid"})
 */
class PropertyType {
    
    const ELEMENT_STRING        = 1;
    const ELEMENT_TEXT          = 2;
    const ELEMENT_SELECT        = 3;
    
    static public $elementMap = array(
        self::ELEMENT_STRING        => 'text',
        self::ELEMENT_TEXT          => 'textarea',
        self::ELEMENT_SELECT        => 'choice',
    );
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * This name should be display as name of form element
     * 
     * @var string $name
     * 
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $name;
    
    /**
     * @var string $element;
     * 
     * @ORM\Column(type="integer", nullable=false)
     */
    private $element;
    
    /**
     * @var string $options
     * 
     * @ORM\Column(type="string", nullable=true)
     */
    private $options;
    
    private $optionsArray;
    
    /**
     * @var bool $expanded
     * 
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $expanded;

    /**
     * @var bool $multiple
     * 
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $multiple;

    /**
     * @var ArrayCollection $types
     * 
     * @ORM\ManyToMany(targetEntity="Type", mappedBy="properties")
     */
    private $types;
    
    /**
     * @var ArrayCollection $values
     * 
     * @ORM\OneToMany(targetEntity="Value", mappedBy="property")
     */
    private $values;

    
    public function __construct() {
        
        $this->optionsArray = $this->options ? unserialize($this->options) : array();
        $this->values       = new ArrayCollection();
        $this->types        = new ArrayCollection();
        
        $this->expanded = FALSE;
        $this->multiple = FALSE;
    }
    
    public function __toString() {
        
        return $this->name;
    }
    
    /**
     * @return integer
     */
    public function getId() {
        
        return $this->id;
    }
    
    /**
     * @param integer $id
     * @return PropertyType
     */
    public function setId($id) {
        
        $this->id = $id;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getName() {
        
        return $this->name;
    }
    
    /**
     * @param string $name
     * @return PropertyType 
     */
    public function setName($name) {
        
        $this->name = $name;
        
        return $this;
    }
    
    /**
     * @return integer
     */
    public function getElement() {
        
        return $this->element;
    }
    
    /**
     * @param integer $element
     * @return PropertyType 
     */
    public function setElement($element) {
        
        $this->element = $element;
        
        return $this;
    }
    
    public function getOptions($unserialized = TRUE) {
        
        $this->options && !$this->optionsArray && $this->optionsArray = unserialize($this->options);
        
        return $unserialized ? $this->optionsArray : $this->options;
    }
    
    public function setOptions($options) {
        
        if(is_string($options)) {
            
            $this->options      = $options;
            $this->optionsArray = unserialize($options);
            
        } else {
            
            $this->optionsArray = $options;
            $this->options      = serialize($options);
        }
        
        return $this;
    }
    
    /**
     * Returns element name or NULL if not found.
     * 
     * @param integer $element
     * @return mixed
     */
    public function getElementName($element = NULL) {
        
        NULL === $element && $element = $this->element;
        
        return array_key_exists($element, self::$elementMap) ? self::$elementMap[$element] : NULL;
    }
    
    /**
     * @return bool
     */
    public function getExpanded() {
        
        return $this->expanded;
    }
    
    /**
     * @param bool $exp
     * @return PropertyType 
     */
    public function setExpanded($exp) {
        
        $this->expanded = (bool) $exp;
        
        return $this;
    }
    
    /**
     * @return bool
     */
    public function getMultiple() {
        
        return $this->multiple;
    }
    
    /**
     * @param bool $multi
     * @return PropertyType 
     */
    public function setMultiple($multi) {
        
        $this->multiple = (bool) $multi;
        
        return $this;
    }
    
    /**
     * @return ArrayCollection;
     */
    public function getType() {
        
        return $this->types;
    }
    
    /**
     * @param ArrayCollection $types
     * @return PropertyType
     */
    public function setTypes(ArrayCollection $types) {
        
        $this->types = $types;
        
        return $this;
    }
    
    /**
     * @param Type $type
     * @return PropertyType 
     */
    public function addType(Type $type) {
        
        $this->types->add($type);
        
        return $this;
    }
    
    /**
     * @param Type $type
     * @return PropertyType 
     */
    public function removeType(Type $type) {
        
        $this->types->removeElement($type);
        
        return $this;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getValues() {
        
        return $this->values;
    }
    
    /**
     * @param ArrayCollection $values
     * @return PropertyType 
     */
    public function setValues(ArrayCollection $values) {
        
        $this->values = $values;
        
        return $this;
    }
    
    /**
     * @param Value $value
     * @return PropertyType 
     */
    public function addValue(Value $value) {
        
        $this->values->add($value);
        
        return $this;
    }
    
    /**
     * @param Value $value
     * @return PropertyType 
     */
    public function removeValue(Value $value) {
        
        $this->values->removeElement($value);
        
        return $this;
    }
    
    public function isElementValid(ExecutionContext $context) {
        
        if(!array_key_exists($this->element, self::$elementMap)) {
            
            $context->addViolation('Invalid element type.', array(), null);
            return;
        }
    }
    
    /**
     * Checks if given value is valid for this type. If given $val is an array
     * with more than one element, then it can be valid only if type is "multiple select".
     * 
     * @param MIX $val
     * @return boolean
     */
    public function isValueValid($val) {
        
        // validate array of values
        if(is_array($val)) {
            
            // we can pass array of values for text or string element only if it
            // has one and only one element
            if($this->element === self::ELEMENT_STRING || $this->element === self::ELEMENT_TEXT) {
                if(count($val) !== 1) return FALSE;
            }
            
            // array of values is valid only if each it's element is.
            elseif($this->element === self::ELEMENT_SELECT) {
                $options = $this->getOptions();
                foreach($val as $v) {
                    if(!array_key_exists($v, $options)) return FALSE;
                }
            }
        } 
        // validate scalar value
        else {
            // every value is valid for text and string element so we have to check
            // it for options element only
            if(!array_key_exists($val, $this->getOptions())) return FALSE;
        }
        
        return TRUE;
    }
    
    public function getOptionName($key) {
        if(!is_array($this->optionsArray)) {
            
            try {
                $this->optionsArray = unserialize($this->options);
            } catch (\Exception $exc) {
                
                return NULL;
            }

        }
        if(!is_array($this->optionsArray)) return $key;
        return array_key_exists($key, $this->optionsArray) ? $this->optionsArray[$key] : '';
    }
    
    /**
     * Returns value option pointed by given $name.
     * 
     * @param String $name
     * @return boolean
     */
    public function getOptionValueByName($name) {
        
        if(!is_array($this->optionsArray)) {
            
            try {
                $this->optionsArray = unserialize($this->options);
            } catch (\Exception $exc) {
                
                return FALSE;
            }

        }
        
        $reversed = array_flip($this->optionsArray);
        return array_key_exists($name, $reversed) ? $reversed[$name] : FALSE;
    }

    /**
     * Get types
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getTypes()
    {
        return $this->types;
    }
    
    /**
     * Checks if element is selectable.
     * @return Boolean
     */
    public function isSelectable() {
        
        return $this->element === self::ELEMENT_SELECT;
    }
}