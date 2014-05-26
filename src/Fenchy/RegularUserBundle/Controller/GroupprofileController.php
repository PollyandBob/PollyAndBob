<?php

namespace Fenchy\RegularUserBundle\Controller;
use Fenchy\RegularUserBundle\Form\UserGroupLocationType;

use Fenchy\UtilBundle\Entity\Location;

use Fenchy\RegularUserBundle\Entity\GroupMembers;

use Fenchy\MessageBundle\Entity\Message;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Fenchy\RegularUserBundle\Form\UserRegularType;
use Fenchy\RegularUserBundle\Form\LeaveReviewType;
use Fenchy\RegularUserBundle\Form\LeaveReviewProfileType;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\RegularUserBundle\Entity\UserGroup;
use Fenchy\UserBundle\Entity\User;
use Fenchy\RegularUserBundle\Form\AboutMeType, Fenchy\NoticeBundle\Form\InterestsAndActivitiesType, Fenchy\NoticeBundle\Entity\Notice, Fenchy\NoticeBundle\Entity\Review, Fenchy\GalleryBundle\Entity\Gallery, Fenchy\GalleryBundle\Entity\Image;
use Fenchy\RegularUserBundle\Entity\Document;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Fenchy\UserBundle\Entity\NotificationGroupInterval;
use Fenchy\UserBundle\Entity\NotificationQueue;
use Fenchy\UserBundle\Form\UserLocationType;
use Fenchy\RegularUserBundle\Form\UserGroupType;
use Fenchy\RegularUserBundle\Form\UserAboutSettingsType;
use Doctrine\Tests\Common\Annotations\True;
use Doctrine\Tests\Common\Annotations\False;

class GroupprofileController extends Controller
{
	/*		Version 2.1 from 12th Aug, 2013			*/
	public function userProfileMainAction($userId)
	{
		return $this->render('FenchyRegularUserBundle:Myprofile:userProfileMain.html.twig');
	}

	public function creategroupAction()
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getEntityManager();

