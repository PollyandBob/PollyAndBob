<?php

namespace Fenchy\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\RegularUserBundle\Form\UserRegularLocationType;
use Fenchy\UserBundle\Entity\Unsubscriber;


class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('UserBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function unsubscribeAction($hashed_email)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        if ($hashed_email) {
            $unsubscriber = $em->getRepository('UserBundle:Unsubscriber')
                    ->findOneBy(array('hashed_email' => $hashed_email));

            if (!$unsubscriber) {
                $unsubscriber = new Unsubscriber();
                $unsubscriber->setHashedEmail($hashed_email);
                $em->persist($unsubscriber);
                $em->flush();
                
                $unsubscibed = $this->get('translator')->trans('user.unsubscribe');
                $this->get('session')->setFlash('positive', $unsubscibed);                
            }            
        }       

        return $this->redirect($this->generateUrl('fenchy_frontend_homepage'));
    }
    
    
    /**
     * Finishe register process for user.
     * User has to pass address data.
     * @Template
     * @param type $token
     * @return type
     */
    public function confirmAction($token)
    {
        $request = $this->getRequest();
        $user    = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($token);

        if (null === $user || null == $token) {
        	$user_with_confirmation_token_not_exist = $this->get('translator')
			    ->trans('user.user_with_confirmation_token_not_exist',
			        array('%s%' => $token));

            return  $this->redirect($this->generateUrl('reg_token_expired'));
            
            throw new \Exception($user_with_confirmation_token_not_exist);
            
        }
        
        return $this->forward("FOSUserBundle:Registration:confirm", array('token' => $token));
    }
    
    public function tokenExpiredAction()
    {
        return $this->render('UserBundle:Default:tokenExpired.html.twig');
    }
    
   
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}