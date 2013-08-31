<?php

namespace Fenchy\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Fenchy\NoticeBundle\Entity\Notice;

class LoadNoticesData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $notice = new Notice();
        $notice->setType($manager->merge($this->getReference('event')));
        $notice->setUser($manager->merge($this->getReference('user-1')));
        $notice->setTitle('Street Art & Graffiti workshop & tour');
        $notice->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque nec neque arcu, non semper libero. Aliquam erat volutpat. Quisque faucibus odio facilisis nibh ullamcorper pharetra. Quisque porttitor dui sed nisl pharetra ac iaculis orci faucibus. Vivamus scelerisque, nisl sed laoreet vehicula, mauris massa laoreet arcu, non lacinia eros quam porttitor risus. Quisque auctor scelerisque dolor, sit amet commodo est aliquam a. Donec tempus, lacus vitae volutpat sollicitudin, arcu mauris vulputate mauris, et cursus tellus nisl vitae sem. Aliquam viverra libero at ante vestibulum in viverra nunc aliquet. Nullam pharetra molestie pulvinar. In lacus elit, aliquet vulputate lacinia eget, scelerisque ut purus. Fusce luctus mi sed turpis commodo et viverra enim tincidunt. Fusce at arcu nec enim suscipit pellentesque quis a quam. Nam eu enim non massa tincidunt feugiat.

        Phasellus suscipit tincidunt mauris, sit amet porta augue ornare ultrices. Sed ipsum quam, vulputate sit amet porta id, mattis at diam. Vestibulum metus mauris, tempus non consequat eget, condimentum id felis. Praesent elit dolor, pretium vitae lobortis et, consectetur vitae lacus. Maecenas urna nulla, tristique eget vulputate blandit, posuere eget metus. Vestibulum adipiscing lacus sit amet neque imperdiet quis sagittis ante dignissim. Suspendisse nec porttitor odio. Etiam ultrices lectus vitae metus gravida vel mollis felis condimentum. Ut odio sem, mattis id consectetur id, dictum nec justo. Curabitur sed mauris vitae ante luctus dapibus sit amet ut dolor. Vivamus mollis, tellus at tempus convallis, leo risus mattis ipsum, in porttitor mi ante laoreet risus.');
        
        $notice->setDraft(false);
        $notice->setCreatedAt(new \DateTime("2013-04-29 09:00:00"));
        $notice->setStartDate(new \DateTime("2013-04-29 09:00:00"));
        $notice->setEndDate(new \DateTime("2013-04-29 09:00:00"));
        $notice->setOnDashboard(true);

        
        $manager->persist($notice);

        $gallery = $notice->getGallery();
        $this->addReference("gallery-1", $gallery);         
        $this->addReference("notice-1", $notice);
        

        
        $notice = new Notice();
        $notice->setType($manager->merge($this->getReference('event')));
        $notice->setUser($manager->merge($this->getReference('user-1')));
        $notice->setTitle('Festsall Kreuzberg');
        $notice->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque nec neque arcu, non semper libero. Aliquam erat volutpat. Quisque faucibus odio facilisis nibh ullamcorper pharetra. Quisque porttitor dui sed nisl pharetra ac iaculis orci faucibus. Vivamus scelerisque, nisl sed laoreet vehicula, mauris massa laoreet arcu, non lacinia eros quam porttitor risus. Quisque auctor scelerisque dolor, sit amet commodo est aliquam a. Donec tempus, lacus vitae volutpat sollicitudin, arcu mauris vulputate mauris, et cursus tellus nisl vitae sem. Aliquam viverra libero at ante vestibulum in viverra nunc aliquet. Nullam pharetra molestie pulvinar. In lacus elit, aliquet vulputate lacinia eget, scelerisque ut purus. Fusce luctus mi sed turpis commodo et viverra enim tincidunt. Fusce at arcu nec enim suscipit pellentesque quis a quam. Nam eu enim non massa tincidunt feugiat.

        Phasellus suscipit tincidunt mauris, sit amet porta augue ornare ultrices. Sed ipsum quam, vulputate sit amet porta id, mattis at diam. Vestibulum metus mauris, tempus non consequat eget, condimentum id felis. Praesent elit dolor, pretium vitae lobortis et, consectetur vitae lacus. Maecenas urna nulla, tristique eget vulputate blandit, posuere eget metus. Vestibulum adipiscing lacus sit amet neque imperdiet quis sagittis ante dignissim. Suspendisse nec porttitor odio. Etiam ultrices lectus vitae metus gravida vel mollis felis condimentum. Ut odio sem, mattis id consectetur id, dictum nec justo. Curabitur sed mauris vitae ante luctus dapibus sit amet ut dolor. Vivamus mollis, tellus at tempus convallis, leo risus mattis ipsum, in porttitor mi ante laoreet risus.');
        
        $notice->setDraft(false);
        $notice->setCreatedAt(new \DateTime("2013-04-29 09:01:00"));
        $notice->setStartDate(new \DateTime("2013-04-29 09:01:00"));
        $notice->setEndDate(new \DateTime("2013-04-29 09:01:00"));
        $notice->setOnDashboard(true);
        
        $manager->persist($notice);

        $gallery = $notice->getGallery();
        $this->addReference("gallery-2", $gallery);
        $this->addReference("notice-2", $notice);
        

        $notice = new Notice();
        $notice->setType($manager->merge($this->getReference('good')));
        $notice->setUser($manager->merge($this->getReference('user-1')));
        $notice->setTitle('Acoustic Guitar Fender F35+Hard Case');
        $notice->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque nec neque arcu, non semper libero. Aliquam erat volutpat. Quisque faucibus odio facilisis nibh ullamcorper pharetra. Quisque porttitor dui sed nisl pharetra ac iaculis orci faucibus. Vivamus scelerisque, nisl sed laoreet vehicula, mauris massa laoreet arcu, non lacinia eros quam porttitor risus. Quisque auctor scelerisque dolor, sit amet commodo est aliquam a. Donec tempus, lacus vitae volutpat sollicitudin, arcu mauris vulputate mauris, et cursus tellus nisl vitae sem. Aliquam viverra libero at ante vestibulum in viverra nunc aliquet. Nullam pharetra molestie pulvinar. In lacus elit, aliquet vulputate lacinia eget, scelerisque ut purus. Fusce luctus mi sed turpis commodo et viverra enim tincidunt. Fusce at arcu nec enim suscipit pellentesque quis a quam. Nam eu enim non massa tincidunt feugiat.

        Phasellus suscipit tincidunt mauris, sit amet porta augue ornare ultrices. Sed ipsum quam, vulputate sit amet porta id, mattis at diam. Vestibulum metus mauris, tempus non consequat eget, condimentum id felis. Praesent elit dolor, pretium vitae lobortis et, consectetur vitae lacus. Maecenas urna nulla, tristique eget vulputate blandit, posuere eget metus. Vestibulum adipiscing lacus sit amet neque imperdiet quis sagittis ante dignissim. Suspendisse nec porttitor odio. Etiam ultrices lectus vitae metus gravida vel mollis felis condimentum. Ut odio sem, mattis id consectetur id, dictum nec justo. Curabitur sed mauris vitae ante luctus dapibus sit amet ut dolor. Vivamus mollis, tellus at tempus convallis, leo risus mattis ipsum, in porttitor mi ante laoreet risus.');
        
        $notice->setDraft(false);
        $notice->setCreatedAt(new \DateTime("2013-04-29 09:02:00"));
        $notice->setStartDate(new \DateTime("2013-04-29 09:02:00"));
        $notice->setEndDate(new \DateTime("2013-04-29 09:02:00"));
        $notice->setOnDashboard(true);
        
        
        $manager->persist($notice);
        
        $gallery = $notice->getGallery();
        $this->addReference("gallery-3", $gallery); 
        $this->addReference("notice-3", $notice);        
        

        $notice = new Notice();
        $notice->setType($manager->merge($this->getReference('good')));
        $notice->setUser($manager->merge($this->getReference('user-1')));
        $notice->setTitle('Sewing Classes @ Sewing Cafe');
        $notice->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque nec neque arcu, non semper libero. Aliquam erat volutpat. Quisque faucibus odio facilisis nibh ullamcorper pharetra. Quisque porttitor dui sed nisl pharetra ac iaculis orci faucibus. Vivamus scelerisque, nisl sed laoreet vehicula, mauris massa laoreet arcu, non lacinia eros quam porttitor risus. Quisque auctor scelerisque dolor, sit amet commodo est aliquam a. Donec tempus, lacus vitae volutpat sollicitudin, arcu mauris vulputate mauris, et cursus tellus nisl vitae sem. Aliquam viverra libero at ante vestibulum in viverra nunc aliquet. Nullam pharetra molestie pulvinar. In lacus elit, aliquet vulputate lacinia eget, scelerisque ut purus. Fusce luctus mi sed turpis commodo et viverra enim tincidunt. Fusce at arcu nec enim suscipit pellentesque quis a quam. Nam eu enim non massa tincidunt feugiat.

        Phasellus suscipit tincidunt mauris, sit amet porta augue ornare ultrices. Sed ipsum quam, vulputate sit amet porta id, mattis at diam. Vestibulum metus mauris, tempus non consequat eget, condimentum id felis. Praesent elit dolor, pretium vitae lobortis et, consectetur vitae lacus. Maecenas urna nulla, tristique eget vulputate blandit, posuere eget metus. Vestibulum adipiscing lacus sit amet neque imperdiet quis sagittis ante dignissim. Suspendisse nec porttitor odio. Etiam ultrices lectus vitae metus gravida vel mollis felis condimentum. Ut odio sem, mattis id consectetur id, dictum nec justo. Curabitur sed mauris vitae ante luctus dapibus sit amet ut dolor. Vivamus mollis, tellus at tempus convallis, leo risus mattis ipsum, in porttitor mi ante laoreet risus.');
        
        $notice->setDraft(false);
        $notice->setCreatedAt(new \DateTime("2013-04-29 09:03:00"));
        $notice->setStartDate(new \DateTime("2013-04-29 09:03:00"));
        $notice->setEndDate(new \DateTime("2013-04-29 09:03:00"));
        $notice->setOnDashboard(true);
        
        $manager->persist($notice);
        
        $gallery = $notice->getGallery();
        $this->addReference("gallery-4", $gallery);     
        $this->addReference("notice-4", $notice);           
        
        $manager->flush();

    }
    
    public function getOrder()
    {
        return 3;
    }
}