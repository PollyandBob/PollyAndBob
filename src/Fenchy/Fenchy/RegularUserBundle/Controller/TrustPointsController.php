<?php

namespace Fenchy\RegularUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Fenchy\RegularUserBundle\Entity\UserRegular;
use Fenchy\UserBundle\Entity\Reference;


class trustPointsController extends Controller
{

    /**
     * @Template
     */
    public function indexAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        
        $trustPoints = 0;
        
        $numberOfFriends = $user->getRegularUser()->countMyFriends();
        $trustPoints += $numberOfFriends;
        
        $ref = $this->getDoctrine()
                    ->getManager()
                    ->getRepository('UserBundle:Reference')
                    ->findBy(array(
                        'ref_user' => $user,
                        'active' => true)); 
        
        $numberOfReferences = count($ref);
        $trustPoints += $numberOfReferences;

        return $this->render('FenchyRegularUserBundle:TrustPoints:index.html.twig',array(
                                                                                    'nrTrustPoints' => $trustPoints,
                                                                                    'nrReferences'  => $numberOfReferences,
                                                                                    'nrFriends'  => $numberOfFriends,
                                                                                    ));
    }
    
}