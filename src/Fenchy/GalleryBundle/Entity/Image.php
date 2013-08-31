<?php

namespace Fenchy\GalleryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Table(name="images")
 * @ORM\Entity(repositoryClass="Fenchy\GalleryBundle\Entity\ImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;
    
    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sequence;
    
    /**
     * @var Gallery $gallery
     * 
     * @ORM\ManyToOne(targetEntity="Gallery", inversedBy="images")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id", nullable=true)
     */
    private $gallery;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;
    
    /**
     * @var boolean
     * 
     * @ORM\Column(type="boolean", nullable=true);
     */
    protected $crop;
    
    public function __construct()
    {
        
    }
    
    public function __clone() {
        
        $this->id = NULL;
    }
    
    public function __toString() {
        
        return $this->getSrc('thumbnail');
    }
    
    public function getPathByType($type = 'thumbnail') {
        
        return $this->getSrc($type);
        
    }


    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }
    
    public function getName() {
        
        return $this->name;
    }
    
    public function setName($name) {
        
        $this->name = $name;
        
        return $this;
    }

    public function getSequence() {
        
        return $this->sequence;
    }
    
    public function setSequence($seq) {
        
        $this->sequence = $seq;
        
        return $this;
    }
    
    public function getGallery()
    {
        return $this->gallery;
    }
    
    public function setGallery(Gallery $gallery)
    {
        $this->gallery = $gallery;
        
        return $this;
    }
    
    /**
     * Return TRUE if file has not been uploaded.
     * 
     * @return bool
     */
    public function isEmpty() {
        
        return NULL == $this->name;
    }
    
    public function setUpdatedAt($date = NULL) {
        
        if(NULL === $date) {
            $this->updated_at = new \DateTime('now');
        }
        else {
            $this->updated_at = $date;
        }
    }
    
    /**
     * Get crop
     * @return boolean
     */
    public function getCrop() {
        
        return $this->crop;
    }
    
    /**
     * Set crop.
     * @param boolean $bool
     * @return \Fenchy\GalleryBundle\Entity\Image
     */
    public function setCrop($bool) {
        
        $this->crop = (bool) $bool;
        return $this;
    }
    
    /**
     * Check if Image has been cropped.
     * @return type
     */
    public function isCropped() {
        
        return (bool) $this->crop;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return (bool) $this->updated_at;
    }
    
    public function getJQCarouselData() {
        
        if($this->isCropped()) {
            return array(
                'big_source' => $this->getSrc('crop_big'),
                'small_source' => $this->getSrc('crop_small'),
                'original_source' => $this->getSrc('originals')
            );
        }
        
        return array(
            'big_source' => $this->getSrc('originals'),
            'small_source' => $this->getSrc('thumbnail'),
            'original_source' => $this->getSrc('originals')
        );
    }
    
    public function getSrc($size) {
        
        return $this->gallery->getSrc().'/'.$size.'/'.$this->name;
    }
    
    public function getAvatar($small = TRUE) {
        
        if($this->isCropped()) {
            return $this->gallery->getSrc().'/'.($small ? 'crop_small' : 'crop_big').'/'.$this->name;
        }
        else {
            return $this->getSrc($small ? 'thumbnail' : 'medium');
        }
    }
}