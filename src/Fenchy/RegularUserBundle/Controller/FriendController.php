<?php

namespace Fenchy\RegularUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\RegularUserBundle\Entity\Invitation;
use Fenchy\UserBundle\Entity\User;
use Fenchy\UserBundle\Entity\NotificationGroupInterval;
use Fenchy\UserBundle\Entity\NotificationQueue;
use Fenchy\RegularUserBundle\Form\FriendDeleteType;

class FriendController extends Controller {

    /**
     * Contacts list while displaying other user
     * @return array 
     */
    public function indexAction($id) {

        $ru = $this->get('security.context')->getToken()->getUser()->getRegularUser();

        $friends = array();

        if (null !== $id) {
            $regularUser = $this->get('doctrine.orm.entity_manager')
                    ->getRepository('FenchyRegularUserBundle:UserRegular')
                    ->findOneById($id);
            if (null !== $regularUser && $regularUser !== $ru) {

                $ru = $regularUser;
                $friends = $regularUser->getMyFriends();
            } else {
                $friends = $ru->getMyFriends();
            }
        } else {
            $friends = $ru->getMyFriends();
        }

        $userId = $ru->getUser()->getId();
        $displayUser = false;

        if ($userId != NULL) {

            $userOther = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')->getAllData($userId);

            if (!$userOther instanceof \Fenchy\UserBundle\Entity\User)
                return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
            $displayUser = $userOther;
        }

        return $this->render('FenchyRegularUserBundle:Friend:index.html.twig', array(
                    'contacts' => $friends,
                    'displayUser' => $displayUser
        ));
    }

    /**
     * @param int $id
     * @return array()
     * @throws NotFoundHttpException 
     */
    public function inviteAction($id) {
        $regularUser = $this->get('security.context')->getToken()->getUser()->getRegularUser();
        $em = $this->getDoctrine()->getManager();

        $friend = $em->getRepository('FenchyRegularUserBundle:UserRegular')->find($id);

        $user_id = null;
        
        if($friend) {
            $user_id = $friend->getUser()->getId();
        }
        
        if (null === $friend || $friend === $this->get('security.context')->getToken()->getUser()->getRegularUser()) {
            throw new NotFoundHttpException('User not found!');
        }

        // check if user is your friend already
        if ($regularUser->existsFriend($friend)) {
            $this->get('session')->setFlash('negative-overlayer', $this->get('translator')->trans(
                            'regularuser.friends.exists', array('%friend%' => $friend->getName())
            ));
            return $this->redirect($this->generateUrl('fenchy_regular_user_user_profilev2', array('userId' => $id)));
        }

        // check if friend send invitation for you already - accept invitation
        if ($friend->isInvited($regularUser)) {
            $invitation = $em->getRepository('FenchyRegularUserBundle:Invitation')->findOneBy(array(
                'invitation_by' => $friend,
                'accepted' => null,
                'invitation_for' => $regularUser
            ));
            return $this->redirect($this->generateUrl('fenchy_regular_user_friend_invitation_response', array(
                                'id' => $invitation->getId(),
                                'response' => 'accept'
            )));
        }

        // check if you send invitation already
        if ($regularUser->isInvited($friend)) {
            $this->get('session')->setFlash('negative-overlayer', $this->get('translator')->trans(
                            'regularuser.friends.invitation.already', array('%friend%' => $friend->getName())
            ));
            
            return $this->redirect($this->generateUrl('fenchy_regular_user_user_profilev2', array('userId' => $user_id)));
        }

        $invitation = new Invitation();
        $invitation->setInvitationBy($regularUser)
                ->setInvitationFor($friend);
        $em->persist($invitation);
        $em->flush();
        if ($this->container->getParameter('notifications_enabled'))
            $this->inviteNotification($regularUser->getUser(), $friend->getUser(), $invitation);

        $this->get('session')->setFlash('positive-overlayer', 'regularuser.friends.invitation.send');
        return $this->redirect($this->generateUrl('fenchy_regular_user_user_profilev2', array('userId' => $user_id)));
    }

