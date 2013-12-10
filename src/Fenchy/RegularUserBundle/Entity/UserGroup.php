<?php

namespace Fenchy\RegularUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

use Fenchy\UserBundle\Entity\User;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\GalleryBundle\Entity\Gallery;
use Fenchy\RegularUserBundle\Entity\GroupMembers;
use Fenchy\UtilBundle\Entity\Location;
use Fenchy\NoticeBundle\Entity\Notice;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Table(name="user__group")
 * @ORM\Entity(repositoryClass="Fenchy\RegularUserBundle\Entity\UserGroupRepository")
 */
class UserGroup
{
	const STATUS_OPEN   = 1;
	const STATUS_CLOSED = 2;
	const OPEN          = 'open';
	const CLOSED        = 'closed';
	
	static public $statusMap = array(
			self::STATUS_OPEN   => self::OPEN,
			self::STATUS_CLOSED => self::CLOSED,
	);
	
	
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var User $user; 
     * @ORM\ManyToOne(targetEntity="Fenchy\UserBundle\Entity\User", inversedBy="user_group")
     */
    private $user;
    
    /**
     * @var string $groupname
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $groupname;

    /**
     * @var string aboutGroup
     * @ORM\Column(name="about_group", type="text", nullable=true)
     * @Assert\MaxLength(1000)
     */
    private $aboutGroup;  
    
	/**
     * @var integer $status
     * 
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $status;
    
    /**
     * Logged User
     * @var ArrayCollection $usergroup
     *
     * @ORM\OneToMany(targetEntity="Fenchy\RegularUserBundle\Entity\GroupMembers", mappedBy="current", cascade={"remove"})
     * 
     */
    private $usergroup;
    
    
    /**
     * @var ArrayCollection $messages
     *
     * @ORM\OneToMany(targetEntity="Fenchy\MessageBundle\Entity\Message", mappedBy="usergroup")
     */
    private $messages;
    
    /**
     * @var Location $location
     *
     * @ORM\OneToOne(targetEntity="Fenchy\UtilBundle\Entity\Location", mappedBy="usergroup", cascade={"persist"})
     */
    protected $location;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path2;
    
    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;
    
    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file2;
    
    /**
     * @var \DateTime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $created_at;
    
    /**
     * @var integer $locSave
     *
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    private $locSave;
    
    /**
     * @var ArrayCollection $comments
     *
     * @ORM\OneToMany(targetEntity="Fenchy\NoticeBundle\Entity\Comment", mappedBy="aboutUserGroup")
     * @ORM\OrderBy({"created_at"="DESC"})
     */
    private $comments;
    
    /**
     * @var ArrayCollection $notices
     *
     * @ORM\OneToMany(targetEntity="Fenchy\NoticeBundle\Entity\Notice", mappedBy="usergroup", cascade={"remove", "persist"})
     * @ORM\OrderBy({"created_at"="DESC"})
     */
    private $notices;
    
    protected $prevNoticesQuantity = FALSE;
    
    public function __construct()
    {
    	
    	$this->user       = new ArrayCollection();
    	$this->usergroup  = new ArrayCollection();    	
    	$this->messages   = new ArrayCollection();
    	$this->comments   = new ArrayCollection();
    	$this->notices   = new ArrayCollection();
    }
    
    public function getId()
    {
    	return $this->id;
    }
    
    public function setId($id)
    {
    	return $this->id = $id;
    }
    
    /**
     * Set Comment
     * @param \Doctrine\Common\Collections\ArrayCollection $comments
     * @return \Fenchy\RegularUserBundle\Entity\UserGroup
     */
    public function setComments(ArrayCollection $comments) {
    
    	$this->comments = $comments;
    
    	return $this;
    }   

    public function getComments() {
    
    	return $this->comments;
    }
    
