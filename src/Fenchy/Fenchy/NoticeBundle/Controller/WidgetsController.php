<?php

namespace Fenchy\NoticeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class WidgetsController extends Controller {

    public function userListingsAction($notice_id) {

        $notice = $this->getDoctrine()->getEntityManager()->getRepository('FenchyNoticeBundle:Notice')->find($notice_id);

        $user = null;
        
        $repo = $this->getDoctrine()->getEntityManager()->getRepository('FenchyNoticeBundle:Notice');
        $recent_listings = $repo->getUserRecentListings(3, $notice);
        
        if($recent_listings) {
            $user = $recent_listings[0]->getUser();
        }
        
        return $this->render('FenchyNoticeBundle:Widgets:userListings.html.twig', array(
            'listings' => $recent_listings,
            'user' => $user
        ));
    }
    
    public function similarListingsAction($notice_id) {

        $notice = $this->getDoctrine()->getEntityManager()->getRepository('FenchyNoticeBundle:Notice')->find($notice_id);
        if ( ! $notice instanceof \Fenchy\NoticeBundle\Entity\Notice )
            return new Response('');
      
        $repo = $this->getDoctrine()->getEntityManager()->getRepository('FenchyNoticeBundle:Notice');
        $similar_listings = $repo->getSimilarListings(3, $notice);
        
        return $this->render('FenchyNoticeBundle:Widgets:similarListings.html.twig', array(
                    'listings' => $similar_listings
        ));
    }

}
