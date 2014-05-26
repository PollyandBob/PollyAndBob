<?php

namespace Fenchy\NoticeBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Fenchy\NoticeBundle\Entity\Type;
use Doctrine\Common\Collections\ArrayCollection;

class TypeData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $help = new Type();
        $helpProp = new ArrayCollection();
        
        $helpProp->add($manager->merge($this->getReference('direction')));
        
        $help->setName('help')
                ->setSequence(2)
                ->setProperties($helpProp)
                ->setLocationChangeAvailable(FALSE);
        
        $manager->persist($help);
        $this->addReference('help', $help);
        
        
        $good = new Type();
        $goodProp = new ArrayCollection();
        
        $goodProp->add($manager->merge($this->getReference('direction')));
        $goodProp->add($manager->merge($this->getReference('goods_to')));
        $goodProp->add($manager->merge($this->getReference('price')));
        
        $good->setName('goods')
                ->setSequence(1)
                ->setProperties($goodProp)
                ->setLocationChangeAvailable(FALSE);
        
        $manager->persist($good);
        $this->addReference('good', $good);
        
        
        $event = new Type();
        $eventProp = new ArrayCollection();
        
        $eventProp->add($manager->merge($this->getReference('event_type')));
        
        $event->setName('events')
                ->setSequence(3)
                ->setProperties($eventProp)
                ->setLocationChangeAvailable(TRUE);
        
        $manager->persist($event);
        $this->addReference('event', $event);
        

        $other = new Type();
        $otherProp = new ArrayCollection();
        
        $other->setName('others')
                ->setSequence(4)
                ->setProperties($otherProp)
                ->setLocationChangeAvailable(FALSE);
        
        $manager->persist($other);
        $this->addReference('other', $other);
        
        $manager->flush();
    }
    
    public function getOrder(){
        return 2;
    }
}