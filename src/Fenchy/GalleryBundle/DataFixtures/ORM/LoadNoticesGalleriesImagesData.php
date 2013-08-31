<?php

namespace Fenchy\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Fenchy\GalleryBundle\Entity\Image;

class LoadNoticesGalleriesImagesData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        
        $image = new Image();
        $image->setGallery($manager->merge($this->getReference('gallery-1')));
        $image->setName('517e29877a76e0.jpeg');
        $image->setSequence(0);
        $manager->persist($image);

        
        $image = new Image();
        $image->setGallery($manager->merge($this->getReference('gallery-2')));
        $image->setName('517e2a07e37fb0.jpeg');
        $image->setSequence(0);
        $manager->persist($image);        
        

        
        $image = new Image();
        $image->setGallery($manager->merge($this->getReference('gallery-3')));
        $image->setName('517e2a554f6460.jpeg');
        $image->setSequence(0);
        $manager->persist($image);                
        
        
        $image = new Image();
        $image->setGallery($manager->merge($this->getReference('gallery-4')));
        $image->setName('517e2a99c1fc50.jpeg');
        $image->setSequence(0);
        $manager->persist($image);          
        
        $manager->flush();

    }
    
    public function getOrder()
    {
        return 7;
    }
}