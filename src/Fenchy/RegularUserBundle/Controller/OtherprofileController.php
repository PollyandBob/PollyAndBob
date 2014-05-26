<?php

namespace Fenchy\RegularUserBundle\Controller;

use Fenchy\RegularUserBundle\Entity\Neighbors;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Fenchy\RegularUserBundle\Form\UserRegularType;
use Fenchy\RegularUserBundle\Form\LeaveReviewType;
use Fenchy\RegularUserBundle\Form\LeaveReviewProfileType;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\RegularUserBundle\Entity\Document;
use Fenchy\UserBundle\Entity\User;
use Fenchy\RegularUserBundle\Form\AboutMeType,
    Fenchy\NoticeBundle\Form\InterestsAndActivitiesType,
    Fenchy\NoticeBundle\Entity\Notice,
    Fenchy\NoticeBundle\Entity\Review,
    Fenchy\GalleryBundle\Entity\Gallery,
    Fenchy\GalleryBundle\Entity\Image;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Fenchy\UserBundle\Entity\NotificationGroupInterval;
use Fenchy\UserBundle\Entity\NotificationQueue;
use Fenchy\UserBundle\Form\UserLocationType;
use Fenchy\RegularUserBundle\Form\UserAboutSettingsType;
use Doctrine\Tests\Common\Annotations\True;
use Doctrine\Tests\Common\Annotations\False;
use Doctrine\Tests\Common\Annotations\Null;


class OtherprofileController extends Controller
{
    /*		Version 2.1 from 12th Aug, 2013			*/
	public function userProfileMainAction( $userId )
	{		
		return $this->render('FenchyRegularUserBundle:Myprofile:userProfileMain.html.twig');		
	}
		
