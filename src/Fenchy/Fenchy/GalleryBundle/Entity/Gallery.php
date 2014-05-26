<?php

namespace Fenchy\GalleryBundle\Entity;

use Fenchy\RegularUserBundle\Entity\UserRegular;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Fenchy\NoticeBundle\Entity\Notice;
use Fenchy\UserBundle\Entity\User;

/**
 * @ORM\Table(name="galleries")
 * @ORM\Entity(repositoryClass="Fenchy\GalleryBundle\Entity\GalleryRepository")
 */
class Gallery
{
    /**
     * Folder where all galleries are stored.
     * @var string 
     */
    public static $store_folder = 'images';
    
    public static $base_dir = '/uploads';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    
    /**
     * @var ArrayCollection $images
     * 
     * @ORM\OneToMany(targetEntity="Image", mappedBy="gallery", cascade={"all"})
     * @ORM\OrderBy({"sequence"="ASC"})
     */
    private $images;
    
    /**
     * @var UserRegular $regular_user;
     * 
     * @ORM\OneToOne(targetEntity="Fenchy\RegularUserBundle\Entity\UserRegular", inversedBy="gallery")
     * @ORM\JoinColumn(name="regular_id", referencedColumnName="id", nullable=true)
     */
    private $regular_user;
    
    /**
     * @var UserRegular $regular_user;
     * 
     * @ORM\OneToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="gallery")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;
    
    /**
     * @var Notice notice
     * 
     * @ORM\OneToOne(targetEntity="Fenchy\NoticeBundle\Entity\Notice", inversedBy="gallery")
     * @ORM\JoinColumn(name="notice_id", referencedColumnName="id", nullable=true)
     */
    private $notice;
    
    /**
     * @var Gallery tmp
     * 
     * @ORM\OneToOne(targetEntity="Fenchy\GalleryBundle\Entity\Gallery", mappedBy="original", cascade={"persist", "remove"})
     */
    private $tmp;
    
    /**
     * @var Gallery original
     * 
     * @ORM\OneToOne(targetEntity="Fenchy\GalleryBundle\Entity\Gallery", inversedBy="tmp")
     * @ORM\JoinColumn(name="original_id", referencedColumnName="id", nullable=true)
     */
    private $original;
    
    private $prevImagesQuantity = FALSE;
    
    public function __construct()
    {
        $this->images = new ArrayCollection();
    }
    
    public function __clone() {
        
        $this->id = NULL;
        if(NULL != $this->images)
            foreach($this->images as $img) $img->setId(NULL);
    }
    
    public function getId() {
        
        return $this->id;
    }
    
    public function setId($id) {
        
        $this->id = $id;
        
        return $this;
    }
    
    public function getRegularUser()
    {
        return $this->regular_user;
    }
    
    public function setRegularUser($regularUser)
    {
        $this->regular_user = $regularUser;
        
        return $this;
    }
    
    public function getUser() {
        
        return $this->user;
    }
    
    public function findUser() {
        
        if (NULL !== $this->user) {
            return $this->user;
        } elseif (NULL !== $this->regular_user) {
            return $this->regular_user->getUser();
        } elseif (NULL !== $this->notice) {
            return $this->notice->getUser();
        }
        
        return NULL;
    }
    
    public function unsetUser() {
        
        $this->user = NULL;
        $this->regular_user = NULL;
        $this->notice = NULL;
        
        return $this;
    }
    
    public function setUser(User $user) {
        
        $this->user = $user;
        
        return $this;
    }
    
    public function getImages()
    {
        return $this->images;
    }
    
    public function setImages(ArrayCollection $images)
    { 

        $this->prevImagesQuantity = $this->images->count();
        $this->images = $images;
        
        foreach($images as $image) {
            
            $image->setGallery($this);
        }
        
        
        return $this;
    }
    
    public function addImage(Image $image)
    {

        if (FALSE === $this->prevImagesQuantity) $this->prevImagesQuantity = $this->images->count();
        $this->images->add($image->setSequence($this->getImagesQuantity()));
        $image->setGallery($this);
        
        return $this;
    }
    
    public function removeImage(Image $image)
    { 

        if (FALSE === $this->prevImagesQuantity) $this->prevImagesQuantity = $this->images->count();
        $this->images->removeElement($image);
        
        $this->prevImagesQuantity = $this->prevImagesQuantity - 1;
        
        return $this;
    }
    
    public function getPrevImagesQuantity() {
        
        return $this->prevImagesQuantity;
    }
    
    public function getMainImage() {
        
        foreach ($this->images as $img) {

            if (!$img->isEmpty()) {
                return $img;
            }
        }
        
        return FALSE;
    }
    
    /**
     * Returns TRUE if has any Image entity
     * @return bool
     */
    public function hasImages() {
        
        foreach($this->images as $img) {
            if(!$img->isEmpty()) {
                return TRUE;
            }
        }
        
        return FALSE;
        
    }
    
