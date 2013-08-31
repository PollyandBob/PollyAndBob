<?php

namespace Fenchy\NoticeBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Fenchy\NoticeBundle\Entity\PropertyType;

class PropertyTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        
        $pt = new PropertyType();
        $pt->setElement(PropertyType::ELEMENT_SELECT)
                ->setName('for')
                ->setExpanded(FALSE)
                ->setMultiple(FALSE)
                ->setOptions(array(
                    1 => 'to_swap',
                    2 => 'to_lend',
                    3 => 'to_use',
                    4 => 'to_give_away'
                ));
        
        $pt2 = new PropertyType();
        $pt2->setElement(PropertyType::ELEMENT_SELECT)
                ->setName('what')
                ->setExpanded(FALSE)
                ->setMultiple(FALSE)
                ->setOptions(array(
                    1 => 'sports',
                    2 => 'party',
                    3 => 'arts',
                    4 => 'music',
                    5 => 'other'
                ));
        
        $pt5 = new PropertyType();
        $pt5->setElement(PropertyType::ELEMENT_SELECT)
                ->setName('direction')
                ->setExpanded(FALSE)
                ->setMultiple(FALSE)
                ->setOptions(array(
                    1 => 'offer',
                    2 => 'need'
                ));
        
        $price = new PropertyType();
        $price->setElement(PropertyType::ELEMENT_STRING)
                ->setName('price');
        
        $manager->persist($pt);
        $manager->persist($pt2);
        $manager->persist($pt5);
        $manager->persist($price);

        
        $this->addReference('goods_to', $pt);
        $this->addReference('event_type', $pt2);
        $this->addReference('direction', $pt5);
        $this->addReference('price', $price);
        
        $manager->flush();
    }
    
    public function getOrder(){
        return 1;
    }
}