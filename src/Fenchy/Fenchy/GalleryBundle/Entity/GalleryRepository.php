<?php

namespace Fenchy\GalleryBundle\Entity;
use Doctrine\ORM\EntityRepository;

/**
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class GalleryRepository extends EntityRepository
{
    /**
     * Returns gallery selected by given id with it's images.
     * If $drafts is set to TRUE draft images will be joined too.
     * @param integer $id
     * @param boolean $drafts
     * @return array
     */
    public function findWithImages($id) {
        
        return $this->createQueryBuilder('g')
                ->select('g, i')
                ->join('g.images', 'i')
                ->where('g.id = :id')
                ->orderBy('i.sequence')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
    }
    
    public function findWithOriginal($id) {
        
        return $this->createQueryBuilder('g')
                ->select('g, i')
                ->leftJoin('g.images', 'i')
                ->leftJoin('g.original', 'og')
                ->leftJoin('og.images', 'oi')
                ->where('g.id = :id')
                ->orderBy('i.sequence')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
    }
}