	public function aboutOtherChoiceAction($userId)
	{	
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getEntityManager();
				
		if ( $userId != NULL )
		{
			
			$userOther = $em->getRepository('UserBundle:User')->getAllData( $userId );
			
			if ( ! $userOther instanceof \Fenchy\UserBundle\Entity\User )
				return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
			$displayUser = $userOther;
			$usersOwnProfile = 0;
		}
		else 
		{
			
			if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
				return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
			$displayUser = $userLoggedIn;
			$usersOwnProfile = 1;
		}	
		
		$request = $this->getRequest();
	
		if ($userLoggedIn->getId()==$userId)
		{
			return new RedirectResponse($this->container->get('router')->generate('fenchy_regular_user_user_myprofile_aboutmychoice'));
		}
		
		return $this->render('FenchyRegularUserBundle:Otherprofile:aboutOtherChoice.html.twig', 
				array('displayUser' => $displayUser,
					  'userId'		=> $userId));
	
	}
	public function otherAvatarAction($userId)
	{
		$em = $this->getDoctrine()->getEntityManager();
		
		if ( $userId != NULL ) 
		{
			$userOther = $em->getRepository('UserBundle:User')->getAllData( $userId );
			
			if ( ! $userOther instanceof \Fenchy\UserBundle\Entity\User )
				return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
			$displayUser = $userOther;
			$usersOwnProfile = 0;
		}
		else 
		{
			
			if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
				return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
			$displayUser = $userLoggedIn;
			$usersOwnProfile = 1;
		}
		
		$document = new Document();
		$em = $this->getDoctrine()->getEntityManager();
		$result = $em->getRepository('FenchyRegularUserBundle:Document')->findById($displayUser->getId());
		
		if($result)
		{
			$proimg = $result->getWebPath();
			$covrimg = $result->getWebPath2();
                        $cropX = $result->getCropX();
			$cropY = $result->getCropY();
		
		}
		else
		{
			$proimg = '';
			$covrimg = '';
                        $cropX = 0;
			$cropY = 0;
		
		}
		
		$verified = $this->getDoctrine()
				->getRepository('UserBundle:LocationVerification')->getStatus($displayUser);
		
		$identity = $this->getDoctrine()
				->getRepository('UserBundle:IdentityVerification')->getIdentityStatus($displayUser);
			
		
		// Added By bhumi for Manager type
		
		$userRepository = $this->getDoctrine()
			->getRepository('UserBundle:User');
			
		$managertype = $em
			->getRepository('FenchyRegularUserBundle:UserRegular')
			->getManagerType($userRepository->find($displayUser));
		
		// Added By jignesh for Manager type
                $data['cropX'] = $cropX;
		$data['cropY'] = $cropY;    
		return $this->render('FenchyRegularUserBundle:Otherprofile:otherAvatar.html.twig',
				array(
						'locale' => $this->getRequest()->getLocale(),
						'displayUser' => $displayUser,
						'userId'		=> $userId,
						'verified'	 => $verified,
						'identity'	 => $identity,
						'managertype'=>$managertype,
						'profilepath'	 => $proimg,
						'coverpath'	 => $covrimg,
                                                'cropX'          => $cropX,
                                                'cropY'          => $cropY,
				));
	}
	public function otherNeighborsAction($userId)
	{
		
		$em = $this->getDoctrine()->getEntityManager();
				
		if ( $userId != NULL ) 
		{
			
			$userOther = $em->getRepository('UserBundle:User')->getAllData( $userId );
			
			if ( ! $userOther instanceof \Fenchy\UserBundle\Entity\User )
				return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
			$displayUser = $userOther;
			$usersOwnProfile = 0;
		}
			
		
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
		$users2 = array();
		$managertype = array();
		$i = 0 ;
		$filterdata = "";
		foreach ($users as $user) {
				
			//$avatar = $user->getRegularUser()->getAvatar(false);
                        $avatar = '';
			$em = $this->getDoctrine()->getEntityManager();
			$neighbor = $em->getRepository('FenchyRegularUserBundle:Neighbors')->findById($currentuid,$user->getId());
			if(!$neighbor)
			{
				$neighbor = $em->getRepository('FenchyRegularUserBundle:Neighbors')->findById($user->getId(),$currentuid);
			}	
			$neighbors = '';
			if($neighbor)
			{
				$neighbors = $neighbor->getId();
			}
		 if($neighbors)
		 {
			
			$document = new Document();
			$em = $this->getDoctrine()->getEntityManager();
			$result = $em->getRepository('FenchyRegularUserBundle:Document')->findById($user->getId());
				
			if($result){

                            if($result->getWebPath())
                            {
                                    $avatar = $result->getWebPath();
                            }
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
				
				
// 				$url = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins='.$origin.'&destinations='.$destination.'&mode=driving&language=en&sensor=false';
// 				$data = file_get_contents($url);
// 				//$data = utf8_decode($data);
// 				$obj = json_decode($data);
		
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
				
				if($gmap_distance > $mindist && $gmap_distance < $maxdist && $gmap_distance <= $dist)
				{
					// Added By jignesh for Manager type
					
					$managertype[$i++] = $em
					        ->getRepository('FenchyRegularUserBundle:UserRegular')
					        ->getManagerType($user);
					
					// Added By jignesh for Manager type
					$user->setTwitterUsername($avatar);
					$users2[] = $user;
				
				}
			}
		  
		  }
		}
		
		return $this->render('FenchyRegularUserBundle:Otherprofile:otherNeighbors.html.twig',
				array(
						'locale'            => $this->getRequest()->getLocale(),
						'displayUser'		=> $displayUser,
						'users'				=> $users,
						'users2'			=> $users2,
						'filterdata'		=> $filterdata,
						'listingsPagination'=>$this->container->getParameter('listings_pagination'),
						'filterEmptyCat'=>$emptyFilter['categories'],
						'filterEmptyPD'=>$emptyFilter['postDate'],
						'filterDistanceSliderMinUser'=>$this->container->getParameter('filter_min_distance_user'),
						'filterDistanceSliderMaxUser'=>$this->container->getParameter('filter_max_distance_user'),
						'filterDistanceSliderDefaultUser'=>$this->container->getParameter('distance_slider_default_user'),
						'filterDistanceSliderSnapUser'=>$this->container->getParameter('distance_slider_snap_user'),
						'filterLastUrl' => $this->get('session')->get('lastFilterUrl'),
						'userId'		=> $userId,
						'managertype' => $managertype
				));
	}
	public function otherFilterUsersAction($userId)
	{	
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getEntityManager();
		
		if ( $userId != NULL )
		{
				
			$userOther = $em->getRepository('UserBundle:User')->getAllData( $userId );
				
			if ( ! $userOther instanceof \Fenchy\UserBundle\Entity\User )
				return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
			$displayUser = $userOther;
			$usersOwnProfile = 0;
		}
		else
		{
				
			if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
				return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
			$displayUser = $userLoggedIn;
			$usersOwnProfile = 1;
		}
		
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
		$users2 = array();
		$filterdata = "";
		$managertype = array();
		$i = 0;
		foreach ($users as $user) {
			
			//$avatar = $user->getRegularUser()->getAvatar(false);
                        $avatar = '';
			$em = $this->getDoctrine()->getEntityManager();
			$neighbor = $em->getRepository('FenchyRegularUserBundle:Neighbors')->findById($currentuid,$user->getId());
			if(!$neighbor)
			{
				$neighbor = $em->getRepository('FenchyRegularUserBundle:Neighbors')->findById($user->getId(),$currentuid);
			}
			$neighbors = '';
			if($neighbor)
			{
				$neighbors = $neighbor->getId();
			}
		if($neighbors)
		{
			
                $document = new Document();
                $em = $this->getDoctrine()->getEntityManager();
                $result = $em->getRepository('FenchyRegularUserBundle:Document')->findById($user->getId());
                
                if($result){
                    
                    if($result->getWebPath())
                    {
                            $avatar = $result->getWebPath();
                    }
                    else
                    {
                            $avatar = 'images/default_profile_picture.png';
                    }
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
				
				
// 				$url = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins='.$origin.'&destinations='.$destination.'&mode=driving&language=en&sensor=false';
// 				$data = file_get_contents($url);
// 				//$data = utf8_decode($data);
// 				$obj = json_decode($data);
				
				//echo($obj->rows[0]->elements[0]->distance->text); //km
				//echo($obj->rows[0]->elements[0]->distance->value); // meters
				$mindist = $this->container->getParameter('filter_min_distance_user');// minimum distance
				$maxdist = $this->container->getParameter('filter_max_distance_user');// maximum distance 
				$dist = $request->query->get('dist'); // slider distance
				
				
				
				if($gmap_distance > $mindist && $gmap_distance < $maxdist && $gmap_distance <= $dist)
				{		
					// Added By jignesh for Manager type
					
					$managertype[$i] = $em
					        ->getRepository('FenchyRegularUserBundle:UserRegular')
					        ->getManagerType($user);
					
					// Added By jignesh for Manager type
					
					$filterdata .= '<div class="persen">
									<a href="'.$this->generateUrl('fenchy_regular_user_user_otherprofile_aboutotherchoice').'/'.$userid.'">
									<img src="'.'/'.$avatar.'" alt="" />
										<div class="persenname">
											<p>'.$firstname.'</p>
										</div>';
			        $filterdata .=	'<div class="neighbor '.$managertype[$i][2].'">
											<p>'.$managertype[$i][1].'</p>
											<span>'.$user->getActivity().'</span>
										</div>
									</a>
								   </div>';
				}
			}
		 }
		 $i++;
		}
		
		echo $filterdata;
		
		
		return $this->render('FenchyRegularUserBundle:Otherprofile:otherFilterUsers.html.twig',
				array(
						'locale'            => $this->getRequest()->getLocale(),
						'displayUser'		=> $displayUser,
						'users'				=> $users,
						'getdst'			=> $dist,
						'filterdata'		=> $filterdata,
						'listingsPagination'=>$this->container->getParameter('listings_pagination'),
						'filterEmptyCat'=>$emptyFilter['categories'],
						'filterEmptyPD'=>$emptyFilter['postDate'],
						'filterDistanceSliderMinUser'=>$this->container->getParameter('filter_min_distance_user'),
						'filterDistanceSliderMaxUser'=>$this->container->getParameter('filter_max_distance_user'),
						'filterDistanceSliderDefaultUser'=>$this->container->getParameter('distance_slider_default_user'),
						'filterDistanceSliderSnapUser'=>$this->container->getParameter('distance_slider_snap_user'),
						'filterLastUrl' => $this->get('session')->get('lastFilterUrl'),
						'userId'		=> $userId,
						'managertype' => $managertype
				));
	}
	public function otherCommunityPointsAction($userId)
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getEntityManager();
		
