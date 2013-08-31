<?php

namespace Fenchy\RegularUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Fenchy\RegularUserBundle\Form\UserRegularType;
use Fenchy\RegularUserBundle\Form\LeaveReviewType;
use Fenchy\RegularUserBundle\Form\LeaveReviewProfileType;
use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\UserBundle\Entity\User;
use Fenchy\RegularUserBundle\Form\AboutMeType,
    Fenchy\NoticeBundle\Form\InterestsAndActivitiesType,
    Fenchy\NoticeBundle\Entity\Notice,
    Fenchy\NoticeBundle\Entity\Review,
    Fenchy\GalleryBundle\Entity\Gallery,
    Fenchy\GalleryBundle\Entity\Image;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Fenchy\UserBundle\Entity\NotificationGroupInterval;
use Fenchy\UserBundle\Entity\NotificationQueue;
use Fenchy\UserBundle\Form\UserLocationType;
use Fenchy\RegularUserBundle\Form\UserAboutSettingsType;
use Doctrine\Tests\Common\Annotations\True;
use Doctrine\Tests\Common\Annotations\False;

class MyprofileController extends Controller
{
    /*		Version 2.1 from 12th Aug, 2013			*/
	public function userProfileMainAction( $userId )
	{		
		return $this->render('FenchyRegularUserBundle:Myprofile:userProfileMain.html.twig');		
	}
		
	public function aboutMyChoiceAction()
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
				
		if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
			return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
		$displayUser = $userLoggedIn;		
		
		$user = $this->get('security.context')->getToken()->getUser();
	
		if (!($user instanceof User)) 
		{
			return $this->redirect($this->generateUrl('fos_user_security_login'));
		}
	
		$user_regular = $user->getRegularUser();
	
		$form = $this->createForm(new UserAboutSettingsType(), $user_regular);
	
		$request = $this->getRequest();
	
		if ('POST' == $request->getMethod()) 
		{	
			$form->bindRequest($request);
	
			if ($form->isValid()) 
			{	
				$em = $this->getDoctrine()->getEntityManager();
	
				$regular_user = $form->getData();
	
				$data_saved = $this->get('translator')->trans('settings.flash.data_saved');
				$this->get('session')->setFlash('positive', $data_saved);
	
				$this->get('fenchy.reputation_counter')->update($regular_user->getUser());
	
				$em->persist($regular_user);
				$em->flush();
				return $this->redirect($this->generateUrl('fenchy_regular_user_user_myprofile_aboutmychoice'));
			}
			else 
			{	
				$form_errors = $this->get('translator')
				->trans('settings.flash.form_errors');
	
				$this->get('session')->setFlash('negative', $form_errors);
			}
		}
		return $this->render('FenchyRegularUserBundle:Myprofile:aboutMyChoice.html.twig', 
				array('form' => $form->createView(),
					  'displayUser' => $displayUser));
	
	}
	public function myAvatarAction($userId = NULL)
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
		
		if ( $userId != NULL ) 
		{
		
			$userOther = $em->getRepository('UserBundle:User')->getAllData( $userId );
		
			if ( ! $userOther instanceof \Fenchy\UserBundle\Entity\User )
				return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
			$displayUser = $userOther;
			$usersOwnProfile = 0;
		}
		else 
		{
			
			if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
				return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
			$displayUser = $userLoggedIn;
			$usersOwnProfile = 1;
		}

		return $this->render('FenchyRegularUserBundle:Myprofile:myAvatar.html.twig',
				array(
						'locale' => $this->getRequest()->getLocale(),
						'displayUser' => $displayUser,
				));
	}
	public function myNeighborsAction()
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
		
		if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
			return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
		$displayUser = $userLoggedIn;
		
		
		return $this->render('FenchyRegularUserBundle:Myprofile:myNeighbors.html.twig',
				array(
						'locale'            => $this->getRequest()->getLocale(),
						'displayUser'		=> $displayUser,
				));
	}
	public function myCommunityPointsAction()
	{
		$userLoggedIn = $this->get('security.context')->getToken()->getUser();
				
		if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
			return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
		$displayUser = $userLoggedIn;

		$em = $this->getDoctrine()->getManager();
		$reviewRepo = $em->getRepository('FenchyNoticeBundle:Review');
		$noticeRepo = $em->getRepository('FenchyNoticeBundle:Notice');
		$userRepo = $em->getRepository('UserBundle:User');
		
		//$displayUser = $userRepo->getAllData( $displayUserId );
		$contactsCount = count( $displayUser->getRegularUser()->getMyFriends() );
		$fbConnect = $displayUser->getFacebookId() == NULL ? FALSE : TRUE;
		$positiveRevCount = $reviewRepo->findCount( array(
				'aboutUser' => $displayUser->getId(),
				'type' => Review::TYPE_POSITIVE ) );
		$revsOOCount = $reviewRepo->findCount( array('author'=>$displayUser->getId()) );
		$listingCount = $noticeRepo->findCount( array('user'=>$displayUser->getId()) );
		
		$reputationBreakdown = array(
				'listingCount'  => $listingCount,
				'positiveRevCount' => $positiveRevCount ,
				'revsOOCount'   => $revsOOCount,
				'fbConnect'     => $fbConnect,
				'contactsCount' => $contactsCount,
		);
		
		$reputationPoints = $this->get('fenchy.reputation_counter')->getPointsList($displayUser);
		
		$profileUrl = $this->get('router')->generate('fenchy_regular_user_user_profilev2', array('userId' => $displayUser->getId()));		
		
		return $this->render('FenchyRegularUserBundle:Myprofile:myCommunityPoints.html.twig',
				array(
						'repBreakdown' => $reputationBreakdown,
						'reputationPoints' => $reputationPoints,
						'displayUser'	=> $displayUser
				));
	}
	public function myLocationAction()
	{
		$user = $this->get('security.context')->getToken()->getUser();

        if (!($user instanceof User)) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        
        $request = $this->getRequest();
        $param = explode("/", $request->getPathInfo());
        $islocation = "".$param[sizeof($param)-1];
      
        $form = $this->createForm(new UserLocationType(), $user);		
			
        $user_regular = $user->getRegularUser();
		$form1 = $this->createForm(new UserAboutSettingsType(), $user_regular);
		
		if ('POST' == $request->getMethod())
        {
            $form->bindRequest($request);
            
            if ($form->isValid()) 
            {
                $em = $this->getDoctrine()->getEntityManager();

                $location = $form->getData();

                $data_saved = $this->get('translator')
                        ->trans('settings.flash.data_saved');
                $this->get('session')->setFlash('positive', $data_saved);

                $em->persist($location);
                $em->flush();                
                return $this->redirect($this->generateUrl('fenchy_regular_user_user_myprofile_mylocation'));
            } 
            else 
            {
            	$form_errors = $this->get('translator')
            	->trans('settings.flash.form_errors');
            	
            	$this->get('session')->setFlash('negative', $form_errors);
            }            
        }

        return $this->render(
                    'FenchyRegularUserBundle:Myprofile:myLocation.html.twig', array(
                    	'form' => $form->createView(),
                    	'user' => $user,                    	
                    	)
        			);	
	}
}
