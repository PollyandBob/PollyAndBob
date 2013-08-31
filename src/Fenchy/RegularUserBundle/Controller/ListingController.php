<?php

namespace Fenchy\RegularUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Fenchy\NoticeBundle\Form\NoticeDeleteType,
    Fenchy\NoticeBundle\Entity\Notice,
    Fenchy\NoticeBundle\Form\NoticeListingType,
    Fenchy\NoticeBundle\Entity\Review;

/**
 * This controller should manage listings (notices) and all other operations should not be here.
 * 
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class ListingController extends Controller
{
    
    /**
     * This simple action returns list of all available types. One type should be
     * choosen for step 2 of creation process (notice form)
     * 
     * @return Response
     */
    public function create1Action() {
        
        $direction = $this->container->getParameter('notice_menu_property');
        $em = $this->getDoctrine()->getManager();
        $types = $em->getRepository('FenchyNoticeBundle:Type')->getAllWithProperties();

        return $this->render(
                'FenchyRegularUserBundle:Listing:create1.html.twig', 
                array(
                    'types' => $types,
                    'direction' => $direction
                ));
    }
    
    /**
     * This is action for second (final) step of listing creation process.
     * Renders notice form.
     * 
     * @param string $typename
     * @param string $direction
     * @throws Exception
     * 
     * @return Response
     */
    public function create2Action($typename, $direction) {
        
        $em = $this->getDoctrine()->getManager();
        
        $user = $this->get('security.context')->getToken()->getUser();
        $location = $user->getLocation();
        
        $type = $em->getRepository('FenchyNoticeBundle:Type')->getByNameWithProperties($typename);
        
        // render form
        if (!$this->getRequest()->isMethod('POST')) {

            // $notice needs to be stored as draft (gallery must point on something)
            $notice = $this->createDraft($user);
            $notice->setType($type);
            $em->persist($notice);
            $em->flush();
            
            // create notice form for specified Type
            $form = $this->createForm(new NoticeListingType($type, $notice), $notice);
            
            // In case we want to display notice gallery we need to manage it together
            // with notice. View data needed for gallery is returned by service.
            $data = $this->get('fenchy.gallery_manager')->manageGallery($notice->getGallery());
            
            $data['notice']   = $notice;
            $data['form']     = $form->createView();
            $data['type']     = $type;
            $data['direction'] = $direction;
            $data['location'] = $location;
            $data['tags']   = $this->get('fenchy_dictionary')->getAllListingTags();
            
            return $this->render(
                'FenchyRegularUserBundle:Listing:create2.html.twig', $data);
        }
        
        // try to stroe notice
        elseif ($this->getRequest()->isMethod('POST')) {
            // Get user's draft notice (there should be one only)
            $notice = $em->getRepository('FenchyNoticeBundle:Notice')->findDraft($user);

            // If current user has no draft then we could create new notice, but we wont
            // We do not like cheaters.
            if(!$notice) {
                throw $this->createNotFoundException('Draft notice not found.');
            }
            
            $form = $this->createForm(new NoticeListingType($type, $notice), $notice);
            
            $form->bind($this->getRequest());
                
            if ($form->isValid()) {
                // Notice is not a draft any more.
                $notice->setDraft(FALSE);

                // We need to manually set all Value entities
                $notice->setValues($this->getValuesFromForm($form->get('type'), $direction));
                $tags = $this->get('fenchy_dictionary')->store($notice->getTags(), TRUE);
                $notice->setDictionaries($tags);
                $this->get('fenchy.reputation_counter')->update($user, \Fenchy\UserBundle\Services\ReputationCounter::TYPE_NOTICE);
                $em->persist($user);
                
                // Find some tags in the notice
                $this->get('fenchy_dictionary')->store($notice->getTags(), TRUE);
                
                // And again we need to call gallery manager to handle gallery changes.
                $this->get('fenchy.gallery_manager')->manageGallery($notice->getGallery(), TRUE);
                
                // PUT ON FB
                $fb_feed = $facebookProvider = $this->get('fenchy.facebook.user')->post($notice);
                
                $positive = $this->get('translator')
                        ->trans('notice.added_successfully');
                
                $negative = '';
                
                if($fb_feed instanceof \Fenchy\NoticeBundle\Entity\FacebookFeed) {
                    $em->persist($fb_feed);
                    $positive .= '<br/>'.$this->get('translator')
                        ->trans('notice.successfully_posted_on_fb');
                }
                elseif(FALSE === $fb_feed) {
                    $negative .= $this->get('translator')
                        ->trans('notice.posting_on_fb_failed');
                }

                $em->persist($notice);
                $em->flush();
                
                // set flash messages
                $this->get('session')->setFlash('positive', $positive);
                if($negative) $this->get('session')->setFlash('negative', $negative);
                
                // Done :) Let the user see new notice.
                $created_at = $notice->getCreatedAt();
                
                if($created_at) {
                    return $this->redirect($this->generateUrl('fenchy_notice_show_slug', array(
                        'slug' => $notice->getSlug(),
                        'year' => $created_at->format('Y'),
                        'month' => $created_at->format('m'),
                        'day' => $created_at->format('d')
                    )));
                } else {
                    return $this->redirect($this->generateUrl('fenchy_regular_user_notice_show', array('id' => $notice->getId())));
                }
                
                
            }
            else {
                // form is invalid so we need to display it again, but gallery 
                // should not be reseted. We need to inform gallery manager that form was invalid.
            	$data = $this->get('fenchy.gallery_manager')->manageGallery($notice->getGallery(), FALSE);
                $data['notice']   = $notice;
                $data['form']     = $form->createView();
                $data['type']     = $type;
                $data['direction'] = $direction;
                $data['location'] = $location;
                $data['tags']   = $this->get('fenchy_dictionary')->getAllListingTags();

                return $this->render(
                    'FenchyRegularUserBundle:Listing:create2.html.twig', $data);
            }
        }
    }
    
    /**
     * Edit listing (notice) action.
     * 
     * @param integer $id
     * @return Response
     * @throws Exception
     */
    public function editAction($id) {
        
        $em = $this->getDoctrine()->getManager();
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        $notice = $em->getRepository('FenchyNoticeBundle:Notice')->findFullDetailed($id);
        
        if(!$notice || $notice->getUser()->getId() !== $user->getId()) throw $this->createNotFoundException('Listing does not exists');
        
        // We do not edit drafts.
        if($notice->getDraft()) return $this->redirect($this->generateUrl('fenchy_regular_user_notice_create1'));
        
        if (!$this->getRequest()->isMethod('POST')) {
            
            // Create form and manage listing gallery
            $form = $this->createForm(new NoticeListingType($notice->getType(), $notice), $notice);
            $data = $this->get('fenchy.gallery_manager')->manageGallery($notice->getGallery());
            $data['notice']   = $notice;
            $data['form']     = $form->createView();
            $data['type']     = $notice->getType();
            $data['location'] = $notice->getLocation();
            $data['tags']     = $this->get('fenchy_dictionary')->getAllListingTags();
            
            return $this->render(
                'FenchyRegularUserBundle:Listing:edit.html.twig', $data);
        }
        // GET
        else {  
        	
            // create and bind form
            $form = $this->createForm(new NoticeListingType($notice->getType(), $notice), $notice);
            
            $form->bind($this->getRequest());
                
            if ($form->isValid()) {
                
                // Notice is not draft any more
                $notice->setDraft(FALSE);
                
                // We need to manually set all Value entities
                $notice->setValues($this->getValuesFromForm($form->get('type')));

                $tags = $this->get('fenchy_dictionary')->store($notice->getTags(), TRUE);
                
                $notice->setDictionaries($tags); // comment by Jignesh Vagh
               
                
                // And again we need to call gallery manager to handle gallery changes.
                $this->get('fenchy.gallery_manager')->manageGallery($notice->getGallery(), TRUE);
                
                // Done :) Let the user see edited notice.
                $success_msg = $this->get('translator')->trans('listing.edit.flash_success');
                $this->get('session')->setFlash('positive', $success_msg);
                
                return $this->redirect($this->generateUrl('fenchy_regular_user_notice_edit', array('id' => $notice->getId())));
            }
            else {
                // form is invalid so we need to display it again, but gallery 
                // should not be reseted. We need to inform gallery manager that form was invalid.
            	$data = $this->get('fenchy.gallery_manager')->manageGallery($notice->getGallery(), FALSE);
                $data['notice']   = $notice;
                $data['form']     = $form->createView();
                $data['type']     = $notice->getType();

                return $this->render(
                    'FenchyRegularUserBundle:Listing:edit.html.twig', $data);
            }
        }
    }
    
    /**
     * Display notice data. 
     * 
     * @param integer $id
     * @return Response
     * @throws Exception
     */
    public function showAction ($id) {
        
        $notice = $this->getDoctrine()
                ->getManager()
                ->getRepository('FenchyNoticeBundle:Notice')
                ->findFullDetailed($id);
        
        if(!$notice) throw $this->createNotFoundException ();
        
        $created_at = $notice->getCreatedAt();
        $year = $created_at->format('Y');
        $month = $created_at->format('m');
        $day = $created_at->format('d');
        
        return $this->redirect($this->generateUrl('fenchy_notice_show_slug', array(
            'slug' => $notice->getSlug(),
            'year' => $year,
            'month' => $month,
            'day' => $day
        )));

    }
    
    /**
     * Display notice data with slug given as an arg.
     * 
     * @param string $slug
     * @param integer $year
     * @param integer $month
     * @param integer $day
     * @return Response
     * @throws Exception
     */
    public function showWithSlugAction ($slug, $year, $month, $day) {
        
        $em = $this->getDoctrine()->getManager();
        
        $notice = $em
                ->getRepository('FenchyNoticeBundle:Notice')
                ->findFullDetailedWithSlug($slug);

        if( ! ($notice instanceof \Fenchy\NoticeBundle\Entity\Notice) ) 
            throw $this->createNotFoundException ();
        
        $pagination = $this->container->getParameter('reviews_pagination');
        $userLoggedIn = $this->get('security.context')->getToken()->getUser();
        $reviewRepo = $em->getRepository('FenchyNoticeBundle:Review');
        
        if ( $notice->getUser() == $userLoggedIn ) 
            $usersOwnListing = true;
        else
            $usersOwnListing = false;
        
        $initialReviewsP = $reviewRepo->findByInJSON(
            $this->container->get('router'),
            array('aboutNotice'=>$notice->getId(), 'type'=>Review::TYPE_POSITIVE),
            array('created_at'=>'DESC'), $pagination+1, 0);
        $initialReviewsPCount = $reviewRepo->findCount(
            array('aboutNotice'=>$notice->getId(), 'type'=>Review::TYPE_POSITIVE) );
        
        $initialReviewsN = $reviewRepo->findByInJSON(
            $this->container->get('router'),
            array('aboutNotice'=>$notice->getId(), 'type'=>Review::TYPE_NEGATIVE),
            array('created_at'=>'DESC'), $pagination+1, 0);
        $initialReviewsNCount = $reviewRepo->findCount(
            array('aboutNotice'=>$notice->getId(), 'type'=>Review::TYPE_NEGATIVE) );
        
        $userId = $notice->getUser()->getId();
        
        if ( $userId != NULL ) {
            
            $userOther = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')->getAllData( $userId );

            if ( ! $userOther instanceof \Fenchy\UserBundle\Entity\User )
                return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
            $displayUser = $userOther;
            $usersOwnProfile = 0;
        }
        
        

        
        return $this->render('FenchyRegularUserBundle:Listing:show.html.twig', array(
            'notice' => $notice,
            'usersOwnListing' => $usersOwnListing,
            'displayUser' => $displayUser,
            'pagination' => $pagination,
            'initialReviewsP' => array("list"=>$initialReviewsP, "count"=>$initialReviewsPCount),
            'initialReviewsN' => array("list"=>$initialReviewsN, "count"=>$initialReviewsNCount)
        ));
    }
    
    /**
     * Creates Notice draft and remove previous user draft.
     * Does NOT persist new draft!
     * @param User $user
     * @return \Fenchy\NoticeBundle\Entity\Notice
     */
    protected function createDraft($user) {
        
        $em = $this->getDoctrine()->getManager();
        $draft = $em->getRepository('FenchyNoticeBundle:Notice')->findOneBy(array('draft' => TRUE, 'user' => $user));
        if($draft) {
            $found = TRUE;
            $em->remove($draft);
            $em->flush();
        } else $found = FALSE;
        
        $notice = new Notice();
        $notice->setUser($user);
        $notice->setDraft(TRUE);
        
        // If there were not old draft we have to add points for new notice to user;
        if(!$found) {
            $this->get('fenchy.reputation_counter')->update($user, \Fenchy\UserBundle\Services\ReputationCounter::TYPE_NOTICE);
            $em->persist($user);
        }
        
        return $notice;
    }
    
    /**
     * Pull PropertyType entities out of form data and creates Value entities for notice
     * @param \Symfony\Component\Form\Form $form
     * @param String|NULL $direction
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    protected function getValuesFromForm(\Symfony\Component\Form\Form $form, $direction = NULL) {
            
        $values = new \Doctrine\Common\Collections\ArrayCollection();

        foreach($form->getChildren() as $child) {

            if ($child->getData() instanceof \Fenchy\NoticeBundle\Entity\Value) {

                $val = $child->getData();
                if(NULL != $direction) {
                    if($val->getProperty()->getName() === \Fenchy\NoticeBundle\Entity\Type::$direction) {
                        $val->setValueFromOptionByString($direction);
                    }
                }
                $values->add($val);
            }
        }

        return $values;
    }
    
    /**
     * manage user's listings action
     * @author Mateusz Krowiak <mkrowiak@pgs-soft.com>
     */
    public function manageAction() {
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $repo = $em->getRepository('FenchyNoticeBundle:Notice');
        
        $listings = $repo->getUserNotices($user);
        
        $userId = $user->getId();
        $displayUser = false;
        
        if ( $userId != NULL ) {
            
            $userOther = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')->getAllData( $userId );

            if ( ! $userOther instanceof \Fenchy\UserBundle\Entity\User )
                return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
            $displayUser = $userOther;

        }
        
        return $this->render('FenchyRegularUserBundle:Listing:manage.html.twig', array(
            'listings' => $listings,
            'displayUser' => $displayUser,
            'usersOwnProfile' => true
        ));
        
    }
    
    public function tagsAction() {
        
        echo json_encode(array(
                'tags' => array(
                    array('tag' =>'aaaa'), 
                    array('tag' => 'aaaaa'), 
                    array('tag' => 'aaaaaa'), 
                    array('tag' => 'bbbb'),
                    array('tag' => 'bbbba'),
                    array('tag' => 'bbb')
                )
                
            ));exit;
    }
    

    /**
     * @Template()
     * @return array 
     */
    public function deleteAction($id) {

        $em = $this->getDoctrine()->getManager();

        $notice = $em->getRepository('FenchyNoticeBundle:Notice')->find($id);

        if (null === $notice) {
            throw new NotFoundHttpException('Notice not found!');
        }

        $form = $this->createForm(new NoticeDeleteType());

        $userId = $this->get('security.context')->getToken()->getUser()->getId();
        $displayUser = false;

        if ($userId != NULL) {
            
            if ($notice->getUser()->getId() !== $userId) {
                throw new \Exception('You have not permission to delete this listing.');
            }

            $userOther = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')->getAllData($userId);

            if (!$userOther instanceof \Fenchy\UserBundle\Entity\User)
                return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
            $displayUser = $userOther;
        }
        
        return array('displayUser' => $displayUser, 'form' => $form->createView(), 'notice' => $notice);

    }

    public function deleteConfirmAction($id) {

        $form = $this->createForm(new NoticeDeleteType());

        $request = $this->getRequest();

        if ('POST' == $request->getMethod()) {

            
            $form->bindRequest($request);
            
            if ($form->isValid()) {

                $regularUser = $this->get('security.context')->getToken()->getUser()->getRegularUser();
                $em = $this->getDoctrine()->getManager();

                $notice = $em->getRepository('FenchyNoticeBundle:Notice')->find($id);

                if (null === $notice) {
                    throw new NotFoundHttpException('Notice not found!');
                }
                
                if ($notice->getUser()->getId() !== $this->get('security.context')->getToken()->getUser()->getId()) {
                    throw new \Exception('You have not permission to delete this listing.');
                }
                
                $notice->releaseReviews();
                $notice->getUser()->removeNotice($notice);
                $this->get('fenchy.reputation_counter')->update($notice->getUser(), \Fenchy\UserBundle\Services\ReputationCounter::TYPE_NOTICE);
                $em->remove($notice);
                $em->persist($notice->getUser());
                $em->flush();

                $this->get('session')->setFlash('positive-overlayer', $this->get('translator')->trans(
                                'listing.manage.flash.delete')
                );
            }
        }

        return $this->redirect($this->generateUrl('fenchy_regular_user_listing_manage'));
    }    
}