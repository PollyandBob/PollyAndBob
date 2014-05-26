<?php

namespace Fenchy\UtilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

use Fenchy\UtilBundle\Entity\Sticker,
    Fenchy\UtilBundle\Form\StickerType;

class StickerController extends Controller
{
    
    public function userStickerAction($id)
    {
        $sticker = new Sticker();

        $em = $this->getDoctrine()
                   ->getEntityManager();
        
        $user = $em
                ->getRepository('UserBundle:User')
                ->find($id);
        
        if(!($user instanceof \Fenchy\UserBundle\User)) {
            
            $this->createNotFoundException();
        }
        
        if($user->getId() === $this->get('security.context')->getToken()->getUser()->getId()) {
            throw new \Exception('You cannot flag yourself!');
        }
        $form = $this->createForm(new StickerType(), $sticker);
        
        $sticker
            ->setReportedBy($this->get('security.context')->getToken()->getUser())
            ->setUser($user);
        
        $request = $this->getRequest();
        
        if($request->isMethod('POST')) {
            
            $form->bindRequest($request);

            if ($form->isValid()) {

                $em->persist($sticker);
                $em->flush();

                return $this->render('FenchyUtilBundle:Sticker:added.html.twig', array(
                    'sticker' => $sticker
                ));
            }

        }
        
        return $this->render('FenchyUtilBundle:Sticker:new.html.twig', array(
            'sticker' => $sticker,
            'form'   => $form->createView()
        ));
    }
    
    public function noticeStickerAction($id)
    {
        $sticker = new Sticker();

        $em = $this->getDoctrine()
                   ->getEntityManager();
        
        $notice = $em
                ->getRepository('FenchyNoticeBundle:Notice')
                ->find($id);
        
        if(!($notice instanceof \Fenchy\NoticeBundle\Entity\Notice)) {
            $this->createNotFoundException();
        }
        
        if($notice->getUser()->getId() === $this->get('security.context')->getToken()->getUser()->getId()) {
            throw new \Exception('You cannot flag own listing!');
        }
        
        $form = $this->createForm(new StickerType(), $sticker);
        
        $sticker
            ->setReportedBy($this->get('security.context')->getToken()->getUser())
            ->setNotice($notice);
        
        $request = $this->getRequest();
        
        if($request->isMethod('POST')) {
            
            $form->bindRequest($request);

            if ($form->isValid()) {

                $em->persist($sticker);
                $em->flush();

                $response = new Response();
                $output[] = array('success' => true, 'sticker' => $sticker);
                $response->headers->set('Content-Type', 'application/json');
                $response->setContent(json_encode($output));
                return $response;
//                 return  $this->container->get('templating')->renderResponse('FenchyUtilBundle:Sticker:new.html.twig', array(
//                     'sticker' => $sticker,
//                 	'form'   => $form->createView(),
//                 	'flagged' => true
//                 ));
            }

        }
        
        return $this->render('FenchyUtilBundle:Sticker:new.html.twig', array(
            'sticker' => $sticker,
            'form'   => $form->createView(),
        	'flagged' => false
        ));
    }
    
    public function reviewStickerAction($id)
    {
        $sticker = new Sticker();

        $em = $this->getDoctrine()
                   ->getEntityManager();
        
        $review = $em
                ->getRepository('FenchyNoticeBundle:Review')
                ->find($id);
        
        if(!($review instanceof \Fenchy\NoticeBundle\Entity\Review)) {
            $this->createNotFoundException();
        }
        
        if($review->getAuthor()->getId() === $this->get('security.context')->getToken()->getUser()->getId()) {
            throw new \Exception('You cannot flag own review!');
        }
        
        $form = $this->createForm(new StickerType(), $sticker);
        
        $sticker
            ->setReportedBy($this->get('security.context')->getToken()->getUser())
            ->setReview($review);
        
        $request = $this->getRequest();
        
        if($request->isMethod('POST')) {
            
            $form->bindRequest($request);

            if ($form->isValid()) {

                $em->persist($sticker);
                $em->flush();

                return $this->render('FenchyUtilBundle:Sticker:added.html.twig', array(
                    'sticker' => $sticker
                ));
            }

        }
        
        return $this->render('FenchyUtilBundle:Sticker:new.html.twig', array(
            'sticker' => $sticker,
            'form'   => $form->createView()
        ));
    }
    
    public function groupStickerAction($id)
    {
        $sticker = new Sticker();

        $em = $this->getDoctrine()
                   ->getEntityManager();
        
        $usergroup = $em
                ->getRepository('FenchyRegularUserBundle:UserGroup')
                ->find($id);
        
        if(!($usergroup instanceof src\Fenchy\RegularUserBundle\Entity\UserGroup)) {
            
            $this->createNotFoundException();
        }
        
        if($usergroup->getUser()->getId() === $this->get('security.context')->getToken()->getUser()->getId()) {
            throw new \Exception('You cannot flag yourself!');
        }
        $form = $this->createForm(new StickerType(), $sticker);
        
        $sticker
            ->setReportedBy($this->get('security.context')->getToken()->getUser())
            ->setGroup($usergroup);
        
        $request = $this->getRequest();
        
        if($request->isMethod('POST')) {
            
            $form->bindRequest($request);

            if ($form->isValid()) {

                $em->persist($sticker);
                $em->flush();

                return $this->render('FenchyUtilBundle:Sticker:added.html.twig', array(
                    'sticker' => $sticker
                ));
            }

        }
        
        return $this->render('FenchyUtilBundle:Sticker:new.html.twig', array(
            'sticker' => $sticker,
            'form'   => $form->createView()
        ));
    }
    
}
