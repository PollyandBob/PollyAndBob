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
                ->setName('findgoods')
                ->setExpanded(FALSE)
                ->setMultiple(FALSE)
                ->setOptions(array(
                    1 => 'to_sell',
                    2 => 'to_lend',
                    3 => 'to_use',
                    4 => 'to_swap',
                	5 => 'to_give'	
                ));
      	$pt1 = new PropertyType();
        $pt1->setElement(PropertyType::ELEMENT_SELECT)
                ->setName('offergoods')
                ->setExpanded(FALSE)
                ->setMultiple(FALSE)
                ->setOptions(array(
                		1 => 'buy',
                		2 => 'propose_price',
                		3 => 'borrow_bring',
                		4 => 'use_it',
                		5 => 'swap_it',
                		6 => 'take_free'
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
        
        $pt6 = new PropertyType();
       	$pt6->setElement(PropertyType::ELEMENT_SELECT)
                ->setName('contact')
                ->setExpanded(FALSE)
                ->setMultiple(FALSE)
                ->setOptions(array(
                		1 => 'contact_me',
                ));
        
        $pt7 = new PropertyType();                
      	$pt7->setElement(PropertyType::ELEMENT_SELECT)
                ->setName('offerservice')
                ->setExpanded(FALSE)
                ->setMultiple(FALSE)
                ->setOptions(array(
                		1 => 'buy',
                		2 => 'propose_time_price',
                ));
       
       	$pt8 = new PropertyType();
      	$pt8->setElement(PropertyType::ELEMENT_SELECT)
                ->setName('findservice')
                ->setExpanded(FALSE)
                ->setMultiple(FALSE)
                ->setOptions(array(                	
                		1 => 'propose_time_price',
                ));
        $pt9 = new PropertyType();
       	$pt9->setElement(PropertyType::ELEMENT_SELECT)
                ->setName('findevent')
                ->setExpanded(FALSE)
                ->setMultiple(FALSE)
                ->setOptions(array(
                		1 => 'buy_a_spot',
                		2 => 'propose_price',
                		3 => 'reserve_spot',
                		4 => 'take_part'
                ));
                                
                
        $price = new PropertyType();
        $price->setElement(PropertyType::ELEMENT_STRING)
                ->setName('price');
        
        $manager->persist($pt);
        $manager->persist($pt1);
        $manager->persist($pt2);
        $manager->persist($pt5);
        $manager->persist($price);
        $manager->persist($pt6);
        $manager->persist($pt7);
        $manager->persist($pt8);
        $manager->persist($pt9);

        
        $this->addReference('goods_to', $pt);
        $this->addReference('goods_from', $pt1);
        $this->addReference('event_type', $pt2);
        $this->addReference('direction', $pt5);
        $this->addReference('price', $price);
        $this->addReference('contact', $pt6);
        $this->addReference('offerservice', $pt7);
        $this->addReference('findservice', $pt8);
        $this->addReference('findevent', $pt9);
        
        $manager->flush();
//         a:5:{i:1;s:7:"to_sell";i:2;s:7:"to_lend";i:3;s:6:"to_use";i:4;s:7:"to_swap";i:5;s:7:"to_give";}
        
//         a:4:{i:1;s:7:"to_swap";i:2;s:7:"to_lend";i:3;s:6:"to_use";i:4;s:12:"to_give_away";}
    }
    
    public function getOrder(){
        return 1;
    }
}