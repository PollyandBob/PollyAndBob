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
    
    
    
    public function getFullDetailedList ($filter = NULL) {
    
    	if(!($filter instanceof \Fenchy\AdminBundle\Entity\TypeFilter)) {
    
    		return $this->createQueryBuilder('t')
                ->select('t, p')
                ->leftJoin('t.properties', 'p')
                ->where('t.sequence > 0')
                ->getQuery()
                ->getResult();
    	}
    
    	    
    	$query = $this->createQueryBuilder('t')
                ->select('t, p')
                ->leftJoin('t.properties', 'p');
    

    
    	if($filter->name) {
    		$query->where('t.name like :name')
    		->setParameter('name', '%'.$filter->name.'%');
    	}
    
    	
    	if($filter->sort === 'stickersQ') {
    		return $query
    		->orderBy($filter->sort, $filter->order)
    		->getQuery()
    		->getResult();
    	}
    
    	return $query
    	->orderBy('t.'.$filter->sort, $filter->order)
    	->getQuery()
    	->getResult();
    }
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
                ->orderBy('t.sequence')
                ->getQuery()
                ->getResult();
    }
    
    public function getAllWithPropertiesFirst() {
    
    	return $this->createQueryBuilder('t')
    	->select('t, p')
    	->leftJoin('t.properties', 'p')
    	->orderBy('t.orderby')
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
    
    public function find($id) {
    
    	return $this->createQueryBuilder('t')
    	->select('t, p')
    	->leftJoin('t.properties', 'p')
    	->where('t.id = :id')
    	->setParameter('id', $id)
    	->getQuery()
    	->getOneOrNullResult();
    }
}