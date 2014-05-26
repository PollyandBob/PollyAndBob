<?php

namespace Fenchy\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Fenchy\UserBundle\Form\FacebookFormType;
use Fenchy\UserBundle\Entity\User;
use Fenchy\RegularUserBundle\Entity\UserRegular;

/**
 * Controller for facebook actions
 * 
 * @author Ireneusz Pliś <iplis@pgs-soft.com>
 */
class FacebookController extends Controller
{
    /**
     * Connect facebook account.
     * 
     * @return Response JSON response with url to redirect
     */
    public function connectAction()
    {
        /* @var $request \Symfony\Component\HttpFoundation\Request */
        $request       = $this->get('request');    
        $facebookId    = $request->get('facebookId');
        $facebookToken = $request->get('token');
        $em            = $this->getDoctrine()->getEntityManager();
        
        //save facebook data in session
        $this->get('session')->set('facebook', array(
                                    'id' => $facebookId,
                                    'token' => $facebookToken
                                ));
        
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        if (null !== $em->getRepository('UserBundle:User')->findOneByFacebookId($facebookId)) {
            //facebookId exists!
            $your_facebook_account_already_connected = $this->get('translator')
			    ->trans('user.your_facebook_account_already_connected');
            $this->get('session')->setFlash('negative', $your_facebook_account_already_connected);
        } else {
            
            $user->setFacebookID($facebookId);
            $this->get('fenchy.reputation_counter')
                        ->update($user, \Fenchy\UserBundle\Services\ReputationCounter::TYPE_FB);
            $this->get('fenchy.reputation_counter')
                        ->update($user, \Fenchy\UserBundle\Services\ReputationCounter::TYPE_PROFILE);
            $user->getRegularUser()->setFacebookPublish(false);
            $em->persist($user);
            $em->flush();
        
            $facebook_connected = $this->get('translator')
			    ->trans('user.facebook_connected');
            $this->get('session')->setFlash('positive', $facebook_connected);
        }
        // prepare json response
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
		
		$response_data = array();
		$noticeUrl = $request->get('noticeUrl');
		if ( null != $noticeUrl )
			$response_data['url'] = $noticeUrl;
		else 
			$response_data['url'] = $this->get('router')->generate('fenchy_regular_user_settings_socialnetworks');
		
        $response->setContent(json_encode($response_data));
        return $response;
    }
    
    /**
     * Disconnect facebook account.
     * 
     * @return RedirectResponse 
     */
    public function disconnectAction()
    {
        
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        $user->setFacebookID(NULL);
        $this->get('fenchy.reputation_counter')
                        ->update($user, \Fenchy\UserBundle\Services\ReputationCounter::TYPE_FB);
        $user->getRegularUser()->setFacebookPublish(false);

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($user);
        $em->flush();
        
	    $facebook_disconnected = $this->get('translator')
	        ->trans('user.facebook_disconnected');
        $this->get('session')->setFlash('positive', $facebook_disconnected);
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
	
	
	public function addTimelineAction()
    {
        /* @var $request \Symfony\Component\HttpFoundation\Request */
        $request       = $this->get('request');    
        $facebookId    = $request->get('facebookId');
        $facebookToken = $request->get('token');
        $em            = $this->getDoctrine()->getEntityManager();
        
        //save facebook data in session
        $this->get('session')->set('facebook', array(
                                    'id' => $facebookId,
                                    'token' => $facebookToken
                                ));
        
        $user = $this->container->get('security.context')->getToken()->getUser();
        
		$existing_user = $em->getRepository('UserBundle:User')->findOneByFacebookId($facebookId);
        if (null !== $existing_user) {
            $existing_user->getRegularUser()->setFacebookPublish(true);
			$em->persist($existing_user);
			$em->flush();
        } else {
            $user->getRegularUser()->setFacebookPublish(true);			
            $user->setFacebookID($facebookId);
            $em->persist($user);
            $em->flush();
        
		    $timeline_connected = $this->get('translator')
			    ->trans('user.facebook_timeline_connected');
            $this->get('session')->setFlash('positive', $timeline_connected);
        }
        // prepare json response
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode(array(
            'url' => $this->get('router')->generate('fenchy_regular_user_settings_socialnetworks')
        )));
        return $response;
    }

    public function disconnectTimelineAction()
    {
        
        $user = $this->container->get('security.context')->getToken()->getUser();
		$user->getRegularUser()->setFacebookPublish(false);
        
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($user);
        $em->flush();
        
	    $timeline_disconnected = $this->get('translator')
	        ->trans('user.facebook_timeline_disconnected');
        $this->get('session')->setFlash('positive', $timeline_disconnected);
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
	
	
	public function loginCheckAction()
	{
		$session = $this->get('session');
		$fb_identity = $session->get('facebook');
		
		if ( empty($fb_identity) || 
		     !isset($fb_identity['id']) || !isset($fb_identity['token']) ) {
			return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
		}
		
		$facebookId    = $fb_identity['id'];
		$facebookToken = $fb_identity['token'];
		
		$facebookProvider = $this->container->get('fenchy.facebook.user');
		
		$user = $facebookProvider->loadUserFromFbMe();

		if ( empty($user) ) {
			// TODO: change to more overall error message
			$some_fb_data_invalid = $this->get('translator')->trans('user.some_fb_data_invalid_for_fenchy');
			$this->get('session')->setFlash('negative', $some_fb_data_invalid);
			return new RedirectResponse($this->container->get('router')
			    ->generate('fenchy_frontend_signin'));	
		}
		
		$form = $this->createForm(new FacebookFormType(TRUE), $user);
		
		$exists_note = $this->get('translator')->
			    trans('user.facebook_user_exists.0').$this->get('translator')->
			    trans('user.facebook_user_exists.1').$this->get('translator')->
			    trans('user.facebook_user_exists.2').$this->get('translator')->
			    trans('user.facebook_user_exists.3').$this->get('translator')->
			    trans('user.facebook_user_exists.4');
		
        return $this->render(
                'UserBundle:Facebook:loginCheck.html.twig',
                array(
                    'form'          => $form->createView(), 
                    'regularUser'   => $user->getRegularUser(),
                    'exists_note'   => $exists_note
                    )
                );
		
	}
	
	public function finalizeAction()
	{
		$request = $this->getRequest();

		$user = new User();
        $user->setPassword('');
		$user->setEnabled(TRUE);
        $user->setLastLogin(new \DateTime());
        $user->setRegularUser(new UserRegular());

		$form = $this->createForm(new FacebookFormType(TRUE), $user);

        if ($request->isMethod('POST')) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
				$user->setRegisteredWith('facebook');
		        $user->setRegisteredWithId($user->getFacebookId());
                $em->persist($user);
                $em->flush();
			    $user_created = $this->get('translator')->trans('user.new_user_created');
			    $this->get('session')->setFlash('positive', $user_created);
			    return new RedirectResponse($this->container->get('router')
			        ->generate('fos_user_profile_show'));
            }
        }
		
		return $this->render(
            'UserBundle:Facebook:loginCheck.html.twig',
            array(
                'form'          => $form->createView(), 
                'regularUser'   => $user->getRegularUser()
            )
        );
	}
	    
}