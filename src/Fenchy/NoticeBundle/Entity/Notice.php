<?php

namespace Fenchy\NoticeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Fenchy\GalleryBundle\Entity\Gallery;
use Fenchy\UserBundle\Entity\User,
    Fenchy\UtilBundle\Entity\Location,
    Fenchy\UtilBundle\Entity\Sticker;

use Gedmo\Mapping\Annotation as Gedmo; // gedmo annotations

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="notice__notices")
 * @ORM\Entity(repositoryClass="Fenchy\NoticeBundle\Entity\NoticeRepository")
 */
class Notice
{
    const ALLOWED_CONTACT      = 1;
    const ALLOWED_MEN          = 2;
    const ALLOWED_WOMEN        = 3;
    
    public static $emptyImagePath = '/images/default_listing_photo.png';
    
    static public $allowedMap = array(
        self::ALLOWED_CONTACT      => 'contact',
        self::ALLOWED_MEN          => 'men',
        self::ALLOWED_WOMEN        => 'women',
    );
    
    public static $iconsMap = array(
            'help_need' => 'icon-download-alt',
            'help_offer' => 'icon-upload-alt',
            'goods_offer' => 'icon-upload-alt', //need change
            'goods_need' => 'icon-download-alt', //need change
            'events' => 'icon-calendar',
            'others' => 'icon-list-alt'
        );
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var string $title
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     */
    private $title;
    
    /**
     * @var string $title
     * @Gedmo\Slug(fields={"title"}, updatable=true, separator="-")
     * @ORM\Column(type="string", length=255, nullable=true)
     */    
    private $slug;
    
    /**
     * @var string
     * 
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank
     * @Assert\MaxLength(10000)
     */
    private $content;
    
    /**
     * @var integer
     * 
     * @ORM\Column(type="integer", nullable=true)
     */
    private $allowed;
    
    /**
     * @var Type
     * 
     * @ORM\ManyToOne(targetEntity="Type", inversedBy="notices")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=true)
     */
    private $type;
    
    /**
     * @var integer
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $draft;
    
    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="notices")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;
    
    /**
     * @var Gallery
     * 
     * @ORM\OneToOne(targetEntity="Fenchy\GalleryBundle\Entity\Gallery", mappedBy="notice", cascade={"remove", "persist"})
     */
    private $gallery;
    
    /**
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="Value", mappedBy="notice", cascade={"all"})
     */
    private $values;
    
    /**
     * @var string
     * 
     * @ORM\Column(type="string", nullable=true) 
     */
    private $link;
    
    /**
     * @var DateTime
     * 
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;
    
    /**
     * @var DateTime
     * 
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\NotBlank
     */
    private $start_date;    
    
    /**
     * @var DateTime
     * 
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\NotBlank
     */
    private $end_date;     
    
    /**
     * @ORM\OneToMany(targetEntity="FacebookFeed", mappedBy="notice", cascade={"remove"})
     */
    private $facebook_feed;
        
    /**
     * @var bool
     */
    private $put_on_fb;
    
    /**
     * @var Location $location
     * 
     * @ORM\OneToOne(targetEntity="Fenchy\UtilBundle\Entity\Location", mappedBy="notice", cascade={"persist", "remove"})
     */
    protected $location;
    
    /**
     * @var ArrayCollection $stickers
     * 
     * @ORM\OneToMany(targetEntity="Fenchy\UtilBundle\Entity\Sticker", mappedBy="notice", cascade={"remove"})
     * @ORM\OrderBy({"created_at"="DESC"})
     */
    private $stickers;
    
    /**
     * @var ArrayCollection $reviews
     * 
     * @ORM\OneToMany(targetEntity="Fenchy\NoticeBundle\Entity\Review", mappedBy="aboutNotice")
     * @ORM\OrderBy({"created_at"="DESC"})
     */
    private $reviews;
    
    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $on_dashboard = false;
    
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $tags;
    
    /**
     *
     * @var ArrayCollection $dictionaries
     * 
     * @ORM\ManyToMany(targetEntity="Fenchy\NoticeBundle\Entity\Dictionary", mappedBy="notices")
     * 
     */
    protected $dictionaries;
	
