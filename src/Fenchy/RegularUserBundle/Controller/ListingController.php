<?php

namespace Fenchy\RegularUserBundle\Controller;

use Fenchy\RegularUserBundle\Entity\UserGroup;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Fenchy\NoticeBundle\Form\NoticeDeleteType,
    Fenchy\NoticeBundle\Entity\Notice,
    Fenchy\NoticeBundle\Form\NoticeListingType,
    Fenchy\NoticeBundle\Entity\Review;
use Fenchy\RegularUserBundle\Entity\Document;
use Fenchy\RegularUserBundle\Entity\NeighborhoodMsg;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Fenchy\UserBundle\Entity\NotificationGroupInterval;
use Fenchy\UserBundle\Entity\NotificationQueue;


use Fenchy\UserBundle\Entity\User;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\GalleryBundle\Entity\Image;
use Paymill\Request;
use Paymill\Models\Request\Preauthorization;
use Paymill\Models\Request\Transaction;
use Fenchy\MessageBundle\Entity\Message;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraints\Time;
use Doctrine\Common\Collections\ArrayCollection;
use \RequestEnvelope;
use \PreapprovalRequest;
use \ConfirmPreapprovalRequest;
use AdaptivePaymentsService;
use \Configuration;
use \ExecutePaymentRequest;

/**
 * This controller should manage listings (notices) and all other operations should not be here. * 
 * 
 */
class ListingController extends Controller
{
    
    /**
     * This simple action returns list of all available types. One type should be
     * choosen for step 2 of creation process (notice form)
     * 
     * @return Response
     */
    public function create1Action() {
        
    	$user = $this->get('security.context')->getToken()->getUser();
    	$em = $this->getDoctrine()->getManager();
    	$alertmsg = true;
    	$managertype = $em->getRepository('FenchyRegularUserBundle:UserRegular')
    		->getManagerType($user);
    	if($user->getActivity() < 400 and $managertype[0] !="pioneer" )
	    {
    		$alertmsg = false;
    	}
    	$groupId=$this->getRequest()->get('groupId');
        $direction = $this->container->getParameter('notice_menu_property');
        $em = $this->getDoctrine()->getManager();
        $types = $em->getRepository('FenchyNoticeBundle:Type')->getAllWithProperties();

        return $this->render(
                'FenchyRegularUserBundle:Listing:create1.html.twig', 
                array(
                    'types' => $types,
                    'direction' => $direction,
                	'alertmsg'=> $alertmsg,
                	'groupId' => $groupId,	
                ));
    }
    
    /**
     * This is action for second (final) step of listing creation process.
     * Renders notice form.
     * 
     * @param string $typename
     * @param string $direction
     * @throws Exception
     * 
     * @return Response
     */
    public function create2Action($typename, $direction) {

        $em = $this->getDoctrine()->getManager();
        $groupIdbyGroup=$this->getRequest()->get('groupId');
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        $location = $user->getLocation();
        $payment = false;
        $success = false;
        $paymentsetting = $em->getRepository('UserBundle:Payment')->checkUser($user);        
        if($paymentsetting)
        	$payment = true;
        else 
        	$payment = false;
        	
        $type = $em->getRepository('FenchyNoticeBundle:Type')->getByNameWithProperties($typename);
        $request = $this->getRequest();
    	$groupId = $request->get('group_ids');
    	$val = $this->get('translator')->trans('regularuser.only_community_Neighborhood_manager');
    	$alertmsg = '';
    	
    	
    	if($typename=='offergroups')
    	{	
    		$managertype = $em->getRepository('FenchyRegularUserBundle:UserRegular')
    		->getManagerType($user);
    		
    		//if ($managertype[1]!='C' || $managertype[1]!='N' || $managertype[2]!='violet') return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
    		
	    	if($user->getActivity() < 400 and $managertype[0] !="pioneer" )
	    	{	
	    		$alertmsg =  "alert('".$val."');";
	    	}
	    	
    	}
    	$saveByUsergroup = null;
    	
    	//print_r($saveByUsergroup->getUserGroup());
    	
		if($groupId)
		{	
			$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);
		}
		else
		{
			$usergroup= "";
		}
		
		if (!$usergroup) {
			$usergroup = new UserGroup();
		}
        
        $form_ugroup = $this->createFormBuilder($usergroup)
        ->add('groupname', null, array( 'max_length'=>25,
          	  'attr' => array('placeholder' => 'regularuser.your_groupname')))
        ->add('aboutGroup', 'textarea', array(
              'attr' => array('placeholder' => 'regularuser.your_aboutgroup')))
        ->add('status', 'choice', array(
        	  'label' => 'regularuser.status',
        	  'choices' => UserGroup::$statusMap))
        ->add('file',null,array('label' => 'settings.general.profile_photo'))
        ->add('file2',null,array('label' => 'settings.general.cover_photo'))
        ->add('cropX','hidden')
		->add('cropY','hidden')
        ->getForm();

        $neighborsNext50 = $this->neighborsNext50();
        $image_gallery = false;
        // render form
        if (!$this->getRequest()->isMethod('POST')) {

           
            // $notice needs to be stored as draft (gallery must point on something)
            $notice = $this->createDraft($user);
            $notice->setType($type);
            $em->persist($notice);
            $em->flush();
            
             // create notice form for specified Type
            $form = $this->createForm(new NoticeListingType($type, $notice), $notice);
            
            // In case we want to display notice gallery we need to manage it together
            // with notice. View data needed for gallery is returned by service.
            $data = $this->get('fenchy.gallery_manager')->manageGallery($notice->getGallery());
            $images = $notice->getGallery()->getImages();
            
            if($this->get('session')->getFlash('image_gallery'))
            {
                $olddata = $notice->getGallery()->getId()-2;
                $appUrl = explode('/',$this->get('kernel')->getRootDir());
                    unset($appUrl[count($appUrl)-1]);
                    $baseDir = implode('/',$appUrl);
                $folder = $baseDir.'/web/uploads/tmp/images/'.$olddata;
                
                //$em->getRepository('FenchyGalleryBundle:Image')->findbyGallery($notice->getGallery()->getId());
                if(file_exists($folder))
                {
                    $srcDir = opendir($folder.'/medium');
                        while($readFile = readdir($srcDir))
                        {
                            $image_gallery = true;
                            if($readFile != '.' && $readFile != '..' && substr($readFile, 0,1)!= '.')
                            {
                                if (!file_exists($baseDir.'/web/uploads/tmp/'.$notice->getGallery()->getFolder().'/medium/'.$readFile)) 
                                {
                                        $img = new Image();
                                        $img->setName($readFile);
                                        $notice->getGallery()->addImage($img);
                                }
                            }
                        }
                   $data = $this->get('fenchy.gallery_manager')->manageGallery($notice->getGallery());
                   $images = $notice->getGallery()->getImages();
                   
                   $notice->getGallery()->setTmp($notice->getGallery());
                   $t = $this->get('punk_ave.file_uploader')->createFolder(
                        array(
                            'folder' => $notice->getGallery()->getFolder(),
                            'max' => 4
                            )
                        );

                        $srcDir = opendir($folder.'/medium');
                        while($readFile = readdir($srcDir))
                        {
                            if($readFile != '.' && substr($readFile, 0,1) != '.' && $readFile != '..')
                            {
                                if (!file_exists($baseDir.'/web/uploads/'.$notice->getGallery()->getFolder().'/medium/'.$readFile)) 
                                {
                                    if(copy($folder.'/medium/' . $readFile, $baseDir.'/web/uploads/'.$notice->getGallery()->getFolder().'/medium/'.$readFile))
                                    {
                                        
                                    }                                    
                                }
                            }
                        }
                        closedir($srcDir); 
                        $srcDir = opendir($folder.'/originals');
                        while($readFile = readdir($srcDir))
                        {
                            if($readFile != '.' && $readFile != '..' && substr($readFile, 0,1) != '.')
                            {
                                if (!file_exists($baseDir.'/web/uploads/'.$notice->getGallery()->getFolder().'/originals/'.$readFile)) 
                                {
                                    if(copy($folder.'/originals/' . $readFile, $baseDir.'/web/uploads/'.$notice->getGallery()->getFolder().'/originals/'.$readFile))
                                    {
                                        //echo "Copy file";
                                    }                                    
                                }
                            }
                        }
                        closedir($srcDir);
                        $srcDir = opendir($folder.'/thumbnail');
                        while($readFile = readdir($srcDir))
                        {
                            if($readFile != '.' && $readFile != '..' && substr($readFile, 0,1) != '.')
                            {
                                if (!file_exists($baseDir.'/web/uploads/'.$notice->getGallery()->getFolder().'/thumbnail/'.$readFile)) 
                                {
                                    if(copy($folder.'/thumbnail/' . $readFile, $baseDir.'/web/uploads/'.$notice->getGallery()->getFolder().'/thumbnail/'.$readFile))
                                    {
                                      
                                    }                                    
                                }
                            }
                        }
                        closedir($srcDir);
                        //exit;
                }
            }
           
            
            $data['notice']   = $notice;
            $data['images']   = $images;
            $data['form']     = $form->createView();
            $data['form_ugroup'] = $form_ugroup->createView();
            $data['type']     = $type;
            $data['direction'] = $direction;
            $data['location'] = $location;
            $data['tags']   = $this->get('fenchy_dictionary')->getAllListingTags();
            $data['displayUser'] = $user;
            $data['alertmsg'] = $alertmsg;
            $data['neighborsNext50'] = $neighborsNext50;
            $data['payment'] = $payment;
            $data['success'] = $success;
            $data['groupId'] = $groupIdbyGroup;
            $data['image_gallery'] = $image_gallery;
            
            return $this->render(
                'FenchyRegularUserBundle:Listing:create2.html.twig', $data);
        }
        
