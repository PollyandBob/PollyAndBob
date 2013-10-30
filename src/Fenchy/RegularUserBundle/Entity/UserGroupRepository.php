<?php

namespace Fenchy\RegularUserBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UserGroupRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserGroupRepository extends EntityRepository
{
    /**
     * Counts usergroups
     * 
     * @param int $id
     * @return integer 
     */
	
	
	public function findById($user_id)
    {
        return $this->createQueryBuilder('u')
                    ->where('u.user_id = :user_id')
    				->setParameter('user_id', $user_id)
    				->getQuery()
    				->getOneOrNullResult();
    }
    public function findAllById($userId)
    {
    	return $this->createQueryBuilder('ug')
    	->where('ug.user = :user')
    	->setParameter('user', $userId)
    	->getQuery()
    	->getResult();	
    }
    
    public function getAllData($id)
    {
    	return $this->createQueryBuilder('u')
    	->where('u.id = :id')
    	->setParameter('id', $id)
    	->getQuery()
    	->getOneOrNullResult();
    }
    
    
    
}