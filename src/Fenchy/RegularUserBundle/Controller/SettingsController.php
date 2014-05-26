<?php

namespace Fenchy\RegularUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fenchy\RegularUserBundle\Form\UserRegularType;
use Fenchy\UserBundle\Form\UserLocationType;
use Fenchy\RegularUserBundle\Form\PopupLocationType;
use Fenchy\UserBundle\Form\ProfileFormType as UserType;
use Fenchy\UserBundle\Form\UserNotificationsType;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\RegularUserBundle\Entity\Document;
use Fenchy\UserBundle\Entity\User;
use Fenchy\UserBundle\Entity\EmailChangeRequest;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\UserBundle\Model\UserInterface;
use Fenchy\RegularUserBundle\Form\UserBasicSettingsType;
use Fenchy\UserBundle\Form\UserEmailType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Controller\ChangePasswordController;
use Fenchy\UserBundle\Form\PaymentDeleteType;
use Fenchy\NoticeBundle\Form\NoticeListingType;

class SettingsController extends Controller {

    /**
     * Display Settings/general form (UserRegular entity)
     * 
     * @return type 
     */
    public function generalAction() {
        $user = $this->get('security.context')->getToken()->getUser();

        if (!($user instanceof User)) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $form = $this->createForm(new UserType(), $user);

        $request = $this->getRequest();

        if ('POST' == $request->getMethod()) {

            $presentEmail = $user->getEmail();
            $form->bindRequest($request);
            $requestedEmail = $form->getData()->getEmail();

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getEntityManager();

                if ($presentEmail !== $requestedEmail) {
                    # User has modified their e-mail address
                    # We do not apply this change immediately, but send confirmation mail
                    # to the new address.
                    $previousChangeReq = $user->getEmailChangeRequest();
                    $ttl = $this->container->getParameter('fos_user.resetting.token_ttl');

                    if (null != $previousChangeReq && $previousChangeReq->isNonExpired($ttl)) {
                        # user has lately already requested e-mail change
                        # we don't allow to reqest new address change until the present token expires
                        # or the user explicitly aborts the request using abrort link
                        # sent to new requested address.
                        $ttlh = round($ttl / 3600);
                        $request_denied = $this->get('translator')
                                ->trans('emailchange.flash.request_denied', array('%ttlh%' => $ttlh));
                        $this->get('session')->setFlash('negative', $request_denied);
                        return $this->redirect($this->generateUrl('fenchy_regular_user_settings_general'));
                    } else {
                        # User haven't requested e-mail change yet OR previous request have expired

                        $user->setEmail($presentEmail);

                        if (null != $previousChangeReq) {
                            $user->setEmailChangeRequest(null);
                            $em->remove($previousChangeReq);
                            $em->flush();
                        }

                        $newChangeReq = new EmailChangeRequest();
                        $tokeng = $this->container->get('fos_user.util.token_generator');
                        $changeToken = $tokeng->generateToken();
                        $newChangeReq->setConfirmationToken($changeToken);
                        $newChangeReq->setUser($user);
                        $newChangeReq->setRequestedEmail($requestedEmail);
                        $em->persist($newChangeReq);

                        $mailer = $this->get('mailer');
                        $confirmUrl = $this->generateUrl('fenchy_regular_user_email_change_confirm', array('token' => $changeToken), true);
                        $abortUrl = $this->generateUrl('fenchy_regular_user_email_change_abort', array('token' => $changeToken), true);
                        $confirmEmail = \Swift_Message::newInstance()
                                ->setFrom($this->container->getParameter('from_email'), $this->container->getParameter('from_name'))
                                ->setTo($requestedEmail)
                                ->setSubject($this->get('translator')->trans('emailchange.email.subject'))
                                ->setBody($this->renderView('UserBundle:ChangeEmail:email-html.html.twig', array('confirmUrl' => $confirmUrl, 'abortUrl' => $abortUrl, 'user' => $user)), 'text/html')
                                ->addPart($this->renderView('UserBundle:ChangeEmail:email-plain.html.twig', array('confirmUrl' => $confirmUrl, 'abortUrl' => $abortUrl, 'user' => $user)), 'text/plain');
                        $mailer->send($confirmEmail);
                        $change_requested = $this->get('translator')
                                ->trans('emailchange.flash.change_requested');
                        $this->get('session')->setFlash('positive', $change_requested);
                    }
                }
                
                $em->persist($user);
                $em->flush();
                return $this->redirect($this->generateUrl('fenchy_regular_user_settings_general'));
            }
        }

