<?php

namespace Fenchy\AdminBundle\Controller;


use Fenchy\AdminBundle\FenchyAdminBundle;

use Fenchy\NoticeBundle\Form\TypeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fenchy\AdminBundle\Entity\UsersFilter,
    Fenchy\AdminBundle\Form\UsersFilterType,
    Fenchy\AdminBundle\Entity\DictionaryFilter,
    Fenchy\AdminBundle\Form\DictionaryFilterType,
    Fenchy\AdminBundle\Entity\NoticesFilter,
    Fenchy\AdminBundle\Form\NoticesFilterType,
    Fenchy\AdminBundle\Entity\ReviewsFilter,
    Fenchy\AdminBundle\Entity\CommentsFilter,
    Fenchy\AdminBundle\Entity\CategoriesFilter,
    Fenchy\AdminBundle\Form\CategoriesFilterType,
	Fenchy\NoticeBundle\Entity\Type,
    Fenchy\AdminBundle\Form\ReviewsFilterType,
    Fenchy\AdminBundle\Form\CommentsFilterType,
    Fenchy\AdminBundle\Form\IdentityVerificationFilter,
	Fenchy\AdminBundle\Form\IdentityVerificationFilterType,
	Fenchy\AdminBundle\Form\LocationVerificationFilter,
	Fenchy\AdminBundle\Form\LocationVerificationFilterType,
	Fenchy\NoticeBundle\Form\CategoryType,
	Fenchy\AdminBundle\Form\CategoryNewType;
use	Fenchy\UserBundle\Entity\IdentityVerification;
use	Fenchy\UserBundle\Entity\LocationVerification;
use Symfony\Component\HttpFoundation\Response;
use Fenchy\MessageBundle\Entity\Message;
use Fenchy\UserBundle\Entity\User;
use Fenchy\UserBundle\Entity\NotificationGroupInterval;
use Fenchy\UserBundle\Entity\NotificationQueue;


