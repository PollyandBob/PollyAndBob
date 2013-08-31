<?php

namespace Fenchy\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FrontendController extends Controller
{
    public function indexAction()
    {
        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            return new RedirectResponse($this->container->get('router')
                ->generate('fenchy_regular_user_news', array('time'=>'today', 'page'=>1)));
        }
        return $this->render('FenchyFrontendBundle:Frontend:index.html.twig',
            array('locale'=>$this->getRequest()->getLocale()));
    }
    
    /* Temporary action for James to work on frontend marksup
     * in templates.
     */
    public function indexV2Action()
    {
        
        $noticeRepo = $this->getDoctrine()->getEntityManager()->getRepository('FenchyNoticeBundle:Notice');
        $notices = $noticeRepo->getDashboardNotices();

        return $this->render('FenchyFrontendBundle:Frontend:indexV2.html.twig',
            array(
                'locale'=>$this->getRequest()->getLocale(),
                'notices' => $notices
                )
        );
    }

    public function profileAction()
    {
        return $this->render('FenchyFrontendBundle:Frontend:profile.html.twig',
            array('locale'=>$this->getRequest()->getLocale()));
    }
    
    public function aboutAction()
    {
        return $this->render('FenchyFrontendBundle:Frontend/Foot:about.html.twig',
            array('locale'=>$this->getRequest()->getLocale()));
    }
    
    public function imprintAction()
    {
        return $this->render('FenchyFrontendBundle:Frontend/Foot:imprint.html.twig',
            array('locale'=>$this->getRequest()->getLocale()));
    }
    
    public function jobsAction()
    {
        return $this->render('FenchyFrontendBundle:Frontend/Foot:jobs.html.twig',
            array('locale'=>$this->getRequest()->getLocale()));
    }
    
    public function languagesAction()
    {
        return $this->render('FenchyFrontendBundle:Frontend/Foot:languages.html.twig',
            array('locale'=>$this->getRequest()->getLocale()));
    }
    
    public function privacyAction()
    {
        return $this->render('FenchyFrontendBundle:Frontend/Foot:privacy.html.twig',
            array('locale'=>$this->getRequest()->getLocale()));
    }
    
    public function privacyPopupAction()
    {
        return $this->render('FenchyFrontendBundle:Frontend/Foot:privacyPopup.html.twig',
            array('locale'=>$this->getRequest()->getLocale()));
    }
    
    public function facebookAction()
    {
        return $this->render('FenchyFrontendBundle:Frontend/Foot:facebook.html.twig',
            array('locale'=>$this->getRequest()->getLocale()));
    }
    
    public function advertisingAction()
    {
        return $this->render('FenchyFrontendBundle:Frontend/Foot:advertising.html.twig',
            array('locale'=>$this->getRequest()->getLocale()));
    }
    
    public function termsAction()
    {
        return $this->render('FenchyFrontendBundle:Frontend/Foot:terms.html.twig',
            array('locale'=>$this->getRequest()->getLocale()));
    }

    public function termsPopupAction()
    {
        return $this->render('FenchyFrontendBundle:Frontend/Foot:termsPopup.html.twig',
            array('locale'=>$this->getRequest()->getLocale()));
    }
    
    public function preregisterAction()
    {
        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            return new RedirectResponse($this->container->get('router')->generate('fos_user_profile_show'));
        }   
                
        return $this->render('UserBundle:Registration:preRegister.html.twig');      
    }
    
    public function interestedAction()
    {
    	$noticeRepo = $this->getDoctrine()->getEntityManager()->getRepository('FenchyNoticeBundle:Notice');
    	$notices = $noticeRepo->getDashboardNotices();
    	
    	$request = $this->get('request');
   		$email=$request->request->get('email');
    	
    	
    	$unsubscriber = 0;
    	if (!$unsubscriber) {
			
    		$message = \Swift_Message::newInstance()
    		->setSubject('Polly & Bob Newsletter')
    		->setFrom($email)
    		->setTo($email)
    		->setContentType('text/html')
    		->setBody($this->renderView('FenchyFrontendBundle:Frontend:invitationEmail.html.twig', array(
    				'note' => "THis is for test",
    				'hashed_email' => $email,
    				'username' => "jigs")));
    	
    		$this->get('mailer')->send($message);
    		$send = true;
    		//echo "<script>alert('asdasd')</script>";
    		
    		if ($send) {
    			$ivitation_to_fenchy_sent = $this->get('translator')->trans('regularuser.newsletter_to_fenchy_sent');
    			$this->get('session')->setFlash('positive', $ivitation_to_fenchy_sent);
    		}
    		
    		return $this->render('FenchyFrontendBundle:Frontend:indexV2.html.twig',
    				array(
    						'locale'=>$this->getRequest()->getLocale(),
    						'notices' => $notices
    				)
    		);
    		
    	}	
    }
    
}