        return $this->render(
                        'FenchyRegularUserBundle:Settings:general.html.twig', array(
                    'form' => $form->createView(),
                    'regularUser' => $user->getRegularUser()
                        )
        );
    }

    /**
     * emailChangeConfirmAction 
     * Triggerd when user clicks 'confirm e-mail address change' link in received e-mail
     * (according to requested e-mail address change in theirs profile).
     */
    public function emailChangeConfirmAction($token) {
        $user = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')
                ->findByEmailChangeConfirmationToken($token);
        if (!is_object($user) || !$user instanceof UserInterface) {
            $response = new RedirectResponse(
                    $this->container->get('router')->generate('fenchy_regular_user_settings_account'));
            return $response;
        }
        $response = new RedirectResponse($this->container->get('router')->generate('fenchy_regular_user_settings_account'));

        $changeRequest = $user->getEmailChangeRequest();
        $ttl = $this->container->getParameter('fos_user.resetting.token_ttl');
        if ($changeRequest->isNonExpired($ttl)) {
            $user->setEmail($changeRequest->getRequestedEmail());
            $user->setEmailChangeRequest(null);
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($changeRequest);
            $this->container->get('fos_user.user_manager')->updateUser($user);
            $this->authenticateUser($user, $response);
            $change_success = $this->get('translator')
                    ->trans('emailchange.flash.change_success');
            $this->get('session')->setFlash('positive', $change_success);
        } else {
            $ttlh = round($ttl / 3600);
            $token_expired = $this->get('translator')
                    ->trans('emailchange.flash.token_expired', array('%ttlh%' => $ttlh));
            $this->get('session')->setFlash('negative', $token_expired);
        };

        return $response;
    }

    /**
     * emailChangeRejectAction
     * Triggerd when user clicks 'abort e-mail adderss change' link in received e-mail
     * (according to requested e-mail address change in theirs profile).
     */
    public function emailChangeAbortAction($token) {
        $user = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')
                ->findByEmailChangeConfirmationToken($token);
        if (!is_object($user) || !$user instanceof UserInterface) {
            $response = new RedirectResponse(
                    $this->container->get('router')->generate('fenchy_regular_user_settings_account'));
            return $response;
        }
        $response = new RedirectResponse($this->container->get('router')->generate('fenchy_regular_user_settings_account'));

        $changeRequest = $user->getEmailChangeRequest();
        $user->setEmailChangeRequest(null);
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($changeRequest);
        $this->container->get('fos_user.user_manager')->updateUser($user);
        $this->authenticateUser($user, $response);
        $change_aborted = $this->get('translator')
                ->trans('emailchange.flash.change_aborted');
        $this->get('session')->setFlash('positive', $change_aborted);
        return $response;
    }

    /**
     * Display Settings/location form (UserRegular entity)
     * 
     * @return type 
     */
    public function locationAction() {
        $user = $this->get('security.context')->getToken()->getUser();

        if (!($user instanceof User)) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $form = $this->createForm(new UserLocationType(), $user);

        $request = $this->getRequest();

        if ('POST' == $request->getMethod()) {

            $form->bindRequest($request);

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getEntityManager();

                $location = $form->getData();

                $data_saved = $this->get('translator')
                        ->trans('settings.flash.data_saved');
                $this->get('session')->setFlash('positive', $data_saved);

                $em->persist($location);
                $em->flush();
                return $this->redirect($this->generateUrl('fenchy_regular_user_settings_location'));
            } else {

            }
        }

        return $this->render(
                        'FenchyRegularUserBundle:Settings:location.html.twig', array(
                    'form' => $form->createView(),
                    'user' => $user
                        )
        );
    }

    /**
     * Save form from locationAction
     */
    public function locationSaveAction() {
        $user = $this->get('security.context')->getToken()->getUser();

        if (!($user instanceof User)) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $request = $this->getRequest();
        $form = $this->createForm(new UserLocationType(), $user);
        $form->bindRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()
                    ->getEntityManager();

            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('fenchy_regular_user_settings_location'));
        } else {

        }

        return $this->render(
                        'FenchyRegularUserBundle:Settings:location.html.twig', array('form' => $form->createView(), 'regularUser' => $ru)
        );
    }

    public function imagesAction() {

        return $this->render(
                        'FenchyRegularUserBundle:Settings:images.html.twig'
        );
    }

    public function askAgainAction(Request $request) {
        $user = $this->get('security.context')->getToken()->getUser();

        $address = $request->get('address');
        $ask_again = $request->get('ask_again');

        $this->setAskAgainQuestion($user, $ask_again);

        return $this->redirect($this->generateUrl($address, array('ask_again' => $ask_again = $user->getAskAgain())));
    }

    /**
     * @Template
     * @return type 
     */
    public function popupLocationAction() {
        $user = $this->get('security.context')->getToken()->getUser();

        if (!($user instanceof User)) {

            throw $this->createNotFoundException('User has not been found.');
        }

        $ru = $user->getRegularUser();

        $form = $this->createForm(new PopupLocationType(), $ru);

        $request = $this->getRequest();

        if ($request->isMethod('POST')) {

            $form->bindRequest($request);

            if ($form->isValid()) {

                // If user location is correct and his email is confirmed we have
                // to give him ROLE_FULL_USER to enabled Fenchy to him
                if ($ru->hasRequiredLocation() && $user->getConfirmationToken() == NULL) {

                    $user->addRole('ROLE_FULL_USER');
                }

                $em = $this->getDoctrine()
                        ->getEntityManager();
                $em->persist($ru);
                $em->flush();
                echo '<script type="text/javascript">javascript:parent.reload();</script>';
                exit;
            }
        }

        return array(
            'form' => $form->createView(),
            'regularUser' => $ru
        );
    }

    private function setAskAgainQuestion($user, $ask_again) {
        $user->setAskAgain($ask_again);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
    }

    /**
     * Authenticate a user with Symfony Security
     * 
     * triggered only on login with facebook
     * 
     *
     * @param \FOS\UserBundle\Model\UserInterface        $user
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return bool true it authenticate was successful, false otherwise
     */
    protected function authenticateUser(UserInterface $user, Response $response) {
        try {
            $user->setLastLogin(new \DateTime());
            $this->container->get('fos_user.user_manager')->updateUser($user);

            $this->container->get('fos_user.security.login_manager')->loginUser(
                    $this->container->getParameter('fos_user.firewall_name'), $user, $response);

            return true;
        } catch (AccountStatusException $ex) {
            // We simply do not authenticate users which do not pass the user
            // checker (not enabled, expired, etc.).
        }
        return false;
    }

    /**
     * General settings with basic user's profile informations
     * 
     */
    public function basicAction() {
        
        $user = $this->get('security.context')->getToken()->getUser();

        if (!($user instanceof User)) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $user_regular = $user->getRegularUser();

        $form = $this->createForm(new UserBasicSettingsType(), $user_regular);

        $request = $this->getRequest();

        $data = array();

        if ('POST' == $request->getMethod()) {

            $form->bindRequest($request);

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getEntityManager();

                $regular_user = $form->getData();

                // we need to call gallery manager to handle gallery changes.
                $this->get('fenchy.gallery_manager')->manageGallery($user_regular->getGallery(), TRUE);

                $data_saved = $this->get('translator')
                        ->trans('settings.flash.data_saved');
                $this->get('session')->setFlash('positive', $data_saved);

                $this->get('fenchy.reputation_counter')->update($regular_user->getUser());

                $em->persist($regular_user);
                $em->flush();
                return $this->redirect($this->generateUrl('fenchy_regular_user_settings_basic'));
            } else {
                
                $form_errors = $this->get('translator')
                        ->trans('settings.flash.form_errors');                
                
                $data = $this->get('fenchy.gallery_manager')->manageGallery($user_regular->getGallery(), FALSE);
                $this->get('session')->setFlash('negative', $form_errors);
            }
        } else {
            $data = $this->get('fenchy.gallery_manager')->manageGallery($user_regular->getGallery());
        }

        $data['form'] = $form->createView();
        $data['regularUser'] = $user_regular;
        $data['languages'] = \Fenchy\UtilBundle\Locale\Locale::getDisplayLanguagesNames(\Locale::getDefault());

        return $this->render('FenchyRegularUserBundle:Settings:basic.html.twig', $data);
    }
    
    public function imageAction()
    {
    	$user = $this->get('security.context')->getToken()->getUser();
    	
    	if (!($user instanceof User)) {
    		return $this->redirect($this->generateUrl('fos_user_security_login'));
    	}
    	$request = $this->getRequest();
    	
    	$user_regular = $user->getRegularUser();
    	
    	$em = $this->getDoctrine()->getManager();
    	$document = $em->getRepository('FenchyRegularUserBundle:Document')->findById($user->getId());
    	
    	if (!$document) {
    		$document = new Document();
    	}
    	echo $this->getRequest()->get('file'); 
    	if($document->getWebPath() == '')
    	{
    		$user->addActivity('5');
    		$em->persist($user);
    	}
    	if($document->getWebPath2() == '')
    	{
    		$user->addActivity('2');
    		$em->persist($user);
    	}
    	$form3 = $this->createFormBuilder($document)
    	->add('user_id','hidden', array(
    			'data' => $user->getId(),))
    	->add('file',null,array('label' => 'settings.general.profile_photo'))
    	->add('user_id','hidden', array(
    			'data' => $user->getId(),))
    	->add('cropX','hidden',array(
    			'data' => $document->getCropX(),))
    	->add('cropY','hidden',array(
    			'data' => $document->getCropY(),))
    	->add('file2',null,array('label' => 'settings.general.cover_photo'))		
        ->getForm();
    	
    	    	
    	if ($this->getRequest()->isMethod('POST')) {
    		$form3->bindRequest($this->getRequest());
    		$user_id= $this->getRequest()->get('user_id');
    		$file = $this->getRequest()->get('file');
    		$file2 = $this->getRequest()->get('file2');
    		$vcropX = $form3->get('cropX')->getData();
    		$vcropY = $form3->get('cropY')->getData();
    		
    		if(!$vcropX)
    		{
    			$vcropX = 0;
    		}
    		if(!$vcropY)
    		{
    			$vcropY = 0;
    		}	
    		if ($form3->isValid()) {
    	
    			$document->upload();
    			$document->uploadcover();
    	
    			$document->setCropX($vcropX);
    			$document->setCropY($vcropY);
    			
    			$em->persist($document);
    			if($document->getWebPath()=="")
    			{
    				$user->setActivity($user->getActivity()-5);
    				$em->persist($user);
    			}
    			if($document->getWebPath2()=="")
    			{
    				$user->setActivity($user->getActivity()-2);
    				$em->persist($user);
    			}
    			$em->flush();
    	
    			return $this->redirect($this->generateUrl('fenchy_regular_user_user_myprofile_aboutmychoice'));
    		}
    	}
    	return $this->redirect($this->generateUrl('fenchy_regular_user_user_myprofile_aboutmychoice'));
    	
    }
    /**
     * 
     * Account section in user's settings
     * E-mail change, Delete account, Change password
     * 
     * @return Response
     */
    public function accountAction() {

        $user = $this->get('security.context')->getToken()->getUser();

        if (!($user instanceof User)) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $request = $this->getRequest();
        
        $user_regular = $user->getRegularUser();
        
        $em = $this->getDoctrine()->getManager();
        $document = $em->getRepository('FenchyRegularUserBundle:Document')->findById($user->getId());
        
        if (!$document) {
        	  $document = new Document();
        }
        
        $form3 = $this->createFormBuilder($document)
        ->add('user_id','hidden', array(
    		  'data' => $user->getId(),))
        ->add('file',null,array('label' => 'settings.general.profile_photo'))
        ->getForm();
        
        $form4 = $this->createFormBuilder($document)
        ->add('user_id','hidden', array(
        		'data' => $user->getId(),))
        		->add('file2',null,array('label' => 'settings.general.cover_photo'))
        		->getForm();
        
//         if ($this->getRequest()->isMethod('POST')) {
//         	$form3->bindRequest($this->getRequest());
//         	$form4->bindRequest($this->getRequest());
//         	$user_id= $this->getRequest()->get('user_id');
//         	$file = $this->getRequest()->get('file');
//         	$file2 = $this->getRequest()->get('file2');
        	
        	
//         	if ($form3->isValid() or $form4->isValid()) {

//         			$document->upload();
//         			$document->uploadcover();
        		
//         			$em->persist($document);
//         			$em->flush();

//         		return $this->redirect($this->generateUrl('fenchy_regular_user_settings_account'));
//         	}
        	
//         }
        
        $form = $this->createForm(new UserEmailType(), $user);

        $data = array();
        if ($request->getMethod() == 'POST') {

            $presentEmail = $user->getEmail();
            $form->bindRequest($request);
            $requestedEmail = $form->getData()->getEmail();

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getEntityManager();


                if ($presentEmail !== $requestedEmail) {
                    # User has modified their e-mail address
                    # We do not apply this change immediately, but send confirmation mail
                    # to the new address.
                    $previousChangeReq = $user->getEmailChangeRequest();
                    $ttl = $this->container->getParameter('fos_user.resetting.token_ttl');

                    if (null != $previousChangeReq && $previousChangeReq->isNonExpired($ttl)) {
                        # user has lately already requested e-mail change
                        # we don't allow to reqest new address change until the present token expires
                        # or the user explicitly aborts the request using abrort link
                        # sent to new requested address.
                        $ttlh = round($ttl / 3600);
                        $request_denied = $this->get('translator')
                                ->trans('emailchange.flash.request_denied', array('%ttlh%' => $ttlh));
                        $this->get('session')->setFlash('negative', $request_denied);
                        return $this->redirect($this->generateUrl('fenchy_regular_user_settings_account'));
                    } else {
                        # User haven't requested e-mail change yet OR previous request have expired

                        $user->setEmail($presentEmail);

                        if (null != $previousChangeReq) {
                            $user->setEmailChangeRequest(null);
                            $em->remove($previousChangeReq);
                            $em->flush();
                        }

                        $newChangeReq = new EmailChangeRequest();
                        $tokeng = $this->container->get('fos_user.util.token_generator');
                        $changeToken = $tokeng->generateToken();
                        $newChangeReq->setConfirmationToken($changeToken);
                        $newChangeReq->setUser($user);
                        $newChangeReq->setRequestedEmail($requestedEmail);
                        $em->persist($newChangeReq);

                        $mailer = $this->get('mailer');
                        $confirmUrl = $this->generateUrl('fenchy_regular_user_email_change_confirm', array('token' => $changeToken), true);
                        $abortUrl = $this->generateUrl('fenchy_regular_user_email_change_abort', array('token' => $changeToken), true);
                        $confirmEmail = \Swift_Message::newInstance()
                                ->setFrom($this->container->getParameter('from_email'), $this->container->getParameter('from_name'))
                                ->setTo($requestedEmail)
                                ->setSubject($this->get('translator')->trans('emailchange.email.subject'))
                                ->setBody($this->renderView('UserBundle:ChangeEmail:email-html.html.twig', array('confirmUrl' => $confirmUrl, 'abortUrl' => $abortUrl, 'user' => $user)), 'text/html')
                                ->addPart($this->renderView('UserBundle:ChangeEmail:email-plain.html.twig', array('confirmUrl' => $confirmUrl, 'abortUrl' => $abortUrl, 'user' => $user)), 'text/plain');
                        $mailer->send($confirmEmail);
                        $change_requested = $this->get('translator')
                                ->trans('emailchange.flash.change_requested');
                        $this->get('session')->setFlash('positive', $change_requested);
                    }
                } else {
                    $data_saved = $this->get('translator')
                            ->trans('settings.flash.data_saved');
                    $this->get('session')->setFlash('positive', $data_saved);
                }

                $em->persist($user);
                $em->flush();

                return $this->redirect($this->generateUrl('fenchy_regular_user_settings_account'));
            }
        }

        if (!is_object($user) || !$user instanceof UserInterface) {
        	throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        $form1 = $this->container->get('fos_user.change_password.form');      
       
        $form5 = $this->createDeleteForm($user->getId());
        
        $verified = $this->getDoctrine()
        			->getRepository('UserBundle:LocationVerification')->getStatus($user);
        $identity = $this->getDoctrine()
        			->getRepository('UserBundle:IdentityVerification')->getIdentityStatus($user);
        $verify_identity = $this->getDoctrine()
        			->getRepository('UserBundle:IdentityVerification')->getVerifyIdentity($user);
        
        $form2 = $this->createForm(new UserNotificationsType(), $user);
        
       
        $data['form'] = $form->createView();
        $data['form1'] = $form1->createView();
        $data['form2'] = $form2->createView();
        $data['form3'] = $form3->createView();
        $data['form4'] = $form4->createView();
        $data['form5'] = $form5->createView();
        $data['verified'] =  $verified;
        $data['identity'] =  $identity;
        $data['verifyIdentity'] =  $verify_identity;
        
                        
        return $this->render('FenchyRegularUserBundle:Settings:account.html.twig', $data);
    }
    
    
    public function changePasswordAction()
    {
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	if (!is_object($user) || !$user instanceof UserInterface) {
    		throw new AccessDeniedException('This user does not have access to this section.');
    	}
//     	$form = $this->createForm(new UserEmailType(), $user);
//     	$form1 = $this->container->get('fos_user.change_password.form');
    	$formHandler = $this->container->get('fos_user.change_password.form.handler');
    	
    	$process = $formHandler->process($user);
    	$data_saved = $this->get('translator')->trans('regularuser.change_pass_success');
        
    	if ($process) {
    		$this->get('session')->setFlash('positive', $data_saved);
    	}
        else
        {
            $data_error = $this->get('translator')->trans('regularuser.wrong_old_password_or_new_confirm_not_matched');
            
            $this->get('session')->setFlash('negative', $data_error);
        }
    	
    	return $this->redirect($this->generateUrl('fenchy_regular_user_settings_account'));
    }
    /**
     * Delete account action in settings section
     * @author Mateusz Krowiak <mkrowiak@pgs-soft.com>
     * @return Response
     */
    public function deleteAccountAction() {
        
        $user = $this->get('security.context')->getToken()->getUser();
        $form = $this->createDeleteForm($user->getId());
        $request = $this->getRequest();
        
        if($request->isMethod('POST')) {
            
            $form->bindRequest($request);

            if ($form->isValid()) {
                
                $this->get('fenchy.messenger')->removeUserMessages();
                $em = $this->getDoctrine()->getManager();
                $messages = $em->getRepository('CunningsoftChatBundle:Message')->findBySenderOrReceiver($user);

        foreach($messages as $message) {

            if($message->getAuthor() == NULL) {
                if($message->getReceiver()->getId() === $user->getId()) {
                    $em->remove($message); // remove if sender and receiver has been deleted
                }
            } elseif($message->getReceiver() == NULL) {
                if($message->getAuthor()->getId() == $user->getId()) {
                    $em->remove($message); // remove if sender and receiver has been deleted
                }
            } else {
                if($message->getAuthor()->getId() == $user->getId()) {
                     $em->remove($message);  
                }
                
                if($message->getReceiver()->getId() == $user->getId()) {
                     $em->remove($message);
                }
            }
        }       
        // We have to flush because after chat remove we can remove user        
            $em->flush();
        
                $em->remove($user);
                $em->flush();

                $this->get('session')->setFlash(
                                'positive',
                                $this->get('translator')
                                    ->trans('user.account_deleted')
                        );

                //$this->get('session')->set('gallery', array(1 => '', 2 => '', 3 => ''));
                
                return $this->redirect($this->generateUrl('fenchy_regular_user_settings_basic'));
            }

            $this->get('session')->setFlash(
                            'negative',
                            $this->get('translator')
                                ->trans('user.account_not_deleted')
                        );
            
        }
        
        return $this->render("FenchyRegularUserBundle:Settings:deleteAccount.html.twig", array(
            'form' 	=> $form->createView(),
            'entity'=> $user,            
        ));
        
    }
    
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }    
    

    public function notificationsAction() {

        $user = $this->get('security.context')->getToken()->getUser();

        if (!($user instanceof User)) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

    

        $form = $this->createForm(new UserNotificationsType(), $user);

        $request = $this->getRequest();

        $data = array();

        if ('POST' == $request->getMethod()) {

            $form->bindRequest($request);

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getEntityManager();

                $regular_user = $form->getData();

                $data_saved = $this->get('translator')
                        ->trans('settings.flash.data_saved');
                $this->get('session')->setFlash('positive', $data_saved);

                $em->persist($regular_user);
                $em->flush();
                return $this->redirect($this->generateUrl('fenchy_regular_user_settings_account'));
            }
        } else {

        }
      
        $data['form'] = $form->createView();
        return $this->redirect($this->generateUrl('fenchy_regular_user_settings_account'));
        //return $this->render('FenchyRegularUserBundle:Settings:notifications.html.twig', $data);
    }
    
    
    /**
     * Social networks - connecting/disconnecting, settings
     * @return Response
     */
    public function socialAction() {
        
        $user = $this->get('security.context')->getToken()->getUser();

        if (!($user instanceof User)) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        
        
        
        return $this->render("FenchyRegularUserBundle:Settings:social.html.twig");
        
    }
    
    /**
     * 
     * @author Mateusz Krowiak <mkrowiak@pgs-soft.com>
     * @return type
     */
    public function fillLocationPopupAction() {
        
        $user = $this->get('security.context')->getToken()->getUser();

        if (!($user instanceof User)) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        
        $form = $this->createForm(new UserLocationType(), $user);

        $request = $this->getRequest();

        if ('POST' == $request->getMethod()) {

            $form->bindRequest($request);

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getEntityManager();

                $location = $form->getData();

                $em->persist($location);
                $em->flush();
                
                $response = new Response(json_encode(array('saved' => 1, 'content' => $this->render('FenchyRegularUserBundle:Settings:fillLocationPopupSuccess.html.twig')->getContent())));
                $response->headers->set('Content-Type', 'application/json');
                
                return $response;
            } 
            else {
            }
        }
        
        return $this->render('FenchyRegularUserBundle:Settings:fillLocationPopup.html.twig', array(
            'form' => $form->createView()
        ));
        
    }
    
    public function paymentAction($noticeType)
    {
    	$user = $this->get('security.context')->getToken()->getUser();
    	
    	if (!($user instanceof User)) {
    		return $this->redirect($this->generateUrl('fos_user_security_login'));
    	}
    	 
    	$repository = $this->getDoctrine()
    			->getRepository('UserBundle:Payment');
    	
    	$query = $repository->createQueryBuilder('p')
			    	->where('p.user = :user')
			    	->setParameter('user', $user->getId())
			    	->getQuery();
			    	
    	$payment = $query->getOneOrNullResult();
    	$isPayment = false;
    	$cvv_code = null;
    	if($payment) 
    	{
    		$isPayment = true;
                if($payment->getCvvCode() != '')                
                    $cvv_code = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5('!p@a#b$o'), base64_decode($payment->getCvvCode()), MCRYPT_MODE_CBC, md5(md5('!p@a#b$o'))), "\0");
    	}    	
    	
    	return $this->render('FenchyRegularUserBundle:Settings:payment.html.twig',array(
    			'user' => $user,
    			'payment' => $payment,
    			'noticeType'=> $noticeType,
    			'groupId'=> $this->getRequest()->get('groupId'),
                        'noticeId'=> $this->getRequest()->get('noticeId'),
    			'cvv_code' => $cvv_code
    	));
    }
    
    public function savePaymentSettingAction()
    {
    	$user = $this->get('security.context')->getToken()->getUser();
    	 
    	if (!($user instanceof User)) {
    		return $this->redirect($this->generateUrl('fos_user_security_login'));
    	}
    	$request = $this->getRequest();
    	$account_holder = $request->get('account_holder')?$request->get('account_holder'):null;
    	$account_no = $request->get('account_no')?$request->get('account_no'):null;
    	$bank_code = $request->get('bank_code')?$request->get('bank_code'):null;
    	$paypal_email = $request->get('paypal_email')?$request->get('paypal_email'):null;
    	$card_type = $request->get('card_type')?$request->get('card_type'):null;
    	$card_no = $request->get('card_no')?$request->get('card_no'):null;
    	$cvv_code = $request->get('cvv_code')?$request->get('cvv_code'):null;
    	$card_holder = $request->get('card_holder')?$request->get('card_holder'):null;
    	$end_month = $request->get('end_month')? $request->get('end_month'):null;
    	$end_year = $request->get('end_year')?$request->get('end_year'):null;
    	$type= $request->get('type')?$request->get('type'):null;
    	$action= $request->get('action');
    	
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$repository = $this->getDoctrine()
    				->getRepository('UserBundle:Payment');
    	
    	$query = $repository->createQueryBuilder('p')
			    	->where('p.user = :user')
			    	->setParameter('user', $user->getId())
			    	->getQuery();
			    	
    	$payment = $query->getOneOrNullResult();
    	if(!$payment)
    	{
    		$payment = new \Fenchy\UserBundle\Entity\Payment();
    	} 

    	$key = '!p@a#b$o';
    	if($cvv_code)
    		$cvv_code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $cvv_code, MCRYPT_MODE_CBC, md5(md5($key))));
   		
    	$payment->setUser($user);
    	$payment->setAccountHolder($account_holder);
    	$payment->setAccountNo($account_no);
    	$payment->setBankCode($bank_code);
    	$payment->setPaypalEmail($paypal_email);
    	$payment->setCardType($card_type);
    	$payment->setCardNo($card_no);
    	$payment->setCvvCode($cvv_code);
    	$payment->setCardHolder($card_holder);
    	$payment->setEndMonth($end_month);
    	$payment->setEndYear($end_year);
    	$payment->setType($type);
    	$payment->setAgreed(true);
    	$em->persist($payment);
    	$em->flush();
    	
	    	$typeindex = 1;
	    	if($payment->getType() == 'debit')
	    		$typeindex = 1;
	    	if($payment->getType() == 'paypal')
	    		$typeindex = 2;
	    	if($payment->getType() == 'credit')
	    		$typeindex = 3;
	    		
	    	$str ='<div class="paymentdetails">';
	    	$str.= '<a class="payment-button" href="javascript:void(0);" onclick="showPaymentForm('.$typeindex.')">';
	    	$str .=	$this->get('translator')->trans('payment.change').'</a>';
	    	$str .= '<a class="payment-button" href="javascript:void(0);" onclick="deletePaymentSetting('.$payment->getId().')">';
	    	$str .=	$this->get('translator')->trans('payment.delete').'</a>';
	    	$str .= '<div class="changeSettingButton">';
			$str .= '<a class="payment-button" href="javascript:void(0);" onclick="showPaymentForm(\'1\')">';
			$str .= $this->get('translator')->trans('payment.direct_debit').'</a>';
			$str .= '<a class="payment-button" href="javascript:void(0);" onclick="showPaymentForm(\'2\')">';
			$str .= $this->get('translator')->trans('payment.paypal').'</a>';
			$str .= '<a class="payment-button" href="javascript:void(0);" onclick="showPaymentForm(\'3\')">';
			$str .= $this->get('translator')->trans('payment.credit_card').'</a>';
	    	$str .= '</div></div>';
	
	    	echo $str;
	    	exit;

    }
    public function deletePaymentSettingAction()
    {
    	$user = $this->get('security.context')->getToken()->getUser();
    	
    	if (!($user instanceof User)) {
    		return $this->redirect($this->generateUrl('fos_user_security_login'));
    	}
    	
    	$form = $this->createForm(new PaymentDeleteType());
    	
    	$request = $this->getRequest();
    	$id = $request->get('id');
    	if ('POST' == $request->getMethod()) 
    	{
    		$form->bindRequest($request);
    		$regularUser = $this->get('security.context')->getToken()->getUser()->getRegularUser();
    		$em = $this->getDoctrine()->getManager();
    		
    		$payment = $em->getRepository('UserBundle:Payment')->find($id);
    		
    		if (null === $payment) {
    			throw new NotFoundHttpException('Not installed payment!');
    		}
    		
    		if ($payment->getUser()->getId() !== $this->get('security.context')->getToken()->getUser()->getId()) {
    			throw new \Exception('You have not permission to delete this installation.');
    		}   		
    		
    		
    		$em->remove($payment);    		
    		$em->flush();
    	}    	
    	$str = '<p style="margin-bottom: 20px;">';
    	$str .= $this->get('translator')->trans('payment.no_default');
		$str .= $this->get('translator')->trans('payment.choose_default');
		$str .='</p><div class="paymentdetails">';
		$str .='<a class="payment-button" href="javascript:void(0);" onclick="showPaymentForm(\'1\')">';
		$str .= $this->get('translator')->trans('payment.direct_debit');
		$str .= '</a>';
		$str .= '<a class="payment-button" href="javascript:void(0);" onclick="showPaymentForm(\'2\')">';
		$str .= $this->get('translator')->trans('payment.paypal');
		$str .= '</a>';
		$str .= '<a class="payment-button" href="javascript:void(0);" onclick="showPaymentForm(\'3\')">';
		$str .= $this->get('translator')->trans('payment.credit_card');
		$str .= '</a></div>';
					
		echo $str;		
    	exit;
    }
}
