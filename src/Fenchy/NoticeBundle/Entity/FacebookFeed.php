<?php

namespace Fenchy\NoticeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fenchy\NoticeBundle\Entity\FacebookFeed
 *
 * @ORM\Table(name="notice__facebook_feed")
 * @ORM\Entity(repositoryClass="Fenchy\NoticeBundle\Entity\FacebookFeedRepository")
 */
class FacebookFeed
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $fbUserId
     *
     * @ORM\Column(name="fbUserId", type="string", length=20)
     */
    private $fbUserId;

    /**
     * @var string $photoId
     *
     * @ORM\Column(name="photoId", type="string", length=20, nullable=true)
     */
    private $photoId;

    /**
     * @var string $postId
     *
     * @ORM\Column(name="postId", type="string", length=35)
     */
    private $postId;

    /**
	 * @ORM\ManyToOne(targetEntity="Notice", inversedBy="facebook_feed")
	 * @ORM\JoinColumn(name="notice_id", referencedColumnName="id")
	 */
    protected $notice;

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
     * Set fbUserId
     *
     * @param string $fbUserId
     * @return FacebookFeed
     */
    public function setFbUserId($fbUserId)
    {
        $this->fbUserId = $fbUserId;
    
        return $this;
    }

    /**
     * Get fbUserId
     *
     * @return string 
     */
    public function getFbUserId()
    {
        return $this->fbUserId;
    }

    /**
     * Set photoId
     *
     * @param string $photoId
     * @return FacebookFeed
     */
    public function setPhotoId($photoId)
    {
        $this->photoId = $photoId;
    
        return $this;
    }

    /**
     * Get photoId
     *
     * @return string 
     */
    public function getPhotoId()
    {
        return $this->photoId;
    }

    /**
     * Set postId
     *
     * @param string $postId
     * @return FacebookFeed
     */
    public function setPostId($postId)
    {
        $this->postId = $postId;
    
        return $this;
    }

    /**
     * Get postId
     *
     * @return string 
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * Set notice
     *
     * @param Fenchy\NoticeBundle\Entity\Notice $notice
     * @return FacebookFeed
     */
    public function setNotice(\Fenchy\NoticeBundle\Entity\Notice $notice = null)
    {
        $this->notice = $notice;
    
        return $this;
    }

    /**
     * Get notice
     *
     * @return Fenchy\NoticeBundle\Entity\Notice 
     */
    public function getNotice()
    {
        return $this->notice;
    }
}