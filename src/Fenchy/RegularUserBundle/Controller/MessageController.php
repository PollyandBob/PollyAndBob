<?php

namespace Fenchy\RegularUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Fenchy\MessageBundle\Entity\Message;
use Fenchy\UserBundle\Entity\User;

use Fenchy\UserBundle\Entity\NotificationGroupInterval;
use Fenchy\UserBundle\Entity\NotificationQueue;

class MessageController extends Controller
{
    /**
     * @Template()
     * @return array 
     */
    public function indexAction($type)
    {
        if($this->getRequest()->get('_format') == 'json') {
            $messages = $this->get('fenchy.messenger')->findReceivedMessages($type, $this->getRequest()->get('ids'));
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
            $messages = $this->get('fenchy.messenger')->findReceivedMessages($type);
        }
        return array(
            'messages' => $messages,
            'type' => null === $type ? 'all' : $type
        );
    }
    
    public function readAction()
    {
        $this->get('fenchy.messenger')->readAll();
        $this->get('session')->setFlash('positive', 'regularuser.message.read');
        return $this->redirect($this->generateUrl('fenchy_regular_user_messages_index'));
    }
    
    /**
     * @Template()
     * @return array
     */
    public function viewAction($id, $read = true)
    {
        $messenger = $this->get('fenchy.messenger');
        $last_message = $messenger->findLastById($id, $read);
        $messages = $messenger->findThreadMessages();
        
        $form = null;
        if ($last_message->isReplyable()) {
            $form = $messenger->createForm()->createView();
        }

        return array(
            'messages' => $messages,
            'form' => $form
        );
    }
    
    /**
     * @Template()
     * @return array 
     */
    public function newAction($id)
    {
        $messenger = $this->get('fenchy.messenger');
        $receiver = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($id);
        if (null === $receiver || $receiver === $this->getUser()) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }
        $messenger->setReceiver($receiver);
        $form = $messenger->createForm();
        
