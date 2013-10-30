<?php

namespace Fenchy\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Fenchy\UserBundle\Entity\User;

/**
 * IdentityVerificationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class IdentityVerificationRepository extends EntityRepository
{
	public function getFullDetailedList($filter = NULL) 
	{	
		if(!($filter instanceof \Fenchy\AdminBundle\Entity\IdentityVerificationFilter))
		{
			return $this->createQueryBuilder('i')						
						->getQuery()
						->getResult();
		}
		$query = $this->createQueryBuilder('i');
				
						
		
		if($filter->username) {
			$query->where('i.username like :username')
			->setParameter('username', '%'.$filter->username.'%');	
			
		}
		
		if($filter->status) {
			$query->andWhere('i.status like :status')
			->setParameter('status', '%'.$filter->status.'%');
		}

		return $query->orderBy('i.'.$filter->sort, $filter->order)
					->getQuery()
					->getResult();		
	}

	public function getIdentityStatus(User $user)
	{
		$query = $this->createQueryBuilder('i')
				->where('i.user = :user')
				->setParameter('user', $user->getId())
				->getQuery();
		$identity_entity = $query->getOneOrNullResult();
		$identity = true;		
		if($identity_entity)
		{
			if(strcasecmp($identity_entity->getStatus(), 'Verified') != 0)
				$identity = false;
		}
		else
		{		
			$identity = false;
		}
		return $identity; 
	}
	
	public function getVerifyIdentity(User $user)
	{
		$query = $this->createQueryBuilder('i')
				->where('i.user = :user')
				->setParameter('user', $user->getId())
				->getQuery();
		$identity_entity = $query->getOneOrNullResult();
		
		$verify_identity = false;
		if(!$identity_entity)
		{
			$verify_identity = true;			
		}
		return $verify_identity;
	}
}