    public function __construct() {
        
        $this->values       = new ArrayCollection();
        $this->draft        = TRUE;
        $this->created_at   = new \DateTime();    
        //$this->start_date   = new \DateTime();
        //$this->end_date     = new \DateTime();
        $this->stickers     = new ArrayCollection();
        $this->reviews      = new ArrayCollection();
        $this->gallery      = new Gallery();
        $this->gallery->setNotice($this);
        $this->dictionaries = new ArrayCollection();
    }
    
    public function __toString() {
        return $this->title;
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
    public function getTitle() {
        
        return $this->title;
    }
    
    /**
     * @param string $title
     * @return Notice
     */
    public function setTitle($title) {
        
        $this->title = $title;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getContent() {
        
        return $this->content;
    }
    
    /**
     * @param string $content
     * @return Notice 
     */
    public function setContent($content) {
        
        $this->content = $content;
        
        return $this;
    }
    
    /**
     * @return integer
     */
    public function getAllowed() {
        
        return $this->allowed;
    }
    
    /**
     * @param integer $allowed
     * @return Notice 
     */
    public function setAllowed($allowed) {
        
        $this->allowed = $allowed;
        
        return $this;
    }
    
    public function getAllowedName($allowed = NULL) {
        
        NULL === $allowed && $allowed = $this->allowed;
        
        return array_key_exists($allowed, self::$allowedMap) ? self::$allowedMap[$allowed] : NULL;
    }
    
    /**
     * @return string
     */
    public function getStartDateDate() {
        return '???';
        return $this->start_at ? $this->start_at->format('d.m.y') : '';
    }
    
    /**
     * @return string
     */
    public function getStartTime() {
        return '???';
        return $this->start_at ? $this->start_at->format('H:i') : '';
    }
    
    /**
     * @return Type
     */
    public function getType() {
        
        return $this->type;
    }
    
    /**
     * @param Type $type
     * @return Notice 
     */
    public function setType(Type $type) {
        
        $this->type = $type;
        
        return $this;
    }
    
    public function getRootType() {
        
        return $this->type ? $this->type->getRoot() : NULL;
    }
    
    /**
     * @return User
     */
    public function getUser() {
        
        return $this->user;
    }
    
    /**
     * @param User $user
     * @return Notice 
     */
    public function setUser(User $user) {
        
        $this->user = $user->addNotice($this);
        
        return $this;
    }
    
    /**
     * @return Gallery
     */
    public function getGallery() {
        
        return $this->gallery;
    }
    
    /**
     * Sets gallery to given one, and itself to gallery notice.
     * @param Gallery $gallery
     * @return Notice 
     */
    public function setGallery(Gallery $gallery) {
        
        $this->gallery = $gallery->setNotice($this);
        
        return $this;
    }
    
    /**
     * Gets main image of notice
     * @return \Fenchy\GalleryBundle\Entity\Image
     */
    public function getMainImage() {
        if($image = $this->gallery->getMainImage()) {
            return $image;
        }
        return self::$emptyImagePath;
    }
    
    public function getMainImageLanding() {
    	if($image = $this->gallery->getMainImage()) {
    		return str_replace("thumbnail","square_l",$image);
    	}
    	return self::$emptyImagePath;
    }
    
    public function getAvatar() {
        
        $url = $this->gallery->getAvatar(FALSE);
        return $url ? $url : self::$emptyImagePath;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getValues() {
        
        return $this->values;
    }
    
    /**
     * @param ArrayCollection $values
     * @return Notice 
     */
    public function setValues(ArrayCollection $values) {
        
        foreach($values as &$val) {
            
            $val->setNotice($this);
        }
        
        $this->values = $values;
        
        return $this;
    }
    
    /**
     * @param Value $value
     * @return Notice 
     */
    public function addValue(Value $value) {
        
        $this->values->add($value->setNotice($this));
        
        return $this;
    }
    
    /**
     * @param Value $value
     * @return Notice 
     */
    public function removeValue(Value $value) {
        
        $this->values->removeElement($value);
        
        return $this;
    }
    
    public function getDraft() {
        
        return $this->draft;
    }
    
    public function setDraft($draft) {
        
        $this->user->countPrevNoticesQuantity();
        $this->draft = (bool) $draft;
        
        return $this;
    }
    
    public function hasValue(PropertyType $property, $val) {

        foreach($this->values as $value) {
            
            if($value->getProperty()->getId() === $property->getId()) {
                
                return $value->hasValue($val);
            }
        }
        
        return FALSE;
    }
    
    public function getLink() {
        
        return $this->link;
    }
    
    public function setLink($link) {
        
        $this->link = $link;
        
        return $this;
    }
    
    public function getOptionValueName($id, $val) {
        
        foreach($this->values as $value) {

            if($value->getProperty()->getId() == $id) {
                
                return $value->getProperty()->getValueName($val);
            }
        }

        return '';
    }
    
     public function getOptionValue($property) {
        
        foreach($this->values as $value) {

            if($value->getProperty()->getId() == $property->getId()) {
                
                return $value->getValue();
            }
        }

        return '';
    }
    
    public function selectParent(ArrayCollection $types) {
        
        foreach($types as $type) {
            if(in_array($type->getId(), $this->type->getRootTypeIdPath())) {
                return $type->getId();
            }
        }
        return NULL;
    }
    
    /**
     * Create and return array selected options (properties or types (if it is selection of subtype))
     */
    public function getValuesList() {
        
        if (NULL == $this->type) {
            
            $result = array();
            
        } else {
            
            $result = $this->type->getParentPath();
        }
        
        if(NULL != $this->values)
            foreach ($this->values as $val) {

                $result[] = $val->getValueAsString();
            }
        
        return $result;
    }
    
    public function getCreatedAt() {
        
        return $this->created_at;
    }
    
    public function setCreatedAt($date) {
        
        $this->created_at = $date;
        
        return $this;
    } 
    
    public function getDirection(){
        
        if(NULL == $this->values)
            return '';
        
        foreach($this->getValues() as $val)
        {
            if($val->getProperty()->getName() == 'direction')
            {
                if ( $val->getValue(true) === '2' )
                    return 'need';
                elseif ( $val->getValue(true) === '1' )
                    return 'offer';
            }
        }
        return '';
    }
	
	/**
     * Add facebook_feed
     *
     * @param Fenchy\NoticeBundle\Entity\FacebookFeed $facebookFeed
     * @return Notice
     */
    public function addFacebookFeed(\Fenchy\NoticeBundle\Entity\FacebookFeed $facebookFeed)
    {
        $this->facebook_feed[] = $facebookFeed;
    
        return $this;
    }

    /**
     * Remove facebook_feed
     *
     * @param Fenchy\NoticeBundle\Entity\FacebookFeed $facebookFeed
     */
    public function removeFacebookFeed(\Fenchy\NoticeBundle\Entity\FacebookFeed $facebookFeed)
    {
        $this->facebook_feed->removeElement($facebookFeed);
    }

    /**
     * Get facebook_feed
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getFacebookFeed()
    {
        return $this->facebook_feed;
    }
	
    public function getPutOnFb() {
        
        return $this->put_on_fb;
    }
    
    public function setPutOnFb($put_on_fb) {  
        $this->put_on_fb = $put_on_fb;    
        return $this;
    } 

    /**
     * Checks if Notice can be edited
     * 
     * @todo when user can be assigned to notice then only notices without them can be edited.
     * 
     * @return bool
     */
    public function canEdit() {
        
        
        return TRUE;
    }
	
    /**
     * Get start_date
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->start_date;
    }
    
    /**
     * Set start_date
     *
     * @param \DateTime $startDate
     * @return Notice
     */
    public function setStartDate($startDate)
    {
        $this->start_date = $startDate;
    
        return $this;
    }

    /**
     * Set end_date
     *
     * @param \DateTime $endDate
     * @return Notice
     */
    public function setEndDate($endDate)
    {
        $this->end_date = $endDate;
    
        return $this;
    }

    /**
     * Get end_date
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->end_date;
    }
    
    /**
     * Set Location
     * 
     * @param Location $location
     * @return Notice 
     */
    public function setLocation(Location $location) {
        
        $this->location = $location->setNotice($this);
        
        return $this;
    }
    
    /**
     * Get Location
     * 
     * @return Location
     */
    public function getLocation() {
        
        return $this->location;
    }
    
    /*
     * Get diff days beetwen start_date and end_date
     * return int
     */
    public function getDatesDiff()
    {
        $datetime1 = $this->getStartDate();
        $datetime2 = $this->getEndDate();

        $diff = $datetime1->diff($datetime2);
        $diff = (int) $diff->format('%a%'); 
            
        return $diff;
    }
        
    /**
     * @return ArrayCollection
     */
    public function getStickers() {
        
        return $this->stickers;
    }
    
    /**
     * Set Stickers.
     * @param ArrayCollection $stickers
     * @return Notice 
     */
    public function setStickers(ArrayCollection $stickers) {

        $this->stickers = $stickers;
        
        return $this;
    }
    
    /**
     * Add Sticker.
     * @param Sticker $sticker
     * @return Notice 
     */
    public function addSticker(Sticker $sticker) {
        
        $this->stickers->add($sticker);

        return $this;
    }
    
    /**
     * @param Sticker $sticker
     * @return Notice 
     */
    public function removeSticker(Sticker $sticker) {
        
        $this->stickers->removeElement($sticker);
        
        return $this;
    }
    
    /**
     * Set Reviews
     * @param \Doctrine\Common\Collections\ArrayCollection $reviews
     * @return \Fenchy\NoticeBundle\Entity\Notice
     */
    public function setReviews(ArrayCollection $reviews) {
        
        $this->reviews = $reviews;
        
        return $this;
    }
    
    public function addReview(Review $review) {
        
        $this->reviews->add($review);
        
        return $this;
    }
    
    public function removeReview(Review $review) {
        
        $this->reviews->removeElement($review);
        
        return $this;
    }
    
    public function getReviews() {

        return $this->reviews;
    }
    
    public function toJsonArray() {
        return array(
                    'title'     => $this->getTitle(),
                    'location'  => ''.$this->getLocation(),
                    'content'   => $this->getContent(),
                    'image'     => ''.$this->getAvatar(),
                    'direction' => $this->getDirection(),
                    'icon'      => $this->getIcon(),
                    'owner'     => $this->user->toJsonArray()
                );
    }
    
    public function getOnDashboard() {
        return $this->on_dashboard;
    }

    public function setOnDashboard($on_dashboard) {
        $this->on_dashboard = $on_dashboard;
    }
    
    public function getSlug() {
        return $this->slug;
    }

    public function setSlug($slug) {
        $this->slug = $slug;
    }

    public function getTypeSlug() {
        
        if($this->getDirection()) {
            return $this->type.'_'.$this->getDirection();
        }
        
        return ''.$this->type;
    }

    public function getIcon() {
        
        $slug = $this->getTypeSlug();
        
        if(!array_key_exists($slug, self::$iconsMap)) return '';
        else return self::$iconsMap[$slug];
    }
    
    public function getTags() {
        
        return $this->tags;
    }
    
    public function setTags($tags) {
        
        $this->tags = $tags;
        
        return $this;
    }
    
    /**
     * Returns Value entity of given propertyType or FALSE if not found
     * @param \Fenchy\NoticeBundle\Entity\PropertyType $property
     * @return boolean
     */
    public function getValueByProperty(PropertyType $property) {
        
        foreach($this->values as $value) {
            if($value->getProperty()->getId() === $property->getId()) return $value;
        }
        
        return FALSE;
    }
    
    public function getDictionaries() {
        
        return $this->dictionaries;
    }
    
    public function setDictionaries($dictionaries) {
        
        foreach($this->dictionaries as $dictionary) $dictionary->removeNotice($this);
        if(NULL !== $dictionaries) $this->dictionaries = $dictionaries;
        else $this->dictionaries = new ArrayCollection();
        foreach($this->dictionaries as $dictionary) $dictionary->addNotice($this);
        
        return $this;
    }
    
    public function addDictionary(Dictionary $dictionary) {
        
        $this->dictionaries->add($dictionary);
        $dictionary->addNotice($this);
        
        return $this;
    }
    
    public function removeDictionary(Dictionary $dictionary) {
        
        $this->dictionaries->removeElement($dictionary);
        $dictionary->addNotice($this);
        
        return $this;
    }
    
    /**
     * Returns Value::value by given type or null if not found;
     * 
     * @param Type|String $type
     * @return string|NULL
     */
    public function get($property)
    {
        if(is_string($property)) {
            $property = $this->type->hasProperty($property);
        }
        
        if($property instanceof PropertyType) {
            
            $val = $this->getValueByProperty($property);
            
            return $val ? ($val->getValue() ? $val->getValue() : NULL) : NULL;
        }
        
        return NULL;
    }
    
    public function releaseReviews() {
        
        foreach($this->reviews as $review) $review->unsetAboutNotice();
        
        return $this;
    }
}