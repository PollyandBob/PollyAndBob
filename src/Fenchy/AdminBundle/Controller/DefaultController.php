<?php

namespace Fenchy\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fenchy\AdminBundle\Entity\UsersFilter,
    Fenchy\AdminBundle\Form\UsersFilterType,
    Fenchy\AdminBundle\Entity\DictionaryFilter,
    Fenchy\AdminBundle\Form\DictionaryFilterType,
    Fenchy\AdminBundle\Entity\NoticesFilter,
    Fenchy\AdminBundle\Form\NoticesFilterType,
    Fenchy\AdminBundle\Entity\ReviewsFilter,
    Fenchy\AdminBundle\Form\ReviewsFilterType;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FenchyAdminBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function usersAction() {
        
        $filter = new UsersFilter();
        
        $form = $this->createForm(new UsersFilterType(), $filter);
        
        $request = $this->getRequest();
        
        if($request->isMethod('POST')) {
            
            $form->bindRequest($request);

            if ($form->isValid()) {
                
                $users = $this->getDoctrine()
                ->getEntityManager()
                ->getRepository('UserBundle:User')
                ->findAllWithStickers($filter);
                
                return $this->render(
                        'FenchyAdminBundle:Default:users.html.twig', 
                        array(
                            'users' => $users,
                            'filter' => $form->createView()
                            )
                        );
            }
        }
        
        $users = $this->getDoctrine()
                ->getEntityManager()
                ->getRepository('UserBundle:User')
                ->findAllWithStickers();
        
        return $this->render(
                'FenchyAdminBundle:Default:users.html.twig', 
                array(
                    'users' => $users,
                    'filter' => $form->createView()
                    )
                );
    }
    
    public function userAction($id) {
        
        $em = $this->getDoctrine()
                    ->getEntityManager();
        
        $user = $em
                ->getRepository('FenchyRegularUserBundle:UserRegular')
                ->find($id);
        
        if(!$user) {
            $this->createNotFoundException();
        }
        
        $form = $this->createForm(new \Fenchy\RegularUserBundle\Form\UserRegularAdminType(), $user);
        
        $request = $this->getRequest();
        
        if($request->isMethod('POST')) {
            
            $form->bindRequest($request);

            if ($form->isValid()) {

                $em->persist($user);
                $em->flush();
                
                $this->get('session')->setFlash(
                        'positive', 
                        'User data updated.'
                        );
                
                return $this->render('FenchyAdminBundle:Default:edit.html.twig',
                        array(
                            'entity' => $user,
                            'form' => $form->createView(),
                            'type' => 'user'
                            )
                        );
            }

        }
        
        return $this->render(
                'FenchyAdminBundle:Default:edit.html.twig',
                array(
                    'entity' => $user,
                    'form' => $form->createView(),
                    'type' => 'user'
                    )
                );
    }
    
    public function searchAction() {
        
        $em = $this->getDoctrine()->getManager();
        
        $filter = new DictionaryFilter();
        
        $form = $this->createForm(new DictionaryFilterType(), $filter);
        
        $request = $this->getRequest();
        
        if($request->isMethod('POST')) {
            
            $form->bindRequest($request);

            if ($form->isValid()) {
                
                $dictionary = $em
                    ->getRepository('FenchyNoticeBundle:Dictionary')
                    ->filterBy($filter);
                
                return $this->render(
                    'FenchyAdminBundle:Default:search.html.twig',
                    array(
                        'dictionary'   => $dictionary,
                        'filter'       => $form->createView()
                    )
                );
            }
        }

        $dictionary = $em->getRepository('FenchyNoticeBundle:Dictionary')
                ->findAll();
            

        return $this->render(
                'FenchyAdminBundle:Default:search.html.twig',
                array(
                    'dictionary'   => $dictionary,
                    'filter'       => $form->createView()
                )
            );
    }
    
    public function dictionarySwitchAction() {
        
        $id = $this->getRequest()->get('id');
        $type = $this->getRequest()->get('type');
        
        if(!$id) {
            $this->createNotFoundException();
        }
        
        if(!$type) {
            $this->createNotFoundException();
        }
        
        $em = $this->getDoctrine()->getManager();
        $dictionary = $em->getRepository('FenchyNoticeBundle:Dictionary')
                ->find($id);
        
        if(!$dictionary) {
            $this->createNotFoundException();
        }
        
        if(!method_exists($dictionary, 'set'.$type)) {
            $this->createNotFoundException();
        }
        
        $dictionary->{'set'.$type}(!$dictionary->{'get'.$type}());
        
        $em->persist($dictionary);
        $em->flush();
        
        echo json_encode(array(
                    'status' => $dictionary->{'get'.$type}() ? 1 : 0,
                    'id'        => $dictionary->getId()
                ));
        exit;
    }
    
    public function noticesAction () {
        
        $em = $this->getDoctrine()->getManager();
        
        $filter = new NoticesFilter();
        
        $form = $this->createForm(new NoticesFilterType(), $filter);
        
        $request = $this->getRequest();
        
        if($request->isMethod('POST')) {
            
            $form->bindRequest($request);

            if ($form->isValid()) {

                $notices = $em
                    ->getRepository('FenchyNoticeBundle:Notice')
                    ->getFullDetailedList($filter);
                
                return $this->render(
                    'FenchyAdminBundle:Default:notices.html.twig',
                    array(
                        'notices'   => $notices,
                        'filter'       => $form->createView()
                    )
                );
            }
        }

        $notices = $em->getRepository('FenchyNoticeBundle:Notice')
                ->getFullDetailedList();
            

        return $this->render(
                'FenchyAdminBundle:Default:notices.html.twig',
                array(
                    'notices'   => $notices,
                    'filter'       => $form->createView()
                )
            );
    }
    
    public function addToDashboardAction($id) {
        
        $em = $this->getDoctrine()
                    ->getEntityManager();
        
        $notice = $em
                ->getRepository('FenchyNoticeBundle:Notice')
                ->find($id);
        
        if(!$notice) {
            $this->createNotFoundException();
        }
        
        $notice->setOnDashboard(true);
        
        $em->persist($notice);
        $em->flush();        
        
     
        $this->get('session')->setFlash(
                'positive', 
                'Notice has been added to dashboard.'
                );
        
        return $this->redirect($this->generateUrl('fenchy_admin_notices'));

    }
    
    public function removeFromDashboardAction($id) {
        
        $em = $this->getDoctrine()
                    ->getEntityManager();
        
        $notice = $em
                ->getRepository('FenchyNoticeBundle:Notice')
                ->find($id);
        
        if(!$notice) {
            $this->createNotFoundException();
        }
        
        $notice->setOnDashboard(false);
        
        $em->persist($notice);
        $em->flush();        
        
     
        $this->get('session')->setFlash(
                'positive', 
                'Notice has been removed from dashboard.'
                );
        
        return $this->redirect($this->generateUrl('fenchy_admin_notices'));

    }    
    
    public function noticeAction($id) {
        
        $em = $this->getDoctrine()
                    ->getEntityManager();
        
        $notice = $em
                ->getRepository('FenchyNoticeBundle:Notice')
                ->find($id);
        
        if(!$notice) {
            $this->createNotFoundException();
        }
        
        $form = $this->createForm(new \Fenchy\NoticeBundle\Form\NoticeAdminType, $notice);
        
        $request = $this->getRequest();
        
        if($request->isMethod('POST')) {
            
            $form->bindRequest($request);

            if ($form->isValid()) {
                
                $removeStickers = $request->get('removeStickers');
                if($removeStickers) {
                    foreach($notice->getStickers() as $sticker) {
                        $sticker->discard();
                    }
                }
                $em->persist($notice);
                $em->flush();
                
                $this->get('session')->setFlash(
                        'positive', 
                        'Notice data updated.'
                        );
                
                return $this->render('FenchyAdminBundle:Default:edit.html.twig',
                        array(
                            'entity' => $notice,
                            'form' => $form->createView(),
                            'type' => 'notice'
                            )
                        );
            }

        }
        
        return $this->render(
                'FenchyAdminBundle:Default:edit.html.twig',
                array(
                    'entity' => $notice,
                    'form' => $form->createView(),
                    'type' => 'notice'
                    )
                );
    }
    
    public function reviewsAction() {
        
        $em = $this->getDoctrine()->getManager();
        
        $filter = new ReviewsFilter();
        
        $form = $this->createForm(new ReviewsFilterType(), $filter);
        
        $request = $this->getRequest();
        
        if($request->isMethod('POST')) {
            
            $form->bindRequest($request);

            if ($form->isValid()) {
                
                $reviews = $em
                    ->getRepository('FenchyNoticeBundle:Review')
                    ->getFullDetailedList($filter);
                
                return $this->render(
                    'FenchyAdminBundle:Default:reviews.html.twig',
                    array(
                        'reviews'   => $reviews,
                        'filter'    => $form->createView()
                    )
                );
            }
        }

        $reviews = $em->getRepository('FenchyNoticeBundle:Review')
                ->getFullDetailedList();
            

        return $this->render(
                'FenchyAdminBundle:Default:reviews.html.twig',
                array(
                    'reviews'   => $reviews,
                    'filter'    => $form->createView()
                )
            );
    }
    
    public function reviewAction($id) {
        
        $em = $this->getDoctrine()
                    ->getEntityManager();
        
        $review = $em
                ->getRepository('FenchyNoticeBundle:Review')
                ->find($id);
        
        if(!$review) {
            $this->createNotFoundException();
        }
        
        $form = $this->createForm(new \Fenchy\NoticeBundle\Form\ReviewAdminType, $review);
        
        $request = $this->getRequest();
        
        if($request->isMethod('POST')) {
            
            $form->bindRequest($request);

            if ($form->isValid()) {
                
                $removeStickers = $request->get('removeStickers');
                if($removeStickers) {
                    foreach($review->getStickers() as $sticker) {
                        $sticker->discard();
                    }
                }
                $em->persist($review);
                $em->flush();
                
                $this->get('session')->setFlash(
                        'positive', 
                        'Review data updated.'
                        );
                
                return $this->render('FenchyAdminBundle:Default:edit.html.twig',
                        array(
                            'entity' => $review,
                            'form' => $form->createView(),
                            'type' => 'review'
                            )
                        );
            }

        }
        
        return $this->render(
                'FenchyAdminBundle:Default:edit.html.twig',
                array(
                    'entity' => $review,
                    'form' => $form->createView(),
                    'type' => 'review'
                    )
                );
    }
    
    public function noticeDeleteAction ($id) {
        
        $notice = $this->getDoctrine()->getRepository('FenchyNoticeBundle:Notice')->getWithUser($id);
        if(NULL === $notice) {
            $this->get('session')->setFlash(
                        'negative', 
                        'Notice not found'
                        );
        }
        else {
            $user = $notice->getUser();
            $user->removeNotice($notice);
            $this->get('fenchy.reputation_counter')->update($user, \Fenchy\UserBundle\Services\ReputationCounter::TYPE_NOTICE);
            $this->getDoctrine()->getManager()->remove($notice);
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
            
            $this->get('session')->setFlash(
                            'positive', 
                            'Notice deleted.'
                            );
        }
        
        return $this->redirect($this->generateUrl('fenchy_admin_notices'));
    }
}
