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
    Fenchy\AdminBundle\Entity\CategoriesFilter,
    Fenchy\AdminBundle\Form\CategoriesFilterType,
	Fenchy\NoticeBundle\Entity\Type,
    Fenchy\AdminBundle\Form\ReviewsFilterType,
    Fenchy\AdminBundle\Form\IdentityVerificationFilter,
	Fenchy\AdminBundle\Form\IdentityVerificationFilterType,
	Fenchy\NoticeBundle\Form\CategoryType,
	Fenchy\AdminBundle\Form\CategoryNewType;
use	Fenchy\UserBundle\Entity\IdentityVerification;



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
    
    public function identityAction () {
    
    	$em = $this->getDoctrine()->getManager();
    
    	$filter = new \Fenchy\AdminBundle\Entity\IdentityVerificationFilter();
    
    	$form = $this->createForm(new \Fenchy\AdminBundle\Form\IdentityVerificationFilterType(), $filter);
    
    	$request = $this->getRequest();
    
    	if($request->isMethod('POST')) 
    	{
    		$form->bindRequest($request);
    
    		if ($form->isValid()) 
    		{
    			$identities = $em
    					->getRepository('UserBundle:IdentityVerification')
    					->getFullDetailedList($filter);   		
    			
    			return $this->render(
    					'FenchyAdminBundle:Default:identity.html.twig',
    					array(
    							'identities'	=> $identities,    							
    							'filter'    => $form->createView()
    					));
    		}
    	}
    
		$identities = $em
    		->getRepository('UserBundle:IdentityVerification')
    		->getFullDetailedList($filter);

    	return $this->render(
    			'FenchyAdminBundle:Default:identity.html.twig',
    			array(  
    					'identities'	=> $identities,
    					'filter'       => $form->createView()
    			)
    	);
    }
    
    public function identitySwitchAction() {
    
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
    	}
    	$em->persist($identity);
    	$em->flush();
    
    	echo json_encode(array(
    			'status' => $identity->getStatus(),
    			'id'        => $identity->getId()
    	));
    	exit;
    }
}
