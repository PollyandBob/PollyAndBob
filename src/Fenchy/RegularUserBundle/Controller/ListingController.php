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
    	
    	$alertmsg = true;
    	
    	if($user->getActivity() < 400 )
    	{
    		$alertmsg = false;
    	}
    	
        $direction = $this->container->getParameter('notice_menu_property');
        $em = $this->getDoctrine()->getManager();
        $types = $em->getRepository('FenchyNoticeBundle:Type')->getAllWithProperties();

        return $this->render(
                'FenchyRegularUserBundle:Listing:create1.html.twig', 
                array(
                    'types' => $types,
                    'direction' => $direction,
                	'alertmsg'=> $alertmsg, 	
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
        
        $user = $this->get('security.context')->getToken()->getUser();
        $location = $user->getLocation();
        $payment = false;
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
	    	if($user->getActivity() < 400 )
	    	{	
	    		$alertmsg =  "alert('".$val."');";
	    	}
    	}
    	
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
        ->add('groupname', null, array(
          	  'attr' => array('placeholder' => 'regularuser.your_groupname')))
        ->add('aboutGroup', 'textarea', array(
              'attr' => array('placeholder' => 'regularuser.your_aboutgroup')))
        ->add('status', 'choice', array(
        	  'label' => 'regularuser.status',
        	  'choices' => UserGroup::$statusMap))
        ->add('file',null,array('label' => 'settings.general.profile_photo'))
        ->add('file2',null,array('label' => 'settings.general.cover_photo'))
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
                
                // PUT ON FB
                //$fb_feed = $facebookProvider = $this->get('fenchy.facebook.user')->post($notice);
                
                $positive = $this->get('translator')
                        ->trans('notice.added_successfully');
                
                $negative = '';
                
//                 if($fb_feed instanceof \Fenchy\NoticeBundle\Entity\FacebookFeed) {
//                     $em->persist($fb_feed);
//                     $positive .= '<br/>'.$this->get('translator')
//                         ->trans('notice.successfully_posted_on_fb');
//                 }
//                 elseif(FALSE === $fb_feed) {
//                     $negative .= $this->get('translator')
//                         ->trans('notice.posting_on_fb_failed');
//                 }

                $em->persist($notice);
                $em->flush();
                
                // set flash messages
                $this->get('session')->setFlash('positive', $positive);
                if($negative) $this->get('session')->setFlash('negative', $negative);
                
                // Done :) Let the user see new notice.
                $created_at = $notice->getCreatedAt();
                
                if($created_at) {
                    return $this->redirect($this->generateUrl('fenchy_notice_show_slug', array(
                        'slug' => $notice->getSlug(),
                        'year' => $created_at->format('Y'),
                        'month' => $created_at->format('m'),
                        'day' => $created_at->format('d')
                    )));
                } else {
                    return $this->redirect($this->generateUrl('fenchy_regular_user_notice_show', array('id' => $notice->getId())));
                }                
                
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
    				$url = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins='.$origin.'&destinations='.$destination.'&mode=driving&language=en&sensor=false';
    				$data = file_get_contents($url);
    				//$data = utf8_decode($data);
    				$obj = json_decode($data);
    
    				//echo($obj->rows[0]->elements[0]->distance->text); //km
    				//echo($obj->rows[0]->elements[0]->distance->value); // meters
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
    
    
    
    				$gmap_distance =  $obj->rows[0]->elements[0]->distance->value; // location with gmap api distance
    
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
        
        if(!$notice || $notice->getUser()->getId() !== $user->getId()) throw $this->createNotFoundException('Listing does not exists');
        
        // We do not edit drafts.
        if($notice->getDraft()) return $this->redirect($this->generateUrl('fenchy_regular_user_notice_create1'));
        
        if (!$this->getRequest()->isMethod('POST')) {
            
            // Create form and manage listing gallery
            $form = $this->createForm(new NoticeListingType($notice->getType(), $notice), $notice);
            $data = $this->get('fenchy.gallery_manager')->manageGallery($notice->getGallery());
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
                $success_msg = $this->get('translator')->trans('listing.edit.flash_success');
                $this->get('session')->setFlash('positive', $success_msg);
                
                return $this->redirect($this->generateUrl('fenchy_regular_user_notice_edit', array('id' => $notice->getId())));
            }
            else {
                // form is invalid so we need to display it again, but gallery 
                // should not be reseted. We need to inform gallery manager that form was invalid.
            	$data = $this->get('fenchy.gallery_manager')->manageGallery($notice->getGallery(), FALSE);
                $data['notice']   = $notice;
                $data['form']     = $form->createView();
                $data['type']     = $notice->getType();

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
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $repo = $em->getRepository('FenchyNoticeBundle:Notice');
        
        $listings = $repo->getUserNotices($user);
        
        $userId = $user->getId();
        $displayUser = false;
        
        if ( $userId != NULL ) {
            
            $userOther = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')->getAllData( $userId );

            if ( ! $userOther instanceof \Fenchy\UserBundle\Entity\User )
                return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
            $displayUser = $userOther;

        }
        $requestRepo = $em->getRepository('FenchyNoticeBundle:Request');
       		$my_req_count = $requestRepo->countUnreadUsersStatusRequests($user);
       		$req_count = $requestRepo->countUnreadUsersRequests($user);
        
        $initialReviews[] = array();
        $initialComments[] = array();
        $initialRequests[] = array();
        $forms[][] = array();
        $count[] = array();
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
        			$count[$i] = 'PENDING';
        		else
        			$count[$i] = $c." RUNNING";
        	}
        	else 
        	{
        		$count[$i] = $status;
        	}
        	$i++;
        }           
        
		//echo "sssss<pre>";print_r($form1[5][0]);exit;