    /**
     * @Template() 
     */
    public function invitationsAction() {
        $regularUser = $this->get('security.context')->getToken()->getUser()->getRegularUser();
        $em = $this->getDoctrine()->getManager();

        $invitations = $em->getRepository('FenchyRegularUserBundle:Invitation')->findBy(array(
            'accepted' => null,
            'invitation_for' => $regularUser
        ));


        $user = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getEntityManager();

        $userId = $user->getId();
        $displayUser = false;

        if ($userId != NULL) {

            $userOther = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')->getAllData($userId);

            if (!$userOther instanceof \Fenchy\UserBundle\Entity\User)
                return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
            $displayUser = $userOther;
        }

        return array(
            'invitations' => $invitations,
            'displayUser' => $displayUser,
            'contacts' => $regularUser->getMyFriends()
        );
    }

    /**
     * Accept/reject response
     * @param int $id
     * @param string $response
     * @return redirect
     * @throws NotFoundHttpException 
     */
    public function invitationResponseAction($id, $response) {
        $regularUser = $this->get('security.context')->getToken()->getUser()->getRegularUser();
        $em = $this->getDoctrine()->getManager();

        $invitation = $em->getRepository('FenchyRegularUserBundle:Invitation')->findOneBy(array(
            'id' => $id,
            'accepted' => null,
            'invitation_for' => $regularUser
        ));

        if (null === $invitation) {
            throw new NotFoundHttpException('Invitation not found!');
        }

        $friend = $invitation->getInvitationBy();

        if ($regularUser->existsFriend($friend)) {
            $this->get('session')->setFlash('negative-overlayer', 'regularuser.friends.exists');
        } else {
            // set invitation as accepted/rejected
            $invitation->setAccepted('accept' === $response);
            $em->persist($invitation);
            $em->flush();

            if ('reject' === $response) {
                $this->get('session')->setFlash('positive-overlayer', $this->get('translator')->trans(
                                'regularuser.friends.invitation.rejected', array('%friend%' => $friend->getName())
                ));
            } else {
                // creating friend-friend relation
                $regularUser->addMyFriend($friend);
                $this->get('fenchy.reputation_counter')->update($regularUser->getUser(), \Fenchy\UserBundle\Services\ReputationCounter::TYPE_FRIEND);
                $this->get('fenchy.reputation_counter')->update($friend->getUser(), \Fenchy\UserBundle\Services\ReputationCounter::TYPE_FRIEND);
                $em->persist($regularUser);
                $em->persist($friend);
                $em->flush();

                $this->get('session')->setFlash('positive-overlayer', $this->get('translator')->trans(
                                'regularuser.friends.added', array('%friend%' => $friend->getName())
                ));
            }
        }

        return $this->redirect($this->generateUrl('fenchy_regular_user_friend_invitations'));
    }

    /**
     * @Template()
     * @return array 
     */
    public function deleteAction($friendId) {
        $regularUser = $this->get('security.context')->getToken()->getUser()->getRegularUser();
        $em = $this->getDoctrine()->getManager();

        $friend = $em->getRepository('FenchyRegularUserBundle:UserRegular')->find($friendId);

        if (null === $friend) {
            throw new NotFoundHttpException('User not found!');
        }

        if (!$regularUser->existsFriend($friend)) {
            $this->get('session')->setFlash('negative-overlayer', $this->get('translator')->trans(
                            'regularuser.friends.not', array('%friend%' => $friend->getName())
            ));
            return $this->redirect($this->generateUrl('fenchy_regular_user_friend_invitations'));
        }

        $form = $this->createForm(new FriendDeleteType());

        $userId = $this->get('security.context')->getToken()->getUser()->getId();
        $displayUser = false;

        if ($userId != NULL) {

            $userOther = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')->getAllData($userId);

            if (!$userOther instanceof \Fenchy\UserBundle\Entity\User)
                return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
            $displayUser = $userOther;
        }
        
        return array('friend' => $friend, 'displayUser' => $displayUser, 'form' => $form->createView());

    }