    /**
     * Sets prevNoticesQuantity value to current notices quantity.
     * This function should be called each time when one of user listings draft status must be changed (before it).
     * called in Fenchy\NoticeBundle\Entity\Notice::setDraft();
     */
    public function countPrevNoticesQuantity() {
    
    	$this->prevNoticesQuantity = $this->getNonDraftNoticesCount();
    }
    /**
     * Set Notices. Clones user location into Notices which has no location.
     * @param ArrayCollection $notices
     * @return UserGroup
     */
    public function setNotices(ArrayCollection $notices) {
    
    	$this->prevNoticesQuantity = $this->getNonDraftNoticesCount();
    	// set notices location
    	foreach($notices as $notice) {
    
    		if(!$notice->getLocation()) {
    			$notice->setLocation(clone $this->getLocation());
    		}
    	}
    
    	$this->notices = $notices;
    
    	return $this;
    }
    
    /**
     * Add Notice. Clones user location into Notice if notice has no set location.
     * @param Notice $notice
     * @return UserGroup
     */
    public function addNotice(Notice $notice) {
    
    	if(!$notice->getLocation()) {
    		$notice->setLocation(clone $this->getLocation());
    	}
    
    	$this->prevNoticesQuantity = $this->getNonDraftNoticesCount();
    	$this->notices->add($notice);
    
    	return $this;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getNotices() {
    
    	return $this->notices;
    }
    
    public function setCreatedAt($created_at)
    {
    	$this->created_at = $created_at;
    
    	return $this;
    }
    
    public function getCreatedAt()
    {
    	return $this->created_at;
    }  
    
    public function setUserGroup(ArrayCollection $usergroup)
    {
    	$this->usergroup = $usergroup;
    }
    
    public function setMessages(ArrayCollection $messages) {
    
    	$this->messages = $messages;
    
    	return $this;
    }
    
    public function getMessages()
    {
    	return $this->messages();
    }  
    
    public function getGroupname()
    {
    	return $this->groupname;
    }
    
    
    public function setGroupname($name)
    {
    	$this->groupname = $name;
    }
    

    public function getAboutGroup()
    {
    	return $this->aboutGroup;
    }
    
    
    public function setAboutGroup($name)
    {
    	$this->aboutGroup = $name;
    }
    
    public function getStatus()
    {
    	return $this->status;
    }
    
    
    public function setStatus($status)
    {
    	$this->status = $status;
    }
    
    public function setLocation(Location $location) {
    
    	$this->location = $location->setUserGroup($this);
    
    	return $this;
    }
    
    public function getLocation() {
    
    	!$this->location && $this->setLocation(new Location());
    
    	return $this->location;
    }
 
    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDirPath().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDirPath().'/'.$this->path;
    }
    
    public function getAbsolutePath2()
    {
    	return null === $this->path2
    	? null
    	: $this->getUploadRootDirPath2().'/'.$this->path2;
    }
    
    public function getWebPath2()
    {
    	return null === $this->path2
    	? null
    	: $this->getUploadDirPath2().'/'.$this->path2;
    }

