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
       
        //count requests
        $req_repo = $this->getDoctrine()->getEntityManager()->getRepository("FenchyNoticeBundle:Request");
        
        if($user instanceof \Fenchy\UserBundle\Entity\User) {
            $req_count = $req_repo->countUnreadUsersRequests($user);
            $my_req_count = $req_repo->countUnreadUsersStatusRequests($user);
        } else {
            $req_count = 0;
        }
        
        $req_count = $req_count>99?99:$req_count;
        
        $alertmsg =  true;
        
        if($user->getActivity() < 400 )
        {
        	$alertmsg =  false;
        }
        
        return $this->render("UserBundle:Widgets:userPanel.html.twig", array(
            'name' => $name,
            'thumb' => $user_regular->getAvatar(),
            'req_unread_count' => $req_count,
        	'my_req_count' =>$my_req_count,
        	'alertmsg' =>  $alertmsg,
        ));
    }
    
    public function testAction() {
        
        $yaml = new Parser();

        $value = $yaml->parse(file_get_contents('C:\xampp\htdocs\fenchy\app\Resources\translations\messages.pl.yml'));
        
        var_dump($value);
        exit;
        
    }

}
