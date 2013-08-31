<?php

namespace Fenchy\RegularUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fenchy\RegularUserBundle\Form\UserRegularType;
use Fenchy\UserBundle\Form\UserLocationType;
use Fenchy\RegularUserBundle\Form\PopupLocationType;
use Fenchy\UserBundle\Form\ProfileFormType as UserType;
use Fenchy\UserBundle\Form\UserNotificationsType;
use Fenchy\RegularUserBundle\Entity\UserRegular;
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

        $form = $this->createForm(new UserEmailType(), $user);

        $request = $this->getRequest();

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

        return $this->render('FenchyRegularUserBundle:Settings:account.html.twig', array(
                    'form' => $form->createView()
        ));
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
                $em->remove($user);
                $em->flush();

                $this->get('session')->setFlash(
                                'positive',
                                $this->get('translator')
                                    ->trans('user.account_deleted')
                        );

                $this->get('session')->set('gallery', array(1 => '', 2 => '', 3 => ''));
                
                return $this->redirect($this->generateUrl('fenchy_regular_user_settings_basic'));
            }

            $this->get('session')->setFlash(
                            'negative',
                            $this->get('translator')
                                ->trans('user.account_not_deleted')
                        );
            
        }
        
        return $this->render("FenchyRegularUserBundle:Settings:deleteAccount.html.twig", array(
            'form' => $form->createView(),
            'entity'      => $user,            
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
                return $this->redirect($this->generateUrl('fenchy_regular_user_settings_notifications'));
            }
        } else {

        }
      
        $data['form'] = $form->createView();
       
        return $this->render('FenchyRegularUserBundle:Settings:notifications.html.twig', $data);
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
            } else {

            }
        }        
        
        
        return $this->render('FenchyRegularUserBundle:Settings:fillLocationPopup.html.twig', array(
            'form' => $form->createView()
        ));
        
    }
    

}
