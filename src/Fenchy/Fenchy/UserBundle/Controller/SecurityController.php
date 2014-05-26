<?php
namespace Fenchy\UserBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as Base;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Base
{
    public function loginAction()
    {

        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_indexv2', array('time' => 'today')));
        }
        return parent::loginAction();
    }
    
    /**
     * Extended login form displaying on whole page
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginExtendedAction() {
        
        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_indexv2', array('time' => 'today')));
        }
        
        $request = $this->container->get('request');
        /* @var $request \Symfony\Component\HttpFoundation\Request */
        $session = $request->getSession();
        /* @var $session \Symfony\Component\HttpFoundation\Session */

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        if ($error) {
            // TODO: this is a potential security risk (see http://trac.symfony-project.org/ticket/9523)
            $error = $error->getMessage();
        }
        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContext::LAST_USERNAME);

        $csrfToken = $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate');

        $data = array(
            'last_username' => $lastUsername,
            'error'         => $error,
            'csrf_token' => $csrfToken,
        );   
        return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_indexv2', array('flag' => 'login' )));
        //return $this->container->get('templating')->renderResponse('FOSUserBundle:Security:login_extended.html.twig', $data);
        
    }
    
    public function loginFacebookAction()
    {
        /* @var $request \Symfony\Component\HttpFoundation\Request */
        $request       = $this->container->get('request');
        /* @var $session \Symfony\Component\HttpFoundation\Session */
        $session       = $request->getSession();
        $facebookId    = $request->get('facebookId');
        $facebookToken = $request->get('token');

        //save facebook data in session
        $session->set('facebook', array(
                                    'id' => $facebookId,
                                    'token' => $facebookToken
                                ));
        
        
        /* @var $facebookProvider \Fenchy\UserBundle\Security\User\Provider\FacebookProvider */
        $facebookProvider = $this->container->get('fenchy.facebook.user');
        
        /* @var $user \Fenchy\UserBundle\Entity\User */
        $user = $facebookProvider->loadOrSaveUserByFbId($facebookId);
        
        if (!($user instanceof UserInterface)) {
        	if ( is_array($user) && 
        	     isset($user['status']) && $user['status'] === 'exists_disconnected' ) {
        	     // redirect to FacebookController \ loginCheck action
                     // to inform user and prompt for new username / e-mail
                $url = $this->container->get('router')->generate('fenchy_facebook_login_check');
                $response = new Response();
                $response->headers->set('Content-Type', 'application/json');
                $response->setContent(json_encode(array('url' => $url)));
                 return $response;
            };
            if (is_string($user)) {
                return new Response($user, 401);
            };
			$could_not_sign_in_via_facebook = $this->container->get('translator')
			    ->trans('user.could_not_sign_in_via_facebook');
            return new Response($could_not_sign_in_via_facebook, 401);
        }
        
        // prepare response
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        if (!$user->isEnabled()) {
            // redirect not activated user to confirmation
            $url = $this->container->get('router')->generate('fos_user_registration_confirm', array(
                                                                                'token' => $user->getConfirmationToken()
                                                                            ));
        } else {
            // authenticate enabled user
            
            if ($this->authenticateUser($user, $response)) {
                $pref_locale = $user->getRegularUser()->getPrefLocale();
                $locale_cookie = new Cookie('locale', $pref_locale, time() + 3600 * 24 * 3, '/');
                $response->headers->setCookie($locale_cookie);
                // set redirect url
                $url = $this->container->get('router')->generate('fenchy_frontend_indexv2', array('time' => 'today'));
            } else {
            	$problem_with_logging_try_again = $this->get('translator')
			        ->trans('user.problem_with_logging_try_again');
                return new Response($problem_with_logging_try_again, 401);
            }
        }
        
        // add json encoded url to redirect
        $response->setContent(json_encode(array('url' => $url)));
        return $response;
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
    protected function authenticateUser(UserInterface $user, Response $response)
    {
        try {
            $user->setLastLogin(new \DateTime());
            $this->container->get('fos_user.user_manager')->updateUser($user);

            $this->container->get('fos_user.security.login_manager')->loginUser(
                $this->container->getParameter('fos_user.firewall_name'),
                $user,
                $response);
                
            return true;
        } catch (AccountStatusException $ex) {
            // We simply do not authenticate users which do not pass the user
            // checker (not enabled, expired, etc.).
        }
        return false;
    }
}