        // try to stroe notice
        elseif ($this->getRequest()->isMethod('POST')) {
        	
        	
            // Get user's draft notice (there should be one only)
            $notice = $em->getRepository('FenchyNoticeBundle:Notice')->findDraft($user);

            // If current user has no draft then we could create new notice, but we wont
            // We do not like cheaters.
            if(!$notice) {
                throw $this->createNotFoundException('Draft notice not found.');
            }
            
            $form = $this->createForm(new NoticeListingType($type, $notice), $notice);
            
            
            
            
            $form->bind($this->getRequest());
                
            if ($form->isValid()) {
                // Notice is not a draft any more.
                $notice->setDraft(FALSE);
				
                // save info usergroup in notice when create by UserGroup Profile
               
	            if($groupIdbyGroup)
		    	{
		    		$saveByUsergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupIdbyGroup);
		    		$notice->setUserGroup($saveByUsergroup);
		    	}
                
                
                // We need to manually set all Value entities
                $notice->setValues($this->getValuesFromForm($form->get('type'), $direction));
                $tags = $this->get('fenchy_dictionary')->store($notice->getTags(), TRUE);
                $notice->setDictionaries($tags);
                $this->get('fenchy.reputation_counter')->update($user, \Fenchy\UserBundle\Services\ReputationCounter::TYPE_NOTICE);
                $em->persist($user);
                
                // Find some tags in the notice
                $this->get('fenchy_dictionary')->store($notice->getTags(), TRUE);
                
                // And again we need to call gallery manager to handle gallery changes.
                $this->get('fenchy.gallery_manager')->manageGallery($notice->getGallery(), TRUE);
                
                $em->persist($notice);
                $em->flush();
                $success = true;
                // Done :) Let the user see new notice.
                
                $data = $this->get('fenchy.gallery_manager')->manageGallery($notice->getGallery(), FALSE);
                $images = $notice->getGallery()->getImages();
               
                $i=0;
                foreach ($images as $image)
                {

                    $image->setCropX($this->getRequest()->get('cropX'.$i));
                    $image->setCropY($this->getRequest()->get('cropY'.$i));
                    $em->persist($image);
                    $em->flush();
                $i++;
                }
                
                
                $data['notice']   = $notice;
                $data['form']     = $form->createView();
                $data['form_ugroup'] = $form_ugroup->createView();
                $data['type']     = $type;
                $data['direction'] = $direction;
                $data['location'] = $location;
                $data['tags']   = $this->get('fenchy_dictionary')->getAllListingTags();
                $data['displayUser'] = $user;
                $data['alertmsg'] = $alertmsg;
                $data['neighborsNext50'] = $neighborsNext50;
                $data['payment'] = $payment;
                $data['success'] = $success;
                $data['groupId'] = $groupIdbyGroup;
                $data['image_gallery'] = $image_gallery;
                
                return $this->render(
                		'FenchyRegularUserBundle:Listing:create2.html.twig', $data);
                
                $created_at = $notice->getCreatedAt();                         
                
            }
            else {
                // form is invalid so we need to display it again, but gallery 
                // should not be reseted. We need to inform gallery manager that form was invalid.
            	$data = $this->get('fenchy.gallery_manager')->manageGallery($notice->getGallery(), FALSE);
                $data['notice']   = $notice;
                $data['form']     = $form->createView();
                $data['form_ugroup'] = $form_ugroup->createView();
                $data['type']     = $type;
                $data['direction'] = $direction;
                $data['location'] = $location;
                $data['tags']   = $this->get('fenchy_dictionary')->getAllListingTags();
                $data['displayUser'] = $user;
                $data['alertmsg'] = $alertmsg;
                $data['neighborsNext50'] = $neighborsNext50;
                $data['payment'] = $payment;
                $data['success'] = $success;
                $data['groupId'] = $groupIdbyGroup;
                $data['image_gallery'] = $image_gallery;
                
                return $this->render(
                    'FenchyRegularUserBundle:Listing:create2.html.twig', $data);
            }
        }
    }
    
    
    protected function neighborsNext50()
    {
    	$userLoggedIn = $this->get('security.context')->getToken()->getUser();
    
    	$displayUser = $userLoggedIn;
    	$filterService = $this->get('listfilter');
    	$emptyFilter = $returnFilter = $filterService->getFilter();
    	//Get Users info
    	$users = $this->getDoctrine()
    	->getEntityManager()
    	->getRepository('UserBundle:User')
    	->findAllWithStickers($emptyFilter);
    
    	$origin = str_replace(" ", "", $displayUser->getLocation());
    	$currentuid = $displayUser->getId();
    	$request = $this->getRequest();
    
    	$filterdata = "";
    	$users2 = array();
    	foreach ($users as $user)
    	{
    
    		//$avatar = $user->getRegularUser()->getAvatar(false);
    		$em = $this->getDoctrine()->getEntityManager();
    		$neighbor = $em->getRepository('FenchyRegularUserBundle:Neighbors')->findById($user->getId(),$displayUser->getId());
    		if(!$neighbor)
    		{
    			$neighbor = $em->getRepository('FenchyRegularUserBundle:Neighbors')->findById($displayUser->getId(),$user->getId());
    		}
    		$neighbors = '';
    		if($neighbor)
    		{
    			$neighbors = $neighbor->getId();
    		}
                $blockneighbor = $em->getRepository('FenchyRegularUserBundle:BlockedNeighbor')->findById($user->getId(),$displayUser->getId());
		if(!$blockneighbor)
		{
			$blockneighbor = $em->getRepository('FenchyRegularUserBundle:BlockedNeighbor')->findById($displayUser->getId(),$user->getId());
		}
    		if(!$neighbors  && !$blockneighbor)
    		{
    			$document = new Document();
    			$result = $em->getRepository('FenchyRegularUserBundle:Document')->findById($user->getId());
    
    			if($result)
    			{
    				$avatar = $result->getWebPath();
    
    			}
    			else
    			{
    				$avatar = 'images/default_profile_picture.png';
    			}
    				
    			$firstname= $user->getRegularUser()->getFirstname();
    			$destination = str_replace(" ", "", $user->getLocation());
    			$userid= $user->getId();
    
    
    			if($user->getLocation()!="" && $userid!=$currentuid)
    			{
    				
    				$lat = $user->getLocation()->getLatitude();
    				$log = $user->getLocation()->getLongitude();
    				
    				$lat2 = $displayUser->getLocation()->getLatitude();
    				$log2 = $displayUser->getLocation()->getLongitude();
    				
    				$theta = $log - $log2;
    				// Find the Great Circle distance
    				$distance = rad2deg(acos((sin(deg2rad($lat)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)))));
    				$distance = $distance * 60 * 1.1515;
    				$gmap_distance = round(($distance * 1609.34), 0);//miles to meter rounded to 0
    				
    				$mindist = $this->container->getParameter('filter_min_distance_user');// minimum distance
    				$maxdist = $this->container->getParameter('filter_max_distance_user');// maximum distance
    
    				$dist1 = $request->getUri();
    
    				if(strpos($dist1,'dst=') !== false)
    				{
    					$dist1 = explode("dst=",$request->getUri());
    					$dist = $dist1[1]; // slider distance
    				}
    				else
    				{
    					$dist = 30000; // slider distance
    				}
    
	    			
    
    				if($gmap_distance >= $mindist && $gmap_distance < $maxdist && $gmap_distance <= $dist)
    				{
    					// Added By jignesh for Manager type
    
    					if($user->getActivity() >= 400 && $user->getActivity() < 1000)
    					{
    						$managertype = "Community Manager";
    						$managertype_alpha = "C";
    						$classColor = "green";
    					}
    					elseif($user->getActivity() >= 1000)
    					{
    						$managertype = "Neighborhood Manager";
    						$managertype_alpha = "N";
    						$classColor = "orange";
    					}
    					elseif($user->getManagerType()==1)
    					{
    						$managertype = "House Manager";
    						$managertype_alpha = "H";
    						$classColor = "blue";
    					}
    					else
    					{
    						$managertype = "";
    						$managertype_alpha = " ";
    						$classColor = "red";
    					}
    					// Added By jignesh for Manager type
    					$user->setTwitterUsername($avatar);
    					$users2[] = $user;
    
    				}
    			}
    		}
    	}
    
    	return $users2;
    
    }
    
    /**
     * Edit listing (notice) action.
     * 
     * @param integer $id
     * @return Response
     * @throws Exception
     */
    public function editAction($id) {
        
        $em = $this->getDoctrine()->getManager();
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        $notice = $em->getRepository('FenchyNoticeBundle:Notice')->findFullDetailed($id);
        $images = $notice->getGallery()->getImages();
        
        //print_r($notice->getGallery()->getImages());
        if(!$notice || ($notice->getUser()->getId() !== $user->getId() && $notice->getUserGroup() == null)) throw $this->createNotFoundException('Listing does not exists');
        
        // We do not edit drafts.
        if($notice->getDraft()) return $this->redirect($this->generateUrl('fenchy_regular_user_notice_create1'));
        $paymentsetting = $em->getRepository('UserBundle:Payment')->checkUser($user);        
        if($paymentsetting)
        	$payment = true;
        else 
        	$payment = false;
        
        if($notice->getUserGroup())
            $groupId = $notice->getUserGroup()->getId();
        else
            $groupId ='';
        if (!$this->getRequest()->isMethod('POST')) {
            
            // Create form and manage listing gallery
            $form = $this->createForm(new NoticeListingType($notice->getType(), $notice), $notice);
            $data = $this->get('fenchy.gallery_manager')->manageGallery($notice->getGallery());
            
            $data['images']   = $images;            
            $data['notice']   = $notice;
            $data['form']     = $form->createView();
            $data['type']     = $notice->getType();
            $data['location'] = $notice->getLocation();
            $data['tags']     = $this->get('fenchy_dictionary')->getAllListingTags();
            $data['payment'] = $payment;
            $data['groupId'] = $groupId;
            $data['image_gallery'] = false;
            
            return $this->render(
                'FenchyRegularUserBundle:Listing:edit.html.twig', $data);
        }
        // GET
        else {  
        	
            // create and bind form
            $form = $this->createForm(new NoticeListingType($notice->getType(), $notice), $notice);
            
            $form->bind($this->getRequest());
                
            if ($form->isValid()) {
                
                // Notice is not draft any more
                $notice->setDraft(FALSE);
                $notice->setClosed(FALSE);
                $notice->setCompleted(FALSE);
                // We need to manually set all Value entities
                $notice->setValues($this->getValuesFromForm($form->get('type')));

                $tags = $this->get('fenchy_dictionary')->store($notice->getTags(), TRUE);
                
                $notice->setDictionaries($tags); // comment by Jignesh Vagh
                // And again we need to call gallery manager to handle gallery changes.
                $this->get('fenchy.gallery_manager')->manageGallery($notice->getGallery(), TRUE);
                
                // Done :) Let the user see edited notice.
//                 $success_msg = $this->get('translator')->trans('listing.edit.flash_success');
//                 $this->get('session')->setFlash('positive', $success_msg);
                
                // Done :) Let the user see new notice.

//                $i=0;
//                foreach ($images as $image)
//                {
//                    $image->setCropX($this->getRequest()->get('cropX'.$i));
//                    $image->setCropY($this->getRequest()->get('cropY'.$i));
//                    $em->persist($image);
//                    $em->flush();
//                $i++;
//                }
                if($notice->getUserGroup()!=null)
                    return $this->redirect($this->generateUrl('fenchy_regular_user_user_groupprofile_groupnotifications', array('groupId'=> $notice->getUserGroup()->getId())));
                else
                    return $this->redirect($this->generateUrl('fenchy_regular_user_listing_manage'));
                
                //return $this->redirect($this->generateUrl('fenchy_regular_user_notice_edit', array('id' => $notice->getId())));
            }
            else {
                // form is invalid so we need to display it again, but gallery 
                // should not be reseted. We need to inform gallery manager that form was invalid.
            	$data = $this->get('fenchy.gallery_manager')->manageGallery($notice->getGallery(), FALSE);
                $data['notice']   = $notice;
                $data['images']   = $images;     
                $data['form']     = $form->createView();
                $data['type']     = $notice->getType();
                $data['location'] = $notice->getLocation();
                $data['tags']     = $this->get('fenchy_dictionary')->getAllListingTags();
                $data['payment'] = $payment;
                $data['groupId'] = $groupId;
                $data['image_gallery'] = false;
                
                return $this->render(
                    'FenchyRegularUserBundle:Listing:edit.html.twig', $data);
            }
        }
    }
    
    /**
     * Display notice data. 
     * 
     * @param integer $id
     * @return Response
     * @throws Exception
     */
    public function showAction ($id) {
        
        $notice = $this->getDoctrine()
                ->getManager()
                ->getRepository('FenchyNoticeBundle:Notice')
                ->findFullDetailed($id);
        
        if(!$notice) throw $this->createNotFoundException ();
        
        $created_at = $notice->getCreatedAt();
        $year = $created_at->format('Y');
        $month = $created_at->format('m');
        $day = $created_at->format('d');
        
        return $this->redirect($this->generateUrl('fenchy_notice_show_slug', array(
            'slug' => $notice->getSlug(),
            'year' => $year,
            'month' => $month,
            'day' => $day
        )));

    }
    
    /**
     * Display notice data with slug given as an arg.
     * 
     * @param string $slug
     * @param integer $year
     * @param integer $month
     * @param integer $day
     * @return Response
     * @throws Exception
     */
    public function showWithSlugAction ($slug, $year, $month, $day) {
        
        $em = $this->getDoctrine()->getManager();
        
        $notice = $em
                ->getRepository('FenchyNoticeBundle:Notice')
                ->findFullDetailedWithSlug($slug);

        if( ! ($notice instanceof \Fenchy\NoticeBundle\Entity\Notice) ) 
            throw $this->createNotFoundException ();
        
        $pagination = $this->container->getParameter('reviews_pagination');
        $userLoggedIn = $this->get('security.context')->getToken()->getUser();
        $reviewRepo = $em->getRepository('FenchyNoticeBundle:Review');
        
        $managertype = $em
	        ->getRepository('FenchyRegularUserBundle:UserRegular')
	        ->getManagerType($notice->getUser());
        $managertypeLoggedIn = array();
        if( $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User  )
        {
	        $managertypeLoggedIn = $em
	        	->getRepository('FenchyRegularUserBundle:UserRegular')
	        	->getManagerType($userLoggedIn);
        }	        
        if ( $notice->getUser() == $userLoggedIn ) 
            $usersOwnListing = true;
        else
            $usersOwnListing = false;
        
        $initialReviewsP = $reviewRepo->findByInJSON(
            $this->container->get('router'),
            array('aboutNotice'=>$notice->getId(), 'type'=>Review::TYPE_POSITIVE),
            array('created_at'=>'DESC'), $pagination+1, 0);
        $initialReviewsPCount = $reviewRepo->findCount(
            array('aboutNotice'=>$notice->getId(), 'type'=>Review::TYPE_POSITIVE) );
        
        $initialReviewsN = $reviewRepo->findByInJSON(
            $this->container->get('router'),
            array('aboutNotice'=>$notice->getId(), 'type'=>Review::TYPE_NEGATIVE),
            array('created_at'=>'DESC'), $pagination+1, 0);
        $initialReviewsNCount = $reviewRepo->findCount(
            array('aboutNotice'=>$notice->getId(), 'type'=>Review::TYPE_NEGATIVE) );
        
        $userId = $notice->getUser()->getId();
        
        if ( $userId != NULL ) {
            
            $userOther = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')->getAllData( $userId );

            if ( ! $userOther instanceof \Fenchy\UserBundle\Entity\User )
                return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
            $displayUser = $userOther;
            $usersOwnProfile = 0;
        }        
        
//         $notice = $em->getRepository('FenchyNoticeBundle:Notice')
// 	        		->findFullDetailedWithSlug($notice->getSlug());
        $pagination = $this->container->getParameter('reviews_pagination');
        
        $reviewRepo = $em->getRepository('FenchyNoticeBundle:Review');
        $initialReviews = $reviewRepo->findByInJSON(
        		$this->container->get('router'),
        		array('aboutNotice'=>$notice->getId()),
        		array('created_at'=>'DESC'), $pagination+1, 0);

        $commentRepo = $em->getRepository('FenchyNoticeBundle:Comment');
        $initialComments = $commentRepo->findByInJSON(
        		$this->container->get('router'),
        		array('aboutNotice'=>$notice->getId()),
        		array('created_at'=>'DESC'), $pagination+1, 0); 
        $commentManagertype = array();
        $i =  0;
        $userRepository = $this->getDoctrine()
        	->getRepository('UserBundle:User');
        foreach($initialComments as $initialComment)
        {
        	$commentManagertype[$i++] = $em
	        	->getRepository('FenchyRegularUserBundle:UserRegular')
	        	->getManagerType($userRepository->find($initialComment['author']['id']));
        }
		$reviewManagertype = array();
        $i =  0;
        foreach($initialReviews as $initialReview)
        {
        	$reviewManagertype[$i++] = $em
	        	->getRepository('FenchyRegularUserBundle:UserRegular')
	        	->getManagerType($userRepository->find($initialReview['author']['id']));
        }
            $userLoggedIn = $this->get('security.context')->getToken()->getUser();
            $oneRequest = null;
            $payment = false;     
            
            $lat = $notice->getLocation()->getLatitude();
            $log = $notice->getLocation()->getLongitude();

            if($userLoggedIn instanceof \Fenchy\UserBundle\Entity\User)
            {
                $lat2 = $userLoggedIn->getLocation()->getLatitude();
                $log2 = $userLoggedIn->getLocation()->getLongitude();

                $theta = $log - $log2;
                // Find the Great Circle distance
                $distance = rad2deg(acos((sin(deg2rad($lat)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)))));
                $distance = $distance * 60 * 1.1515;
                $gmap_distance = round(($distance * 1609.34), 0);//miles to meter rounded to 0
                $dist = 30000;    
                if($gmap_distance <= $dist)
                {
                    // code here for redirect to show notice
                }
                else
                {
                    //return new RedirectResponse($this->container->get('router')->generate('fenchy_notice_indexv2'));
                }
            
                       
                    $paymentsetting = $em->getRepository('UserBundle:Payment')->checkUser($userLoggedIn);
                    if($paymentsetting)
                            $payment = true;
                    else
                            $payment = false;

                    
                    if($notice->getType() == 'offerevents')
                    {
                        $oneRequest = $em->getRepository('FenchyNoticeBundle:Request')->getUserRequest($userLoggedIn, $notice->getId());
                    }
            }
        return $this->render('FenchyRegularUserBundle:Listing:show.html.twig', array(
            'notice' => $notice,
            'usersOwnListing' => $usersOwnListing,
            'displayUser' => $displayUser,
            'pagination' => $pagination,
            'initialReviewsP' => array("list"=>$initialReviewsP, "count"=>$initialReviewsPCount),
            'initialReviewsN' => array("list"=>$initialReviewsN, "count"=>$initialReviewsNCount),
            'initialReviews' => $initialReviews,
            'initialComments'=>	$initialComments,
            'userLoggedIn' =>	$userLoggedIn,
            'managerType' => $managertype,
            'reviewManagertype' => $reviewManagertype,
            'commentManagertype' =>  $commentManagertype,
            'managertypeLoggedIn' => $managertypeLoggedIn,
            'payment' => $payment,
            'oneRequest' => $oneRequest
        ));
    }
    
    
    /**
     * Display notice data with slug given as an arg.
     * 
     * @param string $slug
     * @param integer $year
     * @param integer $month
     * @param integer $day
     * @return Response
     * @throws Exception
     */
    public function msgshowWithSlugAction ($slug, $year, $day) {
        
        $em = $this->getDoctrine()->getManager();
       
        $neighborhoodmsg = $em
                ->getRepository('FenchyRegularUserBundle:NeighborhoodMsg')
                ->findFullDetailedWithSlug($slug);

        $userLoggedIn = $this->get('security.context')->getToken()->getUser();
        $managertype = $em
	        ->getRepository('FenchyRegularUserBundle:UserRegular')
	        ->getManagerType($neighborhoodmsg->getUser());
        $managertypeLoggedIn = array();
        if( $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User  )
        {
	        $managertypeLoggedIn = $em
	        	->getRepository('FenchyRegularUserBundle:UserRegular')
	        	->getManagerType($userLoggedIn);
        }	        
        if ( $neighborhoodmsg->getUser() == $userLoggedIn ) 
            $usersOwnListing = true;
        else
            $usersOwnListing = false;
        
        
        $userId = $neighborhoodmsg->getUser()->getId();
        
        if ( $userId != NULL ) {
            
            $userOther = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')->getAllData( $userId );

            if ( ! $userOther instanceof \Fenchy\UserBundle\Entity\User )
                return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
            $displayUser = $userOther;
            $usersOwnProfile = 0;
        }
        
        $coverpath = "";
        $document = new Document();
    	$result = $em->getRepository('FenchyRegularUserBundle:Document')->findById($userId);
    	
    	if($result)
    	{
    		$coverpath = $result->getWebPath2();
    		if($coverpath == "")
    			$coverpath = 'images/bg_profileheader.png';
                
                $cropX = $result->getCropX();
                $cropY = $result->getCropY();
    	}
    	else
    	{
    		$coverpath = 'images/bg_profileheader.png';
                $cropX = 0;
		$cropY = 0;
    	}
        
        return $this->render('FenchyRegularUserBundle:Listing:neighbormsgshow.html.twig', array(
            'neighborhoodmsg' => $neighborhoodmsg,
            'usersOwnListing' => $usersOwnListing,
            'displayUser' => $displayUser,
            'userLoggedIn' =>	$userLoggedIn,
            'managerType' => $managertype,
            'managertypeLoggedIn' => $managertypeLoggedIn,
            'coverpath'=>$coverpath,
            'cropX' => $cropX,
            'cropY' => $cropY
        ));
    }
    
    public function neighborMsgsendRequestAction()
    {
    	$request = $this->getRequest();
    	$neighbormsgId = $request->get('noticeId');	
    	$text = $request->get('request_text')? $request->get('request_text'): 'Request';
    	
    	$userLoggedIn = $this->get('security.context')->getToken()->getUser();
    	$em = $this->getDoctrine()->getManager();
    	
    	if ( !$request->isMethod('POST') ) {
    		return new Response('',401);
    	}
        
        $targetNeighborMsg = $em->getRepository('FenchyRegularUserBundle:NeighborhoodMsg')->find($neighbormsgId);
        
    	$requestRepo = $em->getRepository('FenchyNoticeBundle:Request');
        
    	if ( ! ($userLoggedIn instanceof \Fenchy\UserBundle\Entity\User) )
    		return new Response('',401);
    
    	if ( $neighbormsgId != NULL ) {
    		
	    	$targetUser = $targetNeighborMsg->getUser();
	    	if ( ! ($targetUser instanceof \Fenchy\UserBundle\Entity\User) )
	    		return new Response('',401);
		    if($text!="")
		    {
		    	$noticerequest = new \Fenchy\NoticeBundle\Entity\Request();
		    	$noticerequest->setTitle($targetNeighborMsg->getTitle());
		    	$userLoggedIn->addOwnRequest($noticerequest);
		    	$em->persist($userLoggedIn);
		    	$noticerequest->setAuthor($userLoggedIn);
		    	$noticerequest->setText($text);
		    	$noticerequest->setStatus('pending');
		    	$noticerequest->setRequeststatus('pending');
		    	$noticerequest->setAboutUser($targetUser);
                        $noticerequest->setAboutNeighborhoodMsg($targetNeighborMsg);
		    	$noticerequest->setIsReadStatus(true);
                        $noticerequest->setRequestBlue(true);
		    	
		    	$em->persist($noticerequest);
		    	$em->flush();
		    	
		    }
    	}
    	return new Response();
    }
    
    /**
     * Creates Notice draft and remove previous user draft.
     * Does NOT persist new draft!
     * @param User $user
     * @return \Fenchy\NoticeBundle\Entity\Notice
     */
    protected function createDraft($user) {
        
        $em = $this->getDoctrine()->getManager();
        $draft = $em->getRepository('FenchyNoticeBundle:Notice')->findOneBy(array('draft' => TRUE, 'user' => $user));
        if($draft) {
            $found = TRUE;
            $em->remove($draft);
            $em->flush();
        } else $found = FALSE;
        
        $notice = new Notice();
        $notice->setUser($user);
        $notice->setDraft(TRUE);
        
        // If there were not old draft we have to add points for new notice to user;
        if(!$found) {
            $this->get('fenchy.reputation_counter')->update($user, \Fenchy\UserBundle\Services\ReputationCounter::TYPE_NOTICE);
            $em->persist($user);
        }
        
        return $notice;
    }
    
    /**
     * Pull PropertyType entities out of form data and creates Value entities for notice
     * @param \Symfony\Component\Form\Form $form
     * @param String|NULL $direction
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    protected function getValuesFromForm(\Symfony\Component\Form\Form $form, $direction = NULL) {
            
        $values = new \Doctrine\Common\Collections\ArrayCollection();

        foreach($form->getChildren() as $child) {

            if ($child->getData() instanceof \Fenchy\NoticeBundle\Entity\Value) {

                $val = $child->getData();
                if(NULL != $direction) {
                    if($val->getProperty()->getName() === \Fenchy\NoticeBundle\Entity\Type::$direction) {
                        $val->setValueFromOptionByString($direction);
                    }
                }
                $values->add($val);                
            }
        }
        return $values;
    }
    
    public function listingMenuAction($my_req_count,$req_count)
    {
        $router = $this->get("router");
        $route = $router->match($this->getRequest()->getPathInfo());
    	$link =$route['_route'];
		return $this->render('FenchyRegularUserBundle:Listing:listingMenu.html.twig', array(
				'my_req_count' => $my_req_count,
				'req_count' => $req_count,
                                'link' => $link,
                                ));
    }
    
    /**
     * manage user's listings action
     * @author Mateusz Krowiak <mkrowiak@pgs-soft.com>
     */
    public function manageAction() {
        
        $user = $this->get('security.context')->getToken()->getUser();
       	//$user->getPaymentId()->getType();
        $em = $this->getDoctrine()->getEntityManager();
        
        $blockUser = array();
        $index=0;
        $blockneighbors = $em->getRepository('FenchyRegularUserBundle:BlockedNeighbor')->findByMe($user->getId());
	foreach ($blockneighbors as $blockneighbor)
        {
            
            if($blockneighbor->getBlocker()->getId() == $user->getId())
            {
                $blockUser[$index++] = $blockneighbor->getBlocked()->getId();
            }
            if($blockneighbor->getBlocked()->getId() == $user->getId())
            {
                $blockUser[$index++] = $blockneighbor->getBlocker()->getId();
            }
        }
        
        $repo = $em->getRepository('FenchyNoticeBundle:Notice');
        $requestRepo = $em->getRepository('FenchyNoticeBundle:Request');
        $neighborMsgRepo = $em->getRepository('FenchyRegularUserBundle:NeighborhoodMsg');
        
        $listings = $repo->getUserNotices($user);
        $neighbourRequests = $requestRepo->getNeighboursRequests($user);
        // Neighborhood messages 
        $neighbourhoodMsgRequests = $neighborMsgRepo->getNeighbourhoodMsgRequests($user);
        
        $neighbormessages[] = array();
        $i=0;$msgForms[] = array();
        foreach ($neighbourRequests as $k =>$neighbourRequest)
        {
        	if($neighbourRequest->getAboutNotice())
        	{
        		unset($neighbourRequests[$k]);
        	}
        	else
        	{
        		$neighbourRequest->setIsRead(true);
        		$em->persist($neighbourRequest);
        		$em->flush($neighbourRequest);
                        
                        $neighbormessages[$i] = $em->getRepository('FenchyNoticeBundle:RequestMessages')->findByNeighbor($neighbourRequest->getId(),$neighbourRequest->getAuthor()->getId(),$neighbourRequest->getAboutUser()->getId());
                        $i++;
        	}
        	
//        	$messenger = $this->get('fenchy.messenger');
//        	$receiver = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($neighbourRequest->getAuthor()->getId());
//        	if (null === $receiver || $receiver === $this->getUser()) {
//        		throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
//        	}
//        	$messenger->setReceiver($receiver);
//        	$msgForm = $messenger->createForm();
//			$msgForms[$i] = $msgForm->createView();
			
        	
        }
        $userId = $user->getId();
        $displayUser = false;
        
        if ( $userId != NULL ) {
            
            $userOther = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')->getAllData( $userId );

            if ( ! $userOther instanceof \Fenchy\UserBundle\Entity\User )
                return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
            $displayUser = $userOther;

        }
        $requestRepo = $em->getRepository('FenchyNoticeBundle:Request');
       		
        
        $initialReviews[] = array();
        $initialComments[] = array();
        $initialRequests[] = array();
        $initialNeighborMsgRequests[] = array();
        $forms[][] = array();
        $forms2[][] = array();
        $count[] = array();
        $count2[] = array();
        $managertype[] = array();
        $managertype2[] = array();
        $messages[] = array();
        $messages2[] = array();
        $templist = array();
        $templist1 = array();
        $statusflag = true; 
        $blue = array();
        $blue2 = array();
        $i=0;$j=0;$d=0;
        foreach ($listings as $k => $listing)
    	{
            if($listing->getUserGroup()!=null)
            {
                $templist[$d++] = $listing;
    		unset($listings[$k]);
            }
    	}
        foreach ($listings as $listing)
        {
        	$notice1 = $this->getDoctrine()
	        	->getManager()
	        	->getRepository('FenchyNoticeBundle:Notice')
	        	->findFullDetailed($listing->getId());
        	
        	$notice = $em
	        	->getRepository('FenchyNoticeBundle:Notice')
	        	->findFullDetailedWithSlug($notice1->getSlug());
        	$pagination = $this->container->getParameter('reviews_pagination');
        	$reviewRepo = $em->getRepository('FenchyNoticeBundle:Review');
        	$commentRepo = $em->getRepository('FenchyNoticeBundle:Comment');
        	
        	 
        	$initialReviews[$i] = $reviewRepo->findByInJSON(
        			$this->container->get('router'),
        			array('aboutNotice'=>$notice->getId()),
        			array('created_at'=>'DESC'), $pagination+1, 0);
        
        	$initialComments[$i] = $commentRepo->findByInJSON(
        			$this->container->get('router'),
        			array('aboutNotice'=>$notice->getId()),
        			array('created_at'=>'DESC'), $pagination+1, 0);
        	
        	$initialRequests[$i] = $requestRepo->findByInJSON(
        			$this->container->get('router'),
        			array('aboutNotice'=>$notice->getId()),
        			array('created_at'=>'DESC'), $pagination+1, 0);
        	
        	
        	$j=0;
        	$forms[][] = array();
        	$blue[$i]= 'true';
                
        	$c = 0;$status='';$statusflag = true; 
        	foreach ( $initialRequests[$i] as $initialRequest )
        	{
        		if ($initialRequest)
        		{
                            if(! in_array($initialRequest['author']['id'], $blockUser))
                            {
                               
                                $messages[$i][$j] = $em->getRepository('FenchyNoticeBundle:RequestMessages')->findByRequest($initialRequest['id'],$initialRequest['author']['id'],$initialRequest['aboutuser']['id']);
        			$targetRequest = $requestRepo->findOneBy( array('id'=>$initialRequest['id']) );
        			$targetRequest->setIsRead(true);
        			$em->persist($targetRequest);
        			$em->flush();        			
        			$userRepository = $this->getDoctrine()
        				->getRepository('UserBundle:User');
        			
        			$managertype[$i][$j] = $em
        				->getRepository('FenchyRegularUserBundle:UserRegular')
        				->getManagerType($userRepository->find($initialRequest['author']['id']));
        			
//		        	$messenger = $this->get('fenchy.messenger');
//		        	$receiver = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($initialRequest['author']['id']);
//		        	if (null === $receiver || $receiver === $this->getUser()) {
//		        		throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
//		        	}
//		        	$messenger->setReceiver($receiver);
//		        	$form = $messenger->createForm();
//		        	$forms[$i][$j] = $form->createView();
		        	
		        	if($initialRequest['status'] != 'pending')
		        	{
		        		$statusflag = false;
		        		if($initialRequest['status'] != "rejected")
		        			if ($initialRequest['status'] == "done")
    							$status = 'done';
	    					else if($status != 'done')
    							$status = $initialRequest['status'];
		        	}		        	
		        	else 
		        		$c++;
		        		
		        	$j++;
                                
                                if(!$initialRequest['blue'])
                                {
                                    $blue[$i]= 'false';
                                }
                            }
                        }
                       
        	}
        	if($statusflag)
        	{
        		if($c == 0)
        			$count[$i] = 'pending';
        		else
        			$count[$i] = $c." running";
        	}
        	else 
        	{
        		if($status == '')
        			$status = 'rejected';
        		$count[$i] = $status;
        	}
                
                foreach ( $initialComments[$i] as $initialComment )
        	{
                    if(!$initialComment['blue'])
                    {
                        $blue[$i]= 'false';
                    }
                }
        	$i++;
                
                
        }   
        
        // Start for neighbormessages thei Requests
        $i=0;$j=0;$d=0;
            foreach ($neighbourhoodMsgRequests as $neighbourhoodMsgRequest)
        {
        	
        	$pagination2 = $this->container->getParameter('reviews_pagination');
        	
        	$initialNeighborMsgRequests[$i] = $requestRepo->findByInJSON(
        			$this->container->get('router'),
        			array('aboutNeighborhoodMsg'=>$neighbourhoodMsgRequest->getId()),
        			array('created_at'=>'DESC'), $pagination2+1, 0);
        	
        	$j=0;
        	$forms2[][] = array();
        	$blue2[$i]= 'true';
                
        	$c = 0;$status='';$statusflag = true; 
        	foreach ( $initialNeighborMsgRequests[$i] as $initialRequest )
        	{
        		if ($initialRequest)
        		{
                            if(! in_array($initialRequest['author']['id'], $blockUser))
                            {
                               
                                $messages2[$i][$j] = $em->getRepository('FenchyNoticeBundle:RequestMessages')->findByRequest($initialRequest['id'],$initialRequest['author']['id'],$initialRequest['aboutuser']['id']);
        			$targetRequest = $requestRepo->findOneBy( array('id'=>$initialRequest['id']) );
        			$targetRequest->setIsRead(true);
        			$em->persist($targetRequest);
        			$em->flush();        			
        			$userRepository = $this->getDoctrine()
        				->getRepository('UserBundle:User');
        			
        			$managertype2[$i][$j] = $em
        				->getRepository('FenchyRegularUserBundle:UserRegular')
        				->getManagerType($userRepository->find($initialRequest['author']['id']));
        			
//		        	$messenger = $this->get('fenchy.messenger');
//		        	$receiver = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($initialRequest['author']['id']);
//		        	if (null === $receiver || $receiver === $this->getUser()) {
//		        		throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
//		        	}
//		        	$messenger->setReceiver($receiver);
//		        	$form = $messenger->createForm();
//		        	$forms2[$i][$j] = $form->createView();
		        	
		        	if($initialRequest['status'] != 'pending')
		        	{
		        		$statusflag = false;
		        		if($initialRequest['status'] != "rejected")
		        			if ($initialRequest['status'] == "done")
    							$status = 'done';
	    					else if($status != 'done')
    							$status = $initialRequest['status'];
		        	}		        	
		        	else 
		        		$c++;
		        		
		        	$j++;
                                
                                if(!$initialRequest['blue'])
                                {
                                    $blue2[$i]= 'false';
                                }
                            }
                        }
                       
        	}

        	$i++;
        }
        // End for neighbormessages thei Requests
        
        $reqlistings = array();
        $j=0;
        $requests = $requestRepo->getNoticeIds($user);
        foreach ($requests as $request)
    	{
    		if($request->getAboutNotice())
    		{
    			$reqlistings[$j++]  = $request->getAboutNotice();
    		}
    	}
    	
    	foreach($reqlistings as $k => $listing)
    	{
    		foreach($reqlistings as $key => $value)
    		{
    			if($k != $key && $listing->getId() == $value->getId())
    			{
    				unset($reqlistings[$k]);
    			}
    		}
    	}
        $d=0;
//    	foreach ($reqlistings as $k => $reqlisting)
//    	{
//            if($reqlisting->getUserGroup()!=null)
//                $templist1[$d++] = $reqlisting;
//    	}
        $em->getRepository('FenchyNoticeBundle:Review')->updateReviewCount1($user);
        $em->getRepository('FenchyNoticeBundle:Comment')->updateCommentCount1($user);
        $em->getRepository('FenchyNoticeBundle:RequestMessages')->updateRequestMessage1($user);
        
        $my_req_count = $requestRepo->countUnreadUsersStatusRequests($user,$templist1);
        $req_count = $requestRepo->countUnreadUsersRequests($user, $templist);
        $my_req_count += $em->getRepository('FenchyNoticeBundle:Review')->countReview($user);
        $my_req_count += $em->getRepository('FenchyNoticeBundle:RequestMessages')->countRequestMessage($user);
        $my_req_count += $em->getRepository('FenchyNoticeBundle:Comment')->countComment($user);
         
        if($user->getPaymentId())        	
        	$cvv_code = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5('!p@a#b$o'), base64_decode($user->getPaymentId()->getCvvCode()), MCRYPT_MODE_CBC, md5(md5('!p@a#b$o'))), "\0");
        else 
        	$cvv_code =0;
        
        // The Closed group Join Requests
        $requestRepo = $em->getRepository('FenchyNoticeBundle:Request');

        $joinClosedGroupRequests = $requestRepo->getJoinClosedGroupsRequestsForMember($user);
        $i=0;$msgForms[] = array();
        foreach ($joinClosedGroupRequests as $k =>$neighbourRequest)
        {
                if($neighbourRequest->getAboutNotice())
                {
                        unset($joinClosedGroupRequests[$k]);
                }
                else
                {
                        $neighbourRequest->setIsRead(true);
                        $em->persist($neighbourRequest);
                        $em->flush($neighbourRequest);
                }

//                $messenger = $this->get('fenchy.messenger');
//                $receiver = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($neighbourRequest->getAuthor()->getId());
//                
//                if (null === $receiver) {
//                        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
//                }
//                $messenger->setReceiver($receiver);
//                $msgForm = $messenger->createForm();
//                        $msgForms[$i] = $msgForm->createView();
                        $i++;

        }
        
	//echo "sssss<pre>";print_r($form1[5][0]);exit;
        //echo "<pre>";print_r($initialRequests[5]);exit;
        return $this->render('FenchyRegularUserBundle:Listing:manage.html.twig', array(
            	'listings' => $listings,
            	'displayUser' => $displayUser,
        		'user' => $user,
            	'usersOwnProfile' => true,
        		'initialReviews' => $initialReviews,
        		'initialComments'=>	$initialComments,
        		'initialRequests' => $initialRequests,
                        'initialNeighborMsgRequests' => $initialNeighborMsgRequests,
        		'managerTypes'	=> $managertype,
                        'managerTypes2'	=> $managertype2,
        		'reviews'	=> sizeof($initialReviews),        		
        		//'forms' => $forms,
        		'count' => $count,
                        //'forms2' => $forms2,
        		'count2' => $count2,
        		'my_req_count' => $my_req_count,
        		'req_count' => $req_count,
        		'neighbourRequests' => $neighbourRequests,
                        'neighbourhoodMsgRequests' => $neighbourhoodMsgRequests,    
                        'joinClosedGroupRequests' => $joinClosedGroupRequests,
        		//'msgForm' => $msgForms,
        		'cvv_code' => $cvv_code,
                        'blockUser' => $blockUser,
                        'blue' => $blue,
                        'blue2' => $blue2,
                        'messages' => $messages,
                        'messages2' => $messages2,
                        'neighbormessages' => $neighbormessages
         ));
        
    }
    
    public function myRequestsAction() {
    
    	$user = $this->get('security.context')->getToken()->getUser();
    
    	$em = $this->getDoctrine()->getEntityManager();
    
        $blockUser = array();
        $index=0;
        $blockneighbors = $em->getRepository('FenchyRegularUserBundle:BlockedNeighbor')->findByMe($user->getId());
	foreach ($blockneighbors as $blockneighbor)
        {
            
            if($blockneighbor->getBlocker()->getId() == $user->getId())
            {
                $blockUser[$index++] = $blockneighbor->getBlocked()->getId();
            }
            if($blockneighbor->getBlocked()->getId() == $user->getId())
            {
                $blockUser[$index++] = $blockneighbor->getBlocker()->getId();
            }
        }
        
    	$requestRepo = $em->getRepository('FenchyNoticeBundle:Request');
    	$requests = $requestRepo->getNoticeIds($user);
		$neighbourRequests = $requestRepo->getRequests($user);
		$neighbormessages[] = array();
		$i=0;$msgForms[] = array();
		foreach ($neighbourRequests as $k =>$neighbourRequest)
		{		
			if($neighbourRequest->getAboutNotice())
			{
				unset($neighbourRequests[$k]);
			}
			else
			{ 
				$neighbourRequest->setIsReadStatus(true);
				$em->persist($neighbourRequest);
				$em->flush($neighbourRequest);	
                                $neighbormessages[$i] = $em->getRepository('FenchyNoticeBundle:RequestMessages')->findByNeighbor($neighbourRequest->getId(),$neighbourRequest->getAuthor()->getId(),$neighbourRequest->getAboutuser()->getId());
                                $i++;
			}
//			$messenger = $this->get('fenchy.messenger');
//			$receiver = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($neighbourRequest->getAboutUser()->getId());
//			if (null === $receiver || $receiver === $this->getUser()) {
//				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
//			}
//			$messenger->setReceiver($receiver);
//			$msgForm = $messenger->createForm();
//			$msgForms[$i] = $msgForm->createView();
//			$i++;
		}
    	$listings = array();
    	$j = 0; $k = 0;

    	foreach ($requests as $request)
    	{
    		if($request->getAboutNotice())
    		{
    			$listings[$j++]  = $request->getAboutNotice();
    		}
    	}
    	
    	foreach($listings as $k => $listing)
    	{
    		foreach($listings as $key => $value)
    		{
    			if($k != $key && $listing->getId() == $value->getId())
    			{
    				unset($listings[$k]);
    			}
    		}
    	}
        $templist1 = array();
        $ind =0;
//    	foreach ($listings as $k => $listing)
//    	{
//    		if($listing->getUserGroup()!=null)
//    		{
//                    //$templist1[$ind++] = $listing;
//    			//unset($listings[$k]);
//    		}
//    	}
    	
    	$userId = $user->getId();
    	$displayUser = false;
    
    	if ( $userId != NULL ) {
    
    		$userOther = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')->getAllData( $userId );
    
    		if ( ! $userOther instanceof \Fenchy\UserBundle\Entity\User )
    			return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
    		$displayUser = $userOther;
    
    	}
    	
    	$initialReviews[] = array();
    	$initialComments[] = array();
    	$initialRequests[] = array();
    	$forms[][] = array();
    	$managertype[] = array();
    	$count[] = array();
    	$statusflag = true;
        $messages[][] = array();
        $blue = array();
    	$i=0;
    	foreach ($listings as $listing)
    	{    	
            if(! in_array($listing->getUser()->getId(), $blockUser))
            {
    		$notice1 = $this->getDoctrine()
		    		->getManager()
		    		->getRepository('FenchyNoticeBundle:Notice')
		    		->findFullDetailed($listing->getId());
		    		 
    		$notice = $em
	    		->getRepository('FenchyNoticeBundle:Notice')
	    		->findFullDetailedWithSlug($notice1->getSlug());
    		
    		$pagination = $this->container->getParameter('reviews_pagination');
    		$reviewRepo = $em->getRepository('FenchyNoticeBundle:Review');
    		$commentRepo = $em->getRepository('FenchyNoticeBundle:Comment');
    		
    
    		$initialReviews[$i] = $reviewRepo->findByInJSON(
    				$this->container->get('router'),
    				array('aboutNotice'=>$notice->getId()),
    				array('created_at'=>'DESC'), $pagination+1, 0);
    
    		$initialComments[$i] = $commentRepo->findByInJSON(
    				$this->container->get('router'),
    				array('aboutNotice'=>$notice->getId()),
    				array('created_at'=>'DESC'), $pagination+1, 0);
    		 
    		$initialRequests[$i] = $requestRepo->findByInJSON(
    				$this->container->get('router'),
    				array('aboutNotice'=>$notice->getId()),
    				array('created_at'=>'DESC'), $pagination+1, 0);
    		
    		$k=0;
    		$forms[][] = array();
    		
    		$c = 0;$status='';$statusflag = true;
                $blue[$i]= 'true';
    		foreach ( $initialRequests[$i] as $initialRequest )
    		{
    			if ($initialRequest)
    			{
                            if(!in_array($initialRequest['author']['id'],$blockUser))
                            {
                                if($initialRequest['author']['id']== $user->getId())
                                {
                                    $messages[$i][$k] = $em->getRepository('FenchyNoticeBundle:RequestMessages')->findByGroupRequest($initialRequest['id'],$initialRequest['author']['id'],$initialRequest['aboutuser']['id']);
                                    $targetRequest = $requestRepo->findOneBy( array('id'=>$initialRequest['id']) );
                                    $targetRequest->setIsReadStatus(true);
                                    $em->persist($targetRequest);
                                    $em->flush();

                                    $userRepository = $this->getDoctrine()
                                                    ->getRepository('UserBundle:User');

                                    $managertype[$i][$k] = $em
                                                    ->getRepository('FenchyRegularUserBundle:UserRegular')
                                                    ->getManagerType($userRepository->find($initialRequest['author']['id']));

//                                    $messenger = $this->get('fenchy.messenger');
//                                    $receiver = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($initialRequest['aboutuser']['id']);
//                                    if (null === $receiver || $receiver === $this->getUser()) {
//                                            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
//                                    }
//                                    $messenger->setReceiver($receiver);
//                                    $form = $messenger->createForm();
//                                    $forms[$i][$k] = $form->createView();
//                                    if($initialRequest['status'] != 'pending')
//                                    {
//                                            $statusflag = false;
//                                            if($initialRequest['status'] != "rejected")
//                                                    if ($initialRequest['status'] == "done")
//                                                            $status = 'done';
//                                                    else if($status != 'done')
                                                            $status = $initialRequest['requeststatus'];
//                                    }
//                                    else
                                            $c++;
                                    $k++;
                                    if(!$initialRequest['req_blue'])
                                    {
                                        $blue[$i]= 'false';
                                    }
                                }
    			}
                    }
    		}
    		if($c>1)
    		{
    			//if($c == 0)
    				//$count[$i] = 'PENDING';
    			//else
    				$count[$i] = $c." RUNNING";
    		}
    		else
    		{
    			if($status == '')
    				$status = 'rejected';
    			$count[$i] = $status;
    		}
                foreach ( $initialComments[$i] as $initialComment )
        	{
                    if(!$initialComment['request_blue'])
                    {
                        $blue[$i]= 'false';
                    }
                }
    		$i++;
            }	
    	}
        
         // Join Closed Group Requests
                $neighbourRequests2 = $requestRepo->getRequestsToUserGroup($user);
		$groupmessages[] = array();
		$i=0;$msgForms1[] = array();
		foreach ($neighbourRequests2 as $k =>$neighbourRequest)
		{	
			if($neighbourRequest->getAboutNotice())
			{
				unset($neighbourRequests[$k]);
			}
			else
			{ 
                            if($neighbourRequest)
                            {
				$neighbourRequest->setIsReadStatus(true);
				$em->persist($neighbourRequest);
				$em->flush($neighbourRequest);
                                $groupmessages[$i] = $em->getRepository('FenchyNoticeBundle:RequestMessages')->findByGroup($neighbourRequest->getId(),$neighbourRequest->getAboutUserGroup()->getUser()->getId(), $neighbourRequest->getAuthor()->getId(),$neighbourRequest->getAboutUserGroup()->getId());
                                $i++;
                            }
			}
//			$messenger = $this->get('fenchy.messenger');
//			$receiver = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($neighbourRequest->getAuthor()->getId());
//			if (null === $receiver) {
//				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
//			}
//			$messenger->setReceiver($receiver);
//			$msgForm = $messenger->createForm();
//			$msgForms1[$i] = $msgForm->createView();
//			$i++;
		}
                
        $templist = array();
        $d = 0;
        $mylistings = $em->getRepository('FenchyNoticeBundle:Notice')->getUserNotices($user);
        foreach ($mylistings as $mylisting)
        {
            if ($mylisting->getUserGroup() != null)
            {
                $templist[$d++] = $mylisting;
            }
        }
        
        $em->getRepository('FenchyNoticeBundle:Review')->updateReviewCount($user);
        $em->getRepository('FenchyNoticeBundle:Comment')->updateCommentCount($user);
        $em->getRepository('FenchyNoticeBundle:RequestMessages')->updateRequestMessage($user);
    	$my_req_count = $requestRepo->countUnreadUsersStatusRequests($user, $templist1);
    	$req_count = $requestRepo->countUnreadUsersRequests($user, $templist);
        $req_count += $em->getRepository('FenchyNoticeBundle:Review')->countReview($user);
        $req_count += $em->getRepository('FenchyNoticeBundle:Comment')->countComment($user);
        $req_count += $em->getRepository('FenchyNoticeBundle:RequestMessages')->countRequestMessage($user);       
        
        
    	return $this->render('FenchyRegularUserBundle:Listing:myRequests.html.twig', array(
    			'listings' => $listings,
    			'displayUser' => $displayUser,
    			'usersOwnProfile' => true,
    			'initialReviews' => $initialReviews,
    			'initialComments'=>	$initialComments,
    			'initialRequests' => $initialRequests,
    			'managerTypes'	=> $managertype,
    			'reviews'	=> sizeof($initialReviews),
    			//'forms' => $forms,
    			'count'=> $count,
    			'my_req_count' => $my_req_count,
    			'req_count' => $req_count,
    			'neighbourRequests' => $neighbourRequests,
    			'msgForm' => $msgForms,
                        'requestsToGroups' => $neighbourRequests2,
    			//'msgForm1' => $msgForms1,
                        'blockUser' => $blockUser,
                        'blue' => $blue,
                        'messages' => $messages,
                        'neighbormessages' => $neighbormessages,
                        'groupmessages' => $groupmessages
    	));
    
    }
    
    public function tagsAction() {
        
        echo json_encode(array(
                'tags' => array(
                    array('tag' =>'aaaa'), 
                    array('tag' => 'aaaaa'), 
                    array('tag' => 'aaaaaa'), 
                    array('tag' => 'bbbb'),
                    array('tag' => 'bbbba'),
                    array('tag' => 'bbb')
                )
                
            ));exit;
    }
    

    /**
     * @Template()
     * @return array 
     */
    public function deleteAction($id) {

        $em = $this->getDoctrine()->getManager();

        $notice = $em->getRepository('FenchyNoticeBundle:Notice')->find($id);

        if (null === $notice) {
            throw new NotFoundHttpException('Notice not found!');
        }

        $form = $this->createForm(new NoticeDeleteType());

        $userId = $this->get('security.context')->getToken()->getUser()->getId();
        $displayUser = false;

        if ($userId != NULL) {
            
            if ($notice->getUser()->getId() !== $userId) {
                throw new \Exception('You have not permission to delete this listing.');
            }

            $userOther = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')->getAllData($userId);

            if (!$userOther instanceof \Fenchy\UserBundle\Entity\User)
                return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
            $displayUser = $userOther;
        }
        
        return array('displayUser' => $displayUser, 'form' => $form->createView(), 'notice' => $notice);

    }

    public function deleteConfirmAction() {

        $form = $this->createForm(new NoticeDeleteType());

        $request = $this->getRequest();
        $id = $request->get('id');
        $groupId = $request->get('groupId');
        
        if ('POST' == $request->getMethod()) {

            
            $form->bindRequest($request);
            
//             if ($form->isValid()) {             	
                $regularUser = $this->get('security.context')->getToken()->getUser()->getRegularUser();
                $em = $this->getDoctrine()->getManager();

                $notice = $em->getRepository('FenchyNoticeBundle:Notice')->find($id);

                if (null === $notice) {
                    throw new NotFoundHttpException('Notice not found!');
                }
                
                if ($notice->getUser()->getId() !== $this->get('security.context')->getToken()->getUser()->getId() && !$notice->getUsergroup()) {                    
                    throw new \Exception('You have not permission to delete this listing.');
                }
                
                $notice->releaseReviews();
                $notice->releaseRequests();
                $notice->releaseComments();
                $notice->getUser()->removeNotice($notice);
                $this->get('fenchy.reputation_counter')->update($notice->getUser(), \Fenchy\UserBundle\Services\ReputationCounter::TYPE_NOTICE);
                $em->remove($notice);
                $em->persist($notice->getUser());
                $em->flush();

                $this->get('session')->setFlash('positive-overlayer', $this->get('translator')->trans(
                                'listing.manage.flash.delete')
                );
           // }
        }
        if($groupId)
            return $this->redirect($this->generateUrl('fenchy_regular_user_user_groupprofile_groupnotifications', array('groupId' => $groupId)));
        else
            return $this->redirect($this->generateUrl('fenchy_regular_user_listing_manage'));
    }
    public function postCommentAction()
    {
    	$request = $this->getRequest();
    	$targetNoticeId = $request->get('noticeId');
    	$text = $request->get('text');
     
    	$userLoggedIn = $this->get('security.context')->getToken()->getUser();
    	$em = $this->getDoctrine()->getManager();
    	if($text != "")
    	{
	    	if ( !$request->isMethod('POST') ) {
	    		return new Response('',401);
	    	}
	    	
	    	$commentRepo = $em->getRepository('FenchyNoticeBundle:Comment');
	    	
	    	if ( ! ($userLoggedIn instanceof \Fenchy\UserBundle\Entity\User) )
	    		return new Response('',401);
	    	
	    	if ( $targetNoticeId != NULL ) {
	    		$noticeRepo = $em->getRepository('FenchyNoticeBundle:Notice');
	    	
	    		$targetNotice = $noticeRepo->findOneBy( array('id'=>$targetNoticeId) );
	    	
	    		if ( ! ($targetNotice instanceof \Fenchy\NoticeBundle\Entity\Notice) )
	    			return new Response('',401);
	    	
	    		$targetUser = $targetNotice->getUser();
	    		if ( ! ($targetUser instanceof \Fenchy\UserBundle\Entity\User) )
	    			return new Response('',401);
	    	
	    		$comment = new \Fenchy\NoticeBundle\Entity\Comment();
	    		$comment->setTitle($targetNotice->getTitle());
	  			$userLoggedIn->addOwnComment($comment);    			
	  			$em->persist($userLoggedIn);
	  			$comment->setAuthor($userLoggedIn);
	  			$comment->setText($text);
	  			$comment->setType(1);
	  			$comment->setAboutUser($targetUser);
	  			$comment->setAboutNotice($targetNotice);
                                
                                if($targetNotice->getUser()->getId() == $userLoggedIn->getId())
                                {
                                    $comment->setBlue(true);
                                }
	  			$em->persist($comment);
	   			$em->flush(); 
	    	}
    	}
    		$commentRepo = $em->getRepository('FenchyNoticeBundle:Comment');
    		$pagination = $this->container->getParameter('reviews_pagination');
   			$initialComments = $commentRepo->findByInJSON(
   					$this->container->get('router'),
   					array('aboutNotice'=> $targetNoticeId ),
   					array('created_at'=>'DESC'), $pagination+1, 0);

   			$result = $em->getRepository('FenchyRegularUserBundle:Document')->findById($userLoggedIn->getId());
   				
   			if($result)
   			{
   				$avatar = $result->getWebPath();
   			}
   			else
   			{
   				$avatar = 'images/default_profile_picture.png';
   			}
   			
   			
   			$managertypeLoggedIn = array();
   			if( $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User  )
   			{
   				$managertypeLoggedIn = $em
	   				->getRepository('FenchyRegularUserBundle:UserRegular')
	   				->getManagerType($userLoggedIn);
   			}
   			
   			$str = '<a href="javascript:void(0);">';
   			$str .= '<img style="position: absolute; margin-top: 10px; display: block; border-radius: 50% 50% 50% 50%;" src="/'.$avatar. '" width="23" height="23" alt="" />';
   			$str .= '</a>';
   			$str .= '<div class="neighbor '. $managertypeLoggedIn[2] .'" style="margin-top: 7px;">';
			$str .= '<p style="color: #FFFFFF !important; font-size: 11px; margin-left: 9px; margin-top: 4px; position: absolute;">'. $managertypeLoggedIn[1].'</p>';
			$str .= '<span style="margin-top: 10px;">'.$userLoggedIn->getActivity().'</span></div>';
			$str .= '<div class="descdata" style="margin-left: 65px; width: 83% ! important; margin-top: 10px;"><p><textarea id="commenttext" cols=5 placeholder="post a comment" ></textarea></p></div>';
   			$str .= '<input style="float: right; margin-left: 16px; margin-top: -56px; position: relative; cursor: pointer;" class="blue_button" type="button" value="POST" onclick="postComment();" />';
   			
   			$userRepository = $this->getDoctrine()
        			->getRepository('UserBundle:User');
   			$commentManagertype = array();
   			$i=0;
   			foreach ($initialComments as $initialComment)
   			{
   				$commentManagertype[$i] = $em
	   				->getRepository('FenchyRegularUserBundle:UserRegular')
	   				->getManagerType($userRepository->find($initialComment['author']['id']));
   				
   				$result = $em->getRepository('FenchyRegularUserBundle:Document')->findById($initialComment['author']['id']);
   				
   				if($result)
   				{
   					$avatar = $result->getWebPath();
   				}
   				else
   				{
   					$avatar = 'images/default_profile_picture.png';
   				}
   				if ($initialComment)
   				{
   					$str .= '<div class="data">';
   					$str .= '<a href="'. $initialComment['author']['profileUrl'] . '">';
   					$str .= '<img style="border-radius: 50% 50% 50% 50%;" src="/'. $avatar .'" width="23" height="23" alt="" /></a>';
   					$str .= '<div class="neighbor '.$commentManagertype[$i][2].'">';
					$str .= '<p>'.$commentManagertype[$i][1].'</p>';
					$str .= '<span>'.$initialComment['author']['activity'].'</span></div> ';
   					$str .= '<p>' .nl2br($initialComment['text']). '</p></div>';
   				}
   				$i++;
   			}
   			$response = array("success" => $str); 
 			return new Response(json_encode($response)); 	
    }
    
    public function writeReviewAction()
    {
    	$request = $this->getRequest();
    	$targetNoticeId = $request->get('noticeId');
    	$text = $request->get('text');
    	$requestId = $request->get('requestId');
        $flag= $request->get('flag');
    	$userLoggedIn = $this->get('security.context')->getToken()->getUser();
    	
    	
    	$em = $this->getDoctrine()->getEntityManager();    	
    	
    	if($text != "")
    	{
    		if ( !$request->isMethod('POST') ) {
    			return new Response('',401);
    		}
    
                $reqObj = $em->getRepository('FenchyNoticeBundle:Request')->find($requestId);
                if($flag=='true')
                {
                    $reqObj->setOtherReview(true);
                    $reqObj->setBlue(FALSE);                    
                }
                else 
                {
                    $reqObj->setMyReview(true);
                    $reqObj->setRequestBlue(FALSE);                    
                }
                $em->persist($reqObj);
                
    		$reviewRepo = $em->getRepository('FenchyNoticeBundle:Review');
    
    		if ( ! ($userLoggedIn instanceof \Fenchy\UserBundle\Entity\User) )
    			return new Response('',401);
    
    		if ( $targetNoticeId != NULL ) {
    			$noticeRepo = $em->getRepository('FenchyNoticeBundle:Notice');
    
    			$targetNotice = $noticeRepo->findOneBy( array('id'=>$targetNoticeId) );
    
    			if ( ! ($targetNotice instanceof \Fenchy\NoticeBundle\Entity\Notice) )
    				return new Response('',401);
    
    			$targetUser = $targetNotice->getUser();
    			if ( ! ($targetUser instanceof \Fenchy\UserBundle\Entity\User) )
    				return new Response('',401);
    
    			$review = new \Fenchy\NoticeBundle\Entity\Review();
    			$review->setTitle($targetNotice->getTitle());
    			$userLoggedIn->addOwnReview($review);
                        $userLoggedIn->addActivity(1);
    			$em->persist($userLoggedIn);
    			$review->setAuthor($userLoggedIn);
    			$review->setText($text);
    			$review->setType(1);
                        if($flag=='true')
                        {
                            $review->setAboutUser($targetUser);
                            $targetUser->addActivity(2);
                            $review->setIsReadStatus(true);
                            $link = $this->generateUrl('fenchy_regular_user_listing_manage');
                        }
                        else
                        {
                            $review->setAboutUser($reqObj->getAuthor());
                            $reqObj->getAuthor()->addActivity(2);
                            $review->setIsRead(true);
                            $link = $this->generateUrl('fenchy_regular_user_listing_requests');
                        }   
    			$review->setAboutNotice($targetNotice);
     			$em->persist($review);
                        
                        
                        $em->persist($targetUser);
                     
     			$em->flush();
     			
     			$messenger = $this->get('fenchy.messenger');
     			$notice = $messenger->setNotice($targetNotice->getId());
     			
     			$receiver = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($targetUser->getId());
                        
                        
                        
     			if (null != $receiver && $receiver != $this->getUser())
     			{
                            
                                $messageObject = new Message();
		    		$messenger->setReceiver($receiver);
//     				
                                $content = $this->get('translator')->trans('request.review_msg_part', array(
    					'%requester%' => $userLoggedIn->getRegularUser()->getFirstname()));
                                
                                $content .= '                                                                    ';
                                $content .= "|".$link."|";
				$content .= '                                                                    ';
				//$content .= $this->get('translator')->trans('regularuser.message.message_last_part');
                                 
                                $messageObject->setTitle($this->get('translator')->trans('regularuser.message.message_part', array(
    					'%username%' => $targetUser->getRegularUser()->getFirstname())));
                                
		    		$messageObject->setContent($content);
		    		$messageObject->setSender($userLoggedIn);
		    		$messageObject->setReceiver($receiver);
		    		$message = $messenger->send($messageObject);
		    		if ( $this->container->getParameter('notifications_enabled'))
		    			$this->requestMessageNotification($message);
     				
     			}
    		}
    	}    	    	

	    	$reviewRepo = $em->getRepository('FenchyNoticeBundle:Review');
	    	$pagination = $this->container->getParameter('reviews_pagination');
	    	$initialReviews = $reviewRepo->findByInJSON(
	    			$this->container->get('router'),
	    			array('aboutNotice'=> $targetNoticeId ),
	    			array('created_at'=>'DESC'), $pagination+1, 0);
	    
	    	$str = '<div id="reviewReplace'.$targetNoticeId.'">';
	        foreach ( $initialReviews as $initialReview )
	        {
	        	$result = $em->getRepository('FenchyRegularUserBundle:Document')->findById($initialReview['author']['id']);
	        	
	        	if($result)
	        	{
	        		$avatar = $result->getWebPath();
	        	}
	        	else
	        	{
	        		$avatar = 'images/default_profile_picture.png';
	        	}	        	
	        	if ($initialReview)
	        	{
	        		$str .= '<div class="data">';
	        		$str .= '<a href="'.$initialReview['author']['profileUrl'].'">';
	        		$str .= '<img src="/'.$avatar.'" width="23" height="23" alt="" style="border-radius: 50% 50% 50% 50%;"/>';
	        		$str .= '</a>';
	        		$str .= '<p>'.$initialReview['text'].'</p>';
	        		$str .= '</div></div>';
	        	}        		
	        }                                    		
	    	$response = array("success" => $str); 
 			return new Response(json_encode($response));
    }
    
    public function writeMessageAction()
    {
        $request = $this->getRequest();
    	$targetNoticeId = $request->get('noticeId');
    	$text = $request->get('text');
    	$requestId = $request->get('requestId');
        $flag= $request->get('flag');
    	$userLoggedIn = $this->get('security.context')->getToken()->getUser();
    	
    	
    	$em = $this->getDoctrine()->getEntityManager();    	
    	
    	if($text != "")
    	{
            $reqObj = $em->getRepository('FenchyNoticeBundle:Request')->find($requestId);
            if($targetNoticeId != 0)
            $notice = $em->getRepository('FenchyNoticeBundle:Notice')->find($targetNoticeId);
            
            $message = new \Fenchy\NoticeBundle\Entity\RequestMessages();
            //$message->setTitle($notice->getTitle());
            $userLoggedIn->addOwnRequestMessage($message);
            $em->persist($userLoggedIn);
            $message->setAuthor($userLoggedIn);
            $message->setText($text);
             $message->setRequest($reqObj);
            $message->setType(1);
            if($flag=='true')
            {
                if($targetNoticeId != 0)
                    $message->setAboutUser($notice->getUser());
                else
                    $message->setAboutUser($reqObj->getAboutUser()); 
                $reqObj->setBlue(FALSE);
                
                $message->setIsReadStatus(true);
            }
            else
            {
                //if($targetNoticeId != 0)
                $message->setAboutUser($reqObj->getAuthor()); 
                $reqObj->setRequestBlue(FALSE);
                $message->setIsRead(TRUE);
            }
            if($reqObj->getAboutUserGroup())
            {
                $message->setAboutUserGroup($reqObj->getAboutUserGroup());
                $message->setAboutUser($reqObj->getAuthor());
            }
            
            if($targetNoticeId != 0)
                $message->setAboutNotice($notice);
            $em->persist($message);
                     
            $em->flush();
            
            if($targetNoticeId != 0)            
                $messages = $em->getRepository('FenchyNoticeBundle:RequestMessages')->findByGroupRequest($reqObj->getId(),$reqObj->getAuthor()->getId(),$reqObj->getAboutUser()->getId());
            else
            {
                if($reqObj->getAboutUserGroup())
                    $messages = $em->getRepository('FenchyNoticeBundle:RequestMessages')->findByGroup($reqObj->getId(),$reqObj->getAboutUserGroup()->getUser()->getId(), $reqObj->getAuthor()->getId(),$reqObj->getAboutUserGroup()->getId());
                else
                    $messages = $em->getRepository('FenchyNoticeBundle:RequestMessages')->findByNeighbor($reqObj->getId(),$reqObj->getAuthor()->getId(),$reqObj->getAboutUser()->getId());
                
            }
            $str = '';
            foreach($messages as $message1)
            {
                $str .= '<div class="descdata" style="margin-left: 201px; width: 60% ! important; height: 44px;"><p>';                                                                       
                $str .= $message1->getCreatedAt()->format("d.m.y").' '.$message1->getCreatedAt()->format("h:i A").'</p>';
                $str .= '<p>'.$message1->getText().'</p>';
		$str .= '<div class="rightimg"><img src="/images/bgright_rightpart.png" alt="" style="height: 83px; left: 24px; margin-top: -59px; position: relative; -webkit-margin-before: -57px !important;"/></div></div>';
            }
                $str .= '<div class="descdata" style="margin-left: 201px; width: 60% ! important; height: 44px;">';                                                                       
                $str .= '<p><input id="messagetext'.$requestId.'" style="width: 80%" type="textarea" placeholder="'.$this->get('translator')->trans("regularuser.write_your_message").'" /></p>';
                $str .= '<input class="blue_button" style="margin-left: 310px; margin-top: 21px;" type="button" id="continue" value="'.$this->get('translator')->trans("btn.send").'" onclick="writeMessage('. $targetNoticeId.', '.$requestId.')"/>';
		$str .= '<div class="rightimg"><img src="/images/bgright_rightpart.png" alt="" style="height: 83px; left: 24px; margin-top: -73px; position: relative; -webkit-margin-before: -69px !important;"/></div></div>';

            $response = array("success" => $str); 
            return new Response(json_encode($response));
        }
         return new Response(json_encode());
    }
    public function sendRequestAction()
    {
    	$request = $this->getRequest();
    	$noticeId = $request->get('noticeId');	
    	$text = $request->get('request_text')? $request->get('request_text'): 'Request';
    	$item = $request->get('item') ? $request->get('item'): null;
    	$price = $request->get('price') ? $request->get('price'): 0;
    	$total = $request->get('total') ? $request->get('total'): 0;
    	//$currency = $request->get('currency') != 'CURRENCY' ? $request->get('currency') : null;
    	$free = $request->get('free') ? $request->get('free') : false;
    	$offerprice = $request->get('offerprice') ? $request->get('offerprice') : 0;
    	$swapmsg = $request->get('swap_msg') ? $request->get('swap_msg') : null;
        $proposed_location = $request->get('proposed_location') ? $request->get('proposed_location') : null;
    	$start_date = $request->get('start_date');
    	$start_time = $request->get('start_time');
    	$end_date = $request->get('end_date');
    	$end_time =  $request->get('end_time');
    	
    	$userLoggedIn = $this->get('security.context')->getToken()->getUser();
    	$em = $this->getDoctrine()->getManager();
    	
    	if ( !$request->isMethod('POST') ) {
    		return new Response('',401);
    	}
    
    	$requestRepo = $em->getRepository('FenchyNoticeBundle:Request');
    
    	if ( ! ($userLoggedIn instanceof \Fenchy\UserBundle\Entity\User) )
    		return new Response('',401);
    
    	if ( $noticeId != NULL ) {
    		$noticeRepo = $em->getRepository('FenchyNoticeBundle:Notice');
    
	    	$targetNotice = $noticeRepo->findOneBy( array('id'=>$noticeId) );
	
	    	if ( ! ($targetNotice instanceof \Fenchy\NoticeBundle\Entity\Notice) )
	    		return new Response('',401);
	    
	    	$currency = $request->get('currency') != 'CURRENCY' ? $request->get('currency') : $targetNotice->getCurrency();
	    	
	    	$targetUser = $targetNotice->getUser();
	    	if ( ! ($targetUser instanceof \Fenchy\UserBundle\Entity\User) )
	    		return new Response('',401);
		    if($text!="")
		    {
		    	$noticerequest = new \Fenchy\NoticeBundle\Entity\Request();
		    	$noticerequest->setTitle($targetNotice->getTitle());
		    	$userLoggedIn->addOwnRequest($noticerequest);
		    	$em->persist($userLoggedIn);
		    	$noticerequest->setAuthor($userLoggedIn);
		    	$noticerequest->setText($text);
		    	$noticerequest->setStatus('pending');
		    	$noticerequest->setRequeststatus('pending');
		    	$noticerequest->setAboutUser($targetUser);
		    	$noticerequest->setAboutNotice($targetNotice);
                        $noticerequest->setIsReadStatus(true);
                        $noticerequest->setRequestBlue(true);
		    	$noticerequest->setPieceSpot($item);
		    	$noticerequest->setPrice($price);
		    	$noticerequest->setSwapMsg($swapmsg);
                        $noticerequest->setProposedLocation($proposed_location);
                        if($targetNotice->getUserGroup() != null)
                                $noticerequest->setAboutUserGroup($targetNotice->getUserGroup());
		    	if($start_date)	
					$noticerequest->setStartDate(new \DateTime($start_date));
		    	
		    	if($start_time)
		    		$noticerequest->setStartTime(new \DateTime($start_time));
		    	
		    	if($end_date)
		    		$noticerequest->setEndDate(new \DateTime($end_date));
		    	    	
		    	if($end_time)
		    		$noticerequest->setEndTime(new \DateTime($end_time));		    	
		    	
		    	if($targetNotice->getType()== 'offerservice' || $targetNotice->getType()== 'service')
                        {
                            if($offerprice>0)
		    		$noticerequest->setTotalprice($offerprice);
                            else
                                $noticerequest->setTotalprice($price);
                        }
		    	else
		    		$noticerequest->setTotalprice($total);
		    	$noticerequest->setCurrency($currency);
		    	$noticerequest->setFree($free);
		    	$noticerequest->setProposeprice($offerprice);
		    	$em->persist($noticerequest);
		    	$em->flush();
		    	
		    	
		    	$messenger = $this->get('fenchy.messenger');
		    	$notice = $messenger->setNotice($targetNotice->getId());
                        
		    	$link = $this->generateUrl('fenchy_regular_user_listing_manage');
                        
		    	$receiver = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($targetUser->getId());
		    	if (null != $receiver && $receiver != $this->getUser())
		    	{
		    		$messageObject = new Message();
		    		$messenger->setReceiver($receiver);
//     				
                                $content = $this->get('translator')->trans('request.request_msg_part', array(
    					'%requester%' => $userLoggedIn->getRegularUser()->getFirstname()));
                                
                                $content .= '                                                                    ';
                                $content .= "|".$link."|";
				$content .= '                                                                    ';
				//$content .= $this->get('translator')->trans('regularuser.message.message_last_part');
                                 
                                $messageObject->setTitle($this->get('translator')->trans('regularuser.message.message_part', array(
    					'%username%' => $targetUser->getRegularUser()->getFirstname())));
                                
		    		$messageObject->setContent($content);
		    		$messageObject->setSender($userLoggedIn);
		    		$messageObject->setReceiver($receiver);
		    		$message = $messenger->send($messageObject);
		    		if ( $this->container->getParameter('notifications_enabled'))
		    			$this->requestMessageNotification($message);
		    	}
		    }
    	}
    	return new Response();
    }
    public function ListAgainAction($id =  null)
    {
    	$id = $this->getRequest()->get('id');
    	 
    	$em = $this->getDoctrine()->getManager();
    
    	$user = $this->get('security.context')->getToken()->getUser();
    	
    	$notice = $em->getRepository('FenchyNoticeBundle:Notice')->findFullDetailed($id);
    	$images = $notice->getGallery()->getImages();
    
        $paymentsetting = $em->getRepository('UserBundle:Payment')->checkUser($user);        
        if($paymentsetting)
        	$payment = true;
        else 
        	$payment = false;
        
        if($notice->getUserGroup())
            $groupId = $notice->getUserGroup()->getId();
        else
            $groupId ='';
        
    	//print_r($notice->getGallery()->getImages());
    	if(!$notice || $notice->getUser()->getId() !== $user->getId() && $notice->getUserGroup() == null) throw $this->createNotFoundException('Listing does not exists');
    
    	// We do not edit drafts.
    	if($notice->getDraft()) return $this->redirect($this->generateUrl('fenchy_regular_user_notice_create1'));
    
    	if (!$this->getRequest()->isMethod('POST')) {
    
    			
    		$notice1 = $this->createDraft($user);
    		$notice1->setType($notice->getType());
    		$em->persist($notice1);
    		$em->flush();
    		
    		// Create form and manage listing gallery
    		$form = $this->createForm(new NoticeListingType($notice->getType(), $notice), $notice);
                /* Copy old listing images to new listing */
                $gallery = $notice1->getGallery();
                    foreach ($images as $image)
                    {
                        $img = new Image();
                        $img->setName($image->getName());
                        $gallery->addImage($img);
                    }
                    $data = $this->get('fenchy.gallery_manager')->manageGallery($notice1->getGallery());
                    $gallery->setTmp($gallery);
                    
                    $t = $this->get('punk_ave.file_uploader')->createFolder(
                        array(
                            'folder' => $gallery->getFolder(),
                            'max' => 4
                            )
                        );
                    
                    $appUrl = explode('/',$this->get('kernel')->getRootDir());
                    unset($appUrl[count($appUrl)-1]);
                    $baseDir = implode('/',$appUrl);
                    
                    foreach ($images as $image)
                    {
                        if(copy($baseDir.'/web/uploads/'.$notice->getGallery()->getFolder().'/medium/'.$image->getName(), $baseDir.'/web/uploads/'.$gallery->getFolder().'/medium/'.$image->getName()))
                        {
                            //echo "Copy file";
                        }
                        if(copy($baseDir.'/web/uploads/'.$notice->getGallery()->getFolder().'/originals/'.$image->getName(), $baseDir.'/web/uploads/'.$gallery->getFolder().'/originals/'.$image->getName()))
                        {
                            //echo "Copy file";
                        }
                        if(copy($baseDir.'/web/uploads/'.$notice->getGallery()->getFolder().'/thumbnail/'.$image->getName(), $baseDir.'/web/uploads/'.$gallery->getFolder().'/thumbnail/'.$image->getName()))
                        {
                            //echo "Copy file";
                        }
                    }
    		/* and of copy old images to new images */
    
    		$data['images']   = $images;
    		$data['notice']   = $notice;
    		$data['form']     = $form->createView();
    		$data['type']     = $notice->getType();
    		$data['location'] = $notice->getLocation();
    		$data['tags']     = $this->get('fenchy_dictionary')->getAllListingTags();
                $data['payment']  = $payment;
                $data['groupId']  = $groupId;
                $data['image_gallery'] = false;
                
    		return $this->render(
    				'FenchyRegularUserBundle:Listing:listAgain.html.twig', $data);
    	}
    
    	// GET
    	else {
    
    		 
    		$notice1 = $em->getRepository('FenchyNoticeBundle:Notice')->findDraft($user);
    		 
    		// If current user has no draft then we could create new notice, but we wont
    		// We do not like cheaters.
    		if(!$notice1) {
    			throw $this->createNotFoundException('Draft notice not found.');
    		}
    
    		$type = $em->getRepository('FenchyNoticeBundle:Type')->getByNameWithProperties($notice->getType()->getName());
    		$form = $this->createForm(new NoticeListingType($type, $notice1), $notice1);
    
    		 
    		$form->bind($this->getRequest());
    		 
    		if ($form->isValid()) {
    			// Notice is not a draft any more.
    			$notice1->setDraft(FALSE);
    			$notice1->setClosed(FALSE);
                        $notice1->setCompleted(FALSE);
                        if($notice->getUserGroup())
                            $notice1->setUserGroup($notice->getUserGroup());
    			// We need to manually set all Value entities
    			$notice1->setValues($this->getValuesFromForm($form->get('type')));
    			$tags = $this->get('fenchy_dictionary')->store($notice1->getTags(), TRUE);
    
    			$notice1->setDictionaries($tags);
    			$this->get('fenchy.reputation_counter')->update($user, \Fenchy\UserBundle\Services\ReputationCounter::TYPE_NOTICE);
    			$em->persist($user);
    			 
    			// Find some tags in the notice
    			$this->get('fenchy_dictionary')->store($notice1->getTags(), TRUE);
    			 
    			// And again we need to call gallery manager to handle gallery changes.
    			$this->get('fenchy.gallery_manager')->manageGallery($notice1->getGallery(), TRUE);
    			 
    			$em->persist($notice1);
    			$em->flush();
    			$success = true;
    			 
    			$images = $notice1->getGallery()->getImages();
    			$i=0;
    			foreach ($images as $image)
    			{
    				 
    				$image->setCropX($this->getRequest()->get('cropX'.$i));
    				$image->setCropY($this->getRequest()->get('cropY'.$i));
    				$em->persist($image);
    				$em->flush();
    				$i++;
    			}
    			if($notice->getUserGroup()) 
                            return $this->redirect($this->generateUrl('fenchy_regular_user_user_groupprofile_groupnotifications', array('groupId' => $notice->getUserGroup()->getId())));
                        else
                            return $this->redirect($this->generateUrl('fenchy_regular_user_listing_manage'));
    			 
    		}
    		else {
    			// form is invalid so we need to display it again, but gallery
    			// should not be reseted. We need to inform gallery manager that form was invalid.
    			$data = $this->get('fenchy.gallery_manager')->manageGallery($notice1->getGallery(), FALSE);
    			$data['notice']   = $notice1;
    			$data['images']   = $images;
    			$data['form']     = $form->createView();
    			$data['type']     = $notice1->getType();
    			$data['location'] = $notice1->getLocation();
    			$data['tags']     = $this->get('fenchy_dictionary')->getAllListingTags();
                        $data['payment']  = $payment;
    			$data['groupId']  = $groupId; 
                        $data['image_gallery'] = false;
                        
    			return $this->render(
    					'FenchyRegularUserBundle:Listing:listAgain.html.twig', $data);
    		}
    	}
    }	 
    public function RepostAction()
    {
    	$request = $this->getRequest();    	
    	$notice_id = $request->get('notice_id');
    	 
    	$em = $this->getDoctrine()->getManager();
      	 
    	$noticeObj = $em->getRepository('FenchyNoticeBundle:Notice')->find($notice_id);
    	$noticeObj->setCreatedAt(new \DateTime());
    	$em->persist($noticeObj);
    	$em->flush();
    	return new Response();
    }
    public function changeStatusAction()
    {
    	$request = $this->getRequest();
    	$status = $request->get('status');   
    	$requestId = $request->get('request_id');
    	$notice_id = $request->get('notice_id');
    	
    	$em = $this->getDoctrine()->getManager();   	

    	$requestRepo = $em->getRepository('FenchyNoticeBundle:Request');
    	 
    	$targetRequest = $requestRepo->findOneBy( array('id'=>$requestId) );
    	
    	$noticeObj = $em->getRepository('FenchyNoticeBundle:Notice')->find($notice_id);
    	$complete = false;
    	if($status == 'accepted')
    	{
    		if($noticeObj->getClosed())
    		{
    			$response = array("success" => 'Closed');
    			return new Response(json_encode($response));
    		}
    		$ava_item = 0;
    		$req_item = $targetRequest->getPieceSpot();
    		$type = $noticeObj->getType();
    		if($type =='goods' || $type =='offergoods')
    		{
    			$ava_item = $noticeObj->getPieces();
    			if($ava_item<=0 && (!$noticeObj->getOnePiece() || $noticeObj->getCompleted()))
    			{
    				$response = array("success" => 'Piece Completed');
    				return new Response(json_encode($response));
    			}    			
    			if($noticeObj->getOnePiece())
    			{
    				$targetRequest->setPieceSpot(1);
    				$targetRequest->setTotalprice(1 * $targetRequest->getProposeprice()>0 ? $targetRequest->getProposeprice() : $targetRequest->getPrice());
    				//$noticeObj->setPieces(0);
    				//if($targetRequest->getPrice()>0)
    					$noticeObj->setCompleted(true);
    				$em->persist($noticeObj);
    				$em->persist($targetRequest);
    				$em->flush();
    			}
    			else if($ava_item<=$req_item)
    			{
    				$targetRequest->setPieceSpot($ava_item);
    				$targetRequest->setTotalprice($ava_item * $targetRequest->getProposeprice()>0 ? $targetRequest->getProposeprice() : $targetRequest->getPrice());
    				$noticeObj->setPieces(0);
    				//if($targetRequest->getPrice()>0)
    					$noticeObj->setCompleted(true);
    				$em->persist($noticeObj);
    				$em->persist($targetRequest);
    				$em->flush();    				
    			}
    			else
    			{
    				$noticeObj->setPieces($ava_item - $req_item);
    				$targetRequest->setTotalprice($req_item * $targetRequest->getPrice());
    			}   			
    		}    		
    		if($type=='offerevents')
    		{
    			$ava_item = $noticeObj->getSpot();
    			if($ava_item<=0 && !$noticeObj->getUnlimited())
    			{
    				$response = array("success" => 'Spot Completed');
    				return new Response(json_encode($response));
    			}
    			if($ava_item<=$req_item)
    			{
                            $targetRequest->setTotalprice($ava_item * $targetRequest->getProposeprice()>0 ? $targetRequest->getProposeprice() : $targetRequest->getPrice());
                            if(!$noticeObj->getUnlimited())
                            {
    				$targetRequest->setPieceSpot($ava_item);
    				
    				//if($targetRequest->getPrice()>0)
    					$noticeObj->setCompleted(true);
    				$noticeObj->setSpot(0);
    				$em->persist($noticeObj);
    				$em->persist($targetRequest);
    				
                            }
                            $em->flush();
    			}
    			else
    			{
                            if(!$noticeObj->getUnlimited())
                            {
    				$noticeObj->setSpot($ava_item - $req_item);
                            }
    				$targetRequest->setTotalprice($req_item * $targetRequest->getProposeprice()>0 ? $targetRequest->getProposeprice() : $targetRequest->getPrice());
                                $em->persist($targetRequest);
    			}
    		}
    	}
    	
    	$em = $this->getDoctrine()->getManager();
    	
    	if($status != 'rejected')
    		$targetRequest->setStatus($status);    	
    	$targetRequest->setRequeststatus($status);
    	$targetRequest->setIsReadStatus(false);
        $targetRequest->setRequestBlue(false);
    	$em->persist($targetRequest);
    	
    	
    	$pagination = $this->container->getParameter('reviews_pagination');
    	$initialRequests = $requestRepo->findByInJSON(
    			$this->container->get('router'),
    			array('aboutNotice'=>$notice_id),
    			array('created_at'=>'DESC'), $pagination+1, 0);
    	

    	$c = 0;$status='';$statusflag = true;
    	$str='';
    	
    	if($targetRequest->getRequestStatus() == "done")
    	{
    		$author = $targetRequest->getAuthor();
    		$aboutUser = $targetRequest->getAboutUser();
    		//$author->addActivity(1);
    		$em->persist($author);
    		$aboutUser->addActivity(1);
    		$em->persist($aboutUser);
    		if($noticeObj->getType() != 'goods' || $noticeObj->getType() != 'offergoods' || $noticeObj->getType() != 'offerevent')
    		{
    			$noticeObj->setClosed(true);
    			$em->persist($noticeObj);
    		}
    	}
    	
    		if ($targetRequest->getRequestStatus() == 'pending')
    		{
    			if($noticeObj->getClosed())
    				$str .='<a href="#dialog405" name="modal" id="vzoom_1944" class="zoomthis blue-button">';
    			else
    				$str .= '<a class="blue-button" href="javascript:void(0);" onclick="changeStatus(\'accepted\','.$requestId.','.$notice_id.');">';
    			$str .= $this->get('translator')->trans('regularuser.accept');
    			$str .= '</a>';
    			$str .= '<a class="blue-button" href="javascript:void(0);" onclick="changeStatus(\'rejected\','.$requestId.','.$notice_id.');">';
    			$str .= $this->get('translator')->trans('regularuser.reject');
    			$str .= '</a>';
    		}
    		
    		if ($targetRequest->getRequestStatus() == 'accepted')
    		{
    			$str.= '<a class="blue-button" href="javascript:void(0);" onclick="changeStatus(\'done\','.$requestId.','.$notice_id.');">';
    			$str .= $this->get('translator')->trans('regularuser.mark_done');
    			$str .= '</a>';
    		
    		}
//     			$str .= '<a class="blue-button" href="javascript:void(0);" onclick="showMessageForm('.$requestId.');">';
//     			$str .= $this->get('translator')->trans('regularuser.message');
//     			$str .= '</a>';
    			
    		$em->flush();
   		foreach ( $initialRequests as $initialRequest )
   		{	
   			if($initialRequest['status'] == 'pending' && $initialRequest['aboutnotice']['completed'])
   			{
   				$requestObj = $em->getRepository('FenchyNoticeBundle:Request')->findOneBy( array('id'=>$initialRequest['id']));
   				$requestObj->setRequeststatus('rejected');
   				$em->persist($requestObj);
   				$em->flush();
   			}	
    		if ($initialRequest)
    		{
    			if($initialRequest['status'] != 'pending')
    			{
    				$statusflag = false;
    				if($initialRequest['status'] != "rejected")
    					if ($initialRequest['status'] == "done")
    						$status = 'done';
	    				else if($status != 'done')
    						$status = $initialRequest['status'];
    			}
    			else
    				$c++;    	
    		}
    	}
    	$count = "";
    	if($statusflag)
    	{
    		if($c == 0)
    			$count = 'pending';
    		else
    			$count = $c." running";
    	}
    	else
    	{
    		if($status == '')
    			$status = 'rejected';
    		$count = $status;
    	}
    	$str .= "^^".$count;
    	$str .= "^^".$targetRequest->getRequeststatus();
        
        $paymentType ='';
        if($noticeObj->getType() =="goods" || $noticeObj->getType() == "service")
        {
            if($targetRequest->getAuthor()->getPaymentId())
                $paymentType = $targetRequest->getAuthor()->getPaymentId()->getType();
        }
        else
        {
            if($noticeObj->getUserGroup()!= null)
                if($noticeObj->getUserGroup()->getPaymentId())
                    $paymentType = $noticeObj->getUserGroup()->getPaymentId()->getType();
            else
                if($noticeObj->getUser()->getPaymentId())
                    $paymentType = $noticeObj->getUser()->getPaymentId()->getType();
        }
        
        if($paymentType == 'paypal')
        {
            $str .= "^^".intval(($targetRequest->getTotalprice()*10)/100);
        }
        else
        {
            if($targetRequest->getTotalprice()>0)
                    $str .= "^^".intval((($targetRequest->getTotalprice()*10)/100)*100);
            else 
                    $str .= "^^".$targetRequest->getTotalprice()*0;
        }
    	if(strcasecmp($targetRequest->getCurrency(),"EURO") == 0)    		
    		$str .= "^^".'EUR';
    	else 
    		$str .= "^^".'USD';
    	if($noticeObj->getClosed() || $noticeObj->getCompleted())
    		$str .= "^^".'closed';
    	else 
    		$str .= "^^".'open';
    	
    	if($targetRequest->getRequestStatus() == "done" && ($noticeObj->getType() =="goods" || $noticeObj->getType() == "service"))
    	{
    		$author = $targetRequest->getAuthor();
    		if($author->getPaymentId())
    		{
    			$str .= "^^".$author->getPaymentId()->getType();
    			$str .= "**". $author->getPaymentId()->getCardNo();
    			$str .= "**". $author->getPaymentId()->getEndMonth();
    			$str .= "**". $author->getPaymentId()->getEndYear();
    			$str .= "**". $author->getPaymentId()->getAccountNo();
    			$str .= "**". $author->getPaymentId()->getBankCode();
    			$str .= "**". $author->getPaymentId()->getAccountHolder();
    			$str .= "**". $author->getPaymentId()->getCardHolder();
                        
    			
    			if($author->getPaymentId()->getCvvCode())
    				$cvv_code = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5('!p@a#b$o'), base64_decode($author->getPaymentId()->getCvvCode()), MCRYPT_MODE_CBC, md5(md5('!p@a#b$o'))), "\0");
    			else 
    				$cvv_code = '';
    			$str .= "**".$cvv_code ;
                        $str .= "**". $author->getPaymentId()->getPaypalEmail();
    		}
    	}
    	$response = array("success" => $str);
    	return new Response(json_encode($response));
    }
    
    public function markAsReadAction()
    {
    	$request = $this->getRequest();    	
    	$noticeId = $request->get('noticeId');
    	$flag = $request->get('flag');
    	    	 
        $requestId = $request->get('requestId');
        $msg = $request->get('msg');
        if($requestId)
        {
            $em = $this->getDoctrine()->getManager()->getRepository('FenchyNoticeBundle:Request')->updateBlueReadStatus(null, $flag, $requestId);
        }
        else if($flag !='comment' && $flag !='comments')      
            $em = $this->getDoctrine()->getManager()->getRepository('FenchyNoticeBundle:Request')->updateBlueReadStatus($noticeId, $flag, null);
    	
        if($msg)
        {
            $em = $this->getDoctrine()->getManager()->getRepository('FenchyNoticeBundle:Request')->updateNeighborMsgBlueReadStatus($noticeId, $flag, null);
        }
        
        $commentRepo = $this->getDoctrine()->getEntityManager()->getRepository('FenchyNoticeBundle:Comment');
        $initialComments = $commentRepo->getAllCommentsWithNotice($noticeId);
        			
        foreach ($initialComments as $initialComment)
        {
            if($flag=='comment' or $flag=='false')
                $initialComment->setBlue(true);
            else
                $initialComment->setRequestBlue(true);
            $this->getDoctrine()->getEntityManager()->persist($initialComment);
            $this->getDoctrine()->getEntityManager()->flush($initialComment);
        }
        
    	return new Response();
    }
    
    public function paymillPaymentAction()
    {    	
    	$user = $this->get('security.context')->getToken()->getUser();
    	
    	$request = $this->getRequest(); 
    	if('POST' == $request->getMethod())
    	{
    		$apiKey = '3239306c3583bb32b7c0261c2253d39f';
    		$paymillrequest = new Request($apiKey);
    		if($request->get('accType')=='credit')
    		{   			
	    		$preAuth = new Preauthorization();
	    		$preAuth->setToken($request->get('token'))//'098f6bcd4621d373cade4e832627b4f6')
	    				->setAmount($request->get('amount'))
	    				->setCurrency($request->get('currency'));
	    		$response = $paymillrequest->create($preAuth);
	    		echo "done";exit;
    		}
    		else 
    		{	    		
	    		$transaction = new Transaction();
	    		$transaction->setAmount($request->get('amount')) // e.g. "4200" for 42.00 EUR
	    					->setCurrency($request->get('currency'))
	    					->setToken($request->get('token'));
	    					//->setDescription('Test Transaction');
	    	
	    		$response = $paymillrequest->create($transaction);
	    		echo "done";exit;
    		}	    	
    	}  	
    }
    
    public function paypalPaymentAction()
    {
        $request = $this->getRequest();  
        $em = $this->getDoctrine()->getEntityManager();
        $requestObj = $em->getRepository('FenchyNoticeBundle:Request')->find($request->get('requestId'));
    
    	$noticeObj = $em->getRepository('FenchyNoticeBundle:Notice')->find($request->get('noticeId'));
        
        
        $requestEnvelope = new RequestEnvelope("en_US");
//    	date_default_timezone_set('GMT');
    	
        if($noticeObj->getUserGroup()!= null)
            $preapprovalRequest = new PreapprovalRequest($requestEnvelope, $request->getScheme().'://'. $request->getHttpHost().$request->getBasePath().$this->get('router')->generate('fenchy_regular_user_paypalcancel', array('requestId' => $request->get('requestId'), 'groupId' => $noticeObj->getUserGroup()->getId())),
    			$request->get('currency'), $request->getScheme().'://'. $request->getHttpHost().$request->getBasePath().$this->get('router')->generate('fenchy_regular_user_user_groupprofile_groupnotifications', array('groupId' => $noticeObj->getUserGroup()->getId())), gmdate("Y-m-d\TH:i:sP"));
        else
            $preapprovalRequest = new PreapprovalRequest($requestEnvelope, $request->getScheme().'://'. $request->getHttpHost().$request->getBasePath().$this->get('router')->generate('fenchy_regular_user_paypalcancel', array('requestId' => $request->get('requestId'), 'groupId' => 0)),
    			$request->get('currency'), $request->getScheme().'://'. $request->getHttpHost().$request->getBasePath().$this->get('router')->generate('fenchy_regular_user_listing_manage'), gmdate("Y-m-d\TH:i:sP"));
    	
            
        $preapprovalRequest->senderEmail = $request->get('paypalEmail');
        
    	//$preapprovalRequest->senderEmail = 'jignesh.vagh@huskeit.com';
    	$preapprovalRequest->maxAmountPerPayment = $request->get('amount');
    	$preapprovalRequest->maxNumberOfPayments = 1;
    	$preapprovalRequest->paymentPeriod = 'DAILY' ;
    	$preapprovalRequest->feesPayer = 'SENDER';
    	$preapprovalRequest->endingDate = gmdate('Y-m-d\TH:i:sP', time() + 100);
    	
    	$service = new AdaptivePaymentsService(array(
				"acct1.UserName" => "bhumi-facilitator_api1.huskerit.com",
				"acct1.Password" => "1367821222",
				"acct1.Signature" => "A8GjU1g3t8ui4Tumz.4hKm0ea5LGA.6yInKL0ENwr-fNC39SIO1OmYZU",
				"acct1.AppId" => "APP-80W284485P519543T",
    			"mode" => "sandbox"
    	));
    	
    		/* wrap API method calls on the service object with a try catch */
    		$response = $service->Preapproval($preapprovalRequest);
                if($response->responseEnvelope->ack != 'Success')
                {                    
                    $requestObj->setStatus('accepted');
                    $requestObj->setRequestStatus('accepted');
                    $em->persist($requestObj);
                    $em->flush($requestObj);
                        $str.= '<a class="blue-button" href="javascript:void(0);" onclick="changeStatus(\'done\','.$requestId.','.$notice_id.');">';
    			$str .= $this->get('translator')->trans('regularuser.mark_done');
    			$str .= '</a>';
                    echo "Fail^^^".$str;
                    exit;
                }
    		//print_r($response);    		
    		$payPalURL = 'https://www.sandbox.paypal.com/webscr&cmd=_ap-preapproval&preapprovalkey='.$response->preapprovalKey;
                echo $payPalURL;
                exit;
    }
    public function paypalCancelAction($requestId, $groupId)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $requestObj = $em->getRepository('FenchyNoticeBundle:Request')->find($requestId);
        $requestObj->setStatus('accepted');
        $requestObj->setRequestStatus('accepted');
        $em->persist($requestObj);
        $em->flush($requestObj);
        if($groupId != 0)
            return $this->redirect($this->generateUrl('fenchy_regular_user_user_groupprofile_groupnotifications', array('groupId' => $groupId)));
        else
            return $this->redirect($this->generateUrl('fenchy_regular_user_listing_manage'));
    }
    public function inviteToListingAction($noticeId)
    {
    	$userLoggedIn = $this->get('security.context')->getToken()->getUser();
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	if (!$userLoggedIn instanceof \Fenchy\UserBundle\Entity\User) 
    		return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
    	
    	$displayUser = $userLoggedIn;
    	
    	$request = $this->getRequest();
    	$inviteNeighbors = $request->get('invite_neighbors');
    	$inviteNeighborsNext50 = $request->get('invite_neighborsnext50');
    	$friends_email = $request->get('friends_email1');
		
    	$link = "";
    	$noticeObj = $em->getRepository('FenchyNoticeBundle:Notice')->find($noticeId);
    	if($noticeObj->getCreatedAt()) {
    		$link = $this->generateUrl('fenchy_notice_show_slug', array(
    				'slug' => $noticeObj->getSlug(),
    				'year' => $noticeObj->getCreatedAt()->format('Y'),
    				'month' => $noticeObj->getCreatedAt()->format('m'),
    				'day' => $noticeObj->getCreatedAt()->format('d')
    		));
    	} else {
    		$link = $this->generateUrl('fenchy_regular_user_notice_show', array('id' => $noticeObj->getId()));
    	}   	 
//     	echo "<pre>"; print_r($inviteNeighbors);
//     	print_r($inviteNeighborsNext50);
//     	print_r($friends_email);
//     	exit;
    	$neighborsNext50 = $this->neighborsNext50();
    	
    	if (!empty($inviteNeighbors))
    	{
    		foreach ($inviteNeighbors as $key => $neighbor)
    		{
    			$userOther = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')->getAllData($neighbor);
    			$neighbor_id = $userOther->getId();
    			//echo $userOther->getEmail();
    	
    			$messenger = $this->get('fenchy.messenger');
    			$messageObject = new Message();
    			$receiver = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($neighbor_id);
    			if (null !== $receiver || $receiver !== $this->getUser())
    			{
    				//throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
    			
                            $messenger->setReceiver($receiver);
                            $title = $this->get('translator')->trans('regularuser.message.subject_notice', array(
                                            '%username%' => $userOther->getRegularUser()->getFirstname())) . "";
                            $content = '';
                            $content .= $this->get('translator')->trans('regularuser.message.message_part', array(
                                            '%username%' => $userOther->getRegularUser()->getFirstname()));
                            $content .= '                                                                    ';
                            //$content .= $userOther->getRegularUser()->getFirstname().' ';
                            $content .= $this->get('translator')->trans('regularuser.message.message_first_part1', array(
                                            '%user%' => $userLoggedIn->getRegularUser()->getFirstname()));
                            $content .= "|".$link."|";  		
                            $content .= '                                                                    ';
                            //$content .= $this->get('translator')->trans('regularuser.message.message_last_part');
                            $messageObject->setTitle($title);

                            $messageObject->setContent($content);

                            $messageObject->setSender($displayUser);
                            $messageObject->setReceiver($receiver);

                            $message = $messenger->send($messageObject);

                            if ($this->container->getParameter('notifications_enabled')) $this->messageNotification($message,$userLoggedIn);
                        }
    		}
    	
    	}
    	 
    	
    	// Invite Neighbors next 50 around me (only for community and neighborhood manager)
    	if ($inviteNeighborsNext50)
    	{   		
    		if ($neighborsNext50)
    		{
    			$i = 1;
    			foreach ($neighborsNext50 as $key => $neighbor)
    			{
    				$userOther = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')->getAllData($neighbor);
    				$neighbor_id = $userOther->getId();
    				//echo $userOther->getEmail();
    	
    				$messenger = $this->get('fenchy.messenger');
    				$messageObject = new Message();
    				$receiver = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($neighbor_id);
    				if (null !== $receiver || $receiver !== $this->getUser())
    				{
    					//throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
    				
                                    $messenger->setReceiver($receiver);
                                    $title = $this->get('translator')->trans('regularuser.message.subject_notice', array(
                                                    '%username%' => $userOther->getRegularUser()->getFirstname())) . "";
                                    $content = ' ';
                                    $content .= $this->get('translator')->trans('regularuser.message.message_part', array(
                                                    '%username%' => $userOther->getRegularUser()->getFirstname()));
                                    $content .= '                                                                    ';
                                    //$content .= $userOther->getRegularUser()->getFirstname().' ';
                                    $content .= $this->get('translator')->trans('regularuser.message.message_first_part1', array(
                                                    '%user%' => $userLoggedIn->getRegularUser()->getFirstname()));
                                    $content .= "|".$link."|";
                                    $content .= '                                                                    ';
                                    //$content .= $this->get('translator')->trans('regularuser.message.message_last_part');
                                    $messageObject->setTitle($title);

                                    $messageObject->setContent($content);

                                    $messageObject->setSender($displayUser);
                                    $messageObject->setReceiver($receiver);

                                    $message = $messenger->send($messageObject);

                                    if ($this->container->getParameter('notifications_enabled')) $this->messageNotification($message, $userLoggedIn);
                                }
    				if ($i == 50)
    				{
    					break;
    				}
    				$i++;
    			}
    		}
    	}

    	// invite another friends by email
    	$data = array(
    			'sender' => $displayUser,
    			'user' => $displayUser,
    			'link'=> $link    			
    			);
    	
    	if (!empty($friends_email))
    	{
    		foreach ($friends_email as $key => $receiverEmail)
    		{
    			if($receiverEmail != "")
    			{
    				$emailNotification = \Swift_Message::newInstance()->setFrom($this->container->getParameter('from_email'), $this->container->getParameter('from_name'))->setTo($receiverEmail)->setSubject($this->get('translator')->trans('regularuser.message.subject_for_invitelistingby_mail'))->setBody($this->renderView('FenchyRegularUserBundle:Notifications:noticeInviteByEmailHTML.html.twig', $data), 'text/html');
	    			//->addPart($this->renderView('FenchyRegularUserBundle:Notifications:reviewEmailPlain.html.twig', $data), 'text/plain');
    				$mailer = $this->get('mailer');
    				$mailer->send($emailNotification);
    			}
    		}
    	}
        if($noticeObj->getUserGroup()!= null)
            return $this->redirect($this->generateUrl('fenchy_regular_user_user_groupprofile_groupnotifications', array('groupId'=> $noticeObj->getUserGroup()->getId())));
        else
            return $this->redirect($this->generateUrl('fenchy_regular_user_listing_manage'));
    }
    protected function messageNotification(Message $message, User $sender)
    {
    	$receiver = $message->getReceiver();
    
    	$notifications = $receiver->getNotifications();
    	$niterator = $notifications->getIterator();
    
    	$message_notification = false;
    	foreach ($niterator as $onen)
    	{
    		if ($onen->getName() == 'message') $message_notification = true;
    	}
    
    	$em = $this->getDoctrine()->getEntityManager();
    	$result = $em->getRepository('FenchyRegularUserBundle:Document')->findById($sender->getId());
    	 
    	if($result)
    	{
    		$avatar = $result->getWebPath();
    		if($avatar == "")
    			$avatar = 'images/default_profile_picture.png';
    	}
    	else
    	{
    		$avatar = 'images/default_profile_picture.png';
    	}
    	if ($message_notification)
    	{
    
    		$interval = $receiver->getNotificationGroupIntervals()->first();
    		if ($interval != null)
    			$interval_val = $interval->getInterval();
    		else $interval_val = null;
    
    		if ($interval_val === NotificationGroupInterval::INTERVAL_DAILY)
    		{
    
    			$queue_processing_hour = $this->container->getParameter('notification_queue_processing_hour');
    
    			$now = new \DateTime;
    			$now_hour = (integer) $now->format('G');
    
    			$send_after = new \DateTime;
    			if ($now_hour >= $queue_processing_hour)
    			{
    				$send_after->modify('+1 day');
    			}
    			$send_after->setTime($queue_processing_hour, 0, 0);
    
    			$toQueue = new NotificationQueue;
    			$toQueue->setSendAfter($send_after)->setFromAddress($this->container->getParameter('from_email'))->setFromName($this->container->getParameter('from_name'))->setToAddress($receiver->getEmail())->setSubject($this->get('translator')->trans('message.notification.email.subject', array(
    					'%username%' => $sender->getRegularUser()->getFirstname())))->setBodyHtml($this->renderView('FenchyMessageBundle:Notifications:messageEmailHTML.html.twig', array(
    							'sender' => $sender,
    							'message' => $message)))->setBodyPlain($this->renderView('FenchyMessageBundle:Notifications:messageEmailPlain.html.twig', array(
    									'sender' => $sender,
    									'message' => $message,
    									'avatar' => $avatar,
    							)));
    							$em = $this->getDoctrine()->getManager();
    							$em->persist($toQueue);
    							$em->flush();
    		}
    		elseif ($interval_val === NotificationGroupInterval::INTERVAL_IMMEDIATELY)
    		{
    			$emailNotification = \Swift_Message::newInstance()->setFrom($this->container->getParameter('from_email'), $this->container->getParameter('from_name'))->setTo($receiver->getEmail())->setSubject($this->get('translator')->trans('message.notification.email.subject', array(
    					'%username%' => $sender->getRegularUser()->getFirstname())))->setBody($this->renderView('FenchyMessageBundle:Notifications:messageEmailHTML.html.twig', array(
    							'sender' => $sender,
    							'message' => $message, 
    							'avatar' => $avatar)), 'text/html')->addPart($this->renderView('FenchyMessageBundle:Notifications:messageEmailPlain.html.twig', array(
    									'sender' => $sender,
    									'message' => $message)), 'text/plain');
    							$mailer = $this->get('mailer');
    							$mailer->send($emailNotification);
    		}
    	}
    
    }

    protected function requestMessageNotification(Message $message)
    {
    	$receiver = $message->getReceiver();
    	$sender = $message->getSender();
    	$notifications = $receiver->getNotifications();
    	$niterator = $notifications->getIterator();
    
    	$request_notification = false;
    	foreach( $niterator as $onen ) {
    		if ( $onen->getName() == 'request' )
    			$request_notification = true;
                
    	}
    
    	if ( $request_notification ) {
    
    		$interval = $receiver->getNotificationGroupIntervals()->first();
    		if ( $interval != null )
    			$interval_val = $interval->getInterval();
    		else
    			$interval_val = null;
    
    		if ( $interval_val === NotificationGroupInterval::INTERVAL_DAILY ) {
    
    			$queue_processing_hour = $this->container->getParameter('notification_queue_processing_hour');
    
    			$now = new \DateTime;
    			$now_hour = (integer)$now->format('G');
    
    			$send_after = new \DateTime;
    			if ( $now_hour >= $queue_processing_hour ) {
    				$send_after->modify('+1 day');
    			}
    			$send_after->setTime($queue_processing_hour, 0, 0);
    
    			$toQueue = new NotificationQueue;
    			$toQueue->setSendAfter($send_after)
    			->setFromAddress($this->container->getParameter('from_email','noreply@pollyandbob.com'))
    			->setFromName($this->container->getParameter('from_name'))
    			->setToAddress($receiver->getEmail())
    			->setSubject($this->get('translator')->trans('message.req_notification.email.subject', array(
    					'%username%' => $sender->getRegularUser()->getFirstname())))
    			->setBodyHtml($this->renderView('FenchyMessageBundle:Notifications:requestEmailHTML.html.twig',
    					array('message' => $message)))
    					->setBodyPlain($this->renderView('FenchyMessageBundle:Notifications:requestEmailPlain.html.twig',
    							array('message' => $message)));
    			$em = $this->getDoctrine()->getManager();
    			$em->persist($toQueue);
    			$em->flush();
    		} elseif ( $interval_val === NotificationGroupInterval::INTERVAL_IMMEDIATELY ) {
    			$emailNotification = \Swift_Message::newInstance()
    			->setFrom($this->container->getParameter('from_email'),
    					$this->container->getParameter('from_name'))
    					->setTo( $receiver->getEmail() )
    					->setSubject( $this->get('translator')->trans('message.req_notification.email.subject', array(
    						'%username%' => $sender->getRegularUser()->getFirstname())))
    					->setBody($this->renderView('FenchyMessageBundle:Notifications:requestEmailHTML.html.twig',
    							array('message' => $message)),
    							'text/html')
    							->addPart($this->renderView('FenchyMessageBundle:Notifications:requestEmailPlain.html.twig',
    									array('message' => $message)),
    									'text/plain');
    			$mailer = $this->get('mailer');
    			$mailer->send($emailNotification);
    		}
    	}
    
    }   
    public function storeSessionAction()
    {
    	$request = $this->getRequest();   	
    	
    	// set and get session attributes
    	$session = $this->get('session');
        $session->getFlashBag()->add('image_gallery', 'true');
    	$session->getFlashBag()->add('title', $request->get('title'));
    	$session->getFlashBag()->add('desc', $request->get('desc'));
    	$session->getFlashBag()->add('tag', $request->get('tag'));
    	$session->getFlashBag()->add('start_date', $request->get('start_date'));
    	$session->getFlashBag()->add('start_time', $request->get('start_time'));
    	$session->getFlashBag()->add('end_date', $request->get('end_date'));
    	$session->getFlashBag()->add('date_arrange', $request->get('date_arrange'));
    	$session->getFlashBag()->add('time_arrange', $request->get('time_arrange'));
    	$session->getFlashBag()->add('end_time', $request->get('end_time'));
    	$session->getFlashBag()->add('location', $request->get('location'));
    	$session->getFlashBag()->add('loc_arrange', $request->get('loc_arrange'));
    	$session->getFlashBag()->add('price', $request->get('price'));
    	$session->getFlashBag()->add('spot', $request->get('spot'));
    	$session->getFlashBag()->add('piece', $request->get('piece'));
    	$session->getFlashBag()->add('unlimited', $request->get('unlimited'));
    	$session->getFlashBag()->add('one_piece', $request->get('one_piece'));
    	$session->getFlashBag()->add('free', $request->get('free'));
    	$session->getFlashBag()->add('payment_setting', $request->get('payment_setting'));
    	$session->getFlashBag()->add('currency', $request->get('currency'));
    	$session->getFlashBag()->add('chk1', $request->get('chk1'));
    	$session->getFlashBag()->add('chk2', $request->get('chk2'));
    	$session->getFlashBag()->add('chk3', $request->get('chk3'));
    	$session->getFlashBag()->add('chk4', $request->get('chk4'));
    	$session->getFlashBag()->add('chk5', $request->get('chk5'));
    	$session->getFlashBag()->add('chk6', $request->get('chk6'));
    	
    	
//     	$this->get('session')->set('title', $title);
//     	$this->get('session')->set('desc', $desc);
//     	$this->get('session')->set('tag', $tag);
    	return new Response();
    }
}
