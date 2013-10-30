<?php

namespace Fenchy\RegularUserBundle\Controller;
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
						'placeholder' => 'regularuser.your_groupname')))->add('aboutGroup', 'textarea', array(
				'attr' => array(
						'placeholder' => 'regularuser.your_aboutgroup')))->add('status', 'choice', array(
				'label' => 'regularuser.status',
				'choices' => UserGroup::$statusMap))->add('file', null, array(
				'label' => 'settings.general.profile_photo'))->add('file2', null, array(
				'label' => 'settings.general.cover_photo'))->getForm();

		$neighborsNext50 = $this->neighborsNext50();

		if ('POST' == $request->getMethod())
		{
			$form->bindRequest($request);

			if ($form->isValid())
			{
				$em = $this->getDoctrine()->getEntityManager();

				$data_saved = $this->get('translator')->trans('settings.flash.data_saved');
				$this->get('session')->setFlash('positive', $data_saved);

				$usergroup->setUser($displayUser);
				$usergroup->upload();
				$usergroup->uploadcover();

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
						$title = $this->get('translator')->trans('regularuser.message.subject', array(
								'%username%' => $userOther->getRegularUser()->getFirstname())) . "";
						$content = '';
						$content .= $this->get('translator')->trans('regularuser.message.message_part', array(
								'%username%' => $userOther->getRegularUser()->getFirstname()));
						$content .= '                                                                    ';
						$content .= $this->get('translator')->trans('regularuser.message.message_first_part');
						$content .= $this->get('translator')->trans('regularuser.message.message_second_part', array(
								'%link%' => $link,
								'%groupname%' => $groupn));
						$content .= '                                                                    ';
						$content .= $this->get('translator')->trans('regularuser.message.message_last_part');
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
							$title = $this->get('translator')->trans('regularuser.message.subject', array(
									'%username%' => $userOther->getRegularUser()->getFirstname())) . "";
							$content = '';
							$content .= $this->get('translator')->trans('regularuser.message.message_part', array(
									'%username%' => $userOther->getRegularUser()->getFirstname()));
							$content .= '                                                                    ';
							$content .= $this->get('translator')->trans('regularuser.message.message_first_part');
							$content .= $this->get('translator')->trans('regularuser.message.message_second_part', array(
									'%link%' => $link,
									'%groupname%' => $groupn));
							$content .= '                                                                    ';
							$content .= $this->get('translator')->trans('regularuser.message.message_last_part');
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
						$emailNotification = \Swift_Message::newInstance()->setFrom($this->container->getParameter('from_email'), $this->container->getParameter('from_name'))->setTo($receiverEmail)->setSubject($this->get('translator')->trans('regularuser.message.subject_for_invitegroupby_mail'))->setBody($this->renderView('FenchyRegularUserBundle:Notifications:groupInviteByEmailHTML.html.twig', $data), 'text/html');
						//->addPart($this->renderView('FenchyRegularUserBundle:Notifications:reviewEmailPlain.html.twig', $data), 'text/plain');
						$mailer = $this->get('mailer');
						$mailer->send($emailNotification);
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

	public function leaveGroupAction($groupId, $msg)
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

		return $this->redirect($this->generateUrl('fenchy_regular_user_user_groupprofile_groupinfo', array(
				'groupId' => $groupId,
				'msg' => $msg)));
	}

	public function groupLeftSidebarAction($groupId)
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getEntityManager();
		$groupMembers = '';

		if ($groupId)
		{
			$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);
		}

		$groupMember = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findById($groupId, $userLoggedIn->getId());

		if ($groupMember)
		{
			$groupMembers = 1;
		}

		return $this->render('FenchyRegularUserBundle:Groupprofile:groupLeftSidebar.html.twig', array(
				'displayUser' => $userLoggedIn,
				'groupMember' => $groupMembers,
				'groupId' => $groupId,
				'usergroup' => $usergroup));

	}

	public function deleteGroupAction($groupId)
	{
		$em = $this->getDoctrine()->getEntityManager();
		if ($groupId)
		{
			$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);
		}

		$em->remove($usergroup);
		$em->flush();
		$this->get('session')->setFlash('positive', 'regularuser.message.group_deleted');

		return $this->redirect($this->generateUrl('fenchy_regular_user_user_myprofile_aboutmychoice'));
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
			if (!$neighbors)
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
						'message' => $message)));
				$em = $this->getDoctrine()->getManager();
				$em->persist($toQueue);
				$em->flush();
			}
			elseif ($interval_val === NotificationGroupInterval::INTERVAL_IMMEDIATELY)
			{
				$emailNotification = \Swift_Message::newInstance()->setFrom($this->container->getParameter('from_email'), $this->container->getParameter('from_name'))->setTo($receiver->getEmail())->setSubject($this->get('translator')->trans('message.notification.email.subject', array(
						'%username%' => $sender->getRegularUser()->getFirstname())))->setBody($this->renderView('FenchyMessageBundle:Notifications:messageEmailHTML.html.twig', array(
						'sender' => $sender,
						'message' => $message)), 'text/html')->addPart($this->renderView('FenchyMessageBundle:Notifications:messageEmailPlain.html.twig', array(
						'sender' => $sender,
						'message' => $message)), 'text/plain');
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

		$usersOwnProfile = 1;

		// Create form for info save of Group Profile
		$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);

		$groupmembers = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findAllById($groupId);

		if (!$usergroup)
		{
			$usergroup = new UserGroup();
		}
		$form = $this->createFormBuilder($usergroup)->add('groupname', null, array(
				'attr' => array(
						'placeholder' => 'regularuser.your_groupname')))->add('aboutGroup', 'textarea', array(
				'attr' => array(
						'placeholder' => 'regularuser.your_aboutgroup')))->add('status', 'choice', array(
				'label' => 'regularuser.status',
				'choices' => UserGroup::$statusMap))->getForm();

		return $this->render('FenchyRegularUserBundle:Groupprofile:createGroup.html.twig', array(
				'displayUser' => $displayUser,
				'form' => $form->createView(),
				'groupId' => $groupId,
				'usergroup' => $usergroup,
				'user' => $usergroup->getUser(),
				'groupmembers' => $groupmembers,));

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

		$groupMembers = '';
		$groupMember = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findById($groupId, $userLoggedIn->getId());

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
				'groupMember' => $groupMembers,));
	}

	public function groupmenuAction($groupId)
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

		$groupMembers2 = '';

		$groupmembers = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findAllById($groupId);
		$membersIds = array();
		foreach ($groupmembers as $member)
		{
			$membersIds[] = $member->getNeighbor();
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

		return $this->render('FenchyRegularUserBundle:Groupprofile:groupmenu.html.twig', array(
				'locale' => $this->getRequest()->getLocale(),
				'displayUser' => $displayUser,
				'usergroup' => $usergroup,
				'groupMember' => $groupMembers2,
				'groupmembers' => $groupmembers,
				'groupId' => $groupId,
				'mutualNeighbors' => $result,));
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

		$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);

		$form = $this->createForm(new UserLocationType(), $displayUser);
		
		if($select_action=="remove")
		{	
			if(!empty($selected_members))
			{	
				foreach ($selected_members as $key=>$val)
				{
					$groupmember = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findById($groupId, $val);
					if ($groupmember)
					{
						$em->remove($groupmember);
						$em->flush();
						$members_removed = 1;
					}
				}
			}
		}
		
		$groupmembers = $em->getRepository('FenchyRegularUserBundle:GroupMembers')->findAllById($groupId);
		

		return $this->render('FenchyRegularUserBundle:Groupprofile:groupMembers.html.twig', array(
				'displayUser' => $displayUser,
				'groupId' => $groupId,
				'user' => $displayUser,
				'groupmembers' => $groupmembers,
				'form' => $form->createView(),
				'usergroup' => $usergroup,
				'members_removed' => $members_removed,
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

		$form = $this->createForm(new UserLocationType(), $displayUser);

		if ('POST' == $request->getMethod())
		{
			$form->bindRequest($request);

			if ($form->isValid())
			{
				$em = $this->getDoctrine()->getEntityManager();

				$location = $form->getData();

				$data_saved = $this->get('translator')->trans('settings.flash.data_saved');
				$this->get('session')->setFlash('positive', $data_saved);

				$em->persist($location);
				$em->flush();
				return $this->redirect($this->generateUrl('fenchy_regular_user_user_groupprofile_groupsettings'));
			}
			else
			{
				$form_errors = $this->get('translator')->trans('settings.flash.form_errors');

				$this->get('session')->setFlash('negative', $form_errors);
			}
		}

		$verified = $this->getDoctrine()->getRepository('UserBundle:LocationVerification')->getStatus($displayUser);

		return $this->render('FenchyRegularUserBundle:Groupprofile:groupSettings.html.twig', array(
				'displayUser' => $displayUser,
				'groupId' => $groupId,
				'form' => $form->createView(),
				'user' => $displayUser,
				'verified' => $verified));

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

	public function groupListingsReviewsAction($userId)
	{
		$em = $this->getDoctrine()->getEntityManager();

		if ($userId != NULL)
		{

			$userOther = $em->getRepository('UserBundle:User')->getAllData($userId);

			if (!$userOther instanceof \Fenchy\UserBundle\Entity\User) return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
			$user = $userOther;
			$usersOwnProfile = 0;
		}

		$request = $this->getRequest();
		$param = explode("/", $request->getPathInfo());
		$islocation = "" . $param[sizeof($param) - 1];

		$form = $this->createForm(new UserLocationType(), $user);

		$user_regular = $user->getRegularUser();

		$em = $this->getDoctrine()->getEntityManager();

		$repo = $em->getRepository('FenchyNoticeBundle:Notice');

		$listings = $repo->getUserNotices($user);
		$initialReviews[] = array();
		$i = 0;
		foreach ($listings as $listing)
		{
			$notice1 = $this->getDoctrine()->getManager()->getRepository('FenchyNoticeBundle:Notice')->findFullDetailed($listing->getId());
			$slug = $notice1->getSlug();
			$notice = $em->getRepository('FenchyNoticeBundle:Notice')->findFullDetailedWithSlug($slug);
			$pagination = $this->container->getParameter('reviews_pagination');
			$reviewRepo = $em->getRepository('FenchyNoticeBundle:Review');

			$initialReviews[$i++] = $reviewRepo->findByInJSON($this->container->get('router'), array(
					'aboutNotice' => $notice->getId()), array(
					'created_at' => 'DESC'), $pagination + 1, 0);

		}
		return $this->render('FenchyRegularUserBundle:Otherprofile:otherListingsReviews.html.twig', array(
				'form' => $form->createView(),
				'user' => $user,
				'userId' => $userId,
				'listings' => $listings,
				'initialReviews' => $initialReviews,
				'reviews' => sizeof($initialReviews),));
	}
	
	public function groupMessagesAction($groupId)
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getEntityManager();
		
		$displayUser = $userLoggedIn;
		
		$form = $this->createForm(new UserLocationType(), $displayUser);
		
		if ($groupId)
		{
			$usergroup = $em->getRepository('FenchyRegularUserBundle:UserGroup')->getAllData($groupId);
		}
		
		return $this->render('FenchyRegularUserBundle:Groupprofile:groupMessages.html.twig', array(
				'displayUser' => $displayUser,
				'form' => $form->createView(),
				'user' => $displayUser,
				'userId' => $displayUser->getId(),
				'groupId' => $groupId,
				'usergroup' => $usergroup,
				));
	}

}
