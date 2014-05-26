<?php

namespace Fenchy\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\HttpFoundation\Response;

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
        $em = $this->getDoctrine()->getEntityManager();
        //count requests
        $req_repo = $this->getDoctrine()->getEntityManager()->getRepository("FenchyNoticeBundle:Request");
        
        $repo = $em->getRepository('FenchyNoticeBundle:Notice');
        $listings = $repo->getUserNotices($user);
        
        
        if($user instanceof \Fenchy\UserBundle\Entity\User) {
            $req_count = $req_repo->countUnreadUsersRequests($user, $listings);
            $my_req_count = $req_repo->countUnreadUsersStatusRequests($user,$listings);
        } else {
            $req_count = 0;
        }
        
        $req_count = $req_count>99?99:$req_count;
        
        $alertmsg =  true;
        
        if($user->getActivity() < 400 )
        {
        	$alertmsg =  false;
        }
        $managertype = $em->getRepository('FenchyRegularUserBundle:UserRegular')->getManagerType($user);
        
        
        
        return $this->render("UserBundle:Widgets:userPanel.html.twig", array(
            'name' => $name,
            'thumb' => $user_regular->getAvatar(),
            'req_unread_count' => $req_count,
        	'my_req_count' =>$my_req_count,
        	'alertmsg' =>  $alertmsg,
        	'managertype' => $managertype[0],
        	'location'=> $user->getLocation()
        ));
    }
    
    public function setLogoutAction()
    {
    	$user = $this->get('security.context')->getToken()->getUser();
    	$em = $this->getDoctrine()->getEntityManager();
        if($user)
        {
            $user->setIsLogin(false);
            $em->persist($user);
            $em->flush($user);
        }
    	return new Response();
    } 
    public function testAction() {
        
        $yaml = new Parser();

        $value = $yaml->parse(file_get_contents('C:\xampp\htdocs\fenchy\app\Resources\translations\messages.pl.yml'));
        
        var_dump($value);
        exit;
        
    }

}
