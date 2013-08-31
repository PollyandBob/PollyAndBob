<?php

namespace Fenchy\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Fenchy\UserBundle\Entity\Notification;

class LoadNotificationData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $notify = array(
            'message'           => 'emails_when_someone_sends_you_a_message',
            'request'           => 'emails_when_someone_sends_you_a_request',
            'contact_request'   => 'emails_when_someone_sends_you_a_contact_request',
            'reference'         => 'emails_when_someone_writes_a_reference_about_you'
        );
        
        foreach($notify as $name => $desc) {
            
            $notification = new Notification();
            $notification->setName($name);
            $notification->setDescription($desc);
            $notification->setNotificationGroup($manager->merge($this->getReference('notificationGroup-1')));
            $manager->persist($notification);
        }
        
        $manager->flush();

    }
    
    public function getOrder()
    {
        return 2;
    }
}