<?php

namespace Fenchy\RegularUserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Fenchy\RegularUserBundle\Entity\UserRegular;

class LoadUserRegularData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user = new UserRegular();
        $user->setGender(1);
        $user->setFirstname('Matthias');
        $user->setLastname('Eisele');
        
        $this->addReference('meisele', $user);
                
        $manager->persist($user);
        
        $user = new UserRegular();
        $user->setGender(1);
        $user->setFirstname('Volker');
        $user->setLastname('Volker');
        
        $this->addReference('vsiems', $user);
                
        $manager->persist($user);
        
        $user = new UserRegular();
        $user->setGender(1);
        $user->setFirstname('sakstinat');
        $user->setLastname('sakstinat');
        
        $this->addReference('sakstinat', $user);
                
        $manager->persist($user);
        
        $user = new UserRegular();
        $user->setGender(1);
        $user->setFirstname('James');
        $user->setLastname('DeBlasse');
        
        $this->addReference('jdeblasse', $user);
                
        $manager->persist($user);
        
        $manager->flush();

    }
    
    public function getOrder()
    {
        return 1;
    }
}