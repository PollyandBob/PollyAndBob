<?php

namespace Fenchy\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\RegularUserBundle\Form\UserRegularTwitterType;
use Fenchy\UserBundle\Entity\User;
use Fenchy\UserBundle\Form\TwitterFormType;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Form;

/**
 * Controller for twitter actions
 * 
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class TwitterController extends Controller
{
    /**
     * Connect to twiter to get redirect url and redirect user to confirm oAuth
     * 
     * @return RedirectResponse 
     */
    public function connectAction()
    {   

      $request = $this->get('request');
      $twitter = $this->get('fos_twitter.service');

      $authURL = $twitter->getLoginUrl($request);

      return $this->redirect($authURL);

      return $response;

    }
    
    /**
     * Disconnect twitter account.
     * 
     * @return RedirectResponse 
     */
    public function disconnectAction () {
        
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        $user->setTwitterID(NULL);
        $user->setTwitterUsername(NULL);
        
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($user);
        $em->flush();
        
		$twitter_disconnected = $this->get('translator')->trans('user.twitter_disconnected');
        $this->get('session')->setFlash('positive', $twitter_disconnected);
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    
    public function loginAction()
    {

    }
    
    /**
     * Action call by twitter response.
     * Gets twitter userdata and access_token.
     * Display twitter confirmation form which contain userdata and location data.
     * 
     * @return Response
     */
    public function loginCheckAction()
    {

        $request = $this->container->get('request');
        /* @var $request \Symfony\Component\HttpFoundation\Request */

        // get oauth data from url
        $oauth_token    = $request->query->get('oauth_token');
        $oauth_verifier = $request->query->get('oauth_verifier');
        
        /**
         * Service for twitter management
         * 
         * @var $provider \Fenchy\UserBundle\Security\User\Provider\TwitterProvider
         */
        $provider = $this->container->get('my.twitter.user');
        
        // Gets access_token and store it in session for future use
        // If we cannot get token, redirect to requested url.
        if(NULL === $provider->getAccessToken($oauth_token, $oauth_verifier)) {
            
			$could_not_get_token = $this->get('translator')->trans('user.could_not_get_token');
            $this->get('session')->setFlash('negative', $could_not_get_token);

            if (NULL === $this->getRequest()->headers->get('referer')) {
                return $this->redirect($this->generateUrl('fos_user_security_login'));
            }      
            
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        /**
         * authorize() returns existing user (by twitter data) or create new one
         * with set twitter data.
         * 
         * @var $user \Fenchy\UserBundle\Entity\User
         */
        $user = $provider->authorize(
                    $this->container->get('security.context')->getToken()->getUser()
                );
		
		if( null == $user ) {
			return new RedirectResponse($this->container->get('router')
			    ->generate('fenchy_frontend_signin'));
		}
		
		/*
		 * To handlie situation when a user, who had registered with Twitter, then disconnected
		 *  his account from twitter, now tries to Login with Twitter  
		 */	
		$twitter_supplied_id = $user->getTwitterId();
        $em = $this->getDoctrine()->getEntityManager();
        $existing_user = $em->getRepository('UserBundle:User')
            ->findBy( array('registeredWith' => 'twitter',
			                'registeredWithId' => $twitter_supplied_id ) );
		if( !empty($existing_user) ) {
			# Generate and display form with usermane filed and appropriate note
			$exists_note = $this->get('translator')->
			    trans('user.twitter_user_exists.0') . $this->get('translator')->
			    trans('user.twitter_user_exists.1') . $this->get('translator')->
			    trans('user.twitter_user_exists.2') . $this->get('translator')->
			    trans('user.twitter_user_exists.3') . $this->get('translator')->
			    trans('user.twitter_user_exists.4');
		    $show_username = TRUE;
		}
		else {
			$exists_note = '';
		    $show_username = FALSE;
		}
		// I case, the validation went wrong again.
		$this->get('session')->set('show_username', $show_username);
		
        // Check if given user is an existing one
        if($provider->isAuthorized()) {
            
            // set user session
            $this->container->get('fos_user.security.login_manager')->loginUser(
                    $this->container->getParameter('fos_user.firewall_name'),
                    $user
                );
            
            // go to profile
            return $this->redirect($this->generateUrl('fenchy_frontend_indexv2'));
            
        }
        
        if(FALSE === $user) {
            
            $could_not_verify_credentials_twitter = $this->get('translator')
			    ->trans('user.could_not_verify_credentials_twitter');
            
            $this->get('session')->setFlash('negative', $could_not_verify_credentials_twitter);
            
            if (NULL === $this->getRequest()->headers->get('referer')) {
                return $this->redirect($this->generateUrl('fenchy_regular_user_settings_socialnetworks'));
            }
            
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        $form = $this->createForm(new TwitterFormType($show_username), $user );
		
        return $this->render(
                'UserBundle:Twitter:loginCheck.html.twig',
                array(
                    'form'          => $form->createView(), 
                    'regularUser'   => $user->getRegularUser(),
                    'exists_note'   => $exists_note
                    )
                );
    }
    
    /**
     * Finalize registration by Twitter, after submiting form from loginCheckAction()
     * 
     * @return Response
     */
    public function finalizeAction () {
        
        /**
         * Handle twitter form.
         * @var \Fenchy\UserBundle\Form\Handler\TwitterFormHandler $formHandler
         */
        $formHandler = $this->container->get('my.twitter.registration.form.handler');
        
        /*
         * If user doesn't exist 
         */
        if(null == $formHandler->getUser())
        {
            
            $if_user_not_exist = $this->get('translator')
			    ->trans('user.user_twitter_reg_not_exist');
            
            throw new \Exception($if_user_not_exist);
        }

        /**
         * FOS param tederminates if account have to be confirmed
         * 
         * @var boolean $confirmationEnabled
         */
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');
        
		$show_username = $this->get('session')->get('show_username');
        $form = $this->createForm(new TwitterFormType($show_username), $formHandler->getUser());
        
        // Push form to form handler (to get form data inside)
        $formHandler->setForm($form);
        
        // Push request to form handler (it is not recommended to push it as service param)
        $formHandler->setRequest($this->getRequest());
        
        /**
         * TRUE if form is correct.
         * 
         * @var bool $process
         */
        $process = $formHandler->process($confirmationEnabled);
        
        if ($process) {
            
            $user = $form->getData();

            // If confirmation is needed display info about email sent
            if ($confirmationEnabled) {
                
                $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                $route = 'fos_user_registration_check_email';
                
            // othervise authenticate user
            } else {
                
                $this->authenticateUser($user);
                $route = 'fos_user_registration_confirmed';
            }

            $this->get('session')->remove('show_username');
            $this->get('session')->setFlash('fos_user_success', 'registration.flash.user_created');

            return $this->redirect($this->generateUrl($route));
        }
 
        // No redirection? Something went wrong. Render form again.
        return $this->render(
                'UserBundle:Twitter:loginCheck.html.twig',
                array(
                    'form'          => $form->createView(), 
                    'regularUser'   => $formHandler->getUser()
                    )
                );
    }
    
}