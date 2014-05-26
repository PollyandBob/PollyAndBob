<?php

namespace Fenchy\RegularUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Fenchy\RegularUserBundle\Form\UserRegularType;
use Fenchy\RegularUserBundle\Form\LeaveReviewType;
use Fenchy\RegularUserBundle\Form\LeaveReviewProfileType;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\RegularUserBundle\Entity\Document;
use Fenchy\UserBundle\Entity\User;
use Fenchy\UserBundle\Form\ProfileFormType as UserType;
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
use Fenchy\RegularUserBundle\Entity\LocationVerification;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;


class MyprofileController extends Controller
{
    /*		Version 2.1 from 12th Aug, 2013			*/
	public function userProfileMainAction( $userId )
	{		
		return $this->render('FenchyRegularUserBundle:Myprofile:userProfileMain.html.twig');		
	}
		
	public function aboutMyChoiceAction()
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
				
		if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
			return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_indexv2'));
		$displayUser = $userLoggedIn;		
		
		$user = $this->get('security.context')->getToken()->getUser();
	
		if (!($user instanceof User)) 
		{
			return $this->redirect($this->generateUrl('fenchy_frontend_indexv2'));
		}
	
		$user_regular = $user->getRegularUser();
	
		$form = $this->createForm(new UserAboutSettingsType(), $user_regular);
	
		$request = $this->getRequest();
	