		if (!$userLoggedIn instanceof \Fenchy\UserBundle\Entity\User) return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));

		$displayUser = $userLoggedIn;

		$usersOwnProfile = 1;
		$request = $this->getRequest();
		$groupId = $request->get('group_ids');
                $managerGroup = $request->get('managergroup');
		// invited neighbors array
		$inviteNeighbors = $request->get('invite_neighbors');
		$inviteNeighborsNext50 = $request->get('invite_neighborsnext50');
		$friends_email = $request->get('friends_email');
		
		if ($groupId)
		{
			$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);
		}
		else
		{
			$usergroup = "";
		}

		if (!$usergroup)
		{
			$usergroup = new UserGroup();
		}

		// Create form for info save of Group Profile
		$form = $this->createFormBuilder($usergroup)->add('groupname', null, array(
				'attr' => array(
						'placeholder' => 'regularuser.your_groupname'), 'max_length'=>25))->add('aboutGroup', 'textarea', array(
				'attr' => array(
						'placeholder' => 'regularuser.your_aboutgroup')))->add('status', 'choice', array(
				'label' => 'regularuser.status',
				'choices' => UserGroup::$statusMap))->add('file', null, array(
				'label' => 'settings.general.profile_photo'))->add('file2', null, array(
				'label' => 'settings.general.cover_photo'))
                ->add('cropX','hidden',array(
                      'data' => $usergroup->getCropX(),))
                ->add('cropY','hidden',array(
                      'data' => $usergroup->getCropY(),))
                ->getForm();

		$neighborsNext50 = $this->neighborsNext50();

		if ('POST' == $request->getMethod())
		{
		$form->bindRequest($request);
                $vcropX = $form->get('cropX')->getData();
                $vcropY = $form->get('cropY')->getData();
                if($request->get('onCreate') == 1 and $vcropY > 0)
                {
                    $vcropY = $vcropY/2.13;
                }
    		
    		if(!$vcropX)
    		{
    			$vcropX = 0;
    		}
    		if(!$vcropY)
    		{
    			$vcropY = 0;
    		}
            
			if ($form->isValid())
			{
				$em = $this->getDoctrine()->getEntityManager();
				
				$location = new Location();
				$data_saved = $this->get('translator')->trans('regularuser.message.group_created');
				$this->get('session')->setFlash('positive', $data_saved);

				$usergroup->setUser($displayUser);
				$location->setLocation($displayUser->getLocation()->getLocation());
                                $location->setLatitude($displayUser->getLocation()->getLatitude());
                                $location->setLongitude($displayUser->getLocation()->getLongitude());
				$usergroup->setLocation($location);
				$usergroup->upload();
				$usergroup->uploadcover();
                
                                $usergroup->setCropX($vcropX);
                                $usergroup->setCropY($vcropY);
//                                if($managerGroup)
//                                {
//                                    if($managerGroup=='communitygroup' or $managerGroup=='neighborhoodgroup')
//                                        $usergroup->setManagerGroup(ucfirst($managerGroup[0]));
//                                }

				$em->persist($usergroup);
				$em->flush();
				if (!$groupId)
				{
					$groupId = $usergroup->getId();
					$groupn = $usergroup->getGroupname();
				}
				$link = $this->generateUrl('fenchy_regular_user_user_groupprofile_groupinfo', array(
						'groupId' => $groupId));

				// Invite My Neighbors from selected option
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
						$title = $this->get('translator')->trans('regularuser.message.subject_group', array(
								'%username%' => $userOther->getRegularUser()->getFirstname())) . "";
						$content = '';
						$content .= $this->get('translator')->trans('regularuser.message.message_part', array(
								'%username%' => $userOther->getRegularUser()->getFirstname()));
						$content .= '                                                                    ';
						//$content .= $this->get('translator')->trans('regularuser.message.message_first_part');
						$content .= $this->get('translator')->trans('regularuser.message.message_first_part', array(
                                                                '%groupname%' => $groupn,
								'%administrator%' => $usergroup->getUser()->getRegularUser()->getFirstname()
								));
						$content .= "|".$link."|";
						$content .= '                                                                    ';
						//$content .= $this->get('translator')->trans('regularuser.message.message_last_part');
						$messageObject->setTitle($title);

						$messageObject->setContent($content);

						$messageObject->setSender($displayUser);
						$messageObject->setReceiver($receiver);

						$message = $messenger->send($messageObject);

						if ($this->container->getParameter('notifications_enabled')) $this->messageNotification($message, $displayUser);
						$this->get('session')->setFlash('positive', 'regularuser.message.group_created');

					}

				}

				// Invite Neighbors next 50 around me (only for community and neighborhood manager)
				if ($inviteNeighborsNext50)
				{
					//echo "sadasd";
					//exit;
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
							$title = $this->get('translator')->trans('regularuser.message.subject_group', array(
									'%username%' => $userOther->getRegularUser()->getFirstname())) . "";
							$content = '';
							$content .= $this->get('translator')->trans('regularuser.message.message_part', array(
									'%username%' => $userOther->getRegularUser()->getFirstname()));
							$content .= '                                                                    ';
							$content .= $this->get('translator')->trans('regularuser.message.message_first_part');
							$content .= $this->get('translator')->trans('regularuser.message.message_first_part', array(
								'%groupname%' => $groupn,
								'%administrator%' => $usergroup->getUser()->getRegularUser()->getFirstname()
								));
                                                        $content .= "|".$link."|";
							$content .= '                                                                    ';
							//$content .= $this->get('translator')->trans('regularuser.message.message_last_part');
							$messageObject->setTitle($title);

							$messageObject->setContent($content);

							$messageObject->setSender($displayUser);
							$messageObject->setReceiver($receiver);

							$message = $messenger->send($messageObject);

							if ($this->container->getParameter('notifications_enabled')) $this->messageNotification($message, $displayUser);
							$this->get('session')->setFlash('positive', 'regularuser.message.group_created');

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
						'link' => $link,
						'groupname' => $groupn);

				if (!empty($friends_email))
				{
					foreach ($friends_email as $key => $receiverEmail)
					{
						$result = filter_var( $receiverEmail, FILTER_VALIDATE_EMAIL );
						if($result)
						{
							$emailNotification = \Swift_Message::newInstance()->setFrom($this->container->getParameter('from_email'), $this->container->getParameter('from_name'))->setTo($receiverEmail)->setSubject($this->get('translator')->trans('regularuser.message.subject_for_invitegroupby_mail'))->setBody($this->renderView('FenchyRegularUserBundle:Notifications:groupInviteByEmailHTML.html.twig', $data), 'text/html');
							//->addPart($this->renderView('FenchyRegularUserBundle:Notifications:reviewEmailPlain.html.twig', $data), 'text/plain');
							$mailer = $this->get('mailer');
							$mailer->send($emailNotification);
						}
					}
				}

				return $this->redirect($this->generateUrl('fenchy_regular_user_user_groupprofile_groupinfo', array(
						'groupId' => $groupId)));
			}
			else
			{
				$form_errors = $this->get('translator')->trans('settings.flash.form_errors');

				$this->get('session')->setFlash('negative', $form_errors);
				if (!$groupId)
				{
					$typename = $request->get('type');
					return $this->redirect($this->generateUrl('fenchy_regular_user_notice_create2', array(
							'typename' => $typename)));
				}
			}
		}

		return $this->render('FenchyRegularUserBundle:Groupprofile:createGroup.html.twig', array(
				'displayUser' => $displayUser,
				'form' => $form->createView(),
				'user' => $displayUser,
				'userId' => $displayUser->getId(),
				'groupId' => $groupId,
				'usergroup' => $usergroup,
				'neighborsNext50' => $neighborsNext50));

	}

	public function joinGroupAction($groupId, $msg)
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getEntityManager();

		if (!$userLoggedIn instanceof \Fenchy\UserBundle\Entity\User) return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));

		$displayUser = $userLoggedIn;

		if ($groupId)
		{
			$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);
		}
		$groupmembers = new GroupMembers();

		$groupmembers->setCurrent($usergroup);
		$groupmembers->setNeighbor($displayUser);

		$em->persist($groupmembers);
		$em->flush();

		//$this->get('session')->setFlash('positive', 'regularuser.message.group_deleted');

		return $this->redirect($this->generateUrl('fenchy_regular_user_user_groupprofile_groupinfo', array(
				'groupId' => $groupId,
				'msg' => $msg)));
	}

	public function leaveGroupAction($groupId, $msg,$requestId)
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getEntityManager();

		$displayUser = $userLoggedIn;

		if ($groupId)
		{
			$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);
		}

		$groupmembers = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findById($groupId, $userLoggedIn->getId());

		$em->remove($groupmembers);
		$em->flush();
                if($requestId != 0 )
                {
                    $requestObj = $em->getRepository('FenchyNoticeBundle:Request')->find($requestId);
                    $em->remove($requestObj);
                    $em->flush();
                }
                
		return $this->redirect($this->generateUrl('fenchy_regular_user_user_groupprofile_groupinfo', array(
				'groupId' => $groupId,
				'msg' => $msg)));
	}

	public function groupLeftSidebarAction($groupId)
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getEntityManager();
		$groupMembers = '';
                $requestRepo = $em->getRepository('FenchyNoticeBundle:Request');
		$aboutusergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);
		$neighbourRequests = $requestRepo->getSingleUserGroupRequests($userLoggedIn,$aboutusergroup);
		$requestObj = null;
		foreach ($neighbourRequests as $k =>$neighbourRequest)
		{
			if($neighbourRequest->getAboutNotice())			
				unset($neighbourRequests[$k]);
			else
				$requestObj = $neighbourRequest;
		}
			
		foreach ($neighbourRequests as $k =>$neighbourRequest)
		{
			if($neighbourRequest->getAboutNotice())
				unset($neighbourRequests[$k]);
			else
				$requestObj = $neighbourRequest;
		}

		if ($groupId)
		{
			$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);
		}

		$groupMember = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findById($groupId, $userLoggedIn->getId());

		$adminmember = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findById($groupId, $userLoggedIn->getId());
		if($adminmember)
		{
			$adminmember = $adminmember->getAdmin();
		}
		if ($groupMember)
		{
			$groupMembers = 1;
		}

		return $this->render('FenchyRegularUserBundle:Groupprofile:groupLeftSidebar.html.twig', array(
				'displayUser' => $userLoggedIn,
				'groupMember' => $groupMembers,
				'groupId' => $groupId,
				'usergroup' => $usergroup,
				'adminmember' => $adminmember,
                                'requestObj' =>$requestObj,
                                ));

	}
        
        public function joinClosedGroupRequestAction()
	{
	
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
		$request = $this->getRequest();
		$targetGroupId = $request->get('neighborId');
		//$remove = $request->get('remove');
				
		$user_regular = $userLoggedIn->getRegularUser();
			
		$em = $this->getDoctrine()->getManager();
		if ( !$request->isMethod('POST') ) {
			return new Response('',401);
		}
		$targetUserGroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($targetGroupId);
			
		if ( ! ($userLoggedIn instanceof \Fenchy\UserBundle\Entity\User) )
			return new Response('',401);
		
		$noticerequest = new \Fenchy\NoticeBundle\Entity\Request();
		$noticerequest->setTitle($userLoggedIn->getRegularUser()->getFirstName(). " ". $this->get('translator')->trans('regularuser.want_to_join_group'));
		$userLoggedIn->addOwnRequest($noticerequest);
		$em->persist($userLoggedIn);
		$noticerequest->setAuthor($userLoggedIn);
		//$noticerequest->setAboutNotice(null);
		$noticerequest->setText($this->get('translator')->trans('regularuser.want_to_join_group'). " " .$targetUserGroup->getGroupname() );
		$noticerequest->setStatus('pending');
		$noticerequest->setRequeststatus('pending');
                $noticerequest->setRequestBlue('true');
                $noticerequest->setIsReadStatus('true');
		$noticerequest->setAboutUserGroup($targetUserGroup);
		$em->persist($noticerequest);
		$em->flush();
				
		return new Response();	
	
	}
        
        public function joinClosedGroupAction()
	{
		
		$user = $this->get('security.context')->getToken()->getUser();
		$request = $this->getRequest();
		$targetNeighborId = $request->get('neighborId');
		
		$neighborRequestId = $request->get('neighbourRequestId');		
		$remove = $request->get('remove');
		$rejectN = $request->get('rejectN');
		$groupId = $request->get('groupId');
                
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
		
		if ($groupId)
		{
			$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);
		}
                $displayUser2 = $em->getRepository('UserBundle:User')->find($targetNeighborId);
		$groupmembers = new GroupMembers();

		$groupmembers->setCurrent($usergroup);
		$groupmembers->setNeighbor($displayUser2);

		$em->persist($groupmembers);
		$em->flush();
                
                $requestObj = $em->getRepository('FenchyNoticeBundle:Request')->find($neighborRequestId);
                $requestObj->setStatus('accepted');
                $requestObj->setIsReadStatus(false);
                $requestObj->setBlue(true);
                $requestObj->setRequestBlue(false);
                $requestObj->setRequeststatus('accepted');
                $em->persist($requestObj);
                $em->flush();
		
		exit;
	
	}

	public function deleteGroupAction($groupId)
	{
		$em = $this->getDoctrine()->getEntityManager();
		if ($groupId)
		{
			$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);
		}
		
                $em->getRepository('FenchyRegularUserBundle:GroupMembers')->removeAllById($groupId);
                
		$members = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findAllById($groupId);
		
		if(!$members)
		{	
				$em->remove($usergroup);
				$em->flush();
				
				$this->get('session')->setFlash('positive', 'regularuser.message.group_deleted');

			return $this->redirect($this->generateUrl('fenchy_regular_user_user_myprofile_aboutmychoice'));
		}
		else
		{
			$this->get('session')->setFlash('negative', 'regularuser.message.all_group_members_remove_first');
			
			return $this->redirect($this->generateUrl('fenchy_regular_user_user_groupprofile_groupinfo', array(
				'groupId' => $groupId)));
			
		}
		
	}

	protected function neighborsNext50()
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();

		$displayUser = $userLoggedIn;
		$filterService = $this->get('listfilter');
		$emptyFilter = $returnFilter = $filterService->getFilter();
		//Get Users info
		$users = $this->getDoctrine()->getEntityManager()->getRepository('UserBundle:User')->findAllWithStickers($emptyFilter);

		$origin = str_replace(" ", "", $displayUser->getLocation());
		$currentuid = $displayUser->getId();
		$request = $this->getRequest();

		$filterdata = "";
		$users2 = array();
		foreach ($users as $user)
		{

			//$avatar = $user->getRegularUser()->getAvatar(false);
			$em = $this->getDoctrine()->getEntityManager();
			$neighbor = $em->getRepository('FenchyRegularUserBundle:Neighbors')->findById($user->getId(), $displayUser->getId());
			if (!$neighbor)
			{
				$neighbor = $em->getRepository('FenchyRegularUserBundle:Neighbors')->findById($displayUser->getId(), $user->getId());
			}
			$neighbors = '';
			if ($neighbor)
			{
				$neighbors = $neighbor->getId();
			}
                        $blockneighbor = $em->getRepository('FenchyRegularUserBundle:BlockedNeighbor')->findById($user->getId(), $displayUser->getId());
                        if(!$blockneighbor)
                        {
                                $blockneighbor = $em->getRepository('FenchyRegularUserBundle:BlockedNeighbor')->findById($displayUser->getId(), $user->getId());
                        }
			if (!$neighbors && !$blockneighbor)
			{
				$document = new Document();
				$result = $em->getRepository('FenchyRegularUserBundle:Document')->findById($user->getId());

				if ($result)
				{
					$avatar = $result->getWebPath();

				}
				else
				{
					$avatar = 'images/default_profile_picture.png';
				}

				$firstname = $user->getRegularUser()->getFirstname();
				$destination = str_replace(" ", "", $user->getLocation());
				$userid = $user->getId();

				if ($user->getLocation() != "" && $userid != $currentuid)
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
					
					
// 					$url = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $origin . '&destinations=' . $destination . '&mode=driving&language=en&sensor=false';
// 					$data = file_get_contents($url);
// 					//$data = utf8_decode($data);
// 					$obj = json_decode($data);

					//echo($obj->rows[0]->elements[0]->distance->text); //km
					//echo($obj->rows[0]->elements[0]->distance->value); // meters
					$mindist = $this->container->getParameter('filter_min_distance_user');// minimum distance
					$maxdist = $this->container->getParameter('filter_max_distance_user');// maximum distance

					$dist1 = $request->getUri();

					if (strpos($dist1, 'dst=') !== false)
					{
						$dist1 = explode("dst=", $request->getUri());
						$dist = $dist1[1]; // slider distance
					}
					else
					{
						$dist = 30000; // slider distance
					}

					if ($gmap_distance >= $mindist && $gmap_distance < $maxdist && $gmap_distance <= $dist)
					{
						// Added By jignesh for Manager type

						if ($user->getActivity() >= 400 && $user->getActivity() < 1000)
						{
							$managertype = "Community Manager";
							$managertype_alpha = "C";
							$classColor = "green";
						}
						elseif ($user->getActivity() >= 1000)
						{
							$managertype = "Neighborhood Manager";
							$managertype_alpha = "N";
							$classColor = "orange";
						}
						elseif ($user->getManagerType() == 1)
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
                                                'avatar' => $avatar,    
						'message' => $message)))->setBodyPlain($this->renderView('FenchyMessageBundle:Notifications:messageEmailPlain.html.twig', array(
						'sender' => $sender,
						'message' => $message,
						'avatar' => $avatar
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
                                                'avatar' => $avatar,    
						'message' => $message)), 'text/html')->addPart($this->renderView('FenchyMessageBundle:Notifications:messageEmailPlain.html.twig', array(
						'sender' => $sender,
						'message' => $message,
						'avatar' => $avatar)), 'text/plain');
				$mailer = $this->get('mailer');
				$mailer->send($emailNotification);
			}
		}

	}

	public function groupInfoAction($groupId)
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getEntityManager();

		$displayUser = $userLoggedIn;
		$request = $this->getRequest();
		$usersOwnProfile = 1;

		// Create form for info save of Group Profile
		$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);

		$groupmembers = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findAllById($groupId);

		if (!$usergroup)
		{
			$usergroup = new UserGroup();
		}
                $imagesave = $this->getRequest()->get('imagesave');
                if($imagesave)
                {
                    $form = $this->createFormBuilder($usergroup)->add('file',null,array('label' => 'settings.general.profile_photo'))
                    ->add('file2',null,array('label' => 'settings.general.cover_photo'))
                    ->add('cropX','hidden',array(
                          'data' => $usergroup->getCropX(),))
                    ->add('cropY','hidden',array(
                          'data' => $usergroup->getCropY(),))
                    ->getForm();
                    
                }
                else
                {
                    $form = $this->createFormBuilder($usergroup)->add('groupname', null, array('max_length'=>25,
				'attr' => array(
						'placeholder' => 'regularuser.your_groupname')))->add('aboutGroup', 'textarea', array(
				'attr' => array(
						'placeholder' => 'regularuser.your_aboutgroup')))->add('status', 'choice', array(
				'label' => 'regularuser.status',
				'choices' => UserGroup::$statusMap))->getForm();
                }
		
                

		$administrators = array();
		$administrators2 = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findAllAdministrators($groupId);

		foreach($administrators2 as $user)
		{
				$document = new Document();
				$em = $this->getDoctrine()->getEntityManager();
				$result = $em->getRepository('FenchyRegularUserBundle:Document')->findById($user->getNeighbor()->getId());
				
				if($result)
				{
					$avatar = $result->getWebPath();
				
				}
				else
				{
					$avatar = 'images/default_profile_picture.png';
				}
				$userm = $user->getNeighbor();
				$userm->setTwitterUsername($avatar);
				
				$administrators[] = $user;
				
		}
		
		if ('POST' == $request->getMethod())
		{
			$form->bindRequest($request);
		
			if ($form->isValid())
			{
				$em = $this->getDoctrine()->getEntityManager();
		
				$location = new Location();
				$data_saved = $this->get('translator')->trans('settings.flash.data_saved');
				$this->get('session')->setFlash('positive', $data_saved);
                                $usergroup->upload();
				$usergroup->uploadcover();
                                
                                
				$em->persist($usergroup);
				$em->flush();
				if (!$groupId)
				{
					$groupId = $usergroup->getId();
					$groupn = $usergroup->getGroupname();
				}
							
				return $this->redirect($this->generateUrl('fenchy_regular_user_user_groupprofile_groupinfo', array(
						'groupId' => $groupId)));
			}
			else
			{
				$form_errors = $this->get('translator')->trans('settings.flash.form_errors');
		
				$this->get('session')->setFlash('negative', $form_errors);
				if (!$groupId)
				{
					return $this->redirect($this->generateUrl('fenchy_regular_user_user_groupprofile_groupinfo', array(
							'groupId' => $groupId)));
				}
			}
			
		}	
		
		// Show comment & send comment part
		$pagination = $this->container->getParameter('reviews_pagination');
		$commentRepo = $em->getRepository('FenchyNoticeBundle:Comment');
		$initialComments = $commentRepo->findGroupCommentsByInJSON(
				$this->container->get('router'),
				array('aboutUserGroup'=>$usergroup->getId()),
				array('created_at'=>'DESC'), $pagination+1, 0);
		
		// Only Group Member & Owner shows comments
		$groupMembers = '';
                $isAdmin = 0;
		$groupMember = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findById($groupId, $displayUser->getId());
		
		if ($groupMember)
		{
			$groupMembers = 1;
                        if($groupMember->getAdmin()==1)
                        {
                            $isAdmin = 1;
                        }
                        
		}
		
		return $this->render('FenchyRegularUserBundle:Groupprofile:createGroup.html.twig', array(
				'displayUser' => $displayUser,
				'form' => $form->createView(),
				'groupId' => $groupId,
				'usergroup' => $usergroup,
				'user' => $usergroup->getUser(),
				'groupmembers' => $groupmembers,
				'administrators' => $administrators,
				'initialComments' => $initialComments,
				'groupMember' => $groupMembers,
                                'isAdmin'  => $isAdmin,));

	}

	
	public function postCommentToGroupAction()
	{
		$request = $this->getRequest();
		$targetgroupId = $request->get('groupId');
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
	
			if ( $targetgroupId != NULL ) {
				$usergroupRepo = $em->getRepository('FenchyRegularUserBundle:UserGroup');
	
				$targetUserGroup = $usergroupRepo->getAllData($targetgroupId);
				
	
				if ( ! ($targetUserGroup instanceof \Fenchy\RegularUserBundle\Entity\UserGroup) )
					return new Response('',401);
	
				$targetUser = $targetUserGroup->getUser();
				if ( ! ($targetUser instanceof \Fenchy\UserBundle\Entity\User) )
					return new Response('',401);
	
				$comment = new \Fenchy\NoticeBundle\Entity\Comment();
				$comment->setTitle($targetUserGroup->getGroupname());
				$userLoggedIn->addOwnComment($comment);
				$em->persist($userLoggedIn);
				$comment->setAuthor($userLoggedIn);
				$comment->setText($text);
				$comment->setType(1);
				$comment->setAboutUser($targetUser);
				$comment->setAboutUserGroup($targetUserGroup);
				$em->persist($comment);
				$em->flush();
			}
		}
		$commentRepo = $em->getRepository('FenchyNoticeBundle:Comment');
		$pagination = $this->container->getParameter('reviews_pagination');
		$initialComments = $commentRepo->findGroupCommentsByInJSON(
				$this->container->get('router'),
				array('aboutUserGroup'=> $targetgroupId),
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
		
		$str = '';
	
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
				$str .= '<p>'.$initialComment['createdAt']->format('d-m-Y').'&nbsp; &nbsp;'.$initialComment['createdAt']->format('h:i A').'</p>';
				$str .= '<div class="aboutdata">';
				$str .= '<p class="redcolor">From ' .$initialComment['author']['name']. '</p>';
				$str .= '<p>' .nl2br($initialComment['text']). '</p></div>';
			}
		}
		$response = array("success" => $str);
		return new Response(json_encode($response));
	}
	
	
	public function groupAvatarAction($groupId)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
                    
		if ($userLoggedIn)
		{
			$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);

			$displayUser = $userLoggedIn;
			$usersOwnProfile = 0;
		}
		else
		{
			$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);
			$displayUser = $userLoggedIn;
		}
                $form = $this->createFormBuilder($usergroup)->add('file',null,array('label' => 'settings.general.profile_photo'))
                ->add('file2',null,array('label' => 'settings.general.cover_photo'))
                ->add('cropX','hidden',array(
                      'data' => $usergroup->getCropX(),))
                ->add('cropY','hidden',array(
                      'data' => $usergroup->getCropY(),))
                ->getForm();
                
		$groupMembers = '';
		$groupMember = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findById($groupId, $userLoggedIn->getId());
		$adminmember = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findById($groupId, $userLoggedIn->getId());
		if($adminmember)
		{
			$adminmember = $adminmember->getAdmin();
		}
		
		if ($groupMember)
		{
			$groupMembers = 1;
		}

		//$em->getRepository('FenchyRegularUserBundle:UserGroup')->findById( $userId );

		return $this->render('FenchyRegularUserBundle:Groupprofile:groupAvatar.html.twig', array(
				'locale' => $this->getRequest()->getLocale(),
				'displayUser' => $displayUser,
				'usergroup' => $usergroup,
				'profilepath' => $usergroup->getWebpath(),
				'coverpath' => $usergroup->getWebpath2(),
				'groupId' => $groupId,
				'groupMember' => $groupMembers,
				'adminmember' => $adminmember,
                                'cropX' => $usergroup->getCropX(),
                                'cropY' => $usergroup->getCropY(),
                                'form'  => $form->createView(),
                ));
	}

	public function groupmenuAction($groupId)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
		$router = $this->get("router");
    	$route = $router->match($this->getRequest()->getPathInfo());
    	$link =$route['_route'];
		if ($userLoggedIn)
		{
			$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);

			$displayUser = $userLoggedIn;
			$usersOwnProfile = 0;
		}
		else
		{
			$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);
			$displayUser = $userLoggedIn;
		}

		$groupMembers2 = '';
		$adminmember = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findById($groupId, $userLoggedIn->getId());
		if($adminmember)
		{
			$adminmember = $adminmember->getAdmin();
		}
		
		$groupmembers = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findAllById($groupId);
		$membersIds = array();
		foreach ($groupmembers as $member)
		{
                        if(!$member->getAdmin())
                        {
			$membersIds[] = $member->getNeighbor();
                        }
		}

		if ($groupmembers)
		{
			$groupMembers2 = 1;
		}

		$neighbors = 0;
		
		$mutualNeighbors = $em->getRepository('FenchyRegularUserBundle:Neighbors')->findMutualGroupMembers($displayUser->getId());
		$curentNeighbors = array();
		foreach ($mutualNeighbors as $mutualNeighbor)
		{
			if ($mutualNeighbor->getNeighbor() != $displayUser)
			{
				$curentNeighbors[] = $mutualNeighbor->getNeighbor();
			}

			if ($mutualNeighbor->getCurrent() != $displayUser)
			{
				$curentNeighbors[] = $mutualNeighbor->getCurrent();
			}
		}

		//$em->getRepository('FenchyRegularUserBundle:UserGroup')->findById( $userId );
		$result = array_intersect($membersIds, $curentNeighbors);
		$type = null;
		$userObj = $usergroup->getUser();
		if($this->getRequest()->get('_format') == 'json') {
			$messages = $this->get('fenchy.messenger')->findReceivedMessagesOfGroup($type, $this->getRequest()->get('ids'),$usergroup);
			$m = array();
			foreach($messages as $msg) {
				$m[] = array(
						'id'    => $msg->getId(),
						'sender' => $msg->getSystem() ? 'Fenchy' : $msg->getSender()->getUserRegular()->getFirstname(),
						'url'   => $this->generateUrl('fenchy_regular_user_messages_view', array('id' => $msg->getId())),
						'red'   => $msg->isUnread() && $msg->getReceiver()->getId() == $this->get('security.context')->getToken()->getUser()->getId() ? 'unread' : '',
						'title' => $msg->getTitle(),
						'date'  => $this->getTimeFrom($msg->getCreatedAt()),
						'avatar'=> $msg->getSender()->getRegularUser()->getAvatar().''
				);
			}
			$messages = $m;
		} else {
			$messages = $this->get('fenchy.messenger')->findReceivedMessagesOfGroup($type,null,$usergroup);
		}
		$groupMessages = array();
		foreach ($messages as $message)
		{
			if($message->getUsergroup() == $usergroup)
			{
				$groupMessages[] = $message;
			}
		}
                $req_count = $em->getRepository('FenchyNoticeBundle:Request')->countUnreadUsersRequestsInGroup($groupId);
                $req_count += $em->getRepository('FenchyNoticeBundle:RequestMessages')->countUnreadMessageInGroup($groupId);
                //$req_count += $em->getRepository('FenchyNoticeBundle:Review')->countUnreadReviewInGroup($groupId);
                $msg_count = $em->getRepository('FenchyMessageBundle:Message')->countGroupHeader($usergroup);
                 
		return $this->render('FenchyRegularUserBundle:Groupprofile:groupmenu.html.twig', array(
				'locale' => $this->getRequest()->getLocale(),
				'displayUser' => $displayUser,
				'usergroup' => $usergroup,
				'groupMember' => $groupMembers2,
				'groupmembers' => $groupmembers,
				'groupId' => $groupId,
				'mutualNeighbors' => $result,
				'messages' => $groupMessages,
				'adminmember'=> $adminmember,
				'link' => $link,
                                'req_count' => $req_count,
                                'msg_count' => $msg_count
				));
				
	}
	
	public function groupListingsReviewsAction($groupId)
	{
		$em = $this->getDoctrine()->getEntityManager();
	
		$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);
		
		$user = $usergroup->getUser();
		$document = new Document();
		$em = $this->getDoctrine()->getEntityManager();
		$result = $em->getRepository('FenchyRegularUserBundle:Document')->findById($user->getId());
		$userId = $user->getId();
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
	
		$listings = $repo->getUserGroupNotices($usergroup);
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
                $userLoggedIn = $this->get('security.context')->getToken()->getUser();
                
                $blockUser = array();
                $index=0;
                $blockneighbors = $em->getRepository('FenchyRegularUserBundle:BlockedNeighbor')->findByMe($userLoggedIn->getId());
                foreach ($blockneighbors as $blockneighbor)
                {
                    if($blockneighbor->getBlocker()->getId() == $userLoggedIn->getId())                 
                        $blockUser[$index++] = $blockneighbor->getBlocked()->getId();
                    
                    if($blockneighbor->getBlocked()->getId() == $userLoggedIn->getId())                    
                        $blockUser[$index++] = $blockneighbor->getBlocker()->getId();
                }
		return $this->render(
				'FenchyRegularUserBundle:Groupprofile:groupListingsReviews.html.twig', array(
						'form' 				=> $form->createView(),
						'user' 				=> $user,
						'userId'			=> $userId,
						'listings' 			=> $listings,
						'initialReviews' 	=> $initialReviews,
						'initialComments'	=> $initialComments,
						'reviews'			=> sizeof($initialReviews),
						'groupId'			=> $groupId,
                                                'blockUser'                     => $blockUser
				)
		);
	}
	
	

	public function groupMembersAction($groupId)
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getEntityManager();
		$request = $this->getRequest();
		$members_removed = 0;
		$displayUser = $userLoggedIn;
		$select_action =  $request->get('select_action');
		$selected_members = $request->get('selected_members');
                $group_msg =  $request->get('group_message');

		$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);
		$groupn = $usergroup->getGroupname();
		$form = $this->createForm(new UserLocationType(), $displayUser);
		$adminmember = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findById($groupId, $userLoggedIn->getId());
		$adminmember1 = 0;
		if($adminmember)
		{
			$adminmember1 = $adminmember->getAdmin();
		}
		if($select_action=="remove")
		{	
			if(!empty($selected_members))
			{	
				foreach ($selected_members as $key=>$val)
				{
					$groupmember = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findById($groupId, $val);
                                        if($groupmember->getAdmin()==false)
                                        {
                                            $requestObjs = $em->getRepository('FenchyNoticeBundle:Request')->findBy(array('author'=> $groupmember->getNeighbor()->getId(), 'aboutUserGroup' => $groupId, 'aboutNotice'=> NULL));
                                            foreach ($requestObjs as $requestObj)                                           
                                                    $em->remove($requestObj);
                                                $em->flush();
                                            if ($groupmember)
                                            {
                                                    $em->remove($groupmember);
                                                    $em->flush();
                                                    $members_removed = 1;
                                            }
                                        }
				}
			}
		}
		$link = $this->generateUrl('fenchy_regular_user_user_groupprofile_groupinfo', array(
						'groupId' => $groupId));

		if($select_action=="send_message")
		{
			if(!empty($selected_members))
			{
				foreach ($selected_members as $key=>$val)
				{
					$userOther = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')->getAllData($val);
					$neighbor_id = $userOther->getId();
					//echo $userOther->getEmail();
					
					$messenger = $this->get('fenchy.messenger');
					$messageObject = new Message();
					$receiver = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($neighbor_id);
					if (null !== $receiver && $receiver !== $this->getUser())
					{
						//throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
					
					$messenger->setReceiver($receiver);
					$title = $this->get('translator')->trans('regularuser.message.subject_members', array(
							'%groupname%' => $groupn)) . "";
					$content = '';
					$content .= $group_msg;
                                        
					//$content .= '                                                                    ';
					//$content .= $this->get('translator')->trans('regularuser.message.message_first_part_members');
					//$content .= $this->get('translator')->trans('regularuser.message.message_second_part_members', array(
					//		'%groupname%' => $groupn, '%administrator%' => $usergroup->getUser()->getRegularUser()->getFirstName()));
                                        
                                        //$content .= "|".$link."|";
					//$content .= '                                                                    ';
					//$content .= $this->get('translator')->trans('regularuser.message.message_last_part');
                                        
					$messageObject->setTitle($title);
					
					$messageObject->setContent($content);
					$messageObject->setFromgroup($usergroup);
					$messageObject->setSender($displayUser);
					$messageObject->setReceiver($receiver);
					
					$message = $messenger->send($messageObject);
					
					if ($this->container->getParameter('notifications_enabled')) $this->messageNotification($message, $displayUser);
					$this->get('session')->setFlash('positive', 'regularuser.msg_send');
                                        }
				}
			}
		}
		
		if($select_action=="admin")
		{
			if(!empty($selected_members))
			{
				foreach ($selected_members as $key=>$val)
				{
					$groupmember = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findById($groupId, $val);
					
					if ($groupmember)
					{
						$groupmember->setAdmin(true);
						$em->persist($groupmember);
						$em->flush();
                                                
                                                $messenger = $this->get('fenchy.messenger');
                                                $messageObject = new Message();
                                                $receiver = $this->getDoctrine()->getRepository('UserBundle:User')->find($groupmember->getNeighbor()->getId());
                                                if (null !== $receiver && $receiver !== $this->getUser())
                                                {
                                                        //throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();

                                                $messenger->setReceiver($receiver);
                                                $title = $this->get('translator')->trans('regularuser.message.subject_members', array(
                                                                '%groupname%' => $groupn)) . "";
                                                $content = '';
                                                $content .= $usergroup->getUser()->getRegularUser()->getFirstName().' set you as admin of group \''.$groupn.'\'';
                                                $content .= '                                                                    ';
                                                //$content .= $this->get('translator')->trans('regularuser.message.message_last_part');
                                                $messageObject->setTitle($title);

                                                $messageObject->setContent($content);

                                                $messageObject->setSender($displayUser);
                                                $messageObject->setReceiver($receiver);

                                                $message = $messenger->send($messageObject);

                                                if ($this->container->getParameter('notifications_enabled')) $this->messageNotification($message, $displayUser);
                                                //$this->get('session')->setFlash('positive', 'regularuser.msg_send');
                                                }
					}
				}
			}	
			
		}
		
		if($select_action=="remove_admin")
		{
			if(!empty($selected_members))
			{
				foreach ($selected_members as $key=>$val)
				{
					$groupmember = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findById($groupId, $val);
						
					if ($groupmember)
					{
						$groupmember->setAdmin(false);
						$em->persist($groupmember);
						$em->flush();
					}
				}
			}
				
		}
		
		$adminManagertype = $em->getRepository('FenchyRegularUserBundle:UserRegular')->getManagerType($usergroup->getUser());
		
		$groupmembers = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findAllById($groupId);
		$managertype = array();
		$groupmembers2 = array();
		$i = 0;
		foreach ($groupmembers as $groupmember1)
		{
			$managertype[$i++] = $em->getRepository('FenchyRegularUserBundle:UserRegular')->getManagerType($groupmember1->getNeighbor());
				
			// Added By bhumi for Manager type
			$groupmembers2[] = $groupmember1;
		}

                $index =0;
                $blockUser = array();
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
        
		return $this->render('FenchyRegularUserBundle:Groupprofile:groupMembers.html.twig', array(
				'displayUser' => $displayUser,
				'groupId' => $groupId,
				'user' => $displayUser,
				'groupmembers' => $groupmembers2,
				'form' => $form->createView(),
				'usergroup' => $usergroup,
				'members_removed' => $members_removed,
				'adminmember1' => $adminmember1,
				'adminManagertype' => $adminManagertype,
				'managertype' => $managertype,
				'blockUser' => $blockUser
			));

	}

	public function groupSettingsAction($groupId)
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getEntityManager();

		$displayUser = $userLoggedIn;

		$usersOwnProfile = 1;

		// Create form for info save of Group Profile
		$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);

		if (!$usergroup)
		{
			$usergroup = new UserGroup();
		}

		$request = $this->getRequest();
		$param = explode("/", $request->getPathInfo());
		$islocation = "" . $param[sizeof($param) - 1];
		
		$form = $this->createForm(new UserGroupLocationType(), $usergroup);
		
		if ('POST' == $request->getMethod())
		{
			$createdAt = strtotime($usergroup->getCreatedAt()->format('Y-m-d H:i:s'));
			$createdAt1 = $usergroup->getCreatedAt();
			$currentDate = time();
			$createdAt1->modify('+1 year');
			$endDate = strtotime($createdAt1->format('Y-m-d H:i:s'));
			
			//$date_diff=$currentDate-$createdAt;
			//$years = round(($date_diff)/(60*60*24*365),1);
			if($currentDate > $createdAt && $endDate > $currentDate)
			{
				$yearsCond = 0;
			}
			else
			{
				$yearsCond = 1;
			}
			
			if($yearsCond < 1 && ($usergroup->getLocsave()==null or $usergroup->getLocsave()==1 ) )
			{	
				$form->bindRequest($request);
				
				if ($form->isValid())
				{
					$em = $this->getDoctrine()->getEntityManager();
					$location = $form->getData();
					
					$data_saved = $this->get('translator')->trans('settings.flash.group_location_saved');
					$this->get('session')->setFlash('positive', $data_saved);
					if($location->getLocsave()==null)
					{
						$location->setLocsave(1);
					}
					elseif($location->getLocsave()==1)
					{
						$location->setLocsave(2);
						$location->setCreatedAt(new \DateTime($createdAt1->format('Y-m-d H:i:s')));
					}
					
					
					$em->persist($location);
					$em->flush();
					return $this->redirect($this->generateUrl('fenchy_regular_user_user_groupprofile_groupsettings',array('groupId' => $groupId)));
				}
				else
				{
					$form_errors = $this->get('translator')->trans('settings.flash.form_errors');
					$this->get('session')->setFlash('negative', $form_errors);
				}
			}
			else
			{
				$form_errors = $this->get('translator')->trans('settings.flash.location_twice_a_year');
				$this->get('session')->setFlash('negative', $form_errors);
			}	
			
		}

		$verified = $this->getDoctrine()->getRepository('UserBundle:LocationVerification')->getStatus($displayUser);

		return $this->render('FenchyRegularUserBundle:Groupprofile:groupSettings.html.twig', array(
				'displayUser' => $displayUser,
				'groupId' => $groupId,
				'form' => $form->createView(),
				'user' => $displayUser,
				'verified' => $verified,
				'usergroup' => $usergroup));

	}

	public function groupNeighborsAction($userId)
	{

		$em = $this->getDoctrine()->getEntityManager();

		if ($userId != NULL)
		{

			$userOther = $em->getRepository('UserBundle:User')->getAllData($userId);

			if (!$userOther instanceof \Fenchy\UserBundle\Entity\User) return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
			$displayUser = $userOther;
			$usersOwnProfile = 0;
		}

		$filterService = $this->get('listfilter');
		$emptyFilter = $returnFilter = $filterService->getFilter();
		//Get Users info
		$users = $this->getDoctrine()->getEntityManager()->getRepository('UserBundle:User')->findAllWithStickers($emptyFilter);

		$origin = str_replace(" ", "", $displayUser->getLocation());
		$currentuid = $displayUser->getId();
		$request = $this->getRequest();

		$filterdata = "";
		foreach ($users as $user)
		{

			$avatar = $user->getRegularUser()->getAvatar(false);
			$firstname = $user->getRegularUser()->getFirstname();
			$destination = str_replace(" ", "", $user->getLocation());
			$userid = $user->getId();

			if ($user->getLocation() != "" && $userid != $currentuid)
			{
				$url = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $origin . '&destinations=' . $destination . '&mode=driving&language=en&sensor=false';
				$data = file_get_contents($url);
				//$data = utf8_decode($data);
				$obj = json_decode($data);

				//echo($obj->rows[0]->elements[0]->distance->text); //km
				//echo($obj->rows[0]->elements[0]->distance->value); // meters
				$mindist = $this->container->getParameter('filter_min_distance_user');// minimum distance
				$maxdist = $this->container->getParameter('filter_max_distance_user');// maximum distance

				$dist1 = $request->getUri();

				if (strpos($dist1, 'dst=') !== false)
				{
					$dist1 = explode("dst=", $request->getUri());
					$dist = $dist1[1]; // slider distance
				}
				else
				{
					$dist = 30000; // slider distance
				}

				$gmap_distance = $obj->rows[0]->elements[0]->distance->value; // location with gmap api distance

				if ($gmap_distance > $mindist && $gmap_distance < $maxdist && $gmap_distance <= $dist)
				{
					// Added By jignesh for Manager type

					if ($user->getActivity() >= 400 && $user->getActivity() < 1000)
					{
						$managertype = "Community Manager";
						$managertype_alpha = "C";
						$classColor = "green";
					}
					elseif ($user->getActivity() >= 1000)
					{
						$managertype = "Neighborhood Manager";
						$managertype_alpha = "N";
						$classColor = "orange";
					}
					elseif ($user->getManagerType() == 1)
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
					$users2[] = $user;

				}
			}

		}

		return $this->render('FenchyRegularUserBundle:Otherprofile:otherNeighbors.html.twig', array(
				'locale' => $this->getRequest()->getLocale(),
				'displayUser' => $displayUser,
				'users' => $users,
				'users2' => $users2,
				'filterdata' => $filterdata,
				'listingsPagination' => $this->container->getParameter('listings_pagination'),
				'filterEmptyCat' => $emptyFilter['categories'],
				'filterEmptyPD' => $emptyFilter['postDate'],
				'filterDistanceSliderMinUser' => $this->container->getParameter('filter_min_distance_user'),
				'filterDistanceSliderMaxUser' => $this->container->getParameter('filter_max_distance_user'),
				'filterDistanceSliderDefaultUser' => $this->container->getParameter('distance_slider_default_user'),
				'filterDistanceSliderSnapUser' => $this->container->getParameter('distance_slider_snap_user'),
				'filterLastUrl' => $this->get('session')->get('lastFilterUrl'),
				'userId' => $userId));
	}

	public function groupFilterUsersAction($userId)
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getEntityManager();

		if ($userId != NULL)
		{

			$userOther = $em->getRepository('UserBundle:User')->getAllData($userId);

			if (!$userOther instanceof \Fenchy\UserBundle\Entity\User) return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
			$displayUser = $userOther;
			$usersOwnProfile = 0;
		}
		else
		{

			if (!$userLoggedIn instanceof \Fenchy\UserBundle\Entity\User) return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
			$displayUser = $userLoggedIn;
			$usersOwnProfile = 1;
		}

		$filterService = $this->get('listfilter');
		$emptyFilter = $returnFilter = $filterService->getFilter();
		//Get Users info
		$users = $this->getDoctrine()->getEntityManager()->getRepository('UserBundle:User')->findAllWithStickers($emptyFilter);

		$origin = str_replace(" ", "", $displayUser->getLocation());
		$currentuid = $displayUser->getId();
		$request = $this->getRequest();

		$filterdata = "";

		foreach ($users as $user)
		{

			$avatar = $user->getRegularUser()->getAvatar(false);
			$firstname = $user->getRegularUser()->getFirstname();
			$destination = str_replace(" ", "", $user->getLocation());
			$userid = $user->getId();

			if ($user->getLocation() != "" && $userid != $currentuid)
			{
				$url = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $origin . '&destinations=' . $destination . '&mode=driving&language=en&sensor=false';
				$data = file_get_contents($url);
				//$data = utf8_decode($data);
				$obj = json_decode($data);

				//echo($obj->rows[0]->elements[0]->distance->text); //km
				//echo($obj->rows[0]->elements[0]->distance->value); // meters
				$mindist = $this->container->getParameter('filter_min_distance_user');// minimum distance
				$maxdist = $this->container->getParameter('filter_max_distance_user');// maximum distance 
				$dist = $request->query->get('dist'); // slider distance
				$gmap_distance = $obj->rows[0]->elements[0]->distance->value; // location with gmap api distance

				if ($gmap_distance >= $mindist && $gmap_distance < $maxdist && $gmap_distance <= $dist)
				{
					// Added By jignesh for Manager type

					if ($user->getActivity() >= 400 && $user->getActivity() < 1000)
					{
						$managertype = "Community Manager";
						$managertype_alpha = "C";
						$classColor = "green";
					}
					elseif ($user->getActivity() >= 1000)
					{
						$managertype = "Neighborhood Manager";
						$managertype_alpha = "N";
						$classColor = "orange";
					}
					elseif ($user->getManagerType() == 1)
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
									<div class="neighbour_bak">
										<img src="/images/person-neighbor.png" alt="">
									</div>
									<a href="' . $this->generateUrl('fenchy_regular_user_user_otherprofile_aboutotherchoice') . '/' . $userid . '">
									<img src="' . $avatar . '" alt="" />
										<div class="persenname">
											<p>' . $firstname . '</p>
										</div>';
					$filterdata .= '<div class="neighbor ' . $classColor . '">
											<p>' . $managertype_alpha . '</p>
											<span>' . $user->getActivity() . '</span>
										</div>
									</a>
								   </div>';
				}
			}

		}

		echo $filterdata;

		return $this->render('FenchyRegularUserBundle:Otherprofile:otherFilterUsers.html.twig', array(
				'locale' => $this->getRequest()->getLocale(),
				'displayUser' => $displayUser,
				'users' => $users,
				'getdst' => $dist,
				'filterdata' => $filterdata,
				'listingsPagination' => $this->container->getParameter('listings_pagination'),
				'filterEmptyCat' => $emptyFilter['categories'],
				'filterEmptyPD' => $emptyFilter['postDate'],
				'filterDistanceSliderMinUser' => $this->container->getParameter('filter_min_distance_user'),
				'filterDistanceSliderMaxUser' => $this->container->getParameter('filter_max_distance_user'),
				'filterDistanceSliderDefaultUser' => $this->container->getParameter('distance_slider_default_user'),
				'filterDistanceSliderSnapUser' => $this->container->getParameter('distance_slider_snap_user'),
				'filterLastUrl' => $this->get('session')->get('lastFilterUrl'),
				'userId' => $userId));
	}


	/**
	 * manage user's listings action
	 * @author Mateusz Krowiak <mkrowiak@pgs-soft.com>
	 */
	public function groupNotificationsAction($groupId) {
	
		$user = $this->get('security.context')->getToken()->getUser();
		$managertype = "";
		$em = $this->getDoctrine()->getEntityManager();
		$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);
		$repo = $em->getRepository('FenchyNoticeBundle:Notice');
	
		$listings = $repo->getUserGroupNotices($usergroup);
		
		$requestRepo = $em->getRepository('FenchyNoticeBundle:Request');
		
		$requests = $requestRepo->getGroupNoticeIds($user);
		
                $blockUser[] = array();
                $index = 0;
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
                
		$listings2 = array();
		$j = 0; $k = 0;
		foreach ($requests as $request)
		{	
			if($request->getAboutNotice())
			{		
				$listings2[$j++]  = $request->getAboutNotice();	
			}		
		}

		foreach ($listings as $k => $listing)
		{
			if($listing->getUserGroup()==null)
			{
				unset($listings[$k]);
			}
		}
		
		foreach($listings2 as $k => $listing1)
		{
			foreach($listings2 as $key => $value)
			{
				if($k != $key && $listing1->getId() == $value->getId())
				{
					unset($listings2[$k]);
				}
			}
		}
		
		
		$userId = $user->getId();
		$adminmember = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findById($groupId, $userId);
		if($adminmember)
		{
			$adminmember = $adminmember->getAdmin();
		}
		if ($usergroup->getUser()->getId() != $userId and $adminmember != 1 ) return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
		
		$displayUser = false;
		
		if ( $userId != NULL ) {
	
			$userOther = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')->getAllData( $userId );
	
			if ( ! $userOther instanceof \Fenchy\UserBundle\Entity\User )
				return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
			$displayUser = $userOther;
	
		}
		$form1 = $this->createForm(new UserLocationType(), $displayUser);
		
		$requestRepo = $em->getRepository('FenchyNoticeBundle:Request');
		//$my_req_count = $requestRepo->countUnreadUsersStatusRequestsInGroup($listings);
		//$req_count = $requestRepo->countUnreadUsersRequestsInGroup($groupId);
	
		$initialReviews[] = array();
		$initialComments[] = array();
		$initialRequests[] = array();
                $messages[] = array();
		$forms[][] = array();
                $blue = array();
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
                        $blue[$i] = 'true';
			$managertype[] = array();
			$c = 0;$status='';$statusflag = true;
			foreach ( $initialRequests[$i] as $initialRequest )
			{
				if ($initialRequest)
				{
                                        $messages[$i][$j] = $em->getRepository('FenchyNoticeBundle:RequestMessages')->findByGroupRequest($initialRequest['id'],$initialRequest['author']['id'],$initialRequest['aboutuser']['id']);
					$userRepository = $this->getDoctrine()
					->getRepository('UserBundle:User');
					 
					$managertype[$i][$j] = $em
					->getRepository('FenchyRegularUserBundle:UserRegular')
					->getManagerType($userRepository->find($initialRequest['author']['id']));
					 
//					$messenger = $this->get('fenchy.messenger');
//					$receiver = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($initialRequest['author']['id']);
//					if (null === $receiver || $receiver === $this->getUser()) {
//						//throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
//					}
//					$messenger->setReceiver($receiver);
//					$form = $messenger->createForm();
//					$forms[$i][$j] = $form->createView();
					 
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
                
                // The Closed group Join Requests
                $requestRepo = $em->getRepository('FenchyNoticeBundle:Request');
                $groupmessages[] = array();
                $neighbourRequests = $requestRepo->getJoinClosedGroupsRequests($usergroup);
                $i=0;$msgForms[] = array();
                foreach ($neighbourRequests as $k =>$neighbourRequest)
                {
                        if($neighbourRequest->getAboutNotice())
                        {
                                unset($neighbourRequests[$k]);
                        }
                        else
                        {
                                //$neighbourRequest->setIsRead(true);
                                $em->persist($neighbourRequest);
                                $em->flush($neighbourRequest);
                                $groupmessages[$i] = $em->getRepository('FenchyNoticeBundle:RequestMessages')->findByGroup($neighbourRequest->getId(),$neighbourRequest->getAboutUserGroup()->getUser()->getId(), $neighbourRequest->getAuthor()->getId(),$neighbourRequest->getAboutUserGroup()->getId());
                                $i++;
                        }

//                        $messenger = $this->get('fenchy.messenger');
//                        $receiver = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($neighbourRequest->getAuthor()->getId());
//                        if (null === $receiver || $receiver === $this->getUser()) {
//                                //throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
//                        }
//                        $messenger->setReceiver($receiver);
//                        $msgForm = $messenger->createForm();
//                                $msgForms[$i] = $msgForm->createView();
                               

                }

                if($usergroup->getPaymentId())        	
                    $cvv_code = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5('!p@a#b$o'), base64_decode($usergroup->getPaymentId()->getCvvCode()), MCRYPT_MODE_CBC, md5(md5('!p@a#b$o'))), "\0");
                else 
                    $cvv_code =0;
                
                $em->getRepository('FenchyNoticeBundle:Request')->updateGroupRequestReadStatus($groupId);
                $em->getRepository('FenchyNoticeBundle:RequestMessages')->updateRequestMessageInGroup($groupId);
                
		//echo "sssss<pre>";print_r($form1[5][0]);exit;
		//        echo "<pre>";print_r($initialRequests[5]);exit;
		return $this->render('FenchyRegularUserBundle:Groupprofile:groupNotifications.html.twig', array(
				'listings' => $listings,
				'displayUser' => $displayUser,
				'groupId' => $groupId,
				'form' => $form1->createView(),
				'usersOwnProfile' => true,
				'initialReviews' => $initialReviews,
				'initialComments'=>	$initialComments,
				'initialRequests' => $initialRequests,
				'managerTypes'	=> $managertype,
                                'neighbourRequests' => $neighbourRequests,
                                'msgForm' => $msgForms,
				'reviews'	=> sizeof($initialReviews),
				'forms' => $forms,
				'count' => $count,
				'messages' => $messages,
                                'groupmessages' => $groupmessages,
                                'usergroup' => $usergroup,
                                'cvv_code' => $cvv_code,
                                'blue' => $blue,
                                'blockUser'=> $blockUser
                                                
				));
	
	}
	
	public function groupNotificationsRequestsAction($groupId)
	{
		$user = $this->get('security.context')->getToken()->getUser();
		$managertype ='';
		$em = $this->getDoctrine()->getEntityManager();
		$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);
		$repo = $em->getRepository('FenchyNoticeBundle:Notice');
		
		$listings2 = $repo->getUserGroupNotices($usergroup);
		
		$requestRepo = $em->getRepository('FenchyNoticeBundle:Request');
		
		$requests = $requestRepo->getGroupNoticeIds($user);
	
		$listings = array();
		$j = 0; $k = 0;
		foreach ($requests as $request)
		{
			if($request->getAboutNotice())
			{
				$listings[$j++]  = $request->getAboutNotice();
			}			
		}
	
		foreach ($listings as $k => $listing)
		{
			if($listing->getUserGroup()==null)
			{
				unset($listings[$k]);
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
			
		$userId = $user->getId();
		$displayUser = false;
	
		if ( $userId != NULL ) {
	
			$userOther = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')->getAllData( $userId );
	
			if ( ! $userOther instanceof \Fenchy\UserBundle\Entity\User )
				return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
			$displayUser = $userOther;
	
		}
		$form1 = $this->createForm(new UserLocationType(), $displayUser);
		//$my_req_count = $requestRepo->countUnreadUsersStatusRequestsInGroup($listings);
		//$req_count = $requestRepo->countUnreadUsersRequestsInGroup($groupId);
			
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
			$managertype[] = array();
			$c = 0;$status='';$statusflag = true;
			foreach ( $initialRequests[$i] as $initialRequest )
			{
				if ($initialRequest)
				{
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
						//else if($status != 'done')
						//$status = $initialRequest['status'];
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
		return $this->render('FenchyRegularUserBundle:Groupprofile:groupNotificationsRequests.html.twig', array(
				'listings' => $listings,
				'displayUser' => $displayUser,
				'groupId' => $groupId,
				'form' => $form1->createView(),
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
		));
	
	}
	
	public function groupMessagesAction($groupId)
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getEntityManager();
		$type = null;
		$displayUser = $userLoggedIn;
		
		$form = $this->createForm(new UserLocationType(), $displayUser);
		
		if ($groupId)
		{
			$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);
		}
		
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
		 
		$request = $this->getRequest();
		
		$userObj = $usergroup->getUser();
		if($this->getRequest()->get('_format') == 'json') {
			$messages = $this->get('fenchy.messenger')->findReceivedMessagesOfGroup($type, $this->getRequest()->get('ids'),$usergroup);
			$m = array();
			foreach($messages as $msg) {
				$m[] = array(
						'id'    => $msg->getId(),
						'sender' => $msg->getSystem() ? 'Fenchy' : $msg->getSender()->getUserRegular()->getFirstname(),
						'url'   => $this->generateUrl('fenchy_regular_user_messages_view', array('id' => $msg->getId())),
						'red'   => $msg->isUnread() && $msg->getReceiver()->getId() == $this->get('security.context')->getToken()->getUser()->getId() ? 'unread' : '',
						'title' => $msg->getTitle(),
						'date'  => $this->getTimeFrom($msg->getCreatedAt()),
						'avatar'=> $msg->getSender()->getRegularUser()->getAvatar().''
				);
			}
			$messages = $m;
		} else {
			$messages = $this->get('fenchy.messenger')->findReceivedMessagesOfGroup($type,null,$usergroup);
		}
		
		$groupMessages = array();
		foreach ($messages as $message)
		{
			if($message->getUsergroup() == $usergroup or $message->getUsergroup() != null)
			{
				$groupMessages[] = $message;
			}
		}
		
		$messagesToNeighbors = $this->messagesToNeighbors($userLoggedIn->getId());
		
		$groupmembers = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findAllById($groupId);
		
                $em->getRepository('FenchyMessageBundle:Message')->updateGroupCountHeader($usergroup);
                
		return $this->render('FenchyRegularUserBundle:Groupprofile:groupMessages.html.twig', array(
				'displayUser' => $displayUser,
				'form' => $form->createView(),
				'user' => $displayUser,
				'userId' => $displayUser->getId(),
				'groupId' => $groupId,
				'usergroup' => $usergroup,
				'messages' => $messages,
				'type' => null === $type ? 'all' : $type,
				'displayUser' => $userLoggedIn,
				'messagesToNeighbors'=>$messagesToNeighbors,
				'userObj' => $userObj,
				'groupmembers' => $groupmembers,
                                'usergroup' => $usergroup
				));
	}
	
	public function groupMessagesViewAction($groupId,$id)
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getEntityManager();
		$type = null;
		$read = true;
		$messenger = $this->get('fenchy.messenger');
		$last_message = $messenger->findLastByIdGroup($id, $read, $groupId);
		$displayUser = $userLoggedIn;
	
		$form = $this->createForm(new UserLocationType(), $displayUser);
	
		if ($groupId)
		{
			$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);
		}
	
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
			
		$request = $this->getRequest();
		$userObj = $usergroup->getUser();
		if($this->getRequest()->get('_format') == 'json') {
			$messages = $this->get('fenchy.messenger')->findReceivedMessagesOfGroup($type, $this->getRequest()->get('ids'),$usergroup);
			$m = array();
			foreach($messages as $msg) {
				$m[] = array(
						'id'    => $msg->getId(),
						'sender' => $msg->getSystem() ? 'Fenchy' : $msg->getSender()->getUserRegular()->getFirstname(),
						'url'   => $this->generateUrl('fenchy_regular_user_messages_view', array('id' => $msg->getId())),
						'red'   => $msg->isUnread() && $msg->getReceiver()->getId() == $this->get('security.context')->getToken()->getUser()->getId() ? 'unread' : '',
						'title' => $msg->getTitle(),
						'date'  => $this->getTimeFrom($msg->getCreatedAt()),
						'avatar'=> $msg->getSender()->getRegularUser()->getAvatar().''
				);
			}
			$messages = $m;
		} else {
			$messages = $this->get('fenchy.messenger')->findReceivedMessagesOfGroup($type,null,$usergroup);
		}
	
		$groupMessages = array();
		foreach ($messages as $message)
		{
			if($message->getUsergroup() == $usergroup)
			{
				$groupMessages[] = $message;
			}
		}
	
		//$messages = $this->get('fenchy.messenger')->findReceivedMessagesOfGroup($type,null,$usergroup);
	
		$messagesView = $messenger->findThreadMessages();
		
		$form1 = null;
		if ($last_message->isReplyable()) {
			$form1 = $messenger->createForm()->createView();
		}
		$em->getRepository('FenchyMessageBundle:Message')->updateGroupCountHeader($usergroup);
                
		return $this->render('FenchyRegularUserBundle:Groupprofile:groupMessagesView.html.twig', array(
				'displayUser' => $displayUser,
				'form' => $form->createView(),
				'user' => $displayUser,
				'userId' => $displayUser->getId(),
				'groupId' => $groupId,
				'usergroup' => $usergroup,
				'messages' => $messages,
				'type' => null === $type ? 'all' : $type,
				'displayUser' => $userLoggedIn,
				'messagesView' => $messagesView,
				'form1' => $form1,
				'userObj' => $userObj
		));
	}
	
	public function messagesToNeighbors($userId)
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
	
		if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
			return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
	
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
	
		return $users2;
	}

	public function groupPaymentAction($noticeType)
	{
		$user = $this->get('security.context')->getToken()->getUser();
		 
		if (!($user instanceof User)) {
			return $this->redirect($this->generateUrl('fos_user_security_login'));
		}
	
		$request = $this->getRequest();
		$groupId = $request->get('groupId');
		
		$repository = $this->getDoctrine()
		->getRepository('UserBundle:GroupPayment');
		 
		$query = $repository->createQueryBuilder('gp')
			->where('gp.group = :group')
			->setParameter('group', $groupId)
			->getQuery();
	
		$payment = $query->getOneOrNullResult();
		$isPayment = false;
		$cvv_code= null;
		if($payment)
		{
			$isPayment = true;
			$cvv_code = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5('!p@a#b$o'), base64_decode($payment->getCvvCode()), MCRYPT_MODE_CBC, md5(md5('!p@a#b$o'))), "\0");
		} 
		return $this->render('FenchyRegularUserBundle:Groupprofile:groupPayment.html.twig',array(
				'user' => $user,
				'payment' => $payment,
				'noticeType'=> $noticeType,
				'groupId'=> $groupId,
				'cvv_code'=> $cvv_code,
		));
	}
	
	public function saveGroupPaymentSettingAction()
	{
		$user = $this->get('security.context')->getToken()->getUser();
	
		if (!($user instanceof User)) {
			return $this->redirect($this->generateUrl('fos_user_security_login'));
		}
		$request = $this->getRequest();
		$account_holder = $request->get('account_holder')?$request->get('account_holder'):null;
		$account_no = $request->get('account_no')?$request->get('account_no'):null;
		$bank_code = $request->get('bank_code')?$request->get('bank_code'):null;
		$paypal_email = $request->get('paypal_email')?$request->get('paypal_email'):null;
		$card_type = $request->get('card_type')?$request->get('card_type'):null;
		$card_no = $request->get('card_no')?$request->get('card_no'):null;
		$cvv_code = $request->get('cvv_code')?$request->get('cvv_code'):null;
		$card_holder = $request->get('card_holder')?$request->get('card_holder'):null;
		$end_month = $request->get('end_month')? $request->get('end_month'):null;
		$end_year = $request->get('end_year')?$request->get('end_year'):null;
		$type= $request->get('type')?$request->get('type'):null;
		$action= $request->get('action');
		$groupId = $request->get('groupId'); 
		
		$em = $this->getDoctrine()->getEntityManager();
		 
		$repository = $this->getDoctrine()
		->getRepository('UserBundle:GroupPayment');
		 
		$query = $repository->createQueryBuilder('gp')
		->where('gp.group = :group')
		->setParameter('group', $groupId)
		->getQuery();
	
		$payment = $query->getOneOrNullResult();
		if(!$payment)
		{
			$payment = new \Fenchy\UserBundle\Entity\GroupPayment();
		}
	
		$key = '!p@a#b$o';
		if($cvv_code)
			$cvv_code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $cvv_code, MCRYPT_MODE_CBC, md5(md5($key))));
		 
		$group = $this->getDoctrine()->getRepository('FenchyRegularUserBundle:UserGroup')->find($groupId);

		$payment->setGroup($group);
		$payment->setAccountHolder($account_holder);
		$payment->setAccountNo($account_no);
		$payment->setBankCode($bank_code);
		$payment->setPaypalEmail($paypal_email);
		$payment->setCardType($card_type);
		$payment->setCardNo($card_no);
		$payment->setCvvCode($cvv_code);
		$payment->setCardHolder($card_holder);
		$payment->setEndMonth($end_month);
		$payment->setEndYear($end_year);
		$payment->setType($type);
		$payment->setAgreed(true);
		$em->persist($payment);
		$em->flush();
		 
		$typeindex = 1;
		if($payment->getType() == 'debit')
			$typeindex = 1;
		if($payment->getType() == 'paypal')
			$typeindex = 2;
		if($payment->getType() == 'credit')
			$typeindex = 3;
		 
		$str ='<div class="paymentdetails">';
		$str.= '<a class="payment-button" href="javascript:void(0);" onclick="showPaymentForm('.$typeindex.')">';
		$str .=	$this->get('translator')->trans('payment.change').'</a>';
		$str .= '<a class="payment-button" href="javascript:void(0);" onclick="deletePaymentSetting('.$payment->getId().')">';
		$str .=	$this->get('translator')->trans('payment.delete').'</a>';
		$str .= '<div class="changeSettingButton">';
		$str .= '<a class="payment-button" href="javascript:void(0);" onclick="showPaymentForm(\'1\')">';
		$str .= $this->get('translator')->trans('payment.direct_debit').'</a>';
		$str .= '<a class="payment-button" href="javascript:void(0);" onclick="showPaymentForm(\'2\')">';
		$str .= $this->get('translator')->trans('payment.paypal').'</a>';
		$str .= '<a class="payment-button" href="javascript:void(0);" onclick="showPaymentForm(\'3\')">';
		$str .= $this->get('translator')->trans('payment.credit_card').'</a>';
		$str .= '</div></div>';
	
		echo $str;
		exit;
	
	}
	public function deleteGroupPaymentSettingAction()
	{
		$user = $this->get('security.context')->getToken()->getUser();
		 
		if (!($user instanceof User)) {
			return $this->redirect($this->generateUrl('fos_user_security_login'));
		}
		 
		 
		$request = $this->getRequest();
		$id = $request->get('id');
		if ('POST' == $request->getMethod())
		{
		
			$em = $this->getDoctrine()->getManager();
	
			$payment = $em->getRepository('UserBundle:GroupPayment')->find($id);
	
			if (null === $payment) {
				throw new NotFoundHttpException('Not installed payment!');
			}
	
			if ($payment->getGroup()->getUser()->getId() !== $this->get('security.context')->getToken()->getUser()->getId()) {
				throw new \Exception('You have not permission to delete this installation.');
			}
	
	
			$em->remove($payment);
			$em->flush();
		}
		$str = '<p style="margin-bottom: 20px;">';
		$str .= $this->get('translator')->trans('payment.no_default');
		$str .= $this->get('translator')->trans('payment.choose_default');
		$str .='</p><div class="paymentdetails">';
		$str .='<a class="payment-button" href="javascript:void(0);" onclick="showPaymentForm(\'1\')">';
		$str .= $this->get('translator')->trans('payment.direct_debit');
		$str .= '</a>';
		$str .= '<a class="payment-button" href="javascript:void(0);" onclick="showPaymentForm(\'2\')">';
		$str .= $this->get('translator')->trans('payment.paypal');
		$str .= '</a>';
		$str .= '<a class="payment-button" href="javascript:void(0);" onclick="showPaymentForm(\'3\')">';
		$str .= $this->get('translator')->trans('payment.credit_card');
		$str .= '</a></div>';
			
		echo $str;
		exit;
	}
}
