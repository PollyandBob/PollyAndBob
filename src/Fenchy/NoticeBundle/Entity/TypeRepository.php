<?php

namespace Fenchy\NoticeBundle\Entity;

use Doctrine\ORM\EntityRepository,
    Doctrine\ORM\Query\Expr;

/**
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class TypeRepository extends EntityRepository
{
    /**
     * Similarity multiplier for text in title.
     * @var numeric $titleSearchMultiplier
     */
    protected $titleSearchMultiplier;
    
    /**
     * Returns leaf types with its properties.
     * 
     * @return Array
     * @uses \Fenchy\NoticeBundle\Services\ListFilter::loadTypes()
     */
    public function getFilterTypes() {
        
        return $this->createQueryBuilder('t')
                ->select('t, p')
                ->leftJoin('t.properties', 'p')
                ->where('t.sequence > 0')
                ->getQuery()
                ->getResult();
    }
    
    public function getAllWithProperties() {
        
        return $this->createQueryBuilder('t')
                ->select('t, p')
                ->leftJoin('t.properties', 'p')
                ->getQuery()
                ->getResult();
    }
    
    /**
     * Returns one property with properties jojined.
     * @param type $name
     * @return type
     */
    public function getByNameWithProperties($name) {
        
        return $this->createQueryBuilder('t')
                ->select('t, p')
                ->leftJoin('t.properties', 'p')
                ->where('t.name = :name')
                ->setParameter('name', $name)
                ->getQuery()
                ->getOneOrNullResult();
    }
}