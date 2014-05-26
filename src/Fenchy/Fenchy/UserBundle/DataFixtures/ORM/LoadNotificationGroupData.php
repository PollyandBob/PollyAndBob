<?php

namespace Fenchy\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Fenchy\UserBundle\Entity\NotificationGroup;

class LoadNotificationGroupData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {     
        $group = new NotificationGroup();
        $group->setName('interval');
        $manager->persist($group);
        
        $manager->flush();
        
        $this->addReference('notificationGroup-1', $group);

    }
    
    public function getOrder()
    {
        return 1;
    }
}