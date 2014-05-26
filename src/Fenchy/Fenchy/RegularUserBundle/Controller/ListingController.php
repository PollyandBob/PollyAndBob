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

/**
 * This controller should manage listings (notices) and all other operations should not be here.
 * 
 * @author Michał Nowak <mnowak@pgs-soft.com>
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
    		if(!$neighbors)
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
    
	    			
    
    				if($gmap_distance > $mindist && $gmap_distance < $maxdist && $gmap_distance <= $dist)
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
        if(!$notice || $notice->getUser()->getId() !== $user->getId()) throw $this->createNotFoundException('Listing does not exists');
        
        // We do not edit drafts.
        if($notice->getDraft()) return $this->redirect($this->generateUrl('fenchy_regular_user_notice_create1'));
        
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
            
            $lat = $notice->getLocation()->getLatitude();
            $log = $notice->getLocation()->getLongitude();

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
            
        $payment = false;        
        $paymentsetting = $em->getRepository('UserBundle:Payment')->checkUser($userLoggedIn);
        if($paymentsetting)
        	$payment = true;
        else
        	$payment = false;
        
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
        	'payment' => $payment
        ));
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
    
    /**
     * manage user's listings action
     * @author Mateusz Krowiak <mkrowiak@pgs-soft.com>
     */
    public function manageAction() {
        
        $user = $this->get('security.context')->getToken()->getUser();
       	//$user->getPaymentId()->getType();
        $em = $this->getDoctrine()->getEntityManager();
        
        $repo = $em->getRepository('FenchyNoticeBundle:Notice');
        $requestRepo = $em->getRepository('FenchyNoticeBundle:Request');
        
        $listings = $repo->getUserNotices($user);
        $neighbourRequests = $requestRepo->getNeighboursRequests($user);
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
        	}
        	
        	$messenger = $this->get('fenchy.messenger');
        	$receiver = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($neighbourRequest->getAuthor()->getId());
        	if (null === $receiver || $receiver === $this->getUser()) {
        		throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        	}
        	$messenger->setReceiver($receiver);
        	$msgForm = $messenger->createForm();
			$msgForms[$i] = $msgForm->createView();
			$i++;
        	
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
        $forms[][] = array();
        $count[] = array();
        $managertype[] = array();
        $statusflag = true; 
        $i=0;$j=0;
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
        	
        	$c = 0;$status='';$statusflag = true; 
        	foreach ( $initialRequests[$i] as $initialRequest )
        	{
        		if ($initialRequest)
        		{
        			$targetRequest = $requestRepo->findOneBy( array('id'=>$initialRequest['id']) );
        			$targetRequest->setIsRead(true);
        			$em->persist($targetRequest);
        			$em->flush();        			
        			$userRepository = $this->getDoctrine()
        				->getRepository('UserBundle:User');
        			
        			$managertype[$i][$j] = $em
        				->getRepository('FenchyRegularUserBundle:UserRegular')
        				->getManagerType($userRepository->find($initialRequest['author']['id']));
        			
		        	$messenger = $this->get('fenchy.messenger');
		        	$receiver = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($initialRequest['author']['id']);
		        	if (null === $receiver || $receiver === $this->getUser()) {
		        		throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		        	}
		        	$messenger->setReceiver($receiver);
		        	$form = $messenger->createForm();
		        	$forms[$i][$j] = $form->createView();
		        	
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
        	$i++;
        }           
        $my_req_count = $requestRepo->countUnreadUsersStatusRequests($user,$listings);
        $req_count = $requestRepo->countUnreadUsersRequests($user, $listings);
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

                $messenger = $this->get('fenchy.messenger');
                $receiver = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($neighbourRequest->getAuthor()->getId());
                
                if (null === $receiver) {
                        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
                }
                $messenger->setReceiver($receiver);
                $msgForm = $messenger->createForm();
                        $msgForms[$i] = $msgForm->createView();
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
        		'managerTypes'	=> $managertype,
        		'reviews'	=> sizeof($initialReviews),        		
        		'forms' => $forms,
        		'count' => $count,
        		'my_req_count' => $my_req_count,
        		'req_count' => $req_count,
        		'neighbourRequests' => $neighbourRequests,
                        'joinClosedGroupRequests' => $joinClosedGroupRequests,
        		'msgForm' => $msgForms,
        		'cvv_code' => $cvv_code
         ));
        
    }
    
    public function myRequestsAction() {
    
    	$user = $this->get('security.context')->getToken()->getUser();
    
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$requestRepo = $em->getRepository('FenchyNoticeBundle:Request');
    	$requests = $requestRepo->getNoticeIds($user);
		$neighbourRequests = $requestRepo->getRequests($user);
		
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
			}
			$messenger = $this->get('fenchy.messenger');
			$receiver = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($neighbourRequest->getAboutUser()->getId());
			if (null === $receiver || $receiver === $this->getUser()) {
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
			}
			$messenger->setReceiver($receiver);
			$msgForm = $messenger->createForm();
			$msgForms[$i] = $msgForm->createView();
			$i++;
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
    	foreach ($listings as $k => $listing)
    	{
    		if($listing->getUserGroup()!=null)
    		{
    			unset($listings[$k]);
    		}
    	}
    	
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
    	$i=0;
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
    		
    		$k=0;
    		$forms[][] = array();
    		
    		$c = 0;$status='';$statusflag = true;
    		foreach ( $initialRequests[$i] as $initialRequest )
    		{
    			if ($initialRequest)
    			{
    				$targetRequest = $requestRepo->findOneBy( array('id'=>$initialRequest['id']) );
    				$targetRequest->setIsReadStatus(true);
    				$em->persist($targetRequest);
    				$em->flush();
    				
    				$userRepository = $this->getDoctrine()
    						->getRepository('UserBundle:User');
    				 
    				$managertype[$i][$k] = $em
		    				->getRepository('FenchyRegularUserBundle:UserRegular')
		    				->getManagerType($userRepository->find($initialRequest['author']['id']));
    				
    				$messenger = $this->get('fenchy.messenger');
    				$receiver = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($initialRequest['aboutuser']['id']);
    				if (null === $receiver || $receiver === $this->getUser()) {
    					throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
    				}
    				$messenger->setReceiver($receiver);
    				$form = $messenger->createForm();
    				$forms[$i][$k] = $form->createView();
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
    				$k++;
    			}
    		}
    		if($statusflag)
    		{
    			if($c == 0)
    				$count[$i] = 'PENDING';
    			else
    				$count[$i] = $c." RUNNING";
    		}
    		else
    		{
    			if($status == '')
    				$status = 'rejected';
    			$count[$i] = $status;
    		}
    		$i++;
    		
    	}
    	$my_req_count = $requestRepo->countUnreadUsersStatusRequests($user, $listings);
    	$req_count = $requestRepo->countUnreadUsersRequests($user, $listings);
                
                // Join Closed Group Requests
                $neighbourRequests2 = $requestRepo->getRequestsToUserGroup($user);
		
		$i=0;$msgForms1[] = array();
		foreach ($neighbourRequests2 as $k =>$neighbourRequest)
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
			}
			$messenger = $this->get('fenchy.messenger');
			$receiver = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($neighbourRequest->getAuthor()->getId());
			if (null === $receiver) {
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
			}
			$messenger->setReceiver($receiver);
			$msgForm = $messenger->createForm();
			$msgForms1[$i] = $msgForm->createView();
			$i++;
		}
        
        
    	return $this->render('FenchyRegularUserBundle:Listing:myRequests.html.twig', array(
    			'listings' => $listings,
    			'displayUser' => $displayUser,
    			'usersOwnProfile' => true,
    			'initialReviews' => $initialReviews,
    			'initialComments'=>	$initialComments,
    			'initialRequests' => $initialRequests,
    			'managerTypes'	=> $managertype,
    			'reviews'	=> sizeof($initialReviews),
    			'forms' => $forms,
    			'count'=> $count,
    			'my_req_count' => $my_req_count,
    			'req_count' => $req_count,
    			'neighbourRequests' => $neighbourRequests,
    			'msgForm' => $msgForms,
                        'requestsToGroups' => $neighbourRequests2,
    			'msgForm1' => $msgForms1,
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
        if ('POST' == $request->getMethod()) {

            
            $form->bindRequest($request);
            
//             if ($form->isValid()) {             	
                $regularUser = $this->get('security.context')->getToken()->getUser()->getRegularUser();
                $em = $this->getDoctrine()->getManager();

                $notice = $em->getRepository('FenchyNoticeBundle:Notice')->find($id);

                if (null === $notice) {
                    throw new NotFoundHttpException('Notice not found!');
                }
                
                if ($notice->getUser()->getId() !== $this->get('security.context')->getToken()->getUser()->getId()) {
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
			$str .= '<p style="color: #FFFFFF !important; font-size: 11px; margin-left: 9px; margin-top: 5px; position: absolute;">'. $managertypeLoggedIn[1].'</p>';
			$str .= '<span style="margin-top: 13px;">'.$userLoggedIn->getActivity().'</span></div>';
			$str .= '<div class="descdata" style="margin-left: 65px; width: 83% ! important; margin-top: 10px;"><p><input id="commenttext" style="width: 80%" type="textarea" placeholder="post a comment"/></p></div>';
   			$str .= '<input style="float: right; margin-left: 16px; margin-top: -42px; position: relative; cursor: pointer;" class="blue_button" type="button" value="POST" onclick="postComment();" />';
   			
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
    	 
    	$userLoggedIn = $this->get('security.context')->getToken()->getUser();
    	$em = $this->getDoctrine()->getManager();
    	
    	$em = $this->getDoctrine()->getEntityManager();    	
    	
    	if($text != "")
    	{
    		if ( !$request->isMethod('POST') ) {
    			return new Response('',401);
    		}
    
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
    			$review->setAboutUser($targetUser);
    			$review->setAboutNotice($targetNotice);
     			$em->persist($review);
                        
                        $targetUser->addActivity(2);
                        $em->persist($targetUser);
                     
     			$em->flush();
     			
     			$messenger = $this->get('fenchy.messenger');
     			$notice = $messenger->setNotice($targetNotice->getId());
     			
     			$receiver = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($targetUser->getId());
     			if (null != $receiver && $receiver != $this->getUser())
     			{
     				$messageObject = new Message();
     				$messenger->setReceiver($receiver);
     				$messageObject->setTitle($this->get('translator')->trans('regularuser.message.message_part', array(
    					'%username%' => $targetUser->getRegularUser()->getFirstname())));
     				$messageObject->setContent($text);
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
    
    public function sendRequestAction()
    {
    	$request = $this->getRequest();
    	$noticeId = $request->get('noticeId');	
    	$text = $request->get('request_text');
    	$item = $request->get('item') ? $request->get('item'): null;
    	$price = $request->get('price') ? $request->get('price'): 0;
    	$total = $request->get('total') ? $request->get('total'): 0;
    	//$currency = $request->get('currency') != 'CURRENCY' ? $request->get('currency') : null;
    	$free = $request->get('free') ? $request->get('free') : false;
    	$offerprice = $request->get('offerprice') ? $request->get('offerprice') : 0;
    	$swapmsg = $request->get('swap_msg') ? $request->get('swap_msg') : null;
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
		    	$noticerequest->setPieceSpot($item);
		    	$noticerequest->setPrice($price);
		    	$noticerequest->setSwapMsg($swapmsg);
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
		    	
		    	$receiver = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($targetUser->getId());
		    	if (null != $receiver && $receiver != $this->getUser())
		    	{
		    		$messageObject = new Message();
		    		$messenger->setReceiver($receiver);
     				$messageObject->setTitle($this->get('translator')->trans('regularuser.message.message_part', array(
    					'%username%' => $targetUser->getRegularUser()->getFirstname())));
		    		$messageObject->setContent($text);
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
    
    	//print_r($notice->getGallery()->getImages());
    	if(!$notice || $notice->getUser()->getId() !== $user->getId()) throw $this->createNotFoundException('Listing does not exists');
    
    	// We do not edit drafts.
    	if($notice->getDraft()) return $this->redirect($this->generateUrl('fenchy_regular_user_notice_create1'));
    
    	if (!$this->getRequest()->isMethod('POST')) {
    
    			
    		$notice1 = $this->createDraft($user);
    		$notice1->setType($notice->getType());
    		$em->persist($notice1);
    		$em->flush();
    		
    		$data = $this->get('fenchy.gallery_manager')->manageGallery($notice1->getGallery());
    		
    		// Create form and manage listing gallery
    		$form = $this->createForm(new NoticeListingType($notice->getType(), $notice), $notice);
    		$data = $this->get('fenchy.gallery_manager')->manageGallery($notice1->getGallery());
    
    		$data['images']   = $images;
    		$data['notice']   = $notice;
    		$data['form']     = $form->createView();
    		$data['type']     = $notice->getType();
    		$data['location'] = $notice->getLocation();
    		$data['tags']     = $this->get('fenchy_dictionary')->getAllListingTags();
    
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
    				if($targetRequest->getPrice()>0)
    					$noticeObj->setCompleted(true);
    				$em->persist($noticeObj);
    				$em->persist($targetRequest);
    				$em->flush();
    			}
    			else if($ava_item<$req_item)
    			{
    				$targetRequest->setPieceSpot($ava_item);
    				$targetRequest->setTotalprice($ava_item * $targetRequest->getProposeprice()>0 ? $targetRequest->getProposeprice() : $targetRequest->getPrice());
    				$noticeObj->setPieces(0);
    				if($targetRequest->getPrice()>0)
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
    			if($ava_item<$req_item)
    			{
    				$targetRequest->setPieceSpot($ava_item);
    				$targetRequest->setTotalprice($ava_item * $targetRequest->getProposeprice()>0 ? $targetRequest->getProposeprice() : $targetRequest->getPrice());
    				//if($targetRequest->getPrice()>0)
    					$noticeObj->setCompleted(true);
    				$noticeObj->setSpot(0);
    				$em->persist($noticeObj);
    				$em->persist($targetRequest);
    				$em->flush();    				
    			}
    			else
    			{
    				$noticeObj->setSpot($ava_item - $req_item);
    				$targetRequest->setTotalprice($req_item * $targetRequest->getProposeprice()>0 ? $targetRequest->getProposeprice() : $targetRequest->getPrice());
    			}
    		}
    	}
    	
    	$em = $this->getDoctrine()->getManager();
    	
    	if($status != 'rejected')
    		$targetRequest->setStatus($status);    	
    	$targetRequest->setRequeststatus($status);
    	$targetRequest->setIsReadStatus(false);
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
    	if($targetRequest->getTotalprice()>0)
    		$str .= "^^".intval((($targetRequest->getTotalprice()*10)/100)*100);
    	else 
    		$str .= "^^".$targetRequest->getTotalprice()*0;
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
    		}
    	}
    	$response = array("success" => $str);
    	return new Response(json_encode($response));
    }
    
    public function markAsReadAction()
    {
    	$request = $this->getRequest();    	
    	$requestId = $request->get('request_id');    	
    	$flag = $request->get('flag');
    	    	 
    	$em = $this->getDoctrine()->getManager();
    	$requestRepo = $em->getRepository('FenchyNoticeBundle:Request');
    	 
    	$targetRequest = $requestRepo->findOneBy( array('id'=>$requestId) );
    	if($flag=="true")
    		$targetRequest->setIsReadStatus(true);
    	elseif($flag=="false")
    		$targetRequest->setIsRead(true);
    	$em->persist($targetRequest);
    	$em->flush();
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
    		if($user->getPaymentId()->getType()=='credit')
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
    			if (null === $receiver || $receiver === $this->getUser())
    			{
    				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
    			}
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
    			$content .= $this->get('translator')->trans('regularuser.message.message_last_part');
    			$messageObject->setTitle($title);
    	
    			$messageObject->setContent($content);
    	
    			$messageObject->setSender($displayUser);
    			$messageObject->setReceiver($receiver);
    	
    			$message = $messenger->send($messageObject);
    	
    			if ($this->container->getParameter('notifications_enabled')) $this->messageNotification($message,$userLoggedIn);
    	
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
    				if (null === $receiver || $receiver === $this->getUser())
    				{
    					throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
    				}
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
    				$content .= $this->get('translator')->trans('regularuser.message.message_last_part');
    				$messageObject->setTitle($title);
    	
    				$messageObject->setContent($content);
    	
    				$messageObject->setSender($displayUser);
    				$messageObject->setReceiver($receiver);
    	
    				$message = $messenger->send($messageObject);
    	
    				if ($this->container->getParameter('notifications_enabled')) $this->messageNotification($message, $userLoggedIn);
    	
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
    			->setFromAddress($this->container->getParameter('from_email'))
    			->setFromName($this->container->getParameter('from_name'))
    			->setToAddress($receiver->getEmail())
    			->setSubject($this->get('translator')->trans('message.req_notification.email.subject', array(
    					'%username%' => $sender->getRegularUser()->getFirstname())))
    			->setBodyHtml($this->renderView('FenchyMessageBundle:Notifications:requestEmailHTML.html.twig',
    					array()))
    					->setBodyPlain($this->renderView('FenchyMessageBundle:Notifications:requestEmailPlain.html.twig',
    							array()));
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
    							array()),
    							'text/html')
    							->addPart($this->renderView('FenchyMessageBundle:Notifications:requestEmailPlain.html.twig',
    									array()),
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
