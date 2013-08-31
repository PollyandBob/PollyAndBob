<?php

namespace Fenchy\UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Fenchy\UserBundle\Entity\User,
    Fenchy\NoticeBundle\Entity\Notice;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="location")
 * @ORM\Entity(repositoryClass="Fenchy\UtilBundle\Entity\LocationRepository")
 */
class Location {
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    
    /**
     *
     * @var String 
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\MaxLength(255)
     */
    private $location;
    
    /**
     *
     * @var String 
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\MaxLength(255)
     */
    private $printable;
    
    /**
     * Goe-localization stored in PostGIS GEOGRAPHY type
     * PHP value in turn will be stored as GeoJSON.
     * 
     * @ORM\Column(type="geography", nullable=true)
     */
    private $pgisGeog = array();

    /**
     * Geo-localization stored in PostGIS GEOMETRY type
     * PHP value in turn will be stored as GeoJSON.
     * 
     * @ORM\Column(type="geometry", nullable=true)
     */
    private $pgisGeom = array();

    /**
     * @var User $user; 
     * @ORM\OneToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="location")
     */
    private $user;
    
    /**
     * @var Notice $notice; 
     * @ORM\OneToOne(targetEntity="Fenchy\NoticeBundle\Entity\Notice", inversedBy="location")
     */
    private $notice;
    
    public function __construct() {}
    
    public function __clone() {
        
        $this->id       = NULL;
        $this->user     = NULL;
        $this->notice   = NULL;
        $this->printable = NULL;
    }
    
    public function __toString() {
        return ''.($this->printable ? $this->printable : $this->location);
    }
    
    /**
     * Set ID
     * @param Integer $id
     * @return Location 
     */
    public function setId($id) {
        
        $this->id = $id;
        
        return $this;
    }
    
    /**
     * Get ID
     * @return Integer 
     */
    public function getId() {
        
        return $this->id;
    }
    
    /**
     * Get Latitude
     * @return type 
     */
    public function getLatitude()
    {
        if ($this->hasPoint())
            return $this->pgisGeog['coordinates'][1];
        else
            return 0;
    }    
    
    /**
     * Set Latitude
     * @param type $latitude 
     * @return Location
     */
    public function setLatitude($latitude)
    {
        $this->pgisGeog['coordinates'][1] = $latitude;
        $this->pgisGeom['coordinates'][1] = $latitude;
        return $this;
    }  
    
    /**
     * Get Longitude
     * @return type 
     */
    public function getLongitude()
    {
        if ($this->hasPoint()) {
            return $this->pgisGeog['coordinates'][0];
        } else 
            return 0;
    }    
    
    /**
     * Set Longitude
     * @param type $longitude
     * @return Location 
     */
    public function setLongitude($longitude)
    {
        $this->pgisGeog['coordinates'][0] = $longitude;
        $this->pgisGeom['coordinates'][0] = $longitude;
        return $this;
    }
    
    /**
     * Returns public address (city and district) as string
     * @return String 
     */
    public function getAddress() {
        
        return $this->printable ? $this->printable : $this->location;
    }
    
    /**
     * Returns form input text for location element.
     * @return String
     */
    public function getGapiAddress() {
        
        return $this->location;
    }
    
    public function setGapiAddress() {
        
    }
    /**
     * Set User
     * @param User $user
     * @return Location 
     */
    public function setUser(User $user) {
        
        $this->user = $user;
        
        return $this;
    }
    
    
    /**
     * Get User
     * @return User 
     */
    public function getUser() {
        
        return $this->user;
    }
    
    /**
     * Set Notice
     * @param Notice $notice
     * @return Location 
     */
    public function setNotice(Notice $notice) {
        
        $this->notice = $notice;
        
        return $this;
    }
    
    /**
     * Get Notice
     * @return Notice 
     */
    public function getNotice() {
        
        return $this->notice;
    }
    
    /**
     * Returns TRUE if all required location data is set. False otherwise.
     * @return bool
     */
    public function hasPoint() {
        
        if(empty($this->pgisGeog))
            return FALSE;
        
        if(!array_key_exists('coordinates', $this->pgisGeog))
            return FALSE;
        
        if(!array_key_exists(0, $this->pgisGeog['coordinates']) || !array_key_exists(1, $this->pgisGeog['coordinates']))
            return FALSE;
        
        return TRUE;
    }
    
    public function hasLocation() {
        if(empty($this->location)) {
            return false;
        } else {
            return true;
        }
    }

    
    /**
     * Setter method for pgisGeog property will be throwing exception
     * by assumption that only get/setLatitude, get/setLongitude methods 
     * have access to location coordinates.
     */
    public function setPgisGeog($pgisGeog)
    {
        throw new \Exception(
            "Coordinates of entity are accessible only by setLatitude, setLongitude methods");
        return false;
    }
    
    /**
     * Setter method for pgisGeog property will be throwing exception
     * by assumption that only get/setLatitude, get/setLongitude methods 
     * have access to location coordinates.
     */
    public function getPgisGeog()
    {
        throw new \Exception(
            "Coordinates of entity are accessible only by getLatitude, getLongitude methods");
        return false;
    }

    /**
     * Setter method for pgisGeom property will be throwing exception
     * by assumption that only get/setLatitude, get/setLongitude methods 
     * have access to location coordinates.
     */
    public function setPgisGeom($pgisGeom)
    {
        throw new \Exception(
            "Coordinates of entity are accessible only by setLatitude, setLongitude methods");
        return false;
    }

    /**
     * Setter method for pgisGeom property will be throwing exception
     * by assumption that only get/setLatitude, get/setLongitude methods 
     * have access to location coordinates.
     */
    public function getPgisGeom()
    {
        throw new \Exception(
            "Coordinates of entity are accessible only by getLatitude, getLongitude methods");
        return false;
    }
    
    /**
     * Gets location represented by string
     * @return String
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * Sets location represented by string
     * @param String $location
     */
    public function setLocation($location) {
        $this->location = $location;
    }
    
    /**
     * Get printable location
     * @return String
     */
    public function getPrintable() {
        
        return $this->printable;
    }
    
    /**
     * Set printable location
     * @param String $location
     * @return \Fenchy\UtilBundle\Entity\Location
     */
    public function setPrintable($location) {
        
        $this->printable = $location;
        
        return $this;
    }


}