		if ( $userId != NULL )
		{
				
			$userOther = $em->getRepository('UserBundle:User')->getAllData( $userId );
				
			if ( ! $userOther instanceof \Fenchy\UserBundle\Entity\User )
				return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
			$displayUser = $userOther;
			$usersOwnProfile = 0;
		}
		else
		{
				
			if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
				return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
			$displayUser = $userLoggedIn;
			$usersOwnProfile = 1;
		}

		$reviewRepo = $em->getRepository('FenchyNoticeBundle:Review');
		$noticeRepo = $em->getRepository('FenchyNoticeBundle:Notice');
		$userRepo = $em->getRepository('UserBundle:User');
		
		//$displayUser = $userRepo->getAllData( $displayUserId );
		$contactsCount = count( $displayUser->getRegularUser()->getMyFriends() );
		$fbConnect = $displayUser->getFacebookId() == NULL ? FALSE : TRUE;
		$positiveRevCount = $reviewRepo->findCount( array(
				'aboutUser' => $displayUser->getId(),
				'type' => Review::TYPE_POSITIVE ) );
		$revsOOCount = $reviewRepo->findCount( array('author'=>$displayUser->getId()) );
		$listingCount = $noticeRepo->findCount( array('user'=>$displayUser->getId()) );
		
		$reputationBreakdown = array(
				'listingCount'  => $listingCount,
				'positiveRevCount' => $positiveRevCount ,
				'revsOOCount'   => $revsOOCount,
				'fbConnect'     => $fbConnect,
				'contactsCount' => $contactsCount,
		);
		
