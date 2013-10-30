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
		
		}
		else
		{
			$proimg = '';
			$covrimg = '';
		
		}
		
		$verified = $this->getDoctrine()
				->getRepository('UserBundle:LocationVerification')->getStatus($displayUser);
		
		$identity = $this->getDoctrine()
				->getRepository('UserBundle:IdentityVerification')->getIdentityStatus($displayUser);
			
		
		// Added By jignesh for Manager type
		
		if($displayUser->getActivity() >= 400 && $displayUser->getActivity() < 1000)
		{
			$managertype = "Community Manager";
			$managertype_alpha = "C";
			$classColor = "green";
		}
		elseif($displayUser->getActivity() >= 1000)
		{
			$managertype = "Neighborhood Manager";
			$managertype_alpha = "N";
			$classColor = "orange";
		}
		elseif($displayUser->getManagerType()==1)
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

		return $this->render('FenchyRegularUserBundle:Otherprofile:otherAvatar.html.twig',
				array(
						'locale' => $this->getRequest()->getLocale(),
						'displayUser' => $displayUser,
						'userId'		=> $userId,
						'verified'	 => $verified,
						'identity'	 => $identity,
						'managertype'=>$managertype,
						'managertype_alpha'=>$managertype_alpha,
						'classColor'		=> $classColor,
						'profilepath'	 => $proimg,
						'coverpath'	 	 => $covrimg,
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
		$filterdata = "";
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
			
			$document = new Document();
			$em = $this->getDoctrine()->getEntityManager();
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
						'userId'		=> $userId
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
			
			$document = new Document();
			$em = $this->getDoctrine()->getEntityManager();
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
				$dist = $request->query->get('dist'); // slider distance
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
					
					$filterdata .= '<div class="persen">
									<a href="'.$this->generateUrl('fenchy_regular_user_user_otherprofile_aboutotherchoice').'/'.$userid.'">
									<img src="'.'/'.$avatar.'" alt="" />
										<div class="persenname">
											<p>'.$firstname.'</p>
										</div>';
			        $filterdata .=	'<div class="neighbor '.$classColor.'">
											<p>'.$managertype_alpha.'</p>
											<span>'.$user->getActivity().'</span>
										</div>
									</a>
								   </div>';
				}
			}
		 }
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
						'userId'		=> $userId
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
		
		return $this->render('FenchyRegularUserBundle:Otherprofile:otherCommunityPoints.html.twig',
				array(
						'repBreakdown' => $reputationBreakdown,
						'reputationPoints' => $reputationPoints,
						'displayUser'	=> $displayUser,
						'userId'		=> $userId
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
		
		if($result)
		{
			$avatar = $result->getWebPath();
				
		}
		else
		{
			$avatar = 'images/default_profile_picture.png';
		}
		$user->setTwitterUsername($avatar);
        
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
        return $this->render(
                    'FenchyRegularUserBundle:Otherprofile:otherListingsReviews.html.twig', array(
                    	'form' 				=> $form->createView(),
                    	'user' 				=> $user,
                   		'userId'			=> $userId,
                   		'listings' 			=> $listings,
                    	'initialReviews' 	=> $initialReviews,
                    	'initialComments'	=> $initialComments,
                    	'reviews'			=> sizeof($initialReviews),
                    	)
        			);	
	}
	
	public function addNeighborsAction()
	{
		
		$user = $this->get('security.context')->getToken()->getUser();
		$request = $this->getRequest();
		$targetNeighborId = $request->get('neighborId');
		$remove = $request->get('remove');
		
		 
		$user_regular = $user->getRegularUser();
		 
		$em = $this->getDoctrine()->getManager();
		
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
				}
				else
				{
					$em->persist($neighbor);
				}
				$em->flush();
				
		}
		
		exit;
	
	}
	
	public function checkNeighborAction($neighborId)
	{
		$userLogged = $this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getEntityManager();
		
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
		$origin = str_replace(" ", "", $userLogged->getLocation());
		
		$destination = str_replace(" ", "", $neighbor_info->getLocation());
		
		$url = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins='.$origin.'&destinations='.$destination.'&mode=driving&language=en&sensor=false';
		$data = file_get_contents($url);
		//$data = utf8_decode($data);
		$obj = json_decode($data);
		 
		$gmap_distance =  $obj->rows[0]->elements[0]->distance->value;
		
		
		return $this->render(
				'FenchyRegularUserBundle:Otherprofile:otherLeftSidebar.html.twig', array(
						'check'		=> $valId,
						'userId'	=> $neighborId,
						'distanceBetween' => $gmap_distance,
						'userLogged' => $userLogged,
						'displayUser' => $displayUser
				)
		);
		
	}
	
	
}