    public function deleteConfirmAction($friendId) {

        $form = $this->createForm(new FriendDeleteType());

        $request = $this->getRequest();

        if ('POST' == $request->getMethod()) {

            
            $form->bindRequest($request);
            
            if ($form->isValid()) {

                $regularUser = $this->get('security.context')->getToken()->getUser()->getRegularUser();
                $em = $this->getDoctrine()->getManager();

                $friend = $em->getRepository('FenchyRegularUserBundle:UserRegular')->find($friendId);

                if (null === $friend) {
                    throw new NotFoundHttpException('User not found!');
                }

                if (!$regularUser->existsFriend($friend)) {
                    $this->get('session')->setFlash('negative-overlayer', $this->get('translator')->trans(
                                    'regularuser.friends.not', array('%friend%' => $friend->getName())
                    ));
                    return $this->redirect($this->generateUrl('fenchy_regular_user_friend_invitations'));
                }

                $regularUser->removeMyFriend($friend);

                $this->get('fenchy.reputation_counter')->update($regularUser->getUser(), \Fenchy\UserBundle\Services\ReputationCounter::TYPE_FRIEND);
                $this->get('fenchy.reputation_counter')->update($friend->getUser(), \Fenchy\UserBundle\Services\ReputationCounter::TYPE_FRIEND);

                $em->persist($regularUser);
                $em->persist($friend);
                $em->flush();                

                $this->get('session')->setFlash('positive-overlayer', $this->get('translator')->trans(
                                'regularuser.friends.deleted', array('%friend%' => $friend->getName())
                ));
            }
        }

        return $this->redirect($this->generateUrl('fenchy_regular_user_friend_invitations'));
    }

    protected function inviteNotification(User $inviter, User $invitee, Invitation $invitation) {
        $notifications = $invitee->getNotifications();
        $niterator = $notifications->getIterator();

        $inviteNotification = false;
        foreach ($niterator as $onen) {
            if ($onen->getName() == 'contact_request')
                $inviteNotification = true;
        }

        if ($inviteNotification) {

            $interval = $invitee->getNotificationGroupIntervals()->first();
            if ($interval != null)
                $interval_val = $interval->getInterval();
            else
                $interval_val = null;

            $data = array(
                'sender' => $inviter,
                'user' => $invitee,
                'invitation' => $invitation
            );
            
            if ($interval_val === NotificationGroupInterval::INTERVAL_DAILY) {

                $queue_processing_hour = $this->container->getParameter('notification_queue_processing_hour');

                $now = new \DateTime;
                $now_hour = (integer) $now->format('G');

                $send_after = new \DateTime;
                if ($now_hour >= $queue_processing_hour) {
                    $send_after->modify('+1 day');
                }
                $send_after->setTime($queue_processing_hour, 0, 0);

                $toQueue = new NotificationQueue;
                $toQueue->setSendAfter($send_after)
                        ->setFromAddress($this->container->getParameter('from_email'))
                        ->setFromName($this->container->getParameter('from_name'))
                        ->setToAddress($invitee->getEmail())
                        ->setSubject($this->get('translator')->trans('invitations.email.subject', array('%username%' => $inviter->getRegularUser()->getFirstname())))
                        ->setBodyHtml($this->renderView('FenchyRegularUserBundle:Notifications:inviteEmailHTML.html.twig', $data))
                        ->setBodyPlain($this->renderView('FenchyRegularUserBundle:Notifications:inviteEmailPlain.html.twig', $data));
                $em = $this->getDoctrine()->getManager();
                $em->persist($toQueue);
                $em->flush();
            } elseif ($interval_val === NotificationGroupInterval::INTERVAL_IMMEDIATELY) {
                $emailNotification = \Swift_Message::newInstance()
                        ->setFrom($this->container->getParameter('from_email'), $this->container->getParameter('from_name'))
                        ->setTo($invitee->getEmail())
                        ->setSubject($this->get('translator')->trans('invitations.email.subject', array('%username%' => $inviter->getRegularUser()->getFirstname())))
                        ->setBody($this->renderView('FenchyRegularUserBundle:Notifications:inviteEmailHTML.html.twig', $data), 'text/html')
                        ->addPart($this->renderView('FenchyRegularUserBundle:Notifications:inviteEmailPlain.html.twig', $data), 'text/plain');
                $mailer = $this->get('mailer');
                $mailer->send($emailNotification);
            }
        }
    }

}