		$reputationPoints = $this->get('fenchy.reputation_counter')->getPointsList($displayUser);
		
		$profileUrl = $this->get('router')->generate('fenchy_regular_user_user_profilev2', array('userId' => $displayUser->getId()));
				
		$requestRepo = $em->getRepository('FenchyNoticeBundle:Request');
		$neighbourServed = $requestRepo->countUsersDoneRequest($displayUser);
		$completedAcitivities = $requestRepo->countMyRequestMarkAsDone($displayUser);
		
		$verified = $this->getDoctrine()
			->getRepository('UserBundle:LocationVerification')->getStatus($displayUser);
		
		$identity = $this->getDoctrine()
			->getRepository('UserBundle:IdentityVerification')->getIdentityStatus($displayUser);
		
		$em = $this->getDoctrine()->getEntityManager();
		$result = $em->getRepository('FenchyRegularUserBundle:Document')->findById($displayUser->getId());
			
		if($result)
		{
			if($result->getWebPath()!="")
			{
				$reputationPoints['profilePercent'] += 0.25;
			}
				
			if($result->getWebPath2()!="")
			{
				$reputationPoints['profilePercent'] += 0.10;
			}
		}
		$reputationPoints['profile'] = $reputationPoints['profilePercent'] * 20;
		$reputationPoints['profilePercent'] = ($reputationPoints['profilePercent'] *100) .'%';
		$inviteFriendPoint  = $em->getRepository('UserBundle:TellFriend')->getPoint($displayUser);
                
