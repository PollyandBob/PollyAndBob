<?php

namespace Fenchy\UserBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Fenchy\UserBundle\Entity\User,
    Fenchy\UserBundle\Entity\Notification,
    Fenchy\UserBundle\Entity\NotificationGroup,
    Fenchy\UserBundle\Entity\NotificationGroupInterval;

class UserNotificationListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if ($entity instanceof \Fenchy\UserBundle\Entity\User) {
            
            $groups = $em->getRepository('UserBundle:NotificationGroup')->findAll();
            $entity->setUsername($entity->getUsername());
            foreach ($groups as $group) {
                
                $interval = new NotificationGroupInterval();
                $interval->setInterval(NotificationGroupInterval::INTERVAL_IMMEDIATELY)
                        ->setNotificationGroup($group)
                        ->setUser($entity);
            }
        }
        
        if($entity instanceof \Fenchy\UserBundle\Entity\NotificationGroup) {
            
            $users = $em->getRepository('UserBundle:User')->findAll();
            
            foreach ($users as $user) {
                
                $interval = new NotificationGroupInterval();
                $interval->setInterval(NotificationGroupInterval::INTERVAL_IMMEDIATELY)
                        ->setNotificationGroup($entity)
                        ->setUser($user);
            }
        }
    }
}