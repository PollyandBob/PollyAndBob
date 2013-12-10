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

class ProfileController extends Controller
{
    public function indexAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $regularUser = $user->getRegularUser();
        
        $contacts = $user->getRegularUser()->getMyFriends();
        
        $completed = $this->getCompleted();
        
        $references = $this->getReferences($user);

        return $this->render('FenchyRegularUserBundle:Profile:index.html.twig', array(
            'references' => $references,
            'user' => $regularUser,
            'contacts' => $contacts,
            'completed' => $completed
        ));
    }
    
    private function getReferences($user)
    {
        return $this->getDoctrine()->getManager()->getRepository('UserBundle:Reference')
                    ->findBy(array(
                        'ref_user' => $user,
                        'active' => true)); 
    }
    
    private function getCompleted()
    {
        return array(); 
    }


    /**
     * @Template
     */
    public function aboutMeAction() {
        
        $user = $this->get('security.context')->getToken()->getUser();
        $regularUser = $user->getRegularUser();

        $form = $this->createForm(new AboutMeType(), $regularUser);
        
        $request = $this->getRequest();
        
        if($request->isMethod('POST')) {
            
            $form->bindRequest($request);

            if ($form->isValid()) {

                $em = $this->getDoctrine()
                           ->getEntityManager();
                $em->persist($regularUser);
                $em->flush();
                
                $this->get('session')->setFlash('positive', '"About me" has been updated');
                
                return $this->redirect($this->generateUrl('fenchy_regular_user_about_me'));
            }
        }
        
        $intAndAct = $this->getDoctrine()
                ->getEntityManager()
                ->getRepository('FenchyNoticeBundle:Notice')->findInterestsAndActivities($user);
        
        return array(
                    'form'          => $form->createView(), 
                    'regularUser'   => $regularUser,
                    'intAndAct'     => $intAndAct
                    );
    }
    
    /**
     * @Template
     */
    public function interestsAndActivitiesAction($noticeId = NULL) {
        
        $user = $this->get('security.context')->getToken()->getUser();
        if (NULL === $noticeId) {
            
            $notice = new Notice();
            
        } else {
            
            $notice = $this->getDoctrine()
                    ->getEntityManager()
                    ->getRepository('FenchyNoticeBundle:Notice')
                    ->find($noticeId);
            
            if(!$notice || $notice->getUser()->getId() != $user->getId()) {
                
                throw $this->createNotFoundException('Interest or activity not found');
            }
        }
        $form = $this->createForm(new InterestsAndActivitiesType(), $notice);
        
        $request = $this->getRequest();
        
        if($request->isMethod('POST')) {
            
            $form->bindRequest($request);

            if ($form->isValid()) {

                $notice->setUser($user);
                $notice->setDraft(FALSE);
                $em = $this->getDoctrine()
                           ->getEntityManager();
                if($notice->getGallery() == NULL) {
                    // get gallery by stored id if exists
                   $gIds = $this->get('session')->get('gallery');

                    if ($gIds == null)
                        $gIds = array(1 => NULL, 2 => NULL, 3 => NULL);

                    if (array_key_exists(1, $gIds) && $galleryId = $gIds[1]) {

                        $gallery = $em->getRepository('FenchyGalleryBundle:Gallery')->find($galleryId);
                    } else {
                        $gallery = new Gallery();
                        for ($i = $gallery->getImages()->count(); $i < $this->container->getParameter('gallery_notice_max'); $i++) {

                            $img = new Image();
                            $gallery->addImage($img->setSequence($i));
                        }
                    }

                    $notice->setGallery($gallery);
                }
                
                $em->persist($notice);
                $em->flush();
                
                $this->get('session')->setFlash('positive', 'Interests and activities has been updated');
                
                $gIds = $this->get('session')->get('gallery');
                $gIds[1] = NULL;
                $this->get('session')->set('gallery', $gIds);
                
                return $this->redirect($this->generateUrl('fenchy_regular_user_about_me'));
            }
        }
        
        return array(
                    'form'     => $form->createView(), 
                    'notice'   => $notice
                    );
    }

    public function chooseLanguageAction($locale) {
        //$rresponse = new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_indexv2'));
	    if ($this->get('session')->has('currenturl')) {
	    	$rresponse = new RedirectResponse(($this->get('session')->get('currenturl')));
		}
		else {
			$rresponse = new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_indexv2'));
		} 
        if ( in_array($locale, array( 'pl', 'en', 'de' ))) {
            $this->get('session')->set('locale',$locale);
            if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
                $ruser = $this->get('security.context')->getToken()->getUser()->getUserRegular();
                $ruser_id = $ruser->getId();
                $em = $this->getDoctrine()->getEntityManager();
                $ru_rep = $em->getRepository('FenchyRegularUserBundle:UserRegular')->find($ruser_id);
                $ru_rep->setPrefLocale($locale);
                $em->persist($ru_rep);
                $em->flush();
            }
            
        }
        
        $locale_cookie = new Cookie('locale', $locale, time() + 3600 * 24 * 3, '/');
        $rresponse->headers->setCookie($locale_cookie);
        return $rresponse;
    }
    
    
    /*
     * Displays User's profile
     * If $userId is given will display profile of user with specified Id,
     * otherwise will display profile of user currently logged in.
     * If no user logged in, no userId - then 404 
     * 
     * 
     */
    public function userProfileV2Action( $userId ) {

        $userLoggedIn = $this->get('security.context')->getToken()->getUser();
        $request = $this->getRequest();
        $review = new Review();
        $em = $this->getDoctrine()->getManager();
        $reviewRepo = $em->getRepository('FenchyNoticeBundle:Review');
        $noticeRepo = $em->getRepository('FenchyNoticeBundle:Notice');
        $pagination = $this->container->getParameter('reviews_pagination');
        
        if ( $userId != NULL ) {
            
            $userOther = $em->getRepository('UserBundle:User')->getAllData( $userId );

            if ( ! $userOther instanceof \Fenchy\UserBundle\Entity\User )
                return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
            $displayUser = $userOther;
            $usersOwnProfile = 0;
        }
        else {
            // TODO: redirect to 404 in fact
            if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
                return new RedirectResponse($this->container->get('router')->generate('fenchy_frontend_homepage'));
            $displayUser = $userLoggedIn;
            $usersOwnProfile = 1;        
        }
        
        //updates User's reviews with `is_read` true state
        if($userId === null && $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User) {
            $reviewRepo->updateUsersReviewsWithIsRead(true, $userLoggedIn);
        }

        $initialReviews = $reviewRepo->findByInJSON(
            $this->container->get('router'),
            array('aboutUser'=>$displayUser->getId()), array('created_at'=>'DESC'), $pagination+1, 0);
        $initialReveiwsCount = $reviewRepo->findCount( array('aboutUser'=>$displayUser->getId()) );
        
        $initialListingsObj = $noticeRepo->findBy( 
            array('user' => $displayUser->getId(), 'draft' => FALSE),
            array('created_at' => 'DESC'), $pagination + 1, 0);
        $initialListings = array();
        foreach ( $initialListingsObj as $listingObj ) {
            $listingAssoc = $listingObj->toJsonArray();
            $listingAssoc['url'] = $this->container->get('router')
                ->generate('fenchy_notice_show_slug', array(
                    'slug' => $listingObj->getSlug(),
                    'year' => $listingObj->getCreatedAt()->format('Y'),
                    'month' => $listingObj->getCreatedAt()->format('m'),
                    'day' => $listingObj->getCreatedAt()->format('d')
                ));
            $listingAssoc['ownerUrl'] = $this->container->get('router')
                ->generate('fenchy_regular_user_user_profilev2', array(
                    'userId' => $listingObj->getUser()->getId()
                ));
            $initialListings[] = $listingAssoc;
            
        }
        $initialListingsCount = $noticeRepo->findCount( array('user'=>$displayUser->getId()) );
        
        $initialReviewsOO = $reviewRepo->findByInJSON(
            $this->container->get('router'),
            array('author'=>$displayUser->getId()), array('created_at'=>'DESC'), $pagination+1, 0);
        $initialReviewsOOCount = $reviewRepo->findCount( array('author'=>$displayUser->getId()) );
        
        return $this->render('FenchyRegularUserBundle:Profile:userProfileV2.html.twig',
            array(
                'locale'            => $this->getRequest()->getLocale(),
                'pagination'        => $pagination,
                'displayUser'       => $displayUser,
                'usersOwnProfile'   => $usersOwnProfile,
                'initialReviews'    => array( 'count' => $initialReveiwsCount, 'list' => $initialReviews ),
                'initialListings'   => array( 'count' => $initialListingsCount, 'list' => $initialListings ),
                'initialReviewsOO'  => array( 'count' => $initialReviewsOOCount, 'list' => $initialReviewsOO )
            ));
    }
    
    public function repBreakdownWidgetAction( $displayUserId ) {
        
        $em = $this->getDoctrine()->getManager();
        $reviewRepo = $em->getRepository('FenchyNoticeBundle:Review');
        $noticeRepo = $em->getRepository('FenchyNoticeBundle:Notice');
        $userRepo = $em->getRepository('UserBundle:User');
        
        $displayUser = $userRepo->getAllData( $displayUserId );
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

        $profileUrl = $this->get('router')->generate('fenchy_regular_user_user_profilev2', array('userId' => $displayUserId));

        return $this->render("FenchyRegularUserBundle:Widgets:repBreakdown.html.twig", array(
            'displayUser'       => $displayUser,
            'repBreakdown'      => $reputationBreakdown,
            'reputationPoints'  => $reputationPoints,
            'profileUrl' => $profileUrl
        ));
        
    }
    
    public function userReviewsEditFormAction( $reviewId ) {
        $request = $this->getRequest();
        if ( !$request->isMethod('POST') ) {
            return new Response('',401);
        }

        $userLoggedIn = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $is_profile = $request->get('profile')?:0;
        
        if($is_profile) {
            $review = new Review();
            $form = $this->createForm(new LeaveReviewProfileType(), $review);
            return $this->render('FenchyRegularUserBundle:Partials:bareLeaveReviewForm.html.twig',
                array( 'form' => $form->createView()) );            
        } elseif ( $reviewId == NULL ) {
            $review = new Review();
            $form = $this->createForm(new LeaveReviewType(), $review);
            return $this->render('FenchyRegularUserBundle:Partials:bareLeaveReviewForm.html.twig',
                array( 'form' => $form->createView()) );
        } else {
            if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User ) 
                return new Response('',401);
            
            $reviewRepo = $em->getRepository('FenchyNoticeBundle:Review');
            $toEditReview = $reviewRepo->findBy(array('id'=>$reviewId, 'author'=>$userLoggedIn->getId()));
            $toEditReview = array_pop($toEditReview);
            
            if ( ! $toEditReview instanceof \Fenchy\NoticeBundle\Entity\Review )
                return new Response('',401);
            
            $form = $this->createForm(new LeaveReviewType(), $toEditReview);
            return $this->render('FenchyRegularUserBundle:Partials:bareLeaveReviewForm.html.twig',
                array( 'form' => $form->createView()) );
        }
    }
    
    public function userReviewsSaveAction( $reviewId, $targetNoticeId, $targetUserId ) {
        
        $request = $this->getRequest();
        if ( !$request->isMethod('POST') ) {
            return new Response('',401);
        }
        
        $userLoggedIn = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $reviewRepo = $em->getRepository('FenchyNoticeBundle:Review');
        
        if ( ! ($userLoggedIn instanceof \Fenchy\UserBundle\Entity\User) )
            return new Response('',401);
        
        if ( $reviewId != NULL ) {
            $postedReview = $reviewRepo->findOneBy(array('id'=>$reviewId, 'author'=>$userLoggedIn->getId()));
            
            if ( ! ($postedReview instanceof \Fenchy\NoticeBundle\Entity\Review) )
                return new Response('',401);
            
            $form = $this->createForm(new LeaveReviewType(), $postedReview);
            $form->bindRequest($request);
            if ( $form->isValid() ) {
                $em->persist($postedReview);
                $em->flush();
                $response = new Response();
                $response->headers->set('validate','pass');
                return $response;
            }
            else {
                $response = $this->render('FenchyRegularUserBundle:Partials:bareLeaveReviewForm.html.twig',
                    array( 'form' => $form->createView() ) );
                $response->headers->set('validate','fail');
                return $response;                
            }            
        } else if ( $targetNoticeId != NULL ) {
            $noticeRepo = $em->getRepository('FenchyNoticeBundle:Notice');
            
            $targetNotice = $noticeRepo->findOneBy( array('id'=>$targetNoticeId) );
            
            if ( ! ($targetNotice instanceof \Fenchy\NoticeBundle\Entity\Notice) )
                return new Response('',401);
            
            $targetUser = $targetNotice->getUser();
            if ( ! ($targetUser instanceof \Fenchy\UserBundle\Entity\User) )
                return new Response('',401);
            
            $review = new \Fenchy\NoticeBundle\Entity\Review();
            $review->setTitle($targetNotice->getTitle());
            $form = $this->createForm(new LeaveReviewType(), $review);
            $form->bindRequest($request);
            if ( $form->isValid() ) {
                $userLoggedIn->addOwnReview($review);
                $this->get('fenchy.reputation_counter')
                        ->update($userLoggedIn, \Fenchy\UserBundle\Services\ReputationCounter::TYPE_OWN_REVIEW);
                $em->persist($userLoggedIn);
                if($review->getType() === Review::TYPE_POSITIVE) {
                    $targetUser->addReview($review);
                    $this->get('fenchy.reputation_counter')
                            ->update($targetUser, \Fenchy\UserBundle\Services\ReputationCounter::TYPE_REVIEW);
                    $em->persist($targetUser);
                }
                
                $review->setAuthor($userLoggedIn);
                $review->setAboutUser($targetUser);
                $review->setAboutNotice($targetNotice);
                $em->persist($review);
                $em->flush();
                
                if($userLoggedIn->getId() != $targetNotice->getUser()->getId()) {
                    $this->reviewNotification($review, $userLoggedIn, $targetNotice);
                }
                $response = $this->render('FenchyRegularUserBundle:Partials:postedReview.html.twig' );
                $response->headers->set('validate','pass');
                return $response;
            }
            else {
                $response = $this->render('FenchyRegularUserBundle:Partials:bareLeaveReviewForm.html.twig',
                    array( 'form' => $form->createView() ) );
                $response->headers->set('validate','fail');
                return $response;
            }               
        } else if ( $targetUserId != NULL ) {
            
            $userRepo = $em->getRepository('UserBundle:User');
            
            $targetUser = $userRepo->findBy( array('id'=>$targetUserId) );
            $targetUser = array_pop( $targetUser );
            
            if ( ! ( $targetUser instanceof \Fenchy\UserBundle\Entity\User) )
                return new Response('',401);
                        $review = new \Fenchy\NoticeBundle\Entity\Review();
                        
            $form = $this->createForm(new LeaveReviewProfileType(), $review);
            $form->bindRequest($request);
            if ( $form->isValid() ) {

                $userLoggedIn->addOwnReview($review);

                $this->get('fenchy.reputation_counter')
                        ->update($userLoggedIn, \Fenchy\UserBundle\Services\ReputationCounter::TYPE_OWN_REVIEW);
                $em->persist($userLoggedIn);
                if($review->getType() === Review::TYPE_POSITIVE) {
                    $targetUser->addReview($review);
                    $this->get('fenchy.reputation_counter')
                            ->update($targetUser, \Fenchy\UserBundle\Services\ReputationCounter::TYPE_REVIEW);
                    $em->persist($targetUser);
                }
                $review->setAuthor($userLoggedIn);
                $review->setAboutUser($targetUser);
                $em->persist($review);
                $em->flush();
                $response = $this->render('FenchyRegularUserBundle:Partials:postedReview.html.twig');
                $response->headers->set('validate','pass');
                return $response;

            }
            else
                // TODO: If listings reviews to be enabled - implement correct 
                // validation headers.
                return new Response('',401);
        } else {
            return new Response('',401);
        }
    }
    
    
    public function userReviewsListAction( $userId, $noticeId ) {
        $request = $this->getRequest();
        if ( !$request->isMethod('POST') ) {
            return new Response('',401);
        }
        $userLoggedIn = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        
        if ( $userId != NULL ) {
            $userOther = $em->getRepository('UserBundle:User')->getAllData( $userId );
            if ( ! $userOther instanceof \Fenchy\UserBundle\Entity\User )
                return new Response('',401);
            $displayUser = $userOther;
            $usersOwnProfile = 0;
        }
        else {
            if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
                return new Response('',401);
            $displayUser = $userLoggedIn;
            $usersOwnProfile = 1;        
        }
        
        $reviewRepo = $em->getRepository('FenchyNoticeBundle:Review');
        $pagination = $this->container->getParameter('reviews_pagination');
        
        $reloadRq = $request->get('reload');
        if ( $reloadRq == 'reviews' ) {
            $rType = $request->get('rtype');
     
            $criteria = array('aboutUser'=>$displayUser->getId());
            if ( $rType=='p' )
                $criteria['type'] = Review::TYPE_POSITIVE;
            if ( $rType=='n' )
                $criteria['type'] = Review::TYPE_NEGATIVE;
            if ( $noticeId )
                $criteria['aboutNotice'] = $noticeId;
            
            $freshReviews = $reviewRepo->findByInJSON(
                $this->container->get('router'),
                $criteria,
                array('created_at'=>'DESC'),$pagination+1,0);
            $freshReveiwsCount = $reviewRepo->findCount( $criteria );
            $response = new Response(json_encode( array('count'=>$freshReveiwsCount, 'list'=>$freshReviews) ));
            $response->headers->set('Content-Type', 'application/json');
            return $response;         
        } elseif( $reloadRq == 'reviewsoo' ) {
     
            $criteria = array('author'=>$displayUser->getId());
            
            $freshReviews = $reviewRepo->findByInJSON(
                $this->container->get('router'),
                $criteria,
                array('created_at'=>'DESC'),$pagination+1,0);
            $freshReveiwsCount = $reviewRepo->findCount( $criteria );
            $response = new Response(json_encode( array('count'=>$freshReveiwsCount, 'list'=>$freshReviews) ));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
            
        }

        $loadMoreRq = $request->get('loadmore');
        if ( $loadMoreRq == 'reviews' &&
            ($rangeMin=$request->get('min')) !== NULL && ($rangeMax=$request->get('max')) !== NULL ) {
            $rType = $request->get('rtype');
     
            $criteria = array('aboutUser'=>$displayUser->getId());
            if ( $rType=='p' )
                $criteria['type'] = Review::TYPE_POSITIVE;
            if ( $rType=='n' )
                $criteria['type'] = Review::TYPE_NEGATIVE;
            if ( $noticeId )
                $criteria['aboutNotice'] = $noticeId;
                        
            $freshReviews = $reviewRepo->findByInJSON(
                $this->container->get('router'),
                $criteria,
                array('created_at'=>'DESC'),$pagination+1,$rangeMin);
            $freshReveiwsCount = $reviewRepo->findCount( $criteria );
            $response = new Response(json_encode( array('count'=>$freshReveiwsCount, 'list'=>$freshReviews) ));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if ( $loadMoreRq == 'reviewsoo' &&
            ($rangeMin=$request->get('min')) !== NULL && ($rangeMax=$request->get('max')) !== NULL ) {
            $freshReviewsOO = $reviewRepo->findByInJSON(
                $this->container->get('router'),
                array('author'=>$displayUser->getId()),
                array('created_at'=>'DESC'), $pagination+1,$rangeMin);
            $freshReviewsOOCount = $reviewRepo->findCount( array('author'=>$displayUser->getId()) );
            $response = new Response( json_encode( array('count'=>$freshReviewsOOCount, 'list'=>$freshReviewsOO) ) );
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        
    }
    
    public function userListingsListAction( $userId ) {
        $request = $this->getRequest();
        if ( !$request->isMethod('POST') ) {
            return new Response('',401);
        }
        $userLoggedIn = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        
        if ( $userId != NULL ) {
            $userOther = $em->getRepository('UserBundle:User')->getAllData( $userId );
            if ( ! $userOther instanceof \Fenchy\UserBundle\Entity\User )
                return new Response('',401);
            $displayUser = $userOther;
            $usersOwnProfile = 0;
        }
        else {
            if ( ! $userLoggedIn instanceof \Fenchy\UserBundle\Entity\User )
                return new Response('',401);
            $displayUser = $userLoggedIn;
            $usersOwnProfile = 1;        
        }
        
        $noticeRepo = $em->getRepository('FenchyNoticeBundle:Notice');
        $pagination = $this->container->getParameter('reviews_pagination');
        
        $loadMoreRq = $request->get('loadmore');
        if ( $loadMoreRq == 'listings'  &&
            ($rangeMin=$request->get('min')) !== NULL && ($rangeMax=$request->get('max')) !== NULL ) {
            $freshListingsObj = $noticeRepo->findBy(
                array('user'=>$displayUser->getId()),
                array('created_at'=>'DESC'), $pagination+1, $rangeMin);
            $freshListings = array();
            foreach ( $freshListingsObj as $listingObj ) {
                $listingAssoc = $listingObj->toJsonArray();
                $listingAssoc['url'] = $this->container->get('router')
                    ->generate('fenchy_notice_show_slug', array(
                        'slug' => $listingObj->getSlug(),
                        'year' => $listingObj->getStartDate()->format('Y'),
                        'month' => $listingObj->getStartDate()->format('m'),
                        'day' => $listingObj->getStartDate()->format('d')
                    ));
                $freshListings[] = $listingAssoc;
            }
            $freshListingsCount = $noticeRepo->findCount( array('user'=>$displayUser->getId()) );
            $response = new Response( json_encode( array('count'=>$freshListingsCount, 'list'=>$freshListings) ) );
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        
    }
    
    protected function reviewNotification(Review $review, User $sender, Notice $notice) {
        
        $user = $review->getAboutNotice()->getUser();
        
        $notifications = $user->getNotifications();
        $niterator = $notifications->getIterator();

        $reviewNotification = false;
        foreach ($niterator as $onen) {
            if ($onen->getName() == 'reference')
                $reviewNotification = true;
        }

        if ($reviewNotification) {

            $interval = $user->getNotificationGroupIntervals()->first();
            if ($interval != null)
                $interval_val = $interval->getInterval();
            else
                $interval_val = null;

            $data = array(
                'sender' => $sender,
                'user' => $user,
                'notice' => $notice,
                'review' => $review
            );
            
            if ($interval_val === NotificationGroupInterval::INTERVAL_DAILY) {

                $queue_processing_hour = $this->container->getParameter('notification_queue_processing_hour');

                $now = new \DateTime;
                $now_hour = (integer) $now->format('G');

                $send_after = new \DateTime;
                if ($now_hour >= $queue_processing_hour) {
                    $send_after->modify('+1 day');
                }
                $send_after->setTime($queue_processing_hour, 0, 0);

                $toQueue = new NotificationQueue;
                $toQueue->setSendAfter($send_after)
                        ->setFromAddress($this->container->getParameter('from_email'))
                        ->setFromName($this->container->getParameter('from_name'))
                        ->setToAddress($user->getEmail())
                        ->setSubject($this->get('translator')->trans('regularuser.reviews.email.subject', array('%username%' => $sender->getRegularUser()->getFirstname())))
                        ->setBodyHtml($this->renderView('FenchyRegularUserBundle:Notifications:reviewEmailHTML.html.twig', $data))
                        ->setBodyPlain($this->renderView('FenchyRegularUserBundle:Notifications:reviewEmailPlain.html.twig', $data));
                $em = $this->getDoctrine()->getManager();
                $em->persist($toQueue);
                $em->flush();
            } elseif ($interval_val === NotificationGroupInterval::INTERVAL_IMMEDIATELY) {
                $emailNotification = \Swift_Message::newInstance()
                        ->setFrom($this->container->getParameter('from_email'), $this->container->getParameter('from_name'))
                        ->setTo($user->getEmail())
                        ->setSubject($this->get('translator')->trans('regularuser.reviews.email.subject', array('%username%' => $sender->getRegularUser()->getFirstname())))
                        ->setBody($this->renderView('FenchyRegularUserBundle:Notifications:reviewEmailHTML.html.twig', $data), 'text/html')
                        ->addPart($this->renderView('FenchyRegularUserBundle:Notifications:reviewEmailPlain.html.twig', $data), 'text/plain');
                $mailer = $this->get('mailer');
                $mailer->send($emailNotification);
            }
        }
        
    }
    
}
