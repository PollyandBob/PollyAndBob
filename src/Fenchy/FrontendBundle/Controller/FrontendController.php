<?php

namespace Fenchy\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Fenchy\UserBundle\Entity\User;
use Fenchy\UserBundle\Form\UserLocationType;
use Fenchy\RegularUserBundle\Entity\Shareholder;
use Fenchy\RegularUserBundle\Form\ShareholderType;

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
    public function indexV2Action($flag = NULL)
    {
        $request = $this->getRequest();
        $get = $request->get('time');
        $noticeRepo = $this->getDoctrine()->getEntityManager()->getRepository('FenchyNoticeBundle:Notice');
        $notices = $noticeRepo->getDashboardNotices();

        $requester = $this->getRequest()->get('requester');
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        if (!($user instanceof User)) {
        	
	        return $this->render('FenchyFrontendBundle:Frontend:indexV2.html.twig',
	            array(
	                'locale'=>$this->getRequest()->getLocale(),
	                'notices' => $notices,
	            	'flag' => $flag,
	            	'get' => $get,
	            	'location' => false,
                        'requester' => $requester
	                )
	        );
        }
        else 
        {        	
        	$form = $this->createForm(new UserLocationType(), $user);
			if($user->getLocation()=="")
			{
				return $this->redirect($this->generateUrl('fenchy_regular_user_user_your_location'));
			}
                        else
                        {
                            return $this->redirect($this->generateUrl('fenchy_notice_indexv2'));
                        }
		       	
        	return $this->render('FenchyFrontendBundle:Frontend:indexV2.html.twig',
        			array(
        					'locale'=>$this->getRequest()->getLocale(),
        					'notices' => $notices,
        					'flag' => $flag,
        					'get' => $get,
                                                'requester' => $requester
        			)
        	);
        }
    }
    
    public function boardofneighborAction()
    {
    	return $this->render('FenchyFrontendBundle:Frontend/Foot:boardofneighbor.html.twig',
    			array('locale'=>$this->getRequest()->getLocale()));
    }
    
    public function investinusAction()
    {
    	return $this->render('FenchyFrontendBundle:Frontend/Foot:investinus.html.twig',
    			array('locale'=>$this->getRequest()->getLocale()));
    }

    public function joinusAction()
    {
    	return $this->render('FenchyFrontendBundle:Frontend/Foot:joinus.html.twig',
    			array('locale'=>$this->getRequest()->getLocale()));
    }
    
    public function aboutAndImprintAction()
    {
    	return $this->render('FenchyFrontendBundle:Frontend/Foot:aboutAndImprint.html.twig',
    			array('locale'=>$this->getRequest()->getLocale()));
    }
    
    public function shareAction()
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$form = $this->createForm(new ShareholderType());
    	
    	$request = $this->getRequest();
    	
    	if ('POST' == $request->getMethod())
    	{
    		$form->bindRequest($request);
    	
    		//if ($form->isValid())
    		{
    			$em = $this->getDoctrine()->getEntityManager();
    	
    			$shareholder = $form->getData();    	
    			$em->persist($shareholder);
    			$user = $this->get('security.context')->getToken()->getUser();
    			if ( ! $user instanceof \Fenchy\UserBundle\Entity\User )
    			{
    				$user = $this->getDoctrine()
    					->getRepository('UserBundle:User')->getUserByEmail($shareholder->getEmail());
    			}
    			if($user)
    			{
    				if(!$user->getMember())
    				{
    					$user->addActivity(20);
    					$user->setMember(true);
    					$em->persist($user);
    				}    				
    			}
    				
    			$em->flush();
    			$data['total'] = $shareholder->getTotalamount();
    			$emailNotification = \Swift_Message::newInstance()->setFrom($this->container->getParameter('from_email'), $this->container->getParameter('from_name'))->setTo($shareholder->getEmail())->setSubject($this->get('translator')->trans('regularuser.message.subject_for_buy_share_mail'))->setBody($this->renderView('FenchyRegularUserBundle:Notifications:emailShare.html.twig', $data), 'text/html');

    			$mailer = $this->get('mailer');
    			$mailer->send($emailNotification);
    			
    			return $this->render('FenchyFrontendBundle:Frontend/Foot:share.html.twig',
    			array('locale'=>$this->getRequest()->getLocale(),
    					'form' => $form->createView(),
    					'done'=> 'Done'
    			));
    		}
//     		else
//     		{
//     			$form_errors = $this->get('translator')
//     			->trans('settings.flash.form_errors');
    			 
//     			$this->get('session')->setFlash('negative', $form_errors);
//     		}    		
    	}
    	return $this->render('FenchyFrontendBundle:Frontend/Foot:share.html.twig',
    			array('locale'=>$this->getRequest()->getLocale(),
    					'form' => $form->createView(),
    					'done' => 'Notdone'
    	));
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
    
     public function chatWindowAction()
        {
            $userLoggedIn = $this->get('security.context')->getToken()->getUser();
            $session = $this->getRequest()->getSession();
            $request = $this->getRequest();
            $flag = $request->get('flag');
            if($userLoggedIn instanceof \Fenchy\UserBundle\Entity\User)
            {
                if($flag=='add')
                {
                    $id = explode(',', $userLoggedIn->getChatwindows());
                    $ids = array_unique($id);
                    array_push($ids,  $request->get('userId'));
                    $ids = array_unique($ids);
                    $userLoggedIn->setChatwindows(implode(',',$ids));
                    $this->getDoctrine()->getEntityManager()->persist($userLoggedIn);
                    $this->getDoctrine()->getEntityManager()->flush($userLoggedIn);
                    $session->set('id'.$request->get('userId'), $request->get('userId'));
                }
                else if($flag == 'remove'){
                    
                    $ids = explode(',', $userLoggedIn->getChatwindows());
                    
                    if (($key = array_search($request->get('userId'), $ids)) !== false) 
                    {
                        unset($ids[$key]);
                    }                   
                    $ids = array_unique($ids);
                    
                    $userLoggedIn->setChatwindows(implode(',',$ids));
                    $this->getDoctrine()->getEntityManager()->persist($userLoggedIn);
                    $this->getDoctrine()->getEntityManager()->flush($userLoggedIn);
                    $session->remove('id'.$request->get('userId'));
                }
                else if($flag == 'get')
                {
                    $userArr = $request->get('userId');
                    $str = '';
                    $str1 = '';
                    $id = explode(',', $userLoggedIn->getChatwindows());
                    $ids = array_unique($id);
                    
                    for($i=0; $i< sizeof($userArr); $i++)
                    {
                        if(in_array($userArr[$i], $ids))
                               $str .= ",".$userArr[$i];
                        else
                            $str1 .= ",".$userArr[$i];
                    }                
                    echo $str."^^".$str1;
                }
                else if($flag == 'getonload')
                {
                      echo $userLoggedIn->getChatwindows();
//                    $str = '';
//                    $id = explode(',', $userLoggedIn->getChatwindows());
//                    $ids = array_unique($id);
//                    
//                    for($i=0; $i< sizeof($ids); $i++)
//                    {                        
//                        $str .= ",".$ids[$i];                     
//                    }                
//                    echo $str;
                }
            }
            else
            {
                echo 'none';
            }
            exit;
        }
        
        public function popupChatWindowAction()
        {
            $user = $this->get('security.context')->getToken()->getUser();
            
            $request = $this->getRequest();
            $session = $this->getRequest()->getSession();
            
            $sender = $this->getDoctrine()->getEntityManager()->getRepository('UserBundle:User')->find($request->get('sender'));
            if($user instanceof \Fenchy\UserBundle\Entity\User)
            {
                if($request->get('flag')=='true')
                {
                    $chats = $this->getDoctrine()->getRepository('CunningsoftChatBundle:Message')->findBySenderAndReceiver($user, $sender);
                    $session->set('count'.$sender->getId(),count($chats));
                    echo count($chats);
                    exit;
                }
                else
                {
                    $flag = $session->get('count'.$sender->getId());//intval($request->get('flag'));
                    $chats = $this->getDoctrine()->getRepository('CunningsoftChatBundle:Message')->findBySenderAndReceiver($user, $sender);
                    if($session->get('count'.$sender->getId()) != NULL)
                    {
                        if(count($chats) > $flag)
                        {
                            $session->set('count'.$sender->getId(),count($chats));
                            echo count($chats);
                            exit;
                        }
                    }
                    else
                    {
                        $session->set('count'.$sender->getId(),count($chats));
                    }
                    echo "0";
                    exit;
                }
            }
            else
            {
                echo "none";
                exit;
            }
        }
       
}
