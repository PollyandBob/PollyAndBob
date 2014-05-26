<?php
namespace Fenchy\GalleryBUndle\Services;

use Fenchy\GalleryBundle\Entity\Gallery;

/**
 * Main purpose of this service is to manage galleries
 * 
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class GalleryManager {
    
    /**
     * Maximum number of images in gallery. Set automatically depending on gallery type.
     * @var integer
     */
    protected $max = 4;
    
    protected $request;
    
    /**
     * Some gallery modification needs to update user trust points.
     * 
     * @var Fenchy\UserBundle\Services\ReputationCounter
     */
    protected $reputation_counter;
    
    /**
     * Maximum number of images in typed gallery.
     * @var integer
     */
    protected $notice_max;
    protected $about_max;
    protected $profile_max;

    public function __construct($request, $doctrine, $file_uploader, $reputation_counter, $notice_max, $about_max, $profile_max) {
        
        $this->request = $request;
        $this->file_uploader = $file_uploader;
        $this->doctrine = $doctrine;
        $this->reputation_counter = $reputation_counter;
        $this->notice_max = $notice_max;
        $this->about_max = $about_max;
        $this->profile_max = $profile_max;
    }
    
    /**
     * Manages gallery forms. Should be called in every action which contains gallery.
     * $valid parameter should be set only for POST actions. Determinates what should be done
     * with gallery and which gallery should be displayed (original or tmp).
     * 
     * @param \Fenchy\GalleryBundle\Entity\Gallery $gallery
     * @param MIXED $valid TRUE|FALSE|NULL
     * @return boolean
     * @throws Exception
     */
    public function manageGallery(Gallery $gallery, $valid = NULL) {
        
        // manage saved gallery
        if($this->request->isMethod('POST')) {

            // if form is valid we need to promote tmp gallery to original
            if(TRUE === $valid) {

                $tmp = $gallery->getTmp();
                if(!$tmp) throw new Exception('Temp gallery NOT found.');

                $this->updateGallery($tmp);

                return TRUE;
            }
            
            // if form is not valid we need to display tmp gallery with already uploaded images.
            elseif(FALSE === $valid) {
                
                $tmp = $gallery->getTmp();
                if(!$tmp) throw new Exception('Temp gallery NOT found.');
                $this->setMax($gallery);
                
                // return data needed for gallery view
                return array(
                    'gallery' => $tmp,
                    'max' => $this->max,
                    'galleryType' => $gallery->getNotice() ? 'big': 'small'
                );
            }
            // Throw exception if $valid value is not supported
            else {
                throw new Exception('Validation status is needed to manage gallery.');
            }
        // manage display gallery 
        } else {
            
            // find and set maximum number of photos for given gallery
            $this->setMax($gallery);

            // we need to clone gallery to keep db and hd images consistent.
            $tmp = $this->createTmp($gallery);

            // return data needed for gallery view
            return array(
                'gallery' => $tmp,
                'max' => $this->max,
                'galleryType' => $gallery->getNotice() ? 'big': 'small'
                );
        }
    }
    
    /**
     * Throws exception if gallery is not assigned to notice or regular user.
     * Sets gallery images limit depends on it's type.
     * @param Gallery $gallery
     */
    protected function setMax($gallery) {

        //set images limit
        if($gallery->getOriginal()) {
            if($gallery->getOriginal()->getNotice()) $this->max = $this->notice_max;
            elseif($gallery->getOriginal()->getRegularUser()) $this->max = $this->about_max;
            elseif($gallery->getOriginal()->getUser()) $this->max = $this->profile_max;
            else {
                throw new Exception('Unassigned gallery: '.$gallery->getOriginal()->getId());
            }
        } else {
            if($gallery->getNotice()) $this->max = $this->notice_max;
            elseif($gallery->getRegularUser()) $this->max = $this->about_max;
            elseif($gallery->getUser()) $this->max = $this->profile_max;
            else {
                throw new \Exception('Unassigned gallery: '.$gallery->getId());
            }
        }
    }
    
    /**
     * Creates tmp copy of given gallery. Created copy will be inserted into DB.
     * Tmp gallery replace previous tmp gallery if exists.
     * Uses punkAve to manage temp files.
     * Throws exception if given $gallery is a tmp one already.
     * 
     * @param \Fenchy\GalleryBundle\Entity\Gallery $gallery
     * @return \Fenchy\GalleryBundle\Entity\Gallery
     */
    protected function createTmp(Gallery $gallery) {
        
        if($gallery->isTmp()) {
            $this->createNotFoundException('Original gallery expected, but tmp given.');
        }
        
        $oldTmpImgs = $gallery->createTmp();
        $tmp = $gallery->getTmp();
        
        $em = $this->doctrine->getManager();
        foreach($oldTmpImgs as $img) $em->remove($img);
        $em->persist($tmp);
        $em->flush();

        $this->file_uploader->syncFiles(
                array(
                    'from_folder'       => $gallery->getFolder(), 
                    'to_folder'         => $tmp->getFolder(),
                    'remove_to_folder'  => TRUE,
                    'create_to_folder'  => TRUE,
                    'max_number_of_files' => $this->max
                    )
                );
        
        return $tmp;
    }
    
    /**
     * Updates user/notice gallery, by replacing original gallery with tmp one.
     * Moves temp gallery directory to destination folder.
     * Does NOT remove old gallery folder. It should be removed e.g. by lifeCycle.
     * 
     * @param \Fenchy\GalleryBundle\Entity\Gallery $tmp
     */
    protected function updateGallery(Gallery $tmp) {
        
        if(!$tmp->isTmp()) {
            throw $this->createNotFoundException('Tmp gallery expected, but original given.');
        }
        
        $gallery = $tmp->getOriginal();
        
        if(!$gallery) {
            throw $this->createNotFoundException('Original gallery not found.');
        }

        
        // cleanup galleries
        $folders = array(
                    'from_folder'       => $tmp->getFolder(),
                    'to_folder'         => $gallery->getFolder()
                    );

        $em = $this->doctrine->getManager();
        $oldImages = $gallery->update();

        foreach($oldImages as $img) $em->remove($img);
//        if($gallery->getRegularUser())
//            $this->reputation_counter->update($gallery->getRegularUser()->getUser());
        //@todo: check if user will be updated this way.
        $em->persist($gallery);
        $em->flush();
        
        $this->file_uploader->updateGallery($folders);

    }
}
