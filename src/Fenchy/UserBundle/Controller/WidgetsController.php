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
        
        $templist = array();
        $templist1 = array();
        $d=0; $j=0;
        foreach ($listings as $listing)
        {
            if($listing->getUserGroup()!=null)
                $templist[$d++] = $listing;
        }
        $requests = $req_repo->getNoticeIds($user);
        
        $reqlistings = array();
        $j=0;
        foreach ($requests as $request)
    	{
    		if($request->getAboutNotice())
    		{
    			$reqlistings[$j++]  = $request->getAboutNotice();
    		}
    	}
    	
    	foreach($reqlistings as $k => $listing)
    	{
    		foreach($reqlistings as $key => $value)
    		{
    			if($k != $key && $listing->getId() == $value->getId())
    			{
    				unset($reqlistings[$k]);
    			}
    		}
    	}
//    	foreach ($reqlistings as $k => $reqlisting)
//    	{
//    		if($reqlisting->getUserGroup()!=null)
//    		{
//    			$templist1[$j++] = $reqlisting;
//    		}
//    	}

        
        if($user instanceof \Fenchy\UserBundle\Entity\User) {
            $req_count = $req_repo->countUnreadUsersRequests($user, $templist);
            $my_req_count = $req_repo->countUnreadUsersStatusRequests($user,$templist1);
            $review_count = $em->getRepository('FenchyNoticeBundle:Review')->countReview($user);
            $comment_count = $em->getRepository('FenchyNoticeBundle:Comment')->countComment($user);
            $requestmessage_count = $em->getRepository('FenchyNoticeBundle:RequestMessages')->countRequestMessage($user);
            
        } else {
            $req_count = 0;
            $my_req_count = 0;
            $review_count  = 0;
            $comment_count = 0;
            $requestmessage_count = 0;
        }
        
        $req_count = $req_count>99?99:$req_count;
        
        $alertmsg =  true;
        
        if($user->getActivity() < 400 )
        {
        	$alertmsg =  false;
        }
        $managertype = $em->getRepository('FenchyRegularUserBundle:UserRegular')->getManagerType($user);
        
        $msg_count = $em->getRepository('FenchyMessageBundle:Message')->countHeader($user);
        
        
        return $this->render("UserBundle:Widgets:userPanel.html.twig", array(
            'name' => $name,
            'thumb' => $user_regular->getAvatar(),
            'req_unread_count' => $req_count,
        	'my_req_count' =>$my_req_count,
                'review_count' => $review_count,
                'comment_count' => $comment_count,
                'requestmessage_count' => $requestmessage_count,
        	'alertmsg' =>  $alertmsg,
        	'managertype' => $managertype[0],
        	'location'=> $user->getLocation(),
                'msg_count' => $msg_count
        ));
    }
    
    public function setLogoutAction()
    {
    	$user = $this->get('security.context')->getToken()->getUser();
    	$em = $this->getDoctrine()->getEntityManager();
        if($user instanceof \Fenchy\UserBundle\Entity\User)
        {
            $user->setChatwindows(NULL);
            $user->setIsLogin(false);
            $em->persist($user);
            $em->flush($user);
        }
//        $session = $this->getRequest()->getSession();
//        $users = $this->getDoctrine()->getEntityManager()->getRepository('UserBundle:User')->findAll();
//        foreach($users as $user1)
//        {
//            if($session->get('id'.$user1->getId())!= null)
//            {
//                $session->remove('id'.$user1->getId());
//            }
//        }
    	return new Response();
    } 
    public function testAction() {
        
        $yaml = new Parser();

        $value = $yaml->parse(file_get_contents('C:\xampp\htdocs\fenchy\app\Resources\translations\messages.pl.yml'));
        
        var_dump($value);
        exit;
        
    }

}
