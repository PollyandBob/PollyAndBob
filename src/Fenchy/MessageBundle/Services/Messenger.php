<?php

namespace Fenchy\MessageBundle\Services;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\Common\Collections\ArrayCollection;
use Fenchy\UserBundle\Entity\User,
    Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\MessageBundle\Entity\Message;
use Fenchy\NoticeBundle\Entity\Notice;
use Fenchy\MessageBundle\Form\NewMessageType;
use Fenchy\MessageBundle\Form\ReplyMessageType;
use Fenchy\MessageBundle\Form\RequestMessageType;

/**
 * Message Service
 *
 * @author Ireneusz Plis <iplis@pgs-soft.com>
 */
class Messenger {

    /**
     * @var Message
     */
    protected $message;
    
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;
    
    /**
     * @var \Fenchy\UserBundle\Entity\User
     */
    protected $user;
    

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->setUser();
        $this->clear();
        
    }
    
    /**
     * Sets message as a new one. 
     */
    public function clear()
    {
        $this->message = new Message();
    }
    
    protected function setUser()
    {
        if (null === $this->user) {
            if(null !== $this->container->get('security.context')->getToken()) {
                $this->user = $this->container->get('security.context')->getToken()->getUser();
            }
        }
    }

    protected function getUser()
    {
        if (null === $this->user) {
            $this->setUser();
        }
        return $this->user;
    }

    /**
     * Returns form for new message or for reply.
     * If request method is POST, binds the form with request.
     * @param Message $message
     * @param array $options
     * @return \Symfony\Component\Form\Form
     */
    public function createForm(Message $message = null, array $options = array())
    {
        if (null === $message) {
            $message = $this->message;

        } 
        
        //get right form type
        if ($message->isReply()) {
            $type = new ReplyMessageType();
        } elseif ($message->isRequest()) {
            $type = new RequestMessageType();
        } else {
            $type = new NewMessageType();
        }
        $form = $this->container->get('form.factory')->create($type, $message, $options);
        $request = $this->container->get('request');
        if ($request->isMethod('POST')) {
            $form->bind($request);
        }
        
        null === $message->getSender() && $message->setSender($this->user);
        
        if(!$message->isReplyable()) {
            throw new \Exception('Cannot reply to deleted user');
        }
        
        return $form;
    }

    /**
     * Sets receiver of the message
     * 
     * @param User $receiver
     * @return \Fenchy\MessageBundle\Services\Messenger 
     */
    public function setReceiver(User $receiver)
    {
        $this->message->setReceiver($receiver);
        
        return $this;
    }

    /**
     * Gets receiver of the message
     * 
     * @return \Fenchy\UserBundle\Entity\User 
     */
    public function getReceiver()
    {
        return $this->message->getReceiver();
    }

    /**
     * Sets user's messages as read in this thread
     * @param Message $message 
     */
    public function setAsRead(Message $message)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        while (null !== $message && !$message->isRead()) {
            if ($message->getReceiver() === $this->getUser()) {
                $message->setRead(true);
                $em->persist($message);
            }
            $message = $message->getPrev();
        }
        $em->flush();
    }
    
    /**
     * Sets user's messages as read in this thread
     * @param Message $message 
     */
    public function setAsReadByGroupMember(Message $message, $usergroup)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        while (null !== $message && !$message->isRead()) {
            if($message->getUsergroup())
            {                
                if ($message->getUsergroup()->getId() == $usergroup) {
                    $message->setRead(true);
                    $em->persist($message);
                }
            }
//            else {
//                if ($message->getFromgroup()->getUser() === $this->getUser()) {
//                    $message->setRead(true);
//                    $em->persist($message);
//                }
//            }
            $message = $message->getPrev();
        }
        $em->flush();
    }
    
    /**
     * Sets all messages as read
     */
    public function readAll()
    {
        $unread = $this->findReceivedMessages('unread');

        foreach ($unread as $message) {
            $this->setAsRead($message);
        }
    }

    /**
     * Finds message
     * @param type $id
     * @return Message
     * @throws NotFoundHttpException 
     */
    public function findById($id, $deleted = false)
    {
        $repo = $this->container->get('doctrine.orm.entity_manager')->getRepository('FenchyMessageBundle:Message');
        if ($deleted) {
            $message = $repo->findOneById($id);
        } else {
            $message = $repo->findById($id, $this->getUser());
        }
        if (null === $message) {
            throw new NotFoundHttpException('Message not found!');
        }
        $this->message = $message;
        return $this->setMessage($message);
    }
    
    /**
     * Finds messages
     * @param array $id
     * @return array of Message
     * @throws NotFoundHttpException 
     */
    public function findByIds(array $ids)
    {
        $repo = $this->container->get('doctrine.orm.entity_manager')->getRepository('FenchyMessageBundle:Message');
        $messages = $repo->findByIds($ids, $this->getUser());
        
        if (null === $messages) {
            throw new NotFoundHttpException('Messages not found!');
        }
        
        return $messages;
    }    

    /**
     * Finds last message from thread
     * @param type $id
     * @param bool $read
     * @return type
     * @throws NotFoundHttpException 
     */
    public function findLastById($id, $read = true)
    {
        $repo = $this->container->get('doctrine.orm.entity_manager')->getRepository('FenchyMessageBundle:Message');
        $message = $repo->findLastById($id, $this->getUser());
        
        if (null === $message) {
            throw new NotFoundHttpException('Message not found!');
        }
        if ($read) $this->setAsRead($message);
        
        if ($message->getSystem() || $message->getReceiver() == NULL || $message->getSender() == NULL) {
            $this->message = $message;

        } else {
            $this->message = $this->createReply($message);
        }
        
        return $this->setMessage($message);
    }
    
     /**
     * Finds last message from thread
     * @param type $id
     * @param bool $read
     * @return type
     * @throws NotFoundHttpException 
     */
    public function findLastByIdGroup($id, $read = true, $usergroup)
    {
        $repo = $this->container->get('doctrine.orm.entity_manager')->getRepository('FenchyMessageBundle:Message');
        $message = $repo->findLastByIdGroup($id, $this->getUser(), $usergroup);
       
        if (null === $message) {
            throw new NotFoundHttpException('Message not found!');
        }
        if ($read) $this->setAsReadByGroupMember($message,$usergroup);
        
        if ($message->getSystem() || $message->getReceiver() == NULL || $message->getSender() == NULL) {
            $this->message = $message;

        } else {
            $this->message = $this->createReply($message);
        }
        
        return $this->setMessage($message);
    }
    
    /**
     * Clones message and sets data for reply
     * @param \Fenchy\MessageBundle\Entity\Message $message
     * @return \Fenchy\MessageBundle\Entity\Message reply
     */
    public function createReply(Message $message)
    {
    	$request = $request = $this->container->get('request');
    	$groupId = "";
    	$groupId= $request->get('groupId');
        $group = null;
        if($groupId!='')
            $group = $this->container->get('doctrine.orm.entity_manager')->getRepository('FenchyRegularUserBundle:UserGroup')->find($groupId);
        if($group)
        {
            $reply = clone $message;
            $reply->setFirst($message->getFirst())
                    ->setPrev($message)
                    ->unsetSenderDeletedAt()
                    ->unsetReceiverDeletedAt()
                    ->setFromgroup($group);
            // Check if switch sender and receiver
            if ($message->getReceiver() === $this->getUser()) {
                $reply->setReceiver($message->getSender());
            }
        }
        else
        {
            $reply = clone $message;
            $reply->setFirst($message->getFirst())
                    ->setPrev($message)
                    ->unsetSenderDeletedAt()
                    ->unsetReceiverDeletedAt();                   
            // Check if switch sender and receiver
            if ($message->getReceiver() === $this->getUser()) {
                $reply->setReceiver($message->getSender());
            } 
        }
        return $reply;
    }
    
    /**
     * @return \Fenchy\MessageBundle\Entity\Message 
     */
    public function getMessage()
    {
        return $this->setMessage($this->message);
    }

    /**
     * Sends message
     * @param \Fenchy\MessageBundle\Entity\Message $message 
     */
    public function send(Message $message = null)
    {
        
        if (null === $message) {
            $message = $this->message;
        }

        if (null === $message->getSender() && !$message->getSystem()) {
            $message->setSender($this->getUser());
        }
        
        if($message->getSender() == $message->getReceiver()) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }
        
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->container->get('doctrine.orm.entity_manager');
        $em->persist($message);
        $em->flush();
        
        return $message;
    }

    /**
     * Finds messages (threads) received by user
     * @param string type
     * @return ArrayCollection
     */
    public function findReceivedMessages($type = null, $ids = array())
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $this->setUser();
        return $this->setMessages($em->getRepository('FenchyMessageBundle:Message')->findReceivedMessages($this->getUser(), $type, $ids));
    }
    
    public function findReceivedMessagesOfGroup($type = null, $ids = array(), \Fenchy\RegularUserBundle\Entity\UserGroup $usergroup)
    {
    	$em = $this->container->get('doctrine.orm.entity_manager');
    	return $this->setMessages($em->getRepository('FenchyMessageBundle:Message')->findReceivedMessagesGroup($usergroup, $type, $ids));
    }
    
    /**
     * Finds messages for this thread
     * @return ArrayCollection 
     */
    public function findThreadMessages()
    {
        if ($this->isDeleted($this->message->getPrev())) {
            return new ArrayCollection();
        }
        $em = $this->container->get('doctrine.orm.entity_manager');
        return $this->setMessages(new ArrayCollection($em->getRepository('FenchyMessageBundle:Message')->findThreadMessages($this->message)));
    }
    
    public function countUnread()
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $this->setUser();
        return $em->getRepository('FenchyMessageBundle:Message')->count($this->getUser(), 'unread');
    }
    
    /**
     * Sets notice for message
     * @param int $id
     * @return \Fenchy\NoticeBundle\Entity\Notice
     */
    public function setNotice($id)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $notice = $em->getRepository('FenchyNoticeBundle:Notice')->findOneById($id);
        $pre_title = $this->container->get('translator')->trans('regularuser.message.about_pre_title');
        $this->message->setNotice($notice)
                    ->setReceiver($notice->getUser())
                    ->setSender($this->user)
                    ->setTitle($pre_title.$notice->getTitle());
        return $notice;
    }
    
    /**
     * Alias for cascadeDelete
     * @param Message $message 
     */
    public function delete(Message $message = null)
    {
        $this->cascadeDelete($message);
    }

    /**
     * Delete many messages at once
     * @param array $message 
     */
    public function deleteMany(array $messages)
    {
        if($messages) {
            foreach($messages as $message) {
                $this->cascadeDelete($message);
            }
        }
        
    }    
    
    /**
     * Deletes sender/receiver thread.
     * @param Message $message 
     */
    public function cascadeDelete(Message $message = null)
    {
        if (null === $message) {
            $message = $this->message;
        }
        
        if($message->getSender() !== NULL && $message->getSender()->getId() === NULL)
            $message->unsetSender();
        
        if($message->getReceiver() !== NULL && $message->getReceiver()->getId() === NULL)
            $message->unsetReceiver();
        
        $this->_delete($message->getFirst());
        $this->container->get('doctrine.orm.entity_manager')->flush();
    }

    /**
     * Sets sender/receiver deleted_at for current user
     * Called recursively for whole thread
     * @param Message $message
     */
    private function _delete(Message $message = null)
    {
        if (null === $message) {
            return;
        }
        if ($message->getSender() == $this->getUser()) {
            $message->setSenderDeletedAt();
        }
        if ($message->getReceiver() == $this->getUser()) {
            $message->setReceiverDeletedAt();
        }
        $this->container->get('doctrine.orm.entity_manager')->persist($message);
        
        $this->_delete($message->getNext());
    }
    
    /**
     * Get sender/receiver deleted_at
     * @param Message $message
     * @return \DateTime|null
     */
    public function getDeleted(Message $message = null)
    {
        if (null === $message) {
            $message = $this->message;
        }
        
        if ($message->getReceiver() == $this->getUser()) {
            return $message->getReceiverDeletedAt();
        }
        if ($message->getSender() == $this->getUser()) {
            return $message->getSenderDeletedAt();
        }
    }
    
    /**
     * Checks if message is deleted
     * @param Message $message
     * @return bool
     */
    public function isDeleted(Message $message = null)
    {
        return null !== $this->getDeleted($message);
    }
    
    public function sendWelcomeMessage()
    {
        $trans = $this->container->get('translator');
        
        $firstname = $this->getUser()->getUserRegular()->getFirstname();
        
        $welcome_sender_email = $this->container->getParameter('welcome_message_sender');
        $welcome_sender = $this->container->get('fos_user.user_manager')
            ->findUserByEmail($welcome_sender_email);
        
        $this->clear();
        
        if($welcome_sender) {
            /*
             * To make message a 'system' message add ->setSystem(true)
             */        
            $this->message
                    ->setSender($welcome_sender)
                    ->setReceiver($this->getUser())
                    ->setTitle($trans->trans('message.welcome.title'))
                    ->setContent($trans->trans('message.welcome.content', array('%firstname%'=>$firstname)));
            $this->send();
        }
    }
    
    /**
     * Removes all user messages where second end user is NULL and unsets sender/receiver
     * where it is eq to logged in User.
     * 
     * @author Michał Nowak <mnowak@pgs-soft.com>
     */
    public function removeUserMessages() {
        
        $em = $this->container->get('doctrine.orm.entity_manager');
        $messages = $em->getRepository('FenchyMessageBundle:Message')->findBySenderOrReceiver($this->user);

        foreach ($messages as $message)
        {
            if($message->getFirst()->getId() != $message->getId())
            {
                $em->persist($message->unsetFirstMsg());
            }
            $em->persist($message->unsetNextMsg());
            $em->persist($message->unsetPrevMsg());
            $em->flush();
        }
        foreach($messages as $message) {

           
//            if($message->getSender() == NULL) {
//                if($message->getReceiver()->getId() === $this->user->getId()) {                    
//                    $em->remove($message); // remove if sender and receiver has been deleted
//                }
//            } elseif($message->getReceiver() == NULL) {
//                if($message->getSender()->getId() == $this->user->getId()) {                    
//                    $em->remove($message); // remove if sender and receiver has been deleted
//                }
//            } else {
                if($message->getSender()->getId() == $this->user->getId()) {
                     
                     $em->remove($message); // unset sender if he is going to be deleted
                    
                }
                
                if($message->getReceiver()->getId() == $this->user->getId()) {
                    $em->remove($message);// unset receiver if he is going to be deleted
                }
            //}
        }
        
        // We have to flush twice to avoid situation where same message is from/to user
        // and about user notice.
        $em->flush();
        
        $messages = $em->getRepository('FenchyMessageBundle:Message')->findAboutUserNotices($this->user);

        foreach ($messages as $message) {

            $em->persist($message->unsetNotice()); // release relation by unset message about notice
        }

        $em->flush();
        
    }
    
    public function deleteUserMessage($id) {
        
        $em = $this->container->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('UserBundle:User')->find($id);
        $messages = $em->getRepository('FenchyMessageBundle:Message')->findBySenderOrReceiver($user);

        foreach ($messages as $message)
        {
            if($message->getFirst()->getId() != $message->getId())
            {
                $em->persist($message->unsetFirstMsg());
            }
            
            $em->persist($message->unsetNextMsg());
            $em->persist($message->unsetPrevMsg());
            $em->flush();
        }
        foreach($messages as $message) {

//            if($message->getSender() == NULL) {
//                if($message->getReceiver()->getId() === $id) {
//                    $em->remove($message); // remove if sender and receiver has been deleted
//                }
//            } elseif($message->getReceiver() == NULL) {
//                if($message->getSender()->getId() == $id) {
//                    $em->remove($message); // remove if sender and receiver has been deleted
//                }
//            } else {
                if($message->getSender()->getId() == $id) {
                    $em->persist($message->unsetSender()); // unset sender if he is going to be deleted
                }
                
                if($message->getReceiver()->getId() == $id) {
                    $em->persist($message->unsetReceiver()); // unset receiver if he is going to be deleted
                }
//            }
        }
        
        // We have to flush twice to avoid situation where same message is from/to user
        // and about user notice.
        $em->flush();
        
        
        $messages = $em->getRepository('FenchyMessageBundle:Message')->findAboutUserNotices($user);

        foreach ($messages as $message) {

            $em->persist($message->unsetNotice()); // release relation by unset message about notice
        }

        $em->flush();
    }
    
    /**
     * Removes relation between messages and given notice
     * 
     * @param Notice $notice
     * @uses FenchyRegularUserBUndle\NoticeController::deleteAction()
     * @author Michał Nowak <mnowak@pgs-soft.com>
     */
    public function releaseMessagesAboutNotice(Notice $notice) {
        
        $em = $this->container->get('doctrine.orm.entity_manager');
        
        $messages = $em->getRepository('FenchyMessageBundle:Message')->findByNotice($notice);
        
        foreach ($messages as $message) {

            $em->persist($message->unsetNotice()); // release relation by unset message about notice
        }

        $em->flush();
    }
    
    /**
     * Sets "deleted user" if sender or receiver is null;
     * @param Message $message
     * @return Message 
     * @author Michał Nowak <mnowak@pgs-soft.com>
     */
    private function setMessage(Message $message) {
        
        if($message->getReceiver() == NULL) {
            $user = new User();
            $user->setRegularUser(new UserRegular());
            $user->getRegularUser()->setFirstname($this->container->get('translator')->trans('message.deleted_user.firstname'));
            $message->setReceiver($user);
        }
        if($message->getSender() == NULL) {
            $user = new User();
            $user->setRegularUser(new UserRegular());
            $user->getRegularUser()->setFirstname($this->container->get('translator')->trans('message.deleted_user.firstname'));
            $message->setSender($user);
        }
        
        return $message;
    }
    
    /**
     * @see setMessage()
     * 
     * @param mixed array or collection $messages
     * @return mixed array or collection 
     * @author Michał Nowak <mnowak@pgs-soft.com>
     */
    private function setMessages($messages) {
        
        $user = new User();
        $user->setRegularUser(new UserRegular());
        $user->getRegularUser()->setFirstname($this->container->get('translator')->trans('message.deleted_user.firstname'));
        
        foreach($messages as &$message) {
            
            if($message->getReceiver() == NULL) $message->setReceiver($user);
            
            if($message->getSender() == NULL) $message->setSender($user);
        }
        
        return $messages;
    }
}

?>
