<?php

namespace Fenchy\RegularUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

use Fenchy\UserBundle\Entity\User;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\GalleryBundle\Entity\Gallery;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Table(name="neighborhoodmsg")
 * @ORM\Entity(repositoryClass="Fenchy\RegularUserBundle\Entity\NeighborhoodMsgRepository")
 */
class NeighborhoodMsg
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var User $user; 
     * @ORM\ManyToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="neighborhoodmsg")
     */
    private $user;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    
    public $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    
    /**
     * @var \DateTime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $created_at;
    
    /**
     * @var string $title
     * @Gedmo\Slug(fields={"title"}, updatable=true, separator="-")
     * @ORM\Column(type="string", length=255, nullable=true)
     */    
    private $slug;

    /**
     * @var ArrayCollection $request
     *
     * @ORM\OneToMany(targetEntity="Fenchy\NoticeBundle\Entity\Request", mappedBy="aboutNeighborhoodMsg")
     * @ORM\OrderBy({"created_at"="DESC"})
     */
    private $requests;
    
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
     * Set title
     *
     * @param string $title
     * @return NeighborhoodMsg
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return NeighborhoodMsg
     */
    public function setContent($content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set user
     *
     * @param Fenchy\UserBundle\Entity\User $user
     * @return NeighborhoodMsg
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return NeighborhoodMsg
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
     * Set slug
     *
     * @param string $slug
     * @return NeighborhoodMsg
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->requests = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add requests
     *
     * @param Fenchy\NoticeBundle\Entity\Request $requests
     * @return NeighborhoodMsg
     */
    public function addRequest(\Fenchy\NoticeBundle\Entity\Request $requests)
    {
        $this->requests[] = $requests;
    
        return $this;
    }

    /**
     * Remove requests
     *
     * @param Fenchy\NoticeBundle\Entity\Request $requests
     */
    public function removeRequest(\Fenchy\NoticeBundle\Entity\Request $requests)
    {
        $this->requests->removeElement($requests);
    }

    /**
     * Get requests
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getRequests()
    {
        return $this->requests;
    }
}