		if ('POST' == $request->getMethod()) 
		{	
			$form->bindRequest($request);
	
			if ($form->isValid()) 
			{	
				$em = $this->getDoctrine()->getEntityManager();
	
				$regular_user = $form->getData();
	
                                if($regular_user->getBirthday())
                                $regular_user->setUserAge($regular_user->getBirthday()->diff(new \DateTime('this year'))->format('%y'));
                                else
                                    $regular_user->setUserAge(NULL);
				$data_saved = $this->get('translator')->trans('settings.flash.data_saved');
				$this->get('session')->setFlash('positive', $data_saved);
	
				$this->get('fenchy.reputation_counter')->update($regular_user->getUser(), \Fenchy\UserBundle\Services\ReputationCounter::TYPE_PROFILE);
	
				$em->persist($regular_user);
				$em->flush();
				return $this->redirect($this->generateUrl('fenchy_regular_user_user_myprofile_aboutmychoice'));
			}
			else 
			{	
				$form_errors = $this->get('translator')
				->trans('settings.flash.form_errors');
	
				$this->get('session')->setFlash('negative', $form_errors);
			}
		}
		return $this->render('FenchyRegularUserBundle:Myprofile:aboutMyChoice.html.twig', 
				array('form' => $form->createView(),
					  'displayUser' => $displayUser));
	
	}
	public function myAvatarAction($userId = NULL)
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
		
		if ( $userId != NULL ) 
		{
		
			$userOther = $em->getRepository('UserBundle:User')->getAllData( $userId );
		
			if ( ! $userOther instanceof \Fenchy\UserBundle\Entity\User )
				return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_indexv2'));
			$displayUser = $userOther;
			$usersOwnProfile = 0;
		}
		else 
		{
			
			if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
				return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_indexv2'));
			$displayUser = $userLoggedIn;
			$usersOwnProfile = 1;
		}
		$document = new Document();
		$em = $this->getDoctrine()->getEntityManager();
		$result = $em->getRepository('FenchyRegularUserBundle:Document')->findById($displayUser->getId());
		$user_regular = $userLoggedIn->getRegularUser();
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
		
		$form3 = $this->createFormBuilder($document)
		->add('user_id','hidden', array(
				'data' => $userLoggedIn->getId(),))
                ->add('file',null,array('label' => 'settings.general.profile_photo'))
                ->add('user_id','hidden', array(
				'data' => $userLoggedIn->getId(),))
		->add('cropX','hidden')
		->add('cropY','hidden')
		->add('file2',null,array('label' => 'settings.general.cover_photo'))
		->getForm();
				
		$verified = $this->getDoctrine()
				->getRepository('UserBundle:LocationVerification')->getStatus($userLoggedIn);
		
		$identity = $this->getDoctrine()
				->getRepository('UserBundle:IdentityVerification')->getIdentityStatus($userLoggedIn);
		$verify_identity = $this->getDoctrine()
				->getRepository('UserBundle:IdentityVerification')->getVerifyIdentity($userLoggedIn);
		
		$verify_location = $this->getDoctrine()
				->getRepository('UserBundle:LocationVerification')->getVerifylocation($userLoggedIn);
		
		// Added By bhumi for Manager type
		
		$userRepository = $this->getDoctrine()
			->getRepository('UserBundle:User');
		 
		$managertype = $em
				->getRepository('FenchyRegularUserBundle:UserRegular')
				->getManagerType($userRepository->find($userLoggedIn));
		
 		
		// Added By jignesh for Manager type
		
		$data['locale'] = $this->getRequest()->getLocale();
		$data['displayUser'] = $displayUser;
		$data['verified'] = $verified;
		$data['identity'] = $identity;
		$data['managertype'] = $managertype;		
		$data['verifyIdentity'] = $verify_identity;	
		$data['verifyLocation'] = $verify_location;
		$data['form3'] = $form3->createView();
		$data['profilepath'] = $proimg;
		$data['coverpath'] = $covrimg;
		$data['cropX'] = $cropX;
		$data['cropY'] = $cropY;
		
	
		return $this->render('FenchyRegularUserBundle:Myprofile:myAvatar.html.twig',$data
				);
	}
		
	public function myNeighborsAction()
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
	
		if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
			return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_indexv2'));
	
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
		$managertype = array();
		$i = 0;
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
			
		 if($neighbors)
		 {		
		 	
			$document = new Document();
			$result = $em->getRepository('FenchyRegularUserBundle:Document')->findById($user->getId());
				
                        if ($result)
                        {

                            if ($result->getWebPath())
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
				
				
				$mindist = 0;// minimum distance
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
					// Added By bhumi for Manager type
		
					$managertype[$i++] = $em
					        ->getRepository('FenchyRegularUserBundle:UserRegular')
					        ->getManagerType($user);
					
					// Added By bhumi for Manager type
					$user->setTwitterUsername($avatar);
					$users2[] = $user;
	
				}
			}
		  }	
	
		}
	
		$blockneighbors = $em->getRepository('FenchyRegularUserBundle:BlockedNeighbor')->findBlockByMe($userLoggedIn->getId());
                $blockmanagertype = array();
                $i=0;
                foreach ($blockneighbors as $blockneighbor)
                {
                    $blockmanagertype[$i++] = $em
                            ->getRepository('FenchyRegularUserBundle:UserRegular')
                            ->getManagerType($blockneighbor->getBlocked());
                }
	
		return $this->render('FenchyRegularUserBundle:Myprofile:myNeighbors.html.twig',
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
						'managertype' => $managertype,
                                                'blockneighbors' =>$blockneighbors,
                                                'blockmanagertype' => $blockmanagertype
				));
	}
	
	public function myNeighborsDstAction()
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
	
		if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
			return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_indexv2'));
	
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
		$managertype = array();
		$i=0;
		foreach ($users as $user) {
	
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
					
					$lat = $user->getLocation()->getLatitude();
					$log = $user->getLocation()->getLongitude();
					
					$lat2 = $displayUser->getLocation()->getLatitude();
					$log2 = $displayUser->getLocation()->getLongitude();
					
					$theta = $log - $log2;
					// Find the Great Circle distance
					$distance = rad2deg(acos((sin(deg2rad($lat)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)))));
					$distance = $distance * 60 * 1.1515;
					$gmap_distance = round(($distance * 1609.34), 0);//miles to meter rounded to 0
					
