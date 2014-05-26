<?php
namespace Fenchy\UserBundle\Controller;
use FOS\UserBundle\Controller\RegistrationController as Base;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fenchy\UserBundle\Form\RegistrationFormType;
/**
 * 
 */
class RegistrationController extends Base
{
    public function registerAction()
    {
    	if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
    		return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_indexv2'));
    	}
        //$form = $this->container->get('form.factory')->create(new RegistrationFormType());
        $form = $this->container->get('fos_user.registration.form');

        $formHandler = $this->container->get('my.registration.form.handler');
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');
        
        $process = $formHandler->process($confirmationEnabled);

        if ($process) {
            $user = $form->getData();
			
            $authUser = false;
            if ($confirmationEnabled) {
                #$this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                $route = 'fenchy_frontend_indexv2';
                $route_params = array ( 'time'=>'today' );
                $authUser = true;
                
                $gIds = $this->container->get('session')->get('gallery');
                if ($gIds == null)
                    $gIds = array(1 => '', 2 => '', 3 => '');
                
                $em = $this->container->get('doctrine')->getEntityManager();
                //$gallery = $em->getRepository('FenchyGalleryBundle:Gallery')->find($gIds[2]);
           
                //$gallery->setUser($user);
                
                //$em->persist($gallery);
                //$em->flush();
                
                $gIds = null;
                $this->container->get('session')->set('gallery', $gIds);
                
            } else {
                $authUser = true;
                $route = 'fos_user_registration_confirmed';
                $route_params = array();
            }

            $this->setFlash('fos_user_success', 'registration.flash.user_created');
            $url = $this->container->get('router')->generate($route, $route_params);
            
            $response = new Response(json_encode(array('registered' => 1, 'redirect' => $url)));
            $response->headers->set('Content-Type', 'application/json');

            if ($authUser) {
                $this->authenticateUser($user, $response);
            }

            //return $response;
            
            if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            	return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_indexv2'));
            }
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:registerV2.html.'.$this->getEngine(), array(
            'form' => $form->createView(),
        ));
        
        
        //return parent::registerAction();
    }
    
    public function registerV2Action() {
        
        return $this->container->get('templating')->renderResponse('UserBundle:Registration:registerV2.html.twig',
                array(
                    
                    )
                );   
        
    }
    
    public function confirmAction($token)
    {
        $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        $user->setConfirmationToken(null);
        $user->setEnabled(true);
        $user->setLastLogin(new \DateTime());
        
        //if ($user->getRegularUser()->hasRequiredLocation()) {
            
            $user->addRole('ROLE_FULL_USER');
        //}
        
        $this->container->get('fos_user.user_manager')->updateUser($user);
        $response = new RedirectResponse($this->container->get('router')->generate('fos_user_registration_confirmed'));
        $this->authenticateUser($user, $response);

        return $response;
    }
    
    public function confirmedAction()
    {
        parent::confirmedAction();
        
        // send internal "Welcome" message
        //$this->container->get('fenchy.messenger')->sendWelcomeMessage();
        
        return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_indexv2', array('time' => 'today')));
    }
}