        return array(
            'form' => $form->createView(),
            'id' => $id,
            'user' => $receiver
        );
    }
    
    /**
     *
     */
    public function sendAction($prev_id)
    {
        $messenger = $this->get('fenchy.messenger');
        
        if (null !== $prev_id) {
            $messenger->findLastById($prev_id, false);
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        $form = $messenger->createForm();
        
        if ($form->isValid()) {
            $message = $messenger->send();
            if ( $this->container->getParameter('notifications_enabled'))
                $this->messageNotification($message, $user);
            $this->get('session')->setFlash('positive', 'regularuser.message.sent');
            if (null === $prev_id) {
                return $this->redirect($this->generateUrl('fenchy_regular_user_messages_index'));
            }
            return $this->redirect($this->generateUrl('fenchy_regular_user_messages_view', array('id' => $prev_id)));
        }

        if (null === $prev_id) {
            return $this->forward('FenchyRegularUserBundle:Message:new', array('id' => $messenger->getReceiver()));
        }
        return $this->forward('FenchyRegularUserBundle:Message:view', array(
                                                                            'id' => $prev_id,
                                                                            'read' => false
                                                                        ));
    }
    
    /**
     * @Template()
     * @param int $id
     * @return array 
     */
    public function noticeAction($id)
    {
        $messenger = $this->get('fenchy.messenger');
        $notice = $messenger->setNotice($id);
        $form = $messenger->createForm();
        
        return array(
            'notice' => $notice,
            'form'   => $form->createView()
        );
    }
    
    /**
     * @Template("FenchyRegularUserBundle:Message:noticeRequest.html.twig")
     * @param int $id
     * @return array 
     */
    public function noticeSendAction($id)
    {
        /** @var $messenger \Fenchy\MessageBundle\Services\Messenger */
        $messenger = $this->get('fenchy.messenger');
        $notice = $messenger->setNotice($id);
        $form = $messenger->createForm();

        if ($form->isValid()) {
            $message = $messenger->send();
            if ( $this->container->getParameter('notifications_enabled'))
                $this->noticeMessageNotification($message);
            $this->get('session')->setFlash('positive', 'regularuser.message.sent');
            return $this->redirect($this->generateUrl('fenchy_regular_user_notice', array('id' => $id)));
        }

        return array(
            'notice' => $notice,
            'form'   => $form->createView()
        );
    }
    
    public function deleteAction($id)
    {
        /** @var $messenger \Fenchy\MessageBundle\Services\Messenger */
        $messenger = $this->get('fenchy.messenger');
        $messenger->findById($id, true);
        $messenger->delete();
        $this->get('session')->setFlash('positive', 'regularuser.message.deleted');
        return $this->redirect($this->generateUrl('fenchy_regular_user_messages_index'));
    }
    
    public function deleteSelectedAction() {
        
        $request = $this->getRequest();
        $messages = $request->get('message');
        
        if(!$messages) {
            $this->get('session')->setFlash('negative', 'regularuser.message.check_any_message');
        } else {
            
            $ids = array_keys($messages);

            $messenger = $this->get('fenchy.messenger');
            $messages = $messenger->findByIds($ids);
            $messenger->deleteMany($messages);
            
            $this->get('session')->setFlash('positive', 'regularuser.message.messages_has_been_deleted');
            
        }
        
        return $this->redirect($this->generateUrl('fenchy_regular_user_messages_index'));
        
    }
    
    
    protected function messageNotification(Message $message, User $sender) 
    {
        $receiver = $message->getReceiver();
        
        $notifications = $receiver->getNotifications();
        $niterator = $notifications->getIterator();

        $message_notification = false;
        foreach( $niterator as $onen ) {
            if ( $onen->getName() == 'message' )
                $message_notification = true;
        }
        
        if ( $message_notification ) {
        
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
                    ->setSubject($this->get('translator')->trans('message.notification.email.subject', array('%username%' => $sender->getRegularUser()->getFirstname())))
                    ->setBodyHtml($this->renderView('FenchyMessageBundle:Notifications:messageEmailHTML.html.twig', 
                        array(
                            'sender' => $sender,
                            'message' => $message
                        )))
                    ->setBodyPlain($this->renderView('FenchyMessageBundle:Notifications:messageEmailPlain.html.twig',
                        array('sender' => $sender,
                            'message' => $message)));
                $em = $this->getDoctrine()->getManager();
                $em->persist($toQueue);
                $em->flush();
            } elseif ( $interval_val === NotificationGroupInterval::INTERVAL_IMMEDIATELY ) {
                $emailNotification = \Swift_Message::newInstance()
                    ->setFrom($this->container->getParameter('from_email'),
                        $this->container->getParameter('from_name'))
                    ->setTo( $receiver->getEmail() )
                    ->setSubject( $this->get('translator')->trans('message.notification.email.subject', array('%username%' => $sender->getRegularUser()->getFirstname())))
                    ->setBody($this->renderView('FenchyMessageBundle:Notifications:messageEmailHTML.html.twig',
                        array('sender' => $sender,
                            'message' => $message)),
                        'text/html')
                    ->addPart($this->renderView('FenchyMessageBundle:Notifications:messageEmailPlain.html.twig',
                        array('sender' => $sender,
                            'message' => $message)),
                        'text/plain');
                $mailer = $this->get('mailer');
                $mailer->send($emailNotification);
            }
        }
        
    }
    
    protected function noticeMessageNotification(Message $message)
    {
        $receiver = $message->getReceiver();
        
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
                    ->setSubject($this->get('translator')->trans('message.req_notification.email.subject'))
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
                    ->setSubject( $this->get('translator')->trans('message.req_notification.email.subject'))
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
    
    protected function getTimeFrom(\DateTime $date) {
        $translator = $this->container->get('translator');
        $time2str = $this->time2str($date->getTimestamp(), FALSE);
        
        if (is_string($time2str)) {
            return $translator->trans('util.relative.'.$time2str);
        } elseif (is_array($time2str)) {
            return $translator->transChoice('util.relative.'.$time2str['str'], 
                                             $time2str['number'],
                                             array('%number%' => $time2str['number']));
        }
    }
    
    protected function time2str($ts, $day = false)
    {
        $diff = time() - $ts;
        
        if($diff == 0) {
            return 'now';
        } elseif($diff > 0) {
            $day_diff = floor($diff / 86400);
            if($day_diff == 0) {
                if($day) return 'today';
                if($diff < 60) return 'just_now';
                if($diff < 120) return 'a_minute_ago';
                if($diff < 3600) return array('number' => floor($diff / 60), 'str' => 'x_minutes_ago');
                if($diff < 7200) return 'an_hour_ago';
                if($diff < 86400) return array('number' => floor($diff / 3600), 'str' => 'x_hours_ago');
            }
            if($day_diff == 1) return 'yesterday';
            if($day_diff < 7) return array('number' => $day_diff, 'str' => 'x_days_ago');
            if($day_diff < 31) return array('number' => ceil($day_diff / 7), 'str' => 'x_weeks_ago');
            if($day_diff < 60) return 'last_month';
        } else {
            $diff = abs($diff);
            $day_diff = floor($diff / 86400);
            if($day_diff == 0) {
                if($diff < 120) return 'in_a_minute';
                if($diff < 3600) return array('number' => floor($diff / 60), 'str' => 'in_x_minutes');
                if($diff < 7200) return 'in_an_hour';
                if($diff < 86400) return array('number' => floor($diff / 3600), 'str' => 'in_x_hours');
            }
            if($day_diff == 1) return 'tomorrow';
            if($day_diff < 4) return date('l', $ts); //day of the week
            if($day_diff < 7 + (7 - date('w'))) return 'next_week';
            if(ceil($day_diff / 7) < 4) return array('number' => ceil($day_diff / 7), 'str' => 'in_x_weeks');
            if(date('n', $ts) == date('n') + 1) return 'next_month';
        }
        return null;
    }
    
}