// 					$url = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins='.$origin.'&destinations='.$destination.'&mode=driving&language=en&sensor=false';
// 					$data = file_get_contents($url);
// 					$data = utf8_decode($data);
// 					$obj = json_decode($data);
	
					//echo($obj->rows[0]->elements[0]->distance->text); //km
					//echo($obj->rows[0]->elements[0]->distance->value); // meters
					$mindist = 0;// minimum distance
					$maxdist = $this->container->getParameter('filter_max_distance_user');// maximum distance
	
					$dist1 = $request->getUri();
	
					if(strpos($dist1,'dst=') !== false)
					{
						$dist1 = explode("dst=",$request->getUri());
						$dist = $dist1[1]; // slider distance
					}
					else
					{
						$dist = 29999; // slider distance
					}	
					
					if($gmap_distance >= $mindist && $gmap_distance < $maxdist && $gmap_distance <= $dist)
					{
						// Added By jignesh for Manager type
	
						$managertype[$i] = $em
					        ->getRepository('FenchyRegularUserBundle:UserRegular')
					        ->getManagerType($user);						
						// Added By jignesh for Manager type
						$user->setTwitterUsername($avatar);
						$users2[] = $user;
	
					}
				}
			}
			$i++;
		}
	
	
		return $this->render('FenchyRegularUserBundle:Myprofile:myNeighbors.html.twig',
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
						'managertype' => $managertype
				));
	}
	
	public function myNeighborsDstDstAction()
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
	
		if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
			return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_indexv2'));
	
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
		$managertype = array();
		$i=0;
		foreach ($users as $user) {
	
			//$avatar = $user->getRegularUser()->getAvatar(false);
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
				$lat = $user->getLocation()->getLatitude();
				$log = $user->getLocation()->getLongitude();
					
				$lat2 = $displayUser->getLocation()->getLatitude();
				$log2 = $displayUser->getLocation()->getLongitude();
					
				$theta = $log - $log2;
				// Find the Great Circle distance
				$distance = rad2deg(acos((sin(deg2rad($lat)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)))));
				$distance = $distance * 60 * 1.1515;
				$gmap_distance = round(($distance * 1609.34), 0);//miles to meter rounded to 0

				$mindist = 0;// minimum distance
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
	
					$managertype[$i] = $em
					        ->getRepository('FenchyRegularUserBundle:UserRegular')
					        ->getManagerType($user);
					
					// Added By jignesh for Manager type
					$user->setTwitterUsername($avatar);
					$users2[] = $user;
	
				}
			}
			$i++;
		}
	
	
		return $this->render('FenchyRegularUserBundle:Myprofile:myNeighbors.html.twig',
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
						'managertype' => $managertype
				));
	}
	
	public function filterUsersAction()
	{	
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
		
		if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
			return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_indexv2'));
		
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
		$managertype = array();
		$i=0;
		foreach ($users as $user) {
			
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
// 				$data = utf8_decode($data);
// 				$obj = json_decode($data);
				
				//echo($obj->rows[0]->elements[0]->distance->text); //km
				//echo($obj->rows[0]->elements[0]->distance->value); // meters
				$mindist = 0;// minimum distance
				$maxdist = $this->container->getParameter('filter_max_distance_user');// maximum distance
				 
				$dist = $request->query->get('dist'); // slider distance
				
				
				if($gmap_distance >= $mindist && $gmap_distance < $maxdist && $gmap_distance <= $dist)
				{		
					// Added By bhumi for Manager type
		
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
		
		return $this->render('FenchyRegularUserBundle:Myprofile:filterUsers.html.twig',
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
						'filterLastUrl' => $this->get('session')->get('lastFilterUrl')
				));
	}
	
        
        public function myGroupsAction()
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
                $em = $this->getDoctrine()->getEntityManager();
		if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
			return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_indexv2'));
	
		$displayUser = $userLoggedIn;
		$filterService = $this->get('listfilter');
		$emptyFilter = $returnFilter = $filterService->getFilter();
		//Get Users info
		$resultArray = $em->getRepository('FenchyNoticeBundle:Notice')->getAllUserGroups();
                
                $origin = str_replace(" ", "", $displayUser->getLocation());
		$currentuid = $displayUser->getId();
		$request = $this->getRequest();
	
		$filterdata = "";
		$users2 = array();
		$managertype = array();
		$i = 0;
                $usergroups = array();
		foreach ($resultArray as $usergroup) 
		{
                    $groupMember = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findById($usergroup->getId(), $userLoggedIn->getId());
                    if ($groupMember)
                    {
                        $usergroups[] = $usergroup;
                    }
                    if($usergroup->getUser() == $userLoggedIn)
                    {
                        $usergroups[] = $usergroup;
                    }
		}
	
		
	
		return $this->render('FenchyRegularUserBundle:Myprofile:myGroups.html.twig',
				array(
						'locale'            => $this->getRequest()->getLocale(),
						'displayUser'		=> $displayUser,
						'usergroups'		=> $usergroups,
						'filterdata'		=> $filterdata,
						'listingsPagination'=>$this->container->getParameter('listings_pagination'),
						'filterEmptyCat'=>$emptyFilter['categories'],
						'filterEmptyPD'=>$emptyFilter['postDate'],
						'filterDistanceSliderMinUser'=>$this->container->getParameter('filter_min_distance_user'),
						'filterDistanceSliderMaxUser'=>$this->container->getParameter('filter_max_distance_user'),
						'filterDistanceSliderDefaultUser'=>$this->container->getParameter('distance_slider_default_user'),
						'filterDistanceSliderSnapUser'=>$this->container->getParameter('distance_slider_snap_user'),
						'filterLastUrl' => $this->get('session')->get('lastFilterUrl'),
						'managertype' => $managertype
				));
	}
        
        
        
        public function myCommunityPointsAction()
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
				
		if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
			return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_indexv2'));
		$displayUser = $userLoggedIn;

		$em = $this->getDoctrine()->getManager();
		$reviewRepo = $em->getRepository('FenchyNoticeBundle:Review');
		$requestRepo = $em->getRepository('FenchyNoticeBundle:Request');
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
		$neighbourServed = $requestRepo->countUsersDoneRequest($displayUser);	
		$completedAcitivities = $requestRepo->countMyRequestMarkAsDone($displayUser);
		
		$profileUrl = $this->get('router')->generate('fenchy_regular_user_user_profilev2', array('userId' => $displayUser->getId()));		
		
		$verified = $this->getDoctrine()
				->getRepository('UserBundle:LocationVerification')->getStatus($userLoggedIn);
		
		$identity = $this->getDoctrine()
				->getRepository('UserBundle:IdentityVerification')->getIdentityStatus($userLoggedIn);
		$verify_identity = $this->getDoctrine()
				->getRepository('UserBundle:IdentityVerification')->getVerifyIdentity($userLoggedIn);
		
		$verify_location = $this->getDoctrine()
			->getRepository('UserBundle:LocationVerification')->getVerifylocation($userLoggedIn);
		
		$em = $this->getDoctrine()->getEntityManager();
		$result = $em->getRepository('FenchyRegularUserBundle:Document')->findById($userLoggedIn->getId());
			
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
                $inviteFriendPoint  = $em->getRepository('UserBundle:TellFriend')->getPoint($userLoggedIn);
		
		return $this->render('FenchyRegularUserBundle:Myprofile:myCommunityPoints.html.twig',
				array(
						'repBreakdown' 		=> $reputationBreakdown,
						'reputationPoints' 	=> $reputationPoints,
						'displayUser'	=> $displayUser,
						'verified'		=> $verified,
						'verifyIdentity'=> $verify_identity,
						'identity'		=> $identity,
						'myNeighbourCount' => $this->myNeighborsCount(),
						'neighbourServed' => $neighbourServed,
						'completedAcitivities' => $completedAcitivities,
						'verifyLocation' => $verify_location,
                                                'inviteFriendPoint' => $inviteFriendPoint
				));
	}
	public function myLocationAction()
	{
		$user = $this->get('security.context')->getToken()->getUser();

        if (!($user instanceof User)) {
            return $this->redirect($this->generateUrl('fenchy_frontend_indexv2'));
        }
        
        $request = $this->getRequest();
        $param = explode("/", $request->getPathInfo());
        $islocation = "".$param[sizeof($param)-1];
      
        $form = $this->createForm(new UserLocationType(), $user);		

	if ('POST' == $request->getMethod())
        {
            $form->bindRequest($request);
            
            if ($form->isValid()) 
            {
                $em = $this->getDoctrine()->getEntityManager();

                $location = $form->getData();

                $data_saved = $this->get('translator')
                        ->trans('settings.flash.data_saved');
                
                $em->persist($location);
                $em->flush();   
                if($location->getLocation() !='')
                	$this->get('session')->setFlash('positive', $data_saved);
                return $this->redirect($this->generateUrl('fenchy_regular_user_user_myprofile_mylocation'));
            } 
            else 
            {
            	$form_errors = $this->get('translator')
            	->trans('settings.flash.form_errors');
            	
            	$this->get('session')->setFlash('negative', $form_errors);
            }            
        }
        
        $verified = $this->getDoctrine()
        	->getRepository('UserBundle:LocationVerification')->getStatus($user);
        $verify_location = $this->getDoctrine()
        ->getRepository('UserBundle:LocationVerification')->getVerifylocation($user);
        
        return $this->render(
                    'FenchyRegularUserBundle:Myprofile:myLocation.html.twig', array(
                    	'form' => $form->createView(),
                    	'user' => $user,
                    	'verified' => $verified,
                    	'verifyLocation' => $verify_location		
                    	)
        			);	
	}
        
        public function tellAFriendsAction()
        {
            $request = $this->getRequest();
            $friends_email = $request->get('friends_email1');
            $displayUser = $this->get('security.context')->getToken()->getUser();           
            $value = 0;
            // invite another friends by email
            $data = array(
                            'sender' => $displayUser,
                            'user' => $displayUser,
                            'requester' => base64_encode($displayUser->getId())
                            );

            if (!empty($friends_email))
            {
                    foreach ($friends_email as $key => $receiverEmail)
                    {
                            if($receiverEmail != "")
                            {
                                    $emailNotification = \Swift_Message::newInstance()->setFrom($this->container->getParameter('from_email'), $this->container->getParameter('from_name'))->setTo($receiverEmail)->setSubject($this->get('translator')->trans('regularuser.message.subject_for_invitefriendsby_mail'))->setBody($this->renderView('FenchyRegularUserBundle:Notifications:inviteFriendsByEmailHTML.html.twig', $data), 'text/html');
                                    //->addPart($this->renderView('FenchyRegularUserBundle:Notifications:reviewEmailPlain.html.twig', $data), 'text/plain');
                                    $mailer = $this->get('mailer');
                                    $mailer->send($emailNotification);
                                    $value = 1;
                            }
                    }
            }
            
            return $this->render(
                    'FenchyRegularUserBundle:Myprofile:tellAFriends.html.twig', array(
                    	'displayUser' => $displayUser,
                        'success'     => $value,
                    	));	  
        }
        
	public function verifyLocationAction()
	{
		
		$user = $this->get('security.context')->getToken()->getUser();
		if (!($user instanceof User)) {
			return $this->redirect($this->generateUrl('fenchy_frontend_indexv2'));
		}
		
		$em = $this->getDoctrine()->getEntityManager();
		
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$password = substr(str_shuffle($chars),0,8);
		
		$repository = $this->getDoctrine()
					->getRepository('UserBundle:LocationVerification');
		
		$query = $repository->createQueryBuilder('l')
				->where('l.user = :user')						
				->setParameter('user', $user->getId())				
				->getQuery();
		
		$location = $query->getOneOrNullResult();
		
		$location = $query->getOneOrNullResult();
		$firstname = $this->getRequest()->get('firstname');
		$lastname = $this->getRequest()->get('lastname');
		$address = $this->getRequest()->get('street');
		$additionaladdress = $this->getRequest()->get('additional_address');
                $city = $this->getRequest()->get('city');
                $district = $this->getRequest()->get('district');
                $state = $this->getRequest()->get('state');
                $country = $this->getRequest()->get('country');
		$pincode = $this->getRequest()->get('postcode');
                
		$FullAddress = $this->getRequest()->get('FullAddress');	
                $FullAddress_latitude = $this->getRequest()->get('FullAddress_latitude');	
                $FullAddress_longitude = $this->getRequest()->get('FullAddress_longitude');	
                
		echo $firstname."<br>".$lastname."<br>".$address."<br>".$additionaladdress."<br>".$pincode;
				
		if(!$location) 
		{			
			$location = new \Fenchy\UserBundle\Entity\LocationVerification();
			$location->setPassword($password);
			$location->setUser($user);
			$location->setUsername($user->getRegularUser()->getFirstname());
			$location->setStatus('Requested');
			$location->setLastname($lastname);
			$location->setAddress($address);
			$location->setAdditionalAddress($additionaladdress);
                        $location->setCity($city);
                        $location->setDistrict($district);
                        $location->setState($state);
                        $location->setCountry($country);
			$location->setPincode($pincode);
			$em->persist($location);
                        
                        $UserLocation = $user->getLocation();
                        $UserLocation->setLocation(implode(',', explode(' ', $FullAddress)));
                        $UserLocation->setLatitude($FullAddress_latitude);
                        $UserLocation->setLongitude($FullAddress_longitude);
                        $UserLocation->setStreetNumber($address);
                        $temp = explode(',', $additionaladdress);
                        if(sizeof($temp)>1)
                        {
                            $UserLocation->setRoute($temp[0]);
                            $UserLocation->setSublocality($temp[1]);
                        }
                        else if(sizeof($temp)>0)
                        {
                            $UserLocation->setSublocality($temp[0]);
                            $UserLocation->setRoute(NULL);
                        }
                        else
                        {
                            $UserLocation->setSublocality(NULL);
                            $UserLocation->setRoute(NULL);
                        }
                        $UserLocation->setLocality($city);
                        $UserLocation->setAdministrativeAreaLevel2($district);
                        $UserLocation->setAdministrativeAreaLevel1($state);
                        $UserLocation->setCountry($country);
                        $UserLocation->setPostalCode($pincode);
                            
                        $em->persist($UserLocation);
                        
			$em->flush();
			$message = \Swift_Message::newInstance()
				->setSubject('Location Verification')
				->setFrom($this->container->getParameter('from_email'))
				->setTo($user->getEmail())
				->setBody($this->renderView(
						'FenchyRegularUserBundle:Myprofile:verifyLocation.html.twig',
						array('password' => $password)))
				->setContentType("text/html");
			$this->get('mailer')->send($message);
			
			$message1 = \Swift_Message::newInstance()
			->setSubject('Location Verification Request')
			->setFrom($this->container->getParameter('from_email'))
			->setTo($this->container->getParameter('from_email'))
			->setBody($this->renderView(
					'FenchyRegularUserBundle:Myprofile:adminLocationMailTemplate.html.twig',
					array('password' => $password, 'user'=> $user->getRegularUser()->getName())))
					->setContentType("text/html");
			$this->get('mailer')->send($message1);
		}		
				
		$form = $this->createForm(new UserLocationType(), $user);
		return $this->redirect($this->generateUrl('fenchy_regular_user_user_myprofile_mylocation'));
	}
	public function locationVerificationAction()
	{
		
		$user = $this->get('security.context')->getToken()->getUser();
		if (!($user instanceof User)) {
			return $this->redirect($this->generateUrl('fenchy_frontend_indexv2'));
		}
		
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $this->getDoctrine()
			->getRepository('UserBundle:LocationVerification');
		
		$query = $repository->createQueryBuilder('l')
			->where('l.user = :user')
			->setParameter('user', $user->getId())
			->getQuery();
		
		$location = $query->getOneOrNullResult();
		
		$verified = false;
		$request = $this->getRequest();
		$str="";
		if($request->getMethod() == "POST" && $location)
		{
			if(strcasecmp($location->getStatus(),'Verified') != 0)
			{
				if(strcmp($request->get('pass'),$location->getPassword()) == 0)
				{
					$location->setStatus('Verified');
					if($location->getActivitypoint()==0)
					{
						$location->setActivitypoint(100);
						$user->addActivity(100);
						$em->persist($user);
					}					
					$em->persist($location);
					$em->flush();
					$verified = true;
					$str =  "your location verified.";
				}
				else
				{
					$str = "Please enter correct password.";
				}
			}
			else
			{
				$str = "Your location Verified.";
			}
			
			$response = array("success" => $str); 
 			return new Response(json_encode($response)); 
		}
		
		$form = $this->createForm(new UserLocationType(), $user);
		if(strcasecmp($location->getStatus(), 'Verified') == 0 )
		{
			return $this->redirect($this->generateUrl('fenchy_regular_user_user_myprofile_mylocation'));
		}			
		
		return $this->render('FenchyRegularUserBundle:Myprofile:locationVerification.html.twig',
				array('form'=> $form->createView(), 'user' => $user));
	}
	
	public function identityVerificationAction()
	{
		$user = $this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $this->getDoctrine()
				->getRepository('UserBundle:IdentityVerification');
	
		$query = $repository->createQueryBuilder('i')
				->where('i.user = :user')
				->setParameter('user', $user->getId())
				->getQuery();

		$identity = $query->getOneOrNullResult();	
		
		$message = \Swift_Message::newInstance()
				->setSubject('Identity Verification')
				->setFrom($this->container->getParameter('from_email'))
				->setTo($user->getEmail())
				->setBody($this->renderView(
						'FenchyRegularUserBundle:Myprofile:identityVerification.html.twig',
						array('name' => $user->getUsername())))
				->setContentType("text/html");
		$this->get('mailer')->send($message);
		
		$identity = new \Fenchy\UserBundle\Entity\IdentityVerification();
		$identity->setUser($user);
		$identity->setUsername($user->getUsername());
		$identity->setStatus('Requested');
		$em->persist($identity);
		$em->flush();
		
		return $this->render('FenchyRegularUserBundle:Myprofile:identityVerification.html.twig',
					array('name' => $user->getUsername(),						
					));
		
	}
	
	public function newRegisteredLocationAction()
	{
		$user = $this->get('security.context')->getToken()->getUser();
	
		if (!($user instanceof User)) {
			return $this->redirect($this->generateUrl('fenchy_frontend_indexv2'));
		}
	
		$request = $this->getRequest();
		
		$form = $this->createForm(new UserLocationType(), $user);
	
		if ('POST' == $request->getMethod())
		{
			$form->bindRequest($request);
	
			if ($form->isValid())
			{
				$em = $this->getDoctrine()->getEntityManager();
	
				$location = $form->getData();	
		
				$em->persist($location);
				$em->flush();
				$form_success = $this->get('translator')
				->trans('settings.flash.first_entry_as_pioneer');
				$managertype = $em
				->getRepository('FenchyRegularUserBundle:UserRegular')
				->getManagerType($user);
				
				if($managertype[0]=="pioneer" && $location->getLocation() != '')
				{
					$this->get('session')->getFlashBag()->add('Welcome',$form_success);
					//$this->get('session')->setFlash('positive', $form_success);
				}
				return $this->redirect($this->generateUrl('fenchy_notice_indexv2'));
			}
			else
			{
				$form_errors = $this->get('translator')
				->trans('settings.flash.form_errors');
				 
				$this->get('session')->setFlash('negative', $form_errors);
			}
		}
	
		return $this->render(
				'FenchyRegularUserBundle:Myprofile:newRegisteredLocation.html.twig', array(
						'form' => $form->createView(),
						'user' => $user,
					)
		);
	}
	
	public function myNeighborsCount()
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
	
		if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
			return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_indexv2'));
	
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
		
		$i = 0;
		foreach ($users as $user)
		{
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
				
			if($neighbors)
			{		
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
	
	
					$mindist = 0;// minimum distance
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
						$i++;	
					}
				}
			}
	
		}
		return $i;
	}
        public function setAsLogoutAction()
        {
            $user = $this->get('security.context')->getToken()->getUser();
            if($user)
            {
                $user->setIsLogin(false);
                $this->getDoctrine()->getEntityManager()->persist($user);
                $this->getDoctrine()->getEntityManager()->flush($user);
            }
            return new Response();
        }
        public function checkUserLoginAction()
        {
            $userLoggedIn = $this->get('security.context')->getToken()->getUser();
            if( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User)
            {
                echo 'none';
                exit;
            }
            
            $id = $this->getRequest()->get('user');
            $user = $this->getDoctrine()->getRepository('UserBundle:User')->find($id);
            
            
            $datetime1= $userLoggedIn->getLastActive()->format('Y-m-d H:i:s');
            $datetime2= $user->getLastActive()->format('Y-m-d H:i:s');
           
            $diff =  round((strtotime($datetime1) - strtotime($datetime2)) / 60,2);           
            
            if($diff<=0.3)
            {
                $user->setIsLogin(true);
                $this->getDoctrine()->getEntityManager()->persist($user);
                $this->getDoctrine()->getEntityManager()->flush($user);
                echo "true";
            }
            else
            {
                $user->setIsLogin(false);
                $this->getDoctrine()->getEntityManager()->persist($user);
                $this->getDoctrine()->getEntityManager()->flush($user);
                
               echo 'false';
            }
            exit;
        }
        public function updateChatCountAction()
        {
            $user = $this->get('security.context')->getToken()->getUser();
            if($user)
            {
                $user->setLastActive(new \DateTime());
                $this->getDoctrine()->getEntityManager()->persist($user);
                $this->getDoctrine()->getEntityManager()->flush($user);
            }
             
            $request = $this->getRequest();
            $id = $request->get('user');
            
            //$count = $this->get('fenchy.messenger')->countUnread();
            $count = $this->getDoctrine()->getEntityManager()->getRepository('FenchyMessageBundle:Message')->countHeader($this->getUser());
            $chats = $this->getDoctrine()->getRepository('CunningsoftChatBundle:Message')->findBy(
                    array('receiver' => $this->getUser(), 'read' => 'false')
            );
            
           
            if($id)
            {
                
                if($chats)
                {
                    foreach($chats as $chat)
                    {
                        if($chat->getAuthor()->getId()== $id)
                        {
                            $chat->setRead(true);
                            $this->getDoctrine()->getEntityManager()->persist($chat);
                           
                        }
                    }
                }
                 $this->getDoctrine()->getEntityManager()->flush();
            }
            $count += count($chats);
            echo $count;
            exit;
        }
        
        public function addActivityPointAction()
        {
            $request = $this->getRequest();
            $requester = $request->get('requester');
            $em = $this->getDoctrine()->getEntityManager();
            $facebookIds = $request->get('facebookIds');
            
            if($requester)
            {
                $user = $em->getRepository('UserBundle:User')->find(base64_decode($requester));
                
                $tellfriend = new \Fenchy\UserBundle\Entity\TellFriend();
                $tellfriend->setByEmail(true);
                $tellfriend->setUser($user);
                $em->persist($tellfriend);
                $em->flush($tellfriend);
                        
                $user->addActivity(1);
                $em->persist($user);
                $em->flush($user);
            }
            if($facebookIds)
            {
                $userLoggedIn = $this->get('security.context')->getToken()->getUser();
                 
                $j=1; $flag= true;
                for($i=0; $i< sizeof($facebookIds); $i++)
                {
                    $tellfriend = new \Fenchy\UserBundle\Entity\TellFriend();
                    $tellfriend->setByEmail(false);
                    $tellfriend->setUser($userLoggedIn);
                    $friendExist = $em->getRepository('UserBundle:TellFriend')->checkFacebookIdExist($facebookIds[$i],$userLoggedIn);
                    if(!$friendExist)
                    {
                        if(sizeof($facebookIds)%2 != 0)
                        {
                            if(sizeof($facebookIds) == $j)
                            {
                                $flag =  false;
                                echo 'sizeof=>'.sizeof($facebookIds);
                        echo 'flag=>'.$flag;
                        echo 'j=>'.$j;
                            }
                        }
                        if($flag)
                        {
                            $tellfriend->setFacebookId($facebookIds[$i]);
                            $em->persist($tellfriend);
                            $em->flush($tellfriend);
                        }

                        if($j%2 == 0)
                        {
                            $userLoggedIn->addActivity(1);
                            $em->persist($userLoggedIn);
                            $em->flush($userLoggedIn);
                        }
                        $j++;
                    }
                }   
            }
            exit;
        }
}