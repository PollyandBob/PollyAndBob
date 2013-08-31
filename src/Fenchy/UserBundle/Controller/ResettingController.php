<?php
namespace Fenchy\UserBundle\Controller;
use FOS\UserBundle\Controller\ResettingController as Base;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ResettingController extends Base
{
    public function requestAction()
    {
        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            return new RedirectResponse($this->container->get('router')->generate('fos_user_profile_show'));
        }
        
        return parent::requestAction();
    }
    /**
     * @Template
     */
    public function successAction()
    {
        return array();
    }
    
    protected function getRedirectionUrl(UserInterface $user)
    {
        return $this->container->get('router')->generate('fos_user_resseting_success');
    }
    
    /**
     * Reset user password
     */
    public function resetAction($token)
    {
        $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($token);

        if (null === $user) {
            $tokenNotExsist = $this->container->get('translator')->trans('user.user_with_confirmation_token_not_exist', array('%s%' => $token));
            $this->container->get('session')->setFlash('negative',  $tokenNotExsist);
                        
            return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_signin'));
        }

        if (!$user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_request'));
        }

        $form = $this->container->get('fos_user.resetting.form');
        $formHandler = $this->container->get('fos_user.resetting.form.handler');
        $process = $formHandler->process($user);

        if ($process) {
            $this->setFlash('fos_user_success', 'resetting.flash.success');
            $response = new RedirectResponse($this->getRedirectionUrl($user));
            $this->authenticateUser($user, $response);

            return $response;
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:reset.html.'.$this->getEngine(), array(
            'token' => $token,
            'form' => $form->createView(),
        ));
    }    
}