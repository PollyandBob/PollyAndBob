<?php

namespace Fenchy\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fenchy\UserBundle\Entity\Reference;
use Fenchy\UserBundle\Form\EmailInvitationFormType as EmailType;
use Fenchy\UserBundle\Entity\Unsubscriber;
use Symfony\Component\HttpFoundation\Response;

class FriendController extends Controller
{

    public function indexAction()
    {   
        $fb_id = $this->get('security.context')->getToken()->getUser()->getFacebookId();

        
        if ($fb_id) {
          try {
            // Proceed knowing you have a logged in user who's authenticated.
            $fbk = $this->get('fos_facebook.api');
            $user_friends = $fbk->api('/me/friends');
                        
          } catch (\FacebookApiException $e) {
              $user_friends = null;
              error_log($e);
              $login_url = $fbk->getLoginUrl();
          }
        } else {
            $user_friends = null;
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        //get user references 
        $references = $this->getDoctrine()
                            ->getManager()
                            ->getRepository('UserBundle:Reference')
                            ->findBy(array(
                                'ref_user' => $user,
                                'active' => true));        
        
        
        return $this->render('UserBundle:Friend:index.html.twig', array(
           'friends'    => $user_friends ? $user_friends['data'] : false,
           'references' => $references,
           'ask_again'       => $user->getAskAgain()
         ));         
    }
    

    
    public function facebookAction()
    {
        $fb_id = $this->get('security.context')->getToken()->getUser()->getFacebookId();
        
        if ($fb_id) {
          try {
            // Proceed knowing you have a logged in user who's authenticated.
            $fbk = $this->get('fos_facebook.api');
            $user_friends = $fbk->api('/me/friends');                       
                        
          } catch (\FacebookApiException $e) {
              $user_friends = null;
              error_log($e);
              $login_url = $fbk->getLoginUrl();
              return new Response("<script>top.location.href='$login_url';</script>");
          }
        } else {
            $user_friends = null;
        }
        
        return $this->redirect($this->generateUrl('fenchy_user_friend')); 
                
    }    
    public function newEmailAction()
    {
        $form = $this->createForm(new EmailType());
                               
        $request = $this->getRequest();
        
        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                
                $user = $this->container->get('security.context')->getToken()->getUser();
                $data = $form->getData();
                $note = $data['note'];
                
                unset($data['note']);
                
                $send = false;
                foreach ($data as $email) {
                    if ($email) {
                        
                        $user = $this->get('security.context')->getToken()->getUser();
                        $em = $this->getDoctrine()->getEntityManager();
                        
                        //check if reference exsists
                        $reference = $em->getRepository('UserBundle:Reference')
                                ->findOneBy(array(
                                    'ref_user' => $user,
                                    'new_user_email' => $email));
                        
                        if (!$reference) {
                            $reference = new Reference();                            
                            $reference->setNewUserEmail($email);
                            $reference->setRefUser($user);
                            
                            $em->persist($reference);
                            
                            //send email
                            $ru = $this->get('security.context')->getToken()->getUser()->getRegularUser();
                                                        
                            $hashed_email = sha1($email);
                            
                            //send invitation if email not on unsubscribers list
                            $unsubscriber = $em->getRepository('UserBundle:Unsubscriber')
                                    ->findOneBy(array('hashed_email' => $hashed_email));
                        
                            if (!$unsubscriber) {
                                
                                $message = \Swift_Message::newInstance()
                                    ->setSubject('Ivitation to fenchy.com')
                                    ->setFrom($this->container->getParameter('from_email'))
                                    ->setTo($email)
                                    ->setContentType('text/html')
                                    ->setBody($this->renderView('UserBundle:Friend:invitationEmail.html.twig', array(
                                        'note' => $note,
                                        'hashed_email' => $hashed_email,
                                        'username' => $user->getName())));    
                                
                                $this->get('mailer')->send($message);
                                $send = true;
                            }
                                                        
                        }                           

                    }
                }
                
                $em->flush();
                
                if ($send) {
                    $ivitation_to_fenchy_sent = $this->get('translator')->trans('regularuser.ivitation_to_fenchy_sent');
                    $this->get('session')->setFlash('positive', $ivitation_to_fenchy_sent);
                }
                
                return $this->redirect($this->generateUrl('fenchy_user_friend_new_email'));
            }
        }      
        
        return $this->render('UserBundle:Friend:formEmail.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    public function sendSuccessAction()
    {
    	$ivitation_to_fenchy_sent = $this->get('translator')->trans('regularuser.ivitation_to_fenchy_sent');
        $this->get('session')->setFlash('positive', $ivitation_to_fenchy_sent);
        
        $entity = $this->getDoctrine()
                            ->getManager()
                            ->getRepository('UserBundle:Reference')
                            ->findOneBy(array(
                                'new_user_fb_id' => $this->getRequest()->get('new_user_fb_id')
                            ));

        if (!$entity) {

            $em = $this->getDoctrine()->getManager();
            $user = $this->get('security.context')->getToken()->getUser();

            $reference = new Reference();
            $reference->setRefUserId($user->getId());
            $reference->setNewUserFbId($this->getRequest()->get('new_user_fb_id'));

            $em->persist($reference);
            $em->flush();
        }        
        
        return $this->redirect($this->generateUrl('fenchy_user_friend'));        
    }
    
}