    protected function getUploadRootDirPath()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDirPath();
    }
    
    protected function getUploadRootDirPath2()
    {
    	// the absolute directory path where uploaded
    	// documents should be saved
    	return __DIR__.'/../../../../web/'.$this->getUploadDirPath2();
    }

    protected function getUploadDirPath()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/profileimages';
    }
    
    protected function getUploadDirPath2()
    {
    	// get rid of the __DIR__ so it doesn't screw up
    	// when displaying uploaded doc/image in the view.
    	return 'uploads/coverimages';
    }
    
    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
    	$this->file = $file;
    }
    
    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
    	return $this->file;
    }
    
    /**
     * Sets file2.
     *
     * @param UploadedFile $file2
     */
    public function setFile2(UploadedFile $file2 = null)
    {
    	$this->file2 = $file2;
    }
    
    /**
     * Get file2.
     *
     * @return UploadedFile
     */
    public function getFile2()
    {
    	return $this->file2;
    }
    
    public function upload()
    {
    	// the file property can be empty if the field is not required
    	if (null === $this->getFile()) {
    		return;
    	}
    
    	// use the original file name here but you should
    	// sanitize it at least to avoid any security issues
    
    	// move takes the target directory and then the
    	// target filename to move to
    	$this->getFile()->move(
    			$this->getUploadRootDirPath(),
    			time()."_".$this->getFile()->getClientOriginalName()
    	);
    
    	// set the path property to the filename where you've saved the file
    	$this->path = time()."_".$this->getFile()->getClientOriginalName();
    
    	// clean up the file property as you won't need it anymore
    	$this->file = null;
    }
    
    public function uploadcover()
    {
    	// the file property can be empty if the field is not required
    	if (null === $this->getFile2()) {
    		return;
    	}
    
    	// use the original file name here but you should
    	// sanitize it at least to avoid any security issues
    
    	// move takes the target directory and then the
    	// target filename to move to
    	$this->getFile2()->move(
    			$this->getUploadRootDirPath2(),
    			time()."_".$this->getFile2()->getClientOriginalName()
    	);
    
    	// set the path property to the filename where you've saved the file
    	$this->path2 = time()."_".$this->getFile2()->getClientOriginalName();
    
    	// clean up the file property as you won't need it anymore
    	$this->file2 = null;
    }
    
    /**
     *
     * @return User
     */
    public function getUser()
    {
    	return $this->user;
    }
    
    public function setUser(User $user)
    {
    	$this->user = $user;
    }
    
    /**
     * Set path
     *
     * @param string $path
     * @return UserGroup
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set path2
     *
     * @param string $path2
     * @return UserGroup
     */
    public function setPath2($path2)
    {
        $this->path2 = $path2;
    
        return $this;
    }

    /**
     * Get path2
     *
     * @return string 
     */
    public function getPath2()
    {
        return $this->path2;
    }

    /**
     * Set locSave
     *
     * @param integer $locSave
     * @return UserGroup
     */
    public function setLocsave($locSave)
    {
        $this->locSave = $locSave;
    
        return $this;
    }

    /**
     * Get locSave
     *
     * @return integer 
     */
    public function getLocsave()
    {
        return $this->locSave;
    }

    /**
     * Add usergroup
     *
     * @param Fenchy\RegularUserBundle\Entity\GroupMembers $usergroup
     * @return UserGroup
     */
    public function addUsergroup(\Fenchy\RegularUserBundle\Entity\GroupMembers $usergroup)
    {
        $this->usergroup[] = $usergroup;
    
        return $this;
    }

    /**
     * Remove usergroup
     *
     * @param Fenchy\RegularUserBundle\Entity\GroupMembers $usergroup
     */
    public function removeUsergroup(\Fenchy\RegularUserBundle\Entity\GroupMembers $usergroup)
    {
        $this->usergroup->removeElement($usergroup);
    }

    /**
     * Get usergroup
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUserGroup()
    {
        return $this->usergroup;
    }

    /**
     * Add messages
     *
     * @param Fenchy\MessageBundle\Entity\Message $messages
     * @return UserGroup
     */
    public function addMessage(\Fenchy\MessageBundle\Entity\Message $messages)
    {
        $this->messages[] = $messages;
    
        return $this;
    }

    /**
     * Remove messages
     *
     * @param Fenchy\MessageBundle\Entity\Message $messages
     */
    public function removeMessage(\Fenchy\MessageBundle\Entity\Message $messages)
    {
        $this->messages->removeElement($messages);
    }

    /**
     * Add comments
     *
     * @param Fenchy\NoticeBundle\Entity\Comment $comments
     * @return UserGroup
     */
    public function addComment(\Fenchy\NoticeBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;
    
        return $this;
    }

    /**
     * Remove comments
     *
     * @param Fenchy\NoticeBundle\Entity\Comment $comments
     */
    public function removeComment(\Fenchy\NoticeBundle\Entity\Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Remove notices
     *
     * @param Fenchy\NoticeBundle\Entity\Notice $notices
     */
    public function removeNotice(\Fenchy\NoticeBundle\Entity\Notice $notices)
    {
        $this->notices->removeElement($notices);
    }
}