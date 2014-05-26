<?php

namespace Fenchy\RegularUserBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * BlockedNeighborRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BlockedNeighborRepository extends EntityRepository
{
        /**
	 * Counts neighbors
	 *
	 * @param int $neighborId
	 * @return integer
	 */
	public function findById($neighborId,$user_id)
	{
		return $this->createQueryBuilder('bn')
                    ->where('bn.blocked = :neighbor')
                    ->setParameter('neighbor', $neighborId)
                    ->andWhere('bn.blocker = :current')
                    ->setParameter('current', $user_id)
                    ->getQuery()
                    ->getOneOrNullResult();
	}
        
        /**
	 * Counts neighbors which is blocked by user or user blocked by neighbor
	 *
	 * @param int $user_id
	 * @return array
	 */
	public function findByMe($user_id)
	{
		return $this->createQueryBuilder('bn')
                    ->where('bn.blocked = :user')
                    ->orWhere('bn.blocker = :user')                    
                    ->setParameter('user', $user_id)
                    ->getQuery()
                    ->getResult();
	}
        
        /**
	 * Find neighbors which is blocked by user
	 *
	 * @param int $user_id
	 * @return array
	 */
	public function findBlockByMe($user_id)
	{
		return $this->createQueryBuilder('bn')
                    ->where('bn.blocker = :user')
                    ->setParameter('user', $user_id)
                    ->getQuery()
                    ->getResult();
	}
}