class DefaultController extends Controller
{

	
    public function indexAction($name)
    {
        return $this->render('FenchyAdminBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function categoriesAction() {
    
    	$filter = new CategoriesFilter();
        
        $form = $this->createForm(new CategoriesFilterType(), $filter);
        
        $request = $this->getRequest();
        
        if($request->isMethod('POST')) {
            
            $form->bindRequest($request);

            if ($form->isValid()) {
                
                $categories = $this->getDoctrine()
                ->getEntityManager()
                ->getRepository('FenchyNoticeBundle:Type')
                ->getFullDetailedList($filter);
                
                return $this->render(
                        'FenchyAdminBundle:Default:categories.html.twig',
	    			array(
	    					'categories'=> $categories,
	    					'filter' => $form->createView()
	    					
	    			)
                );
            }
        }
        
       $categories = $this->getDoctrine()
                ->getEntityManager()
                ->getRepository('FenchyNoticeBundle:Type')
                ->getFullDetailedList();
        
    	return $this->render(
    			'FenchyAdminBundle:Default:categories.html.twig',
    			array(
    					'categories'=> $categories,
    					'filter' => $form->createView()
    					
    			)
    	);
    }
    
    public function categoryAction($id)
    {
    	$em = $this->getDoctrine()
                    ->getEntityManager();
        
        $category = $em
                ->getRepository('FenchyNoticeBundle:Type')
                ->find($id);
        
        if(!$category) {
            $this->createNotFoundException();
        }
        
        $form = $this->createForm(new \Fenchy\AdminBundle\Form\CategoriesAdminType(), $category);
        
        $request = $this->getRequest();
        
        if($request->isMethod('POST')) {
            
            $form->bindRequest($request);

            if ($form->isValid()) {

                $em->persist($category);
                $em->flush();
                
                $this->get('session')->setFlash(
                        'positive', 
                        'User data updated.'
                        );
                
                return $this->render('FenchyAdminBundle:Default:edit.html.twig',
    			array(
    					'entity' => $category,
    					'form' => $form->createView(),
    					'type' => 'category'
    			)
              );
            }

        }
        
    	return $this->render(
    			'FenchyAdminBundle:Default:edit.html.twig',
    			array(
    					'entity' => $category,
    					'form' => $form->createView(),
    					'type' => 'category'
    			)
    	);
    	 
    }
    
    public function categoryDeleteAction ($id) {
    
    	$category = $this->getDoctrine()->getRepository('FenchyNoticeBundle:Type')->find($id);
    	if(NULL === $category) {
    		$this->get('session')->setFlash(
    				'negative',
    				'Category not found'
    		);
    	}
    	else {
    		$this->getDoctrine()->getManager()->remove($category);
    		$this->getDoctrine()->getManager()->flush();
    		$this->get('session')->setFlash(
    				'positive',
    				'Notice deleted.'
    		);
    	}
    
    	return $this->redirect($this->generateUrl('fenchy_admin_categories'));
    }
    
    public function categoryNewAction ()
    {
    	$em = $this->getDoctrine()
    	->getEntityManager();
    	
    	
    	$category = new CategoryNewType();
    	$form = $this->createForm($category);
    	
    	$request = $this->getRequest();
    	
    	if($request->isMethod('POST')) {
    		
    		$form->bindRequest($request);
    	
    		if ($form->isValid()) {
    	
    			$category = $form->getData();
    			$em->persist($category);
    			$em->flush();
    			$this->get('session')->setFlash(
    					'positive',
    					'User data updated.'
    			);
    	
    			return $this->redirect($this->generateUrl('fenchy_admin_categories'));
    		}
    	
    	}
    	
    	
    	return $this->render(
    			'FenchyAdminBundle:Default:add.html.twig',
    			array(
    					'entity' => $category,
    					'form' => $form->createView()
    			)
    	);
    	
    }
    
    public function categoryCreateAction()
    {
    	$em = $this->getDoctrine()->getEntityManager();
        
        $category = $em->getRepository('FenchyNoticeBundle:Type');
        
    	$request = $this->getRequest();
    	
    	$form = $this->createForm(new CategoryNewType(), $category);
    	
    	if($request->isMethod('POST')) {
	    	$form->bindRequest($request);
	    
	    	if ($form->isValid()) {
	    		$em = $this->getDoctrine()->getEntityManager();
	    		$em->persist($category);
	    		$em->flush();
	    
	    		return $this->redirect($this->generateUrl('fenchy_admin_categories'));
	    
	    	}
    	}	
    
    	return array(
    			'entity' => $category,
    			'form' => $form->createView()
    	);
    }
    
    public function usersAction() {
        
        $filter = new UsersFilter();
        
        $form = $this->createForm(new UsersFilterType(), $filter);
        
        $request = $this->getRequest();
        $limit_per_page = $this->container->getParameter('pagination_limit');
        $paginator  = $this->get('knp_paginator');
        $session = $this->getRequest()->getSession();
        
        
        if($request->isMethod('POST')) {
            
            $form->bindRequest($request);

            if ($form->isValid()) {
                
                $users = $this->getDoctrine()
                        ->getEntityManager()
                        ->getRepository('UserBundle:User')
                        ->findAllWithSticker($filter);
                
                $session->set('userfilter',$filter);
                $pagination = $paginator->paginate(
                        $users,
                        $this->get('request')->query->get('page', 1),
                        $limit_per_page/*limit per page*/
                    );
                return $this->render(
                        'FenchyAdminBundle:Default:users.html.twig', 
                        array(
                            'users' => $users,
                            'filter' => $form->createView(),
                            'pagination' => $pagination
                            )
                        );
            }
        }
        
        if($session->get('userfilter') == null)
        {
            $users = $this->getDoctrine()
                ->getEntityManager()
                ->getRepository('UserBundle:User')
                ->findAllWithSticker();
        }
        else {
            $users = $this->getDoctrine()
                ->getEntityManager()
                ->getRepository('UserBundle:User')
                ->findAllWithSticker($session->get('userfilter'));
        }
        
        $pagination = $paginator->paginate(
                        $users,
                        $this->get('request')->query->get('page', 1),
                        $limit_per_page/*limit per page*/
                    );
        return $this->render(
                'FenchyAdminBundle:Default:users.html.twig', 
                array(
                    'users' => $users,
                    'filter' => $form->createView(),
                    'pagination' => $pagination
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
        $limit_per_page = $this->container->getParameter('pagination_limit');
        $paginator  = $this->get('knp_paginator');
        $session = $this->getRequest()->getSession();
        
        if($request->isMethod('POST')) {
            
            $form->bindRequest($request);

            if ($form->isValid()) {

                $notices = $em
                    ->getRepository('FenchyNoticeBundle:Notice')
                    ->getFullDetailedListAdmin($filter);
                
                $session->set('noticefilter',$filter);
        
                $pagination = $paginator->paginate(
                        $notices,
                        $this->get('request')->query->get('page', 1),
                        $limit_per_page/*limit per page*/
                    );
                return $this->render(
                    'FenchyAdminBundle:Default:notices.html.twig',
                    array(
                        'notices'   => $notices,
                        'filter'       => $form->createView(),
                        'pagination' => $pagination
                    )
                );
            }
        }

        if($session->get('noticefilter') == null)
        {
            $notices = $em->getRepository('FenchyNoticeBundle:Notice')
                ->getFullDetailedListAdmin();
        }
        else
        {
            $notices = $em->getRepository('FenchyNoticeBundle:Notice')
                ->getFullDetailedListAdmin($session->get('noticefilter'));
        }
       
        $pagination = $paginator->paginate(
                        $notices,
                        $this->get('request')->query->get('page', 1)/*page number*/,
                        $limit_per_page/*limit per page*/
                    );
        return $this->render(
                'FenchyAdminBundle:Default:notices.html.twig',
                array(
                    'notices'   => $notices,
                    'filter'       => $form->createView(),
                    'pagination' => $pagination
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
        $limit_per_page = $this->container->getParameter('pagination_limit');
        $paginator  = $this->get('knp_paginator');
        
        $session = $this->getRequest()->getSession();
        
        if($request->isMethod('POST')) {
            
            $form->bindRequest($request);

            if ($form->isValid()) {
                
                $reviews = $em
                    ->getRepository('FenchyNoticeBundle:Review')
                    ->getFullDetailedList($filter);
                
                
                $session->set('dql',$filter);
                
                $pagination = $paginator->paginate(
                        $reviews,
                        $this->get('request')->query->get('page', 1),
                        $limit_per_page/*limit per page*/
                    );
                return $this->render(
                    'FenchyAdminBundle:Default:reviews.html.twig',
                    array(
                        'reviews'   => $reviews,
                        'filter'    => $form->createView(),
                        'pagination' => $pagination
                    )
                );
            }
        }

        if ($session->get('dql') == null) {
            $reviews = $em->getRepository('FenchyNoticeBundle:Review')
                ->getFullDetailedList();
        }
        else {
            $reviews = $em->getRepository('FenchyNoticeBundle:Review')
                ->getFullDetailedList($session->get('dql'));
        }
        
        $pagination = $paginator->paginate(
                        $reviews,
                        $this->get('request')->query->get('page', 1),
                        $limit_per_page/*limit per page*/
                    );
        return $this->render(
                'FenchyAdminBundle:Default:reviews.html.twig',
                array(
                    'reviews'   => $reviews,
                    'filter'    => $form->createView(),
                    'pagination' => $pagination
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
    
    public function commentsAction() {
        
        $em = $this->getDoctrine()->getManager();
        
        $filter = new CommentsFilter();
        
        $form = $this->createForm(new CommentsFilterType(), $filter);
        
        $request = $this->getRequest();
        $limit_per_page = $this->container->getParameter('pagination_limit');
        $paginator  = $this->get('knp_paginator');
        
        $session = $this->getRequest()->getSession();
        
        if($request->isMethod('POST')) {
            
            $form->bindRequest($request);

            if ($form->isValid()) {
                
                $comments = $em
                    ->getRepository('FenchyNoticeBundle:Comment')
                    ->getFullDetailedList($filter);
                
                
                $session->set('commentfilter',$filter);
                
                $pagination = $paginator->paginate(
                        $comments,
                        $this->get('request')->query->get('page', 1),
                        $limit_per_page/*limit per page*/
                    );
                return $this->render(
                    'FenchyAdminBundle:Default:comments.html.twig',
                    array(
                        'comments'   => $comments,
                        'filter'    => $form->createView(),
                        'pagination' => $pagination
                    )
                );
            }
        }

        if ($session->get('commentfilter') == null) {
            $comments = $em->getRepository('FenchyNoticeBundle:Comment')
                ->getFullDetailedList();
        }
        else {
            $comments = $em->getRepository('FenchyNoticeBundle:Comment')
                ->getFullDetailedList($session->get('commentfilter'));
        }
        
        $pagination = $paginator->paginate(
                        $comments,
                        $this->get('request')->query->get('page', 1),
                        $limit_per_page/*limit per page*/
                    );
        return $this->render(
                'FenchyAdminBundle:Default:comments.html.twig',
                array(
                    'comments'   => $comments,
                    'filter'    => $form->createView(),
                    'pagination' => $pagination
                )
            );
    }
    
    public function RequestsAction() {
        
        $em = $this->getDoctrine()->getManager();
        
        $filter = new \Fenchy\AdminBundle\Entity\RequestsFilter();
        
        $form = $this->createForm(new \Fenchy\AdminBundle\Form\RequestsFilterType(), $filter);
        
        $request = $this->getRequest();
        $limit_per_page = $this->container->getParameter('pagination_limit');
        $paginator  = $this->get('knp_paginator');
        
        $session = $this->getRequest()->getSession();
        
        if($request->isMethod('POST')) {
            
            $form->bindRequest($request);

            if ($form->isValid()) {
                
                $requests = $em
                    ->getRepository('FenchyNoticeBundle:Request')
                    ->getFullDetailedList($filter);
                
                
                $session->set('requestfilter',$filter);
                
                $pagination = $paginator->paginate(
                        $requests,
                        $this->get('request')->query->get('page', 1),
                        $limit_per_page/*limit per page*/
                    );
                return $this->render(
                    'FenchyAdminBundle:Default:requests.html.twig',
                    array(
                        'requests'   => $requests,
                        'filter'    => $form->createView(),
                        'pagination' => $pagination
                    )
                );
            }
        }

        if ($session->get('requestfilter') == null) {
            $requests = $em->getRepository('FenchyNoticeBundle:Request')
                ->getFullDetailedList();
        }
        else {
            $requests = $em->getRepository('FenchyNoticeBundle:Request')
                ->getFullDetailedList($session->get('requestfilter'));
        }
        
        $pagination = $paginator->paginate(
                        $requests,
                        $this->get('request')->query->get('page', 1),
                        $limit_per_page/*limit per page*/
                    );
        return $this->render(
                'FenchyAdminBundle:Default:requests.html.twig',
                array(
                    'requests'   => $requests,
                    'filter'    => $form->createView(),
                    'pagination' => $pagination
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
    
    public function reviewDeleteAction ($id) {
        
        $review = $this->getDoctrine()->getRepository('FenchyNoticeBundle:Review')->find($id);
       
        if(NULL === $review) {
            $this->get('session')->setFlash(
                        'negative', 
                        'Review not found'
                        );
        }
        else {
            $user = $review->getAuthor();
            $user->removeReview($review);
            $user->setActivity($user->getActivity()-1);
            
            $aboutUser = $review->getAboutUser();
            $aboutUser->setActivity($aboutUser->getActivity()-2);
            
            $this->getDoctrine()->getManager()->remove($review);
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->persist($aboutUser);
            $this->getDoctrine()->getManager()->flush();
            
            $this->get('session')->setFlash(
                            'positive', 
                            'Review deleted.'
                            );
        }
        
        return $this->redirect($this->generateUrl('fenchy_admin_reviews'));
    }
    
     public function requestDeleteAction ($id) {
        
        $request = $this->getDoctrine()->getRepository('FenchyNoticeBundle:Request')->find($id);
       
        if(NULL === $request) {
            $this->get('session')->setFlash(
                        'negative', 
                        'Request not found'
                        );
        }
        else {
            $user = $request->getAuthor();
            $user->removeRequest($request);
            
            if($request->getStatus()=='done')
            {
                $aboutUse = $request->getAboutUser();
                $aboutUse->setActivity($aboutUse->getActivity()-1);
                $this->getDoctrine()->getManager()->persist($aboutUse);
            }
                       
            $this->getDoctrine()->getManager()->remove($request);
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
            
            $this->get('session')->setFlash(
                            'positive', 
                            'Request deleted.'
                            );
        }
        
        return $this->redirect($this->generateUrl('fenchy_admin_requests'));
    }
    
     public function commentDeleteAction ($id) {
        
        $comment = $this->getDoctrine()->getRepository('FenchyNoticeBundle:Comment')->find($id);
       
        if(NULL === $comment) {
            $this->get('session')->setFlash(
                        'negative', 
                        'Comment not found'
                        );
        }
        else {
            $user = $comment->getAuthor();
            $user->removeComment($comment);
            
            $this->getDoctrine()->getManager()->remove($comment);
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
            
            $this->get('session')->setFlash(
                            'positive', 
                            'Comment deleted.'
                            );
        }
        
        return $this->redirect($this->generateUrl('fenchy_admin_comments'));
    }
    
     public function userDeleteAction ($id) {
        
        $user = $this->getDoctrine()->getRepository('UserBundle:User')->find($id);
       
        if(NULL === $user) {
            $this->get('session')->setFlash(
                        'negative', 
                        'User not found'
                        );
        }
        else {
            $this->get('fenchy.messenger')->deleteUserMessage($id);
            $em = $this->getDoctrine()->getManager();
               
        $messages = $em->getRepository('CunningsoftChatBundle:Message')->findBySenderOrReceiver($user);

        foreach($messages as $message) {

            if($message->getAuthor() == NULL) {
                if($message->getReceiver()->getId() === $id) {
                    $em->remove($message); // remove if sender and receiver has been deleted
                }
            } elseif($message->getReceiver() == NULL) {
                if($message->getAuthor()->getId() == $id) {
                    $em->remove($message); // remove if sender and receiver has been deleted
                }
            } else {
                if($message->getAuthor()->getId() == $id) {
                    $em->remove($message); // unset sender if he is going to be deleted
                }
                
                if($message->getReceiver()->getId() == $id) {
                    $em->remove($message); // unset receiver if he is going to be deleted
                }
            }
        }
        
        // We have to flush twice because after message remove we can remove user
        
        $em->flush();
        
            $em->remove($user);
            $em->flush();
            
            $this->get('session')->setFlash(
                            'positive', 
                            'User deleted.'
                            );
        }
        
        return $this->redirect($this->generateUrl('fenchy_admin_users'));
    }
    
    public function sendMsgAction () {
        
        $request = $this->getRequest();
        $ids = $request->get('ids');
        foreach ($ids as $id)
        {        
            $user = $this->getDoctrine()->getRepository('UserBundle:User')->find($id);
        
            $messenger = $this->get('fenchy.messenger');
        
            if (null != $user && $user != $this->getUser())
            {
     		$messenger = $this->get('fenchy.messenger');
    		$messageObject = new Message();
    		
    		$messenger->setReceiver($user);
                
                $content = '';
    		//$content = $this->get('translator')->trans('regularuser.message.message_part', array(
    					//'%username%' => $user->getRegularUser()->getFirstname()));
    		
                $content .= $request->get('content');
                $messageObject->setTitle($request->get('title'));
    	
    		$messageObject->setContent($content);
    	
    		$messageObject->setSender($this->getUser());
    		$messageObject->setReceiver($user);
    	
    		$message = $messenger->send($messageObject);
    	
    		if ($this->container->getParameter('notifications_enabled')) $this->messageNotification($message,$this->getUser());
    	
            }
        }
    	return new Response();
    }
    
    
    public function flagDeleteAction ($id, $type) {
        
        $flag = $this->getDoctrine()->getRepository('FenchyUtilBundle:Sticker')->find($id);
       
        if(NULL === $flag) {
            $this->get('session')->setFlash(
                        'negative', 
                        'Flag not found'
                        );
        }
        else {
           
            $em = $this->getDoctrine()->getManager();
            $em->remove($flag);
            $em->flush();
            
            $this->get('session')->setFlash(
                            'positive', 
                            'Flag deleted.'
                            );
        }
        if($type == 'notice')
        {
            return $this->redirect($this->generateUrl('fenchy_admin_notices'));
        }
        else
        {
            return $this->redirect($this->generateUrl('fenchy_admin_users'));
        }
    }
    
    public function identityAction () {
    
    	$em = $this->getDoctrine()->getManager();
    
    	$filter = new \Fenchy\AdminBundle\Entity\IdentityVerificationFilter();
    
    	$form = $this->createForm(new \Fenchy\AdminBundle\Form\IdentityVerificationFilterType(), $filter);
    
    	$request = $this->getRequest();
        $limit_per_page = $this->container->getParameter('pagination_limit');
        $paginator = $this->get('knp_paginator');
        $session = $this->getRequest()->getSession();
        
    	if($request->isMethod('POST')) 
    	{
    		$form->bindRequest($request);
    
    		if ($form->isValid()) 
    		{
    			$identities = $em
    					->getRepository('UserBundle:IdentityVerification')
    					->getFullDetailedList($filter);   		
    			
                        $session->set('identityfilter',$filter);
                
                        $pagination = $paginator->paginate(
                                        $identities,
                                        $this->get('request')->query->get('page', 1),
                                        $limit_per_page/*limit per page*/
                                    );
                
    			return $this->render(
    					'FenchyAdminBundle:Default:identity.html.twig',
    					array(
    							'identities'	=> $identities,    							
    							'filter'    => $form->createView(),
                                                        'pagination' => $pagination
    					));
    		}
    	}
    
        
        if ($session->get('identityfilter') == null) {
            $identities = $em
    		->getRepository('UserBundle:IdentityVerification')
    		->getFullDetailedList();
        }
        else 
        {
           $identities = $em
    		->getRepository('UserBundle:IdentityVerification')
    		->getFullDetailedList($session->get('identityfilter'));
        }
        
	$pagination = $paginator->paginate(
                        $identities,
                        $this->get('request')->query->get('page', 1),
                        $limit_per_page/*limit per page*/
                      );	

    	return $this->render(
    			'FenchyAdminBundle:Default:identity.html.twig',
    			array(  
    					'identities'	=> $identities,
    					'filter'       => $form->createView(),
                                        'pagination' => $pagination
    			)
    	);
    }
    
    public function identitySwitchAction() {
    
    	$user = $this->get('security.context')->getToken()->getUser();
    	$id = $this->getRequest()->get('id');
    	$status = $this->getRequest()->get('status');
    
    	if(!$id) {
    		$this->createNotFoundException();
    	}
    
    	if(!$status) {
    		$this->createNotFoundException();
    	}
    
    	$em = $this->getDoctrine()->getManager();
    	$identity = $em->getRepository('UserBundle:IdentityVerification')->find($id);
    
    	if(!$identity) {
    		$this->createNotFoundException();
    	}   
    	  
    	if(strcasecmp($identity->getStatus(),'Verified')==0) {
    		$identity->setStatus('Requested');
    	}
    	else {
    		$identity->setStatus('Verified');
    		if(!$identity->getActivitypoint())
    		{
    			$identity->setActivitypoint(100);
    			$user->addActivity(100);
    			$em->persist($user);
    		}
    	}
    	$em->persist($identity);
    	$em->flush();
    
    	$str = json_encode(array(
    			'status' => $identity->getStatus(),
    			'id'        => $identity->getId()
    	));
    	return new Response($str);
    }
    
    public function locationAction () {
    
    	$em = $this->getDoctrine()->getManager();
    
    	$filter = new \Fenchy\AdminBundle\Entity\LocationVerificationFilter();
    
    	$form = $this->createForm(new \Fenchy\AdminBundle\Form\LocationVerificationFilterType(), $filter);
    
    	$request = $this->getRequest();
    
        $limit_per_page = $this->container->getParameter('pagination_limit');
        $paginator = $this->get('knp_paginator');
        $session = $this->getRequest()->getSession();
        
    	if($request->isMethod('POST'))
    	{
    		$form->bindRequest($request);
    
    		if ($form->isValid())
    		{
    			$locations = $em
    			->getRepository('UserBundle:LocationVerification')
    			->getFullDetailedList($filter);
    			 
                        $session->set('locationfilter',$filter);
                
                        $pagination = $paginator->paginate(
                                        $locations,
                                        $this->get('request')->query->get('page', 1),
                                        $limit_per_page/*limit per page*/
                                    );
                        
    			return $this->render('FenchyAdminBundle:Default:location.html.twig',
    					array(
    							'locations'	=> $locations,
    							'filter'    => $form->createView(),
                                                        'pagination' => $pagination
    					));
    		}
    	}
    
        if($session->get('locationfilter') == null)
        {
            $locations = $em
                ->getRepository('UserBundle:LocationVerification')
                ->getFullDetailedList();
        }
        else
        {
            $locations = $em
                ->getRepository('UserBundle:LocationVerification')
                ->getFullDetailedList($session->get('locationfilter'));
        }
    
        $pagination = $paginator->paginate(
                                        $locations,
                                        $this->get('request')->query->get('page', 1),
                                        $limit_per_page/*limit per page*/
                                    );
    	return $this->render('FenchyAdminBundle:Default:location.html.twig',
    			array(
    					'locations'	=> $locations,
    					'filter'       => $form->createView(),
                                        'pagination' => $pagination
    			)
    	);
    }
    
    public function locationSwitchAction() {
    
    	$id = $this->getRequest()->get('id');
    	$status = $this->getRequest()->get('status');
    
    	if(!$id) {
    		$this->createNotFoundException();
    	}
    
    	if(!$status) {
    		$this->createNotFoundException();
    	}
    
    	$em = $this->getDoctrine()->getManager();
    	$location = $em->getRepository('UserBundle:LocationVerification')->find($id);
    
    	if(!$location) {
    		$this->createNotFoundException();
    	}
    	 
    	if(strcasecmp($location->getStatus(),'Verified')==0) {
    		$location->setStatus('Requested');
    	}
    	else {
    		$location->setStatus('Verified');
    	}
    	$em->persist($location);
    	$em->flush();
    
    	$str = json_encode(array(
    			'status' => $location->getStatus(),
    			'id'        => $location->getId()
    	));
    	return new Response($str);
    }
    
    protected function messageNotification(Message $message, User $sender)
    {
    	$receiver = $message->getReceiver();
    
    	$notifications = $receiver->getNotifications();
    	$niterator = $notifications->getIterator();
    
    	$message_notification = false;
    	foreach ($niterator as $onen)
    	{
    		if ($onen->getName() == 'message') $message_notification = true;
    	}
    
    	$em = $this->getDoctrine()->getEntityManager();
    	$result = $em->getRepository('FenchyRegularUserBundle:Document')->findById($sender->getId());
    	 
    	if($result)
    	{
    		$avatar = $result->getWebPath();
    		if($avatar == "")
    			$avatar = 'images/default_profile_picture.png';
    	}
    	else
    	{
    		$avatar = 'images/default_profile_picture.png';
    	}
    	if ($message_notification)
    	{
    
    		$interval = $receiver->getNotificationGroupIntervals()->first();
    		if ($interval != null)
    			$interval_val = $interval->getInterval();
    		else $interval_val = null;
    
    		if ($interval_val === NotificationGroupInterval::INTERVAL_DAILY)
    		{
    
    			$queue_processing_hour = $this->container->getParameter('notification_queue_processing_hour');
    
    			$now = new \DateTime;
    			$now_hour = (integer) $now->format('G');
    
    			$send_after = new \DateTime;
    			if ($now_hour >= $queue_processing_hour)
    			{
    				$send_after->modify('+1 day');
    			}
    			$send_after->setTime($queue_processing_hour, 0, 0);
    
    			$toQueue = new NotificationQueue;
    			$toQueue->setSendAfter($send_after)->setFromAddress($this->container->getParameter('from_email'))->setFromName($this->container->getParameter('from_name'))->setToAddress($receiver->getEmail())->setSubject($this->get('translator')->trans('message.notification.email.subject', array(
    					'%username%' => $sender->getRegularUser()->getFirstname())))->setBodyHtml($this->renderView('FenchyMessageBundle:Notifications:messageEmailHTML.html.twig', array(
    							'sender' => $sender,
    							'message' => $message)))->setBodyPlain($this->renderView('FenchyMessageBundle:Notifications:messageEmailPlain.html.twig', array(
    									'sender' => $sender,
    									'message' => $message,
    									'avatar' => $avatar,
    							)));
    							$em = $this->getDoctrine()->getManager();
    							$em->persist($toQueue);
    							$em->flush();
    		}
    		elseif ($interval_val === NotificationGroupInterval::INTERVAL_IMMEDIATELY)
    		{
    			$emailNotification = \Swift_Message::newInstance()->setFrom($this->container->getParameter('from_email'), $this->container->getParameter('from_name'))->setTo($receiver->getEmail())->setSubject($this->get('translator')->trans('message.notification.email.subject', array(
    					'%username%' => $sender->getRegularUser()->getFirstname())))->setBody($this->renderView('FenchyMessageBundle:Notifications:messageEmailHTML.html.twig', array(
    							'sender' => $sender,
    							'message' => $message, 
    							'avatar' => $avatar)), 'text/html')->addPart($this->renderView('FenchyMessageBundle:Notifications:messageEmailPlain.html.twig', array(
    									'sender' => $sender,
    									'message' => $message)), 'text/plain');
    							$mailer = $this->get('mailer');
    							$mailer->send($emailNotification);
    		}
    	}
    
    }
}