		return $this->render('FenchyRegularUserBundle:Otherprofile:otherCommunityPoints.html.twig',
				array(
						'repBreakdown' => $reputationBreakdown,
						'reputationPoints' => $reputationPoints,
						'displayUser'	=> $displayUser,
						'userId'		=> $userId,
						'neighbourCount' => $this->otherNeighborsCount($userId),
						'neighbourServed' => $neighbourServed,
						'completedAcitivities' => $completedAcitivities,
						'verified'		=> $verified,
						'identity'		=> $identity,
                                                'inviteFriendPoint' => $inviteFriendPoint
				));
	}
	public function otherListingsReviewsAction($userId)
	{
		$em = $this->getDoctrine()->getEntityManager();
		
		if ( $userId != NULL )
		{
				
			$userOther = $em->getRepository('UserBundle:User')->getAllData( $userId );
				
			if ( ! $userOther instanceof \Fenchy\UserBundle\Entity\User )
				return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
			$user = $userOther;
			$usersOwnProfile = 0;
		}
		$document = new Document();
		$em = $this->getDoctrine()->getEntityManager();
		$result = $em->getRepository('FenchyRegularUserBundle:Document')->findById($user->getId());
		
		if($result){
                    
                    if($result->getWebPath())
                    {
                            $avatar = $result->getWebPath();
                    }
                }    
                else
                {
                            $avatar = 'images/default_profile_picture.png';
                }
		$user->setTwitterUsername($avatar);
        
		// Added By bhumi for Manager type
		
		$userRepository = $this->getDoctrine()
		->getRepository('UserBundle:User');
			
		$managertype = $em
			->getRepository('FenchyRegularUserBundle:UserRegular')
			->getManagerType($userRepository->find($user));
		
        $request = $this->getRequest();
        $param = explode("/", $request->getPathInfo());
        $islocation = "".$param[sizeof($param)-1];
      	
        $form = $this->createForm(new UserLocationType(), $user);		
			
        $user_regular = $user->getRegularUser();
		
        $em = $this->getDoctrine()->getEntityManager();
        
        $repo = $em->getRepository('FenchyNoticeBundle:Notice');
        
        $listings = $repo->getUserNotices($user);
        $initialReviews[] = array();
        $initialComments[] = array();
        $i=0;
        foreach ($listings as $k => $listing)
    	{
            if($listing->getUserGroup()!=null)
            {
    		unset($listings[$k]);
            }
    	}
       	foreach ($listings as $listing)
       	{
	        $notice1 = $this->getDoctrine()
				        ->getManager()
				        ->getRepository('FenchyNoticeBundle:Notice')
				        ->findFullDetailed($listing->getId());
	        $slug = $notice1->getSlug();
	        $notice = $em
			        ->getRepository('FenchyNoticeBundle:Notice')
			        ->findFullDetailedWithSlug($slug);
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
	        $i++;
       	}
        
        $blockUser = array();
        $index=0;
        $userLoggedIn = $this->get('security.context')->getToken()->getUser();
        
        $blockneighbors = $em->getRepository('FenchyRegularUserBundle:BlockedNeighbor')->findByMe($userLoggedIn->getId());
	foreach ($blockneighbors as $blockneighbor)
        {
            
            if($blockneighbor->getBlocker()->getId() == $userLoggedIn->getId())
            {
                $blockUser[$index++] = $blockneighbor->getBlocked()->getId();
            }
            if($blockneighbor->getBlocked()->getId() == $userLoggedIn->getId())
            {
                $blockUser[$index++] = $blockneighbor->getBlocker()->getId();
            }
        }
        
        return $this->render(
                    'FenchyRegularUserBundle:Otherprofile:otherListingsReviews.html.twig', array(
                    	'form' 				=> $form->createView(),
                    	'user' 				=> $user,
                   		'userId'			=> $userId,
                   		'listings' 			=> $listings,
                    	'initialReviews' 	=> $initialReviews,
                    	'initialComments'	=> $initialComments,
                    	'reviews'			=> sizeof($initialReviews),
                    	'managarType'		=> $managertype,
                        'blockUser' => $blockUser
                    	)
        			);	
	}
	
	public function addNeighborsAction()
	{
		
		$user = $this->get('security.context')->getToken()->getUser();
		$request = $this->getRequest();
		$targetNeighborId = $request->get('neighborId');
		
		$neighborRequestId = $request->get('neighbourRequestId');		
		$remove = $request->get('remove');
		$rejectN = $request->get('rejectN');
		
		$em = $this->getDoctrine()->getManager();
		
		if($rejectN)
		{
			$requestObj = $em->getRepository('FenchyNoticeBundle:Request')->find($neighborRequestId);
			$em->remove($requestObj);
			$em->flush();
			echo "rejected";
			exit;
		} 
		$user_regular = $user->getRegularUser();
		 
		
		$neighborpoint = $em->getRepository('UserBundle:User');
		$neighborforPoint = $neighborpoint->find($targetNeighborId);	
		
		$neighbor = $em->getRepository('FenchyRegularUserBundle:Neighbors')->findById($targetNeighborId,$user->getId());
		if(!$neighbor)
		{
			$neighbor = $em->getRepository('FenchyRegularUserBundle:Neighbors')->findById($user->getId(),$targetNeighborId);
		}
		$neighbor_info = $em->getRepository('UserBundle:User')->getAllData( $targetNeighborId );
		
		if(!$neighbor)
		{
			$neighbor = new Neighbors();
		}
		
		
		if ($targetNeighborId!='' && $user->getId() != $targetNeighborId) {
			
				$neighbor->setCurrent($user);
				$neighbor->setNeighbor($neighbor_info);
				if($remove)
				{
					$em->remove($neighbor);
					$neighborforPoint->setActivity($neighborforPoint->getActivity()-2);
					$em->persist($neighborforPoint);
						
					$user->setActivity($user->getActivity()-2);
					$em->persist($user);
					
					$requestObj = $em->getRepository('FenchyNoticeBundle:Request')->find($neighborRequestId);
					$em->remove($requestObj);
				}
				else
				{
					$em->persist($neighbor);
					$neighborforPoint->addActivity(2);
					$em->persist($neighborforPoint);
					
					$user->addActivity(2);
					$em->persist($user);
					
					$requestObj = $em->getRepository('FenchyNoticeBundle:Request')->find($neighborRequestId);
					$requestObj->setStatus('accepted');
					$requestObj->setIsReadStatus(false);
                                        $requestObj->setBlue(true);
                                        $requestObj->setRequestBlue(false);
					$requestObj->setRequeststatus('accepted');
					$em->persist($requestObj);
					
				}
				$em->flush();
				
		}
		
		exit;
	
	}
	public function unblockNeighborsAction()
        {
            $user = $this->get('security.context')->getToken()->getUser();
            
            $request = $this->getRequest();
            $blockNeighbors = $request->get('selected_neighbors');
            $em = $this->getDoctrine()->getManager();
            if (!empty($blockNeighbors))
            {
    		foreach ($blockNeighbors as $key => $neighbor)
    		{
    			$blockedneighbor = $em->getRepository('FenchyRegularUserBundle:BlockedNeighbor')->findById($neighbor,$user);
    			$em->remove($blockedneighbor);
                                            
                }
                $em->flush();
            }
            return $this->redirect($this->generateUrl('fenchy_regular_user_user_myprofile_myneighbors'));
        }

        public function blockNeighborsAction()
	{
		
		$user = $this->get('security.context')->getToken()->getUser();
		$request = $this->getRequest();
		$targetNeighborId = $request->get('neighborId');
		
		$neighborRequestId = $request->get('neighbourRequestId');		
		$block_unblock = $request->get('obj');
//		$rejectN = $request->get('rejectN');
		
		$em = $this->getDoctrine()->getManager();
		
		if($neighborRequestId !=0)
		{
			$requestObj = $em->getRepository('FenchyNoticeBundle:Request')->find($neighborRequestId);
			$em->remove($requestObj);
			$em->flush();
		} 
		$user_regular = $user->getRegularUser();
		 
		
		
		$neighborforPoint = $em->getRepository('UserBundle:User')->find($targetNeighborId);	
		
		$blockneighbor = $em->getRepository('FenchyRegularUserBundle:BlockedNeighbor')->findById($targetNeighborId,$user->getId());
		if(!$blockneighbor)
		{
			$blockneighbor = $em->getRepository('FenchyRegularUserBundle:BlockedNeighbor')->findById($user->getId(),$targetNeighborId);
		}
		$neighbor_info = $em->getRepository('UserBundle:User')->getAllData( $targetNeighborId );
		
                
                $neighbor = $em->getRepository('FenchyRegularUserBundle:Neighbors')->findById($targetNeighborId,$user->getId());
		if(!$neighbor)
		{
			$neighbor = $em->getRepository('FenchyRegularUserBundle:Neighbors')->findById($user->getId(),$targetNeighborId);
		}
                
		if(!$blockneighbor && $block_unblock == 'Block')
		{
			$blockneighbor = new \Fenchy\RegularUserBundle\Entity\BlockedNeighbor();
		}
		
		
		if ($targetNeighborId !='' && $user->getId() != $targetNeighborId) 
                {			
				if($block_unblock == 'unBlock')
				{
                                        if($blockneighbor)
                                            $em->remove($blockneighbor);
                                            $em->flush();
                                            echo 'unblocked';
                                            exit;
				}
				else
				{
                                        $blockneighbor->setBlocker($user);
                                        $blockneighbor->setBlocked($neighbor_info);
					$em->persist($blockneighbor);
                                        if($neighbor)
                                        {
                                            $em->remove($neighbor);
                                            $neighborforPoint->setActivity($neighborforPoint->getActivity()-2);
                                            $em->persist($neighborforPoint);

                                            $user->setActivity($user->getActivity()-2);
                                            $em->persist($user);
					}
                                        
                                        $groups = $em->getRepository('FenchyRegularUserBundle:UserGroup')->findAllById($user->getId());
                                        if($groups)
                                        {
                                            foreach ($groups as $group)
                                            {
                                                $member = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findMemberByGroupId($group->getId(),$targetNeighborId);
                                                if($member)
                                                {
                                                    $em->remove($member);
                                                }
                                            }
                                        }
                                        
                                        $groups1 = $em->getRepository('FenchyRegularUserBundle:UserGroup')->findAllById($targetNeighborId);
                                        if($groups1)
                                        {
                                            foreach ($groups1 as $group1)
                                            {
                                                $member1 = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findMemberByGroupId($group1->getId(),$user->getId());
                                                if($member1)
                                                {
                                                    $em->remove($member1);
                                                }
                                            }
                                        }
                                        
                                        $em->flush();
                                        echo 'blocked';
                                        exit;
				}
		}
		exit;
	}
        
	public function addNeighborRequestAction()
	{
	
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
		$request = $this->getRequest();
		$targetNeighborId = $request->get('neighborId');
		//$remove = $request->get('remove');
				
		$user_regular = $userLoggedIn->getRegularUser();
			
		$em = $this->getDoctrine()->getManager();
		if ( !$request->isMethod('POST') ) {
			return new Response('',401);
		}
		$targetUser = $em->getRepository('UserBundle:User')->find($targetNeighborId);
			
		if ( ! ($userLoggedIn instanceof \Fenchy\UserBundle\Entity\User) )
			return new Response('',401);
		$oneRequest = $em->getRepository('FenchyNoticeBundle:Request')->getOneRequest($userLoggedIn,$targetUser);
                if(!$oneRequest)
                {
                    $noticerequest = new \Fenchy\NoticeBundle\Entity\Request();
                    $noticerequest->setTitle($userLoggedIn->getRegularUser()->getFirstName(). " ". $this->get('translator')->trans('regularuser.want_to_add_as_neighbour'));
                    $userLoggedIn->addOwnRequest($noticerequest);
                    $em->persist($userLoggedIn);
                    $noticerequest->setAuthor($userLoggedIn);
                    //$noticerequest->setAboutNotice(null);
                    $noticerequest->setText($this->get('translator')->trans('regularuser.add_me_in_your_neighbor'). " " .$targetUser->getRegularUser()->getFirstName()." ".$this->get('translator')->trans('regularuser.to_your_neighborlist'));
                    $noticerequest->setStatus('pending');
                    $noticerequest->setRequeststatus('pending');
                    $noticerequest->setRequestBlue('true');
                    $noticerequest->setIsReadStatus('true');
                    $noticerequest->setAboutUser($targetUser);
                    $em->persist($noticerequest);
                    $em->flush();
                }		
		return new Response();	
	
	}
	public function checkNeighborAction($neighborId)
	{
		$userLogged = $this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getEntityManager();
		$requestRepo = $em->getRepository('FenchyNoticeBundle:Request');
		$aboutuser = $em->getRepository('UserBundle:User')->find($neighborId);
		$neighbourRequests = $requestRepo->getSingleNeighbourRequeste($userLogged,$aboutuser);
		$requestObj = null;
		foreach ($neighbourRequests as $k =>$neighbourRequest)
		{
			if($neighbourRequest->getAboutNotice())			
				unset($neighbourRequests[$k]);
			else
				$requestObj = $neighbourRequest;
		}
		if(!$requestObj)				
			$neighbourRequests = $requestRepo->getSingleNeighbourRequeste($aboutuser,$userLogged);
			
		foreach ($neighbourRequests as $k =>$neighbourRequest)
		{
			if($neighbourRequest->getAboutNotice())
				unset($neighbourRequests[$k]);
			else
				$requestObj = $neighbourRequest;
		}
		
		if ( $neighborId != NULL )
		{
			$userOther = $em->getRepository('UserBundle:User')->getAllData( $neighborId );
				
			if ( ! $userOther instanceof \Fenchy\UserBundle\Entity\User )
				return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
			$displayUser = $userOther;
			$usersOwnProfile = 0;
		}
		else
		{
				
			if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
				return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
			$displayUser = $userLoggedIn;
			$usersOwnProfile = 1;
		}
		
		
		$neighbor = $em->getRepository('FenchyRegularUserBundle:Neighbors')->findById($neighborId,$userLogged->getId());
		if(!$neighbor)
		{
			$neighbor = $em->getRepository('FenchyRegularUserBundle:Neighbors')->findById($userLogged->getId(),$neighborId);
		}
		$neighbor_info = $em->getRepository('UserBundle:User')->getAllData( $neighborId );
		if($neighbor)
		{
			$valId =  $neighbor->getId();
			 
		}
		else 
		{
			$valId =  '';
		}
		
		
		$lat = $userLogged->getLocation()->getLatitude();
		$log = $userLogged->getLocation()->getLongitude();
		
		$lat2 = $neighbor_info->getLocation()->getLatitude();
		$log2 = $neighbor_info->getLocation()->getLongitude();
		
		$theta = $log - $log2;
		// Find the Great Circle distance
		$distance = rad2deg(acos((sin(deg2rad($lat)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)))));
		$distance = $distance * 60 * 1.1515;
		$gmap_distance = round(($distance * 1609.34), 0);//miles to meter rounded to 0
		
                $blockedNeighbor = $em->getRepository('FenchyRegularUserBundle:BlockedNeighbor')->findById($neighborId,$userLogged->getId());
                
                if(!$blockedNeighbor)
		{
			$blockedNeighbor = $em->getRepository('FenchyRegularUserBundle:BlockedNeighbor')->findById($userLogged->getId(),$neighborId);
		}
                
		return $this->render(
				'FenchyRegularUserBundle:Otherprofile:otherLeftSidebar.html.twig', array(
						'check'		=> $valId,
						'userId'	=> $neighborId,
						'distanceBetween' => $gmap_distance,
						'userLogged' => $userLogged,
						'displayUser' => $displayUser,
						'requestObj' =>$requestObj,
                                                'blocked' => $blockedNeighbor
				)
		);
		
	}
	
	public function otherNeighborsCount($userId)
	{
	
		$em = $this->getDoctrine()->getEntityManager();
	
		if ( $userId != NULL )
		{
				
			$userOther = $em->getRepository('UserBundle:User')->getAllData( $userId );
				
			if ( ! $userOther instanceof \Fenchy\UserBundle\Entity\User )
				return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
			$displayUser = $userOther;
			$usersOwnProfile = 0;
		}
			
	
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
		$i = 0 ;
		
		foreach ($users as $user) {
	
			//$avatar = $user->getRegularUser()->getAvatar(false);
			$em = $this->getDoctrine()->getEntityManager();
			$neighbor = $em->getRepository('FenchyRegularUserBundle:Neighbors')->findById($currentuid,$user->getId());
			if(!$neighbor)
			{
				$neighbor = $em->getRepository('FenchyRegularUserBundle:Neighbors')->findById($user->getId(),$currentuid);
			}
			$neighbors = '';
			if($neighbor)
			{
				$neighbors = $neighbor->getId();
			}
			if($neighbors)
			{	
								
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
						$i++;
					}
				}
	
			}
		}
		return $i;	
	}	
}
