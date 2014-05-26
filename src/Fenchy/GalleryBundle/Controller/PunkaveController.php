<?php

namespace Fenchy\GalleryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Fenchy\GalleryBundle\Entity\Image;
use Fenchy\GalleryBundle\Entity\Gallery;

/**
 * Punk Ave Gallery controller.
 */
class PunkaveController extends Controller
{
    
    protected $max = 4;
    /**
     * @Template
     * @param integer $id gallery id
     * @return array
     */
    public function editAction ($id) {
        
        if($this->getRequest()->isMethod('POST')) {

            $tmp = $this->getGallery($id);
            
            $this->updateGallery($tmp);

            //redirect
            return $this->redirect($this->generateUrl('fenchy_frontend_indexv2'));
            
        } else {

            $gallery = $this->getGallery($id);

            // we need to clone gallery to keep db and hd images consistent.
            //@todo: check if image entities are copied correctly
            $tmp = $this->createTmp($gallery);

            return array(
                'gallery' => $tmp,
                'max' => $this->max
                );
        }
    }
    
    /**
     * @Template
     * @param integer $id the gallery ID
     */
    public function showAction($id) {
        
        return array(
            'gallery' => $this->getGallery($id)
        );
    }
    
    
    public function cropAction() {
        
        $request = $this->getRequest();
        $name = $request->get('name');
        if(!$name) {
            echo json_encode(array('status' => FALSE, 'type' => 'a'));
            exit;
        }
        
        $x = $request->get('x');
        $y = $request->get('y');
        $w = $request->get('w');
        $h = $request->get('h');
        
        if(!is_numeric($x) || !is_numeric($y) || !is_numeric($w) || !is_numeric($h)) {
            echo json_encode(array('status' => FALSE, 'type' => 'b'));
            exit;
        }
        
        $image = $this->getDoctrine()->getManager()
                    ->getRepository('FenchyGalleryBundle:Image')
                    ->getTmpByName($name);
        
        if(!$image) {
            echo json_encode(array('status' => FALSE, 'type' => $name));
            exit;
        }

	if($this->get('punk_ave.file_uploader')->cropImage($image, $x, $y, $w, $h)) {
            
            $em = $this->getDoctrine()->getManager();
            $image->setCrop(TRUE);
            $em->persist($image);
            $em->flush();
            
            echo json_encode(array(
                    'status'    => TRUE,
                    'small'     => $image->getAvatar(),
                    'big'       => $image->getAvatar(FALSE),
                    'original'  => $image->getSrc('originals'),
                    'name'      => $image->getName()
                ));
        } else {
            echo json_encode(array('status' => FALSE, 'type' => 'd'));
        }
        exit;
    }
    
    /**
     * 
     * @param id $tmpGalleryId
     */
    public function uploadAction ($tmpGalleryId) {

        $gallery = $this->getGallery($tmpGalleryId);

        $data = $this->get('punk_ave.file_uploader')->handleFileUpload(
                array(
                    'folder' => $gallery->getFolder(),
                    'max' => $this->max
                    )
                );
        
        $action = $data['action'];
        unset($data['action']);
        switch($action) {
            case 'upload':
                foreach($data as $file) {
//                $gallery = new Gallery();
                    if($gallery->getImagesQuantity() >= $this->max) break;
                    $ext = substr($file->name,strripos($file->name,'.'));
                    if(strcasecmp($ext,'.jpeg')== 0 || strcasecmp($ext, '.jpg')==0 || strcasecmp($ext,'.gif') || strcasecmp($ext, '.png') )
                    {
                        $img = new Image();
                        $img->setName($file->name);
                        $gallery->addImage($img);
                    }
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($gallery);
                $em->flush();
                break;
            case 'delete':
                $em =  $this->getDoctrine()->getManager();
                $repo = $em
                    ->getRepository('FenchyGalleryBundle:Image');
                foreach($data as $file) {
                    if($file) {
                        $img = $repo->getByGalleryAndName($tmpGalleryId, $file->name);
                        if($img) {
                            $em->remove($img);
                        }
                    }
                }
                $em->flush();
                break;
        }
        exit(0);
    }
    
    /**
     * Returns gallery by given $id.
     * Throws exception if gallery not found.
     * Sets gallery images limit depends on it's type.
     * @param integer $id
     */
    protected function getGallery($id) {
        
        $gallery = $this->getDoctrine()
                ->getEntityManager()
                ->getRepository('FenchyGalleryBundle:Gallery')
                ->findWithOriginal($id);

        if(NULL === $gallery) {
            throw $this->createNotFoundException('Gallery not found: '.$id);
        }

        //set images limit
        if($gallery->getOriginal()) {
            if($gallery->getOriginal()->getNotice()) $this->max = $this->container->getParameter('gallery_notice_max');
            elseif($gallery->getOriginal()->getRegularUser()) $this->max = $this->container->getParameter('gallery_about_me_max');
            elseif($gallery->getOriginal()->getUser()) $this->max = $this->container->getParameter('gallery_profile_max');
            else {
                throw $this->createNotFoundException('Unassigned gallery: '.$gallery->getOriginal()->getId());
            }
        } else {
            if($gallery->getNotice()) $this->max = $this->container->getParameter('gallery_notice_max');
            elseif($gallery->getRegularUser()) $this->max = $this->container->getParameter('gallery_about_me_max');
            elseif($gallery->getUser()) $this->max = $this->container->getParameter('gallery_profile_max');
            else {
                throw $this->createNotFoundException('Unassigned gallery: '.$gallery->getId());
            }
        }
        return $gallery;
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
        
        $em = $this->getDoctrine()->getManager();
        foreach($oldTmpImgs as $img) $em->remove($img);
        $em->persist($tmp);
        $em->flush();

        $this->get('punk_ave.file_uploader')->syncFiles(
                array(
                    'from_folder'       => $gallery->getFolder(), 
                    'to_folder'         => $tmp->getPath(),
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

        $em = $this->getDoctrine()->getManager();
        $oldImages = $gallery->update();
        foreach($oldImages as $img) $em->remove($img);
        $em->persist($gallery);
        $em->flush();
        
        $this->get('punk_ave.file_uploader')->updateGallery($folders);

    }
}
