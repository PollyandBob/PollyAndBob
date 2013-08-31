<?php

namespace Fenchy\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Fenchy\UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('meisele');
        $user->setEmail('matthias@fenchy.com');
        $user->setPlainPassword('meisele');
        $user->addRole('ROLE_ADMIN');
        $user->addActivity(5);//for gender
        $user->setEnabled(true);
		$user->setRegisteredWith('email');
		$user->setRegisteredWithId('matthias@fenchy.com');
        $user->setRegularUser($manager->merge($this->getReference('meisele')));
        $manager->persist($user);
        
        $this->addReference('user-1', $user);
        
        $user = new User();
        $user->setUsername('sakstinat');
        $user->setEmail('sakstinat@fenchy.com');
        $user->setPlainPassword('sakstinat');
        $user->addRole('ROLE_ADMIN');
        $user->addActivity(5);//for gender
        $user->setEnabled(true);
		$user->setRegisteredWith('email');
		$user->setRegisteredWithId('sakstinat@fenchy.com');		
        $user->setRegularUser($manager->merge($this->getReference('sakstinat')));
        $manager->persist($user);
        
        $this->addReference('user-2', $user);
        
        $user = new User();
        $user->setUsername('vsiems');
        $user->setEmail('vsiems@fenchy.com');
        $user->setPlainPassword('vsiems');
        $user->addRole('ROLE_ADMIN');
        $user->addActivity(5);//for gender
        $user->setEnabled(true);
		$user->setRegisteredWith('email');
		$user->setRegisteredWithId('vsiems@fenchy.com');			
        $user->setRegularUser($manager->merge($this->getReference('vsiems')));
        $manager->persist($user);
        
        $this->addReference('user-3', $user);
        
        $user = new User();
        $user->setUsername('jdeblasse');
        $user->setEmail('jdeblasse@fenchy.com');
        $user->setPlainPassword('jdeblasse');
        $user->addRole('ROLE_ADMIN');
        $user->addActivity(5);//for gender
        $user->setEnabled(true);
		$user->setRegisteredWith('email');
		$user->setRegisteredWithId('jdeblasse@fenchy.com');
        $user->setRegularUser($manager->merge($this->getReference('jdeblasse')));
        $manager->persist($user);
        
        $this->addReference('user-4', $user);
        
        $manager->flush();

    }
    
    public function getOrder()
    {
        return 2;
    }
}