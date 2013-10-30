<?php

namespace Fenchy\RegularUserBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * NeighborsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NeighborsRepository extends EntityRepository
{
	
	/**
	 * Counts neighbors
	 *
	 * @param int $neighborId
	 * @return integer
	 */
	public function findById($neighborId,$user_id)
	{
		return $this->createQueryBuilder('un')
		->where('un.neighbor = :neighbor')
		->setParameter('neighbor', $neighborId)
		->andWhere('un.current = :current')
		->setParameter('current', $user_id)
		->getQuery()
		->getOneOrNullResult();
	}
	
	public function findMutualGroupMembers($userId)
	{
		return $this->createQueryBuilder('un')
		->where('un.neighbor = :neighbor')
		->setParameter('neighbor', $userId)
		->orWhere('un.current = :current')
		->setParameter('current', $userId)
		->getQuery()
		->getResult();
	}
}