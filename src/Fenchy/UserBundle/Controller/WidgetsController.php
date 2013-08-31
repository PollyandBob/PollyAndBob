<?php

namespace Fenchy\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Yaml\Parser;

/**
 * 
 * @author Mateusz Krowiak <mkrowiak@pgs-soft.com>
 */
class WidgetsController extends Controller {

    /**
     * Render user's top-panel
     * @return Response
     */
    public function userPanelAction() {
        
        //fetch user's entities
        $user = $this->get('security.context')->getToken()->getUser();
        $user_regular = $user->getUserRegular();
        
        //user's full name
        $name = $user_regular->getFirstName() . " " . $user_regular->getLastName();
       
        //count reviews
        $rev_repo = $this->getDoctrine()->getEntityManager()->getRepository("FenchyNoticeBundle:Review");
        
        if($user instanceof \Fenchy\UserBundle\Entity\User) {
            $rev_count = $rev_repo->countUnreadUsersReviews($user);
        } else {
            $rev_count = 0;
        }
        
        $rev_count = $rev_count>99?99:$rev_count;
        
        
        return $this->render("UserBundle:Widgets:userPanel.html.twig", array(
            'name' => $name,
            'thumb' => $user_regular->getAvatar(),
            'rev_unread_count' => $rev_count
        ));
    }
    
    public function testAction() {
        
        $yaml = new Parser();

        $value = $yaml->parse(file_get_contents('C:\xampp\htdocs\fenchy\app\Resources\translations\messages.pl.yml'));
        
        var_dump($value);
        exit;
        
    }

}
