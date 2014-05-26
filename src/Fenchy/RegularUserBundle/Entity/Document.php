<?php

namespace Fenchy\RegularUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Fenchy\UserBundle\Entity\User;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\GalleryBundle\Entity\Gallery;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Table(name="document")
 * @ORM\Entity(repositoryClass="Fenchy\RegularUserBundle\Entity\DocumentRepository")
 */
class Document
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\Column(type="integer", length=255, nullable=true)
     * @Assert\NotBlank
     */
    public $user_id;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cropX;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cropY;
    
 
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
    
    public function setCropX($cropX)
    {
    	return $this->cropX = $cropX;
    }
    
    public function getCropX()
    {
    	return $this->cropX;
    }
    
    public function setCropY($cropY)
    {
    	return $this->cropY = $cropY;
    }
    
    public function getCropY()
    {
    	return $this->cropY;
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


    
}