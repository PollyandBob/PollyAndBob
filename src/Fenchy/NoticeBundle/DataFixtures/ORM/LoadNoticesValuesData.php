<?php

namespace Fenchy\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Fenchy\NoticeBundle\Entity\Value;

class LoadNoticesValuesData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        
        $value = new Value();
        $value->setNotice($manager->merge($this->getReference('notice-1')));
        $value->setProperty($manager->merge($this->getReference('event_type')));
        $value->setValue('3');
        $manager->persist($value);
        

        $value = new Value();
        $value->setNotice($manager->merge($this->getReference('notice-2')));
        $value->setProperty($manager->merge($this->getReference('event_type')));
        $value->setValue('4');
        $manager->persist($value);
        
        
        
        $value = new Value();
        $value->setNotice($manager->merge($this->getReference('notice-3')));
        $value->setProperty($manager->merge($this->getReference('direction')));
        $value->setValue('1');
        $manager->persist($value);                

        $value = new Value();
        $value->setNotice($manager->merge($this->getReference('notice-3')));
        $value->setProperty($manager->merge($this->getReference('goods_to')));
        $value->setValue('4');
        $manager->persist($value);  
        
        $value = new Value();
        $value->setNotice($manager->merge($this->getReference('notice-3')));
        $value->setProperty($manager->merge($this->getReference('price')));
        $value->setValue('€10');
        $manager->persist($value);       
        

        
        $value = new Value();
        $value->setNotice($manager->merge($this->getReference('notice-4')));
        $value->setProperty($manager->merge($this->getReference('direction')));
        $value->setValue('2');
        $manager->persist($value);                

        $value = new Value();
        $value->setNotice($manager->merge($this->getReference('notice-4')));
        $value->setProperty($manager->merge($this->getReference('goods_to')));
        $value->setValue('2');
        $manager->persist($value);  
        
        $value = new Value();
        $value->setNotice($manager->merge($this->getReference('notice-4')));
        $value->setProperty($manager->merge($this->getReference('price')));
        $value->setValue('€14.99');
        $manager->persist($value);        
        
        $manager->flush();

    }
    
    public function getOrder()
    {
        return 6;
    }
}