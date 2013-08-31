<?php

namespace Fenchy\RegularUserBundle\Twig;

use Fenchy\UserBundle\Entity\Reference;


class FenchyRegularUserExtension extends \Twig_Extension
{
    private $container;
    
    public function __construct($container)
    {
        $this->container = $container;
    }
    
    public function getFunctions()
    {
        return array(
            'countInvitations' => new \Twig_Function_Method($this, 'countInvitations'),
            'trustPoints' => new \Twig_Function_Method($this, 'trustPoints'),
        );
    }
    
    public function countInvitations()
    {
        $user = $this->container->get('security.context')->getToken()->getUser()->getRegularUser();
        $repo = $this->container->get('doctrine.orm.entity_manager')->getRepository('FenchyRegularUserBundle:Invitation');
        return $repo->count($user);
    }
    
    public function trustPoints($id)
    {
        if(NULL === $id) return 0;
        
        $em = $this->container->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('UserBundle:User'); 
        $ref = $em->getRepository('UserBundle:Reference');
        
        $trustPoints = 0;
        $numberOfReferences = 0;
        $numberOfFriends = 0;
        
        $numberOfReferences = $ref->findBy(array(
                            'ref_user' => $id,
                            'active' => true));
        
        $numberOfReferences = count($numberOfReferences);
        
        $numberOfFriends = $user->find($id);
                
        $numberOfFriends = NULL == $numberOfFriends ? 0 :$numberOfFriends->getUserRegular()->countMyFriends();

        $trustPoints += $numberOfFriends + $numberOfReferences;
        
        return $trustPoints; 
    }
    
    public function getName()
    {
        return 'fenchy_regularuser_extension';
    }
}