<?php

namespace Fenchy\UserBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Fenchy\UserBundle\Entity\User;
use Fenchy\UserBundle\Entity\Reference;

class UserReferenceListener
{    
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();  
        
        if ($entity instanceof \Fenchy\UserBundle\Entity\User) {
            if ($entity->getJustEnabled()) {    //return true if user has been activated
             
                if (NULL != $ref = $entity->enableReference()) {
                    $em->persist($ref);
                    $em->flush();
                    
                    // add recerece user to contacts
                    $ru = $entity->getRegularUser();
                    $fru = $ref->getRefUser()->getRegularUser();
                    if (null !== $ru && null !== $fru) {
                        $ru->addMyFriend($fru);
                        $em->persist($ru);
                        $em->flush();
                    }
                }
            }
        }         
    }
    
    public function prePersist(LifecycleEventArgs $args) 
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        
        if ($entity instanceof \Fenchy\UserBundle\Entity\User) {
            //check if reference exsists
            
            if($entity->getFacebookId()) {
                $refByFacebook = $em->getRepository('UserBundle:Reference')
                        ->findBy(array('new_user_fb_id' => $entity->getFacebookId()));
            } else {
                $refByFacebook = array();
            }

            if($entity->getEmail()) {
                $refByEmail = $em->getRepository('UserBundle:Reference')
                        ->findBy(array('new_user_email' => $entity->getEmail()));
            } else {
                $refByEmail = array();
            }

            if (count($refByFacebook) || count($refByEmail)) {

                foreach($refByFacebook as $ref) {

                    $entity->addGotReference($ref);
                }
                foreach($refByEmail as $ref) {

                    $entity->addGotReference($ref);
                }
            }
            
        }    
    }
    
}