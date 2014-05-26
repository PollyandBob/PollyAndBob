<?php

namespace Fenchy\GalleryBundle\Entity;
use Doctrine\ORM\EntityRepository;

class ImageRepository extends EntityRepository
{
    public function findWithGallery($id) {
        
        return $this->createQueryBuilder('i')
                ->select('i, g')
                ->join('i.gallery', 'g')
                ->where('i.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
    }
    
    public function getByGalleryAndName($galleryId, $name) {
        
        return $this->createQueryBuilder('i')
                ->select('i')
                ->join('i.gallery', 'g')
                ->where('i.name = :name')
                ->andWhere('g.id = :id')
                ->setParameters(array('name' => $name, 'id' => $galleryId))
                ->getQuery()
                ->getOneOrNullResult();
        
    }
    
    public function getTmpByName($name) {
        
        return $this->createQueryBuilder('i')
                ->select('i, g')
                ->join('i.gallery', 'g')
                ->where('i.name = :name')
                ->andWhere('g.original is not null')
                ->setParameter('name', $name)
                ->getQuery()
                ->getOneOrNullResult();
    }
}