//        echo "<pre>";print_r($initialRequests[5]);exit;
        return $this->render('FenchyRegularUserBundle:Listing:manage.html.twig', array(
            	'listings' => $listings,
            	'displayUser' => $displayUser,
            	'usersOwnProfile' => true,
        		'initialReviews' => $initialReviews,
        		'initialComments'=>	$initialComments,
        		'initialRequests' => $initialRequests,
        		'reviews'	=> sizeof($initialReviews),        		
        		'forms' => $forms,
        		'count' => $count,
        		'my_req_count' => $my_req_count,
        		'req_count' => $req_count,
         ));
        
    }
    
    public function myRequestsAction() {
    
    	$user = $this->get('security.context')->getToken()->getUser();
    
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$requestRepo = $em->getRepository('FenchyNoticeBundle:Request');
    	$requests = $requestRepo->getNoticeIds($user);

    	$listings = array();
    	$j = 0; $k = 0;
    	foreach ($requests as $request)
    	{
    		$listings[$j++]  = $request->getAboutNotice();
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
    	
    	$userId = $user->getId();
    	$displayUser = false;
    
    	if ( $userId != NULL ) {
    
    		$userOther = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')->getAllData( $userId );
    
    		if ( ! $userOther instanceof \Fenchy\UserBundle\Entity\User )
    			return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
    		$displayUser = $userOther;
    
    	}
        	
    	$my_req_count = $requestRepo->countUnreadUsersStatusRequests($user);
    	$req_count = $requestRepo->countUnreadUsersRequests($user);
    	
    	$initialReviews[] = array();
    	$initialComments[] = array();
    	$initialRequests[] = array();
    	$forms[][] = array();
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
    			$count[$i] = $status;
    		}
    		$i++;
    	}
    	//echo $count[0];exit;
    	return $this->render('FenchyRegularUserBundle:Listing:myRequests.html.twig', array(
    			'listings' => $listings,
    			'displayUser' => $displayUser,
    			'usersOwnProfile' => true,
    			'initialReviews' => $initialReviews,
    			'initialComments'=>	$initialComments,
    			'initialRequests' => $initialRequests,
    			'reviews'	=> sizeof($initialReviews),
    			'forms' => $forms,
    			'count'=> $count,
    			'my_req_count' => $my_req_count,
    			'req_count' => $req_count,
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
   			$str = '<a href="javascript:void(0);">';
   			$str .= '<img style="position: absolute; margin-top: 10px; display: block; border-radius: 50% 50% 50% 50%;" src="/'.$avatar. '" width="23" height="23" alt="" />';
   			$str .= '</a><div class="descdata" style="margin-left: 34px; width: 89% !important;"><p><input id="commenttext" style="width: 80%" type="textarea" placeholder="post a comment"/></p></div>';
   			$str .= '<input style="float: right; margin-left: 16px; margin-top: -42px; position: relative; cursor: pointer;" class="blue_button" type="button" value="POST" onclick="postComment();" />';
   			
   			foreach ($initialComments as $initialComment)
   			{
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
   					$str .= '<p>' .$initialComment['text']. '</p></div>';
   				}
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
    			$em->persist($userLoggedIn);
    			$review->setAuthor($userLoggedIn);
    			$review->setText($text);
    			$review->setType(1);
    			$review->setAboutUser($targetUser);
    			$review->setAboutNotice($targetNotice);
     			$em->persist($review);
     			$em->flush();
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
    	$currency = $request->get('currency') != 'CURRENCY' ? $request->get('currency') : null;
    	$free = $request->get('free') ? $request->get('free') : false;
    	$offerprice = $request->get('offerprice') ? $request->get('offerprice') : 0;
    	 
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
		    	$noticerequest->setTotalprice($total);
		    	$noticerequest->setCurrency($currency);
		    	$noticerequest->setFree($free);
		    	$noticerequest->setProposeprice($offerprice);
		    	$em->persist($noticerequest);
		    	$em->flush();
		    }
    	}
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
    	if($status != 'rejected')
    		$targetRequest->setStatus($status);
    	$targetRequest->setRequeststatus($status);
    	$targetRequest->setIsReadStatus(false);
    	$em->persist($targetRequest);
    	$em->flush();
    	
    	$pagination = $this->container->getParameter('reviews_pagination');
    	$initialRequests = $requestRepo->findByInJSON(
    			$this->container->get('router'),
    			array('aboutNotice'=>$notice_id),
    			array('created_at'=>'DESC'), $pagination+1, 0);
    	

    	$c = 0;$status='';$statusflag = true;
    	$str='';
    	
    		if ($targetRequest->getRequestStatus() == 'pending')
    		{
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
    			
    		
   		foreach ( $initialRequests as $initialRequest )
   		{	 
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
    	if($statusflag)
    	{
    		if($c == 0)
    			$count = 'PENDING';
    		else
    			$count = $c." RUNNING";
    	}
    	else
    	{
    		$count = $status;
    	}
    	$str .= "^^".$count;
    	$str .= "^^".$targetRequest->getRequeststatus();
    	
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
}