    /**
     * Returns TRUE if has no Image entities with uploaded image.
     */
    public function isEmpty() {
        
        if($this->hasImages()) {
            
            foreach($this->images as $img) {

                if(!$img->isEmpty()) return FALSE;
            }
        }
        return TRUE;
    }
    
    /**
     * @return Notice
     */
    public function getNotice() {
        
        return $this->notice;
    }
    
    /**
     * @param Notice $notice
     * @return Gallery 
     */
    public function setNotice(Notice $notice) {
        
        $this->notice = $notice;
        
        return $this;
    }
    
    public function getOwner() {
        
        if(NULL != $this->notice) {
            
            return $this->notice->getUser();
            
        } elseif(NULL != $this->regular_user) {
            
            return $this->regular_user->getUser();
        } else {
            
            throw new \Exception('Gallery doesn\'t belongs to notice neither to regular user');
        }
    }
    
    public function setTmp($gallery) {
        
        if(NULL === $gallery)
            $this->tmp = NULL;
        elseif($gallery instanceof \Fenchy\GalleryBundle\Entity\Gallery)
            $this->tmp = $gallery->setOriginal($this);
        else {
            throw new Exception('NULL or Gallery object expected, '.get_class($gallery).' given.');
        }
        
        return $this;
    }
    
    public function getTmp() {
        
        return $this->tmp;
    }
    
    public function setOriginal($gallery) {
        
        if(NULL === $gallery)
            $this->original = NULL;
        elseif($gallery instanceof \Fenchy\GalleryBundle\Entity\Gallery)
            $this->original = $gallery;
        else {
            throw new Exception('NULL or Gallery object expected, '.get_class($gallery).' given.');
        }
        
        return $this;
    }
    
    public function getOriginal() {
        
        return $this->original;
    }
    
    public function isTmp() {
        
        return NULL !== $this->original;
    }
    
    /**
     * Returns TRUE if number of images has been changed
     */
    public function setImagesNumber($max) {
        
        if(count($this->images) == $max) return FALSE;
        
        if(count($this->images) < $max) {
            for ($i = count($this->images) + 1; $i <= $max; $i++) {

                $img = new Image();
                $this->addImage($img->setSequence($i));
            }
        } else {
            for($i = count($this->images) - $max; $i >= 0; $i--) {
                $this->images->removeElement($this->images->last());
            }
        }
        
        return TRUE;
    }
    
    public function getImagesQuantity() {
        
        $q = 0;
        foreach($this->images as $image) {
            if(!$image->isEmpty()) $q++;
        }
        
        return $q;
    }
    
    /**
     * Returns path to gallery images inside 'upload' folder.
     * @return string
     */
    public function getFolder() {
        
        if($this->isTmp())
            return 'tmp/' . self::$store_folder . '/'.$this->getOriginal()->getId();
        else
            return self::$store_folder . '/'.$this->getId();
    }
    
    public function getPath() {
        
        if($this->isTmp())
            return 'tmp/' . self::$store_folder . '/';
        else
            return self::$store_folder . '/';
    }
    
    public function getSrc() {
        
        return self::$base_dir.'/'.$this->getFolder();
    }
    
    public function getExistingFiles() {
        
        $files = array();
        foreach($this->images as $img) {
            if(!$img->isEmpty()) {
                $files[] = array(
                        'name'    => $img->getName(),
                        'cropped' => $img->isCropped()
                        );
            }
        }
        
        return $files;
    }
    
    public function createTmp() {
        
        if(!$this->getTmp()) {
            $tmp = clone $this;
            $this->setTmp($tmp);
        }
        
        return $this->getTmp()->unsetUser()->cloneImages($this->getImages());
    }
    
    public function cloneImages($images) {
        
        $old = $this->images;
        $this->images = new ArrayCollection();
        foreach($images as $image) {
            $clone = clone $image;
            $this->images->add($clone->setGallery($this));
        }
        
        return $old;
    }
    
    public function update() {
        
        $oldImages = $this->images;
        if($this->getTmp()) {
            $this->setImages(new ArrayCollection());
            foreach($this->tmp->getImages() as $img) {
                $this->addImage($img);
            }
            return $oldImages;
        }
        
        return array();
    }
    
    public function getJQCarouselData() {
        
        $data = array();
        
        foreach($this->images as $img) {
            $data []= $img->getJQCarouselData();
        }
        
        return array(
                'id' => $this->id,
                'imgs' => $data
                );
    }
    
    public function getAvatar($small = TRUE) {
        
        $main = $this->getMainImage();
        if(!$main) return FALSE;
        
        return $main->getAvatar($small);
    }
    
    public function getFBImage() {
        
        $main = $this->getMainImage();
        if(!$main) return FALSE;
        return "/".self::$store_folder."/{$this->getId()}/originals/{$main->getName()}";
    }
}