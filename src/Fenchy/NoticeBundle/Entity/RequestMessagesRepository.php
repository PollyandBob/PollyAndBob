<?php

namespace Fenchy\NoticeBundle\Entity;


use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityRepository;
use Fenchy\UserBundle\Entity\User; 
/**
 * RequestMessagesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RequestMessagesRepository extends EntityRepository
{
    public function findByRequest($requestId, $aboutUser, $author)
    {
        return $this->createQueryBuilder('rm')
                    ->select('rm')
                    ->where('rm.request = :request')
                    ->andWhere('(rm.aboutUser = :user AND rm.author = :author) OR (rm.author = :user OR rm.aboutUser = :author)')
                    //->andwhere('rm.author = :user OR rm.aboutUser = :author')
                    ->andWhere('rm.aboutNotice IS NOT NULL')
                    ->andWhere('rm.aboutUserGroup IS NULL')
                    ->setParameters(array('request' => $requestId, 'user'=> $aboutUser, 'author'=> $author))
                    ->getQuery()
                    ->getResult();
    }
    
    public function findByGroupRequest($requestId, $aboutUser, $author)
    {
        return $this->createQueryBuilder('rm')
                    ->select('rm')
                    ->where('rm.request = :request')
                    ->andWhere('(rm.aboutUser = :user AND rm.author = :author) OR (rm.author = :user OR rm.aboutUser = :author)')
                    //->andwhere('rm.author = :user OR rm.aboutUser = :author')
                    ->andWhere('rm.aboutNotice IS NOT NULL')
                    //->andWhere('rm.aboutUserGroup IS NOT NULL')
                    ->setParameters(array('request' => $requestId, 'user'=> $aboutUser, 'author'=> $author))
                    ->getQuery()
                    ->getResult();
    }
    
    public function findByNeighbor($requestId, $aboutUser, $author)
    {
        return $this->createQueryBuilder('rm')
                    ->select('rm')
                    ->where('rm.request = :request')
                    ->andWhere('(rm.aboutUser = :user AND rm.author = :author) OR (rm.author = :user OR rm.aboutUser = :author)')
                    //->andwhere('rm.author = :author')
                    ->andWhere('rm.aboutNotice IS NULL')
                    ->andWhere('rm.aboutUserGroup IS NULL')
                    ->setParameters(array('request' => $requestId, 'user'=> $aboutUser, 'author'=> $author))
                    ->getQuery()
                    ->getResult();
    }
    
    public function findByGroup($requestId, $aboutUser, $author, $group)
    {
        return $this->createQueryBuilder('rm')
                    ->select('rm')
                    ->where('rm.request = :request')
                    //->andWhere('rm.aboutUser IS NULL')
                    ->andwhere('rm.author = :author or rm.aboutUser = :author')
                    ->andWhere('rm.aboutNotice IS NULL')
                    ->andWhere('rm.aboutUserGroup = :group')
                    ->setParameters(array('request' => $requestId, 'author'=> $author, 'group'=> $group))
                    ->getQuery()
                    ->getResult();
    }
    
    public function countRequestMessage(User $user)
    {
        $query = $this->createQueryBuilder('rm')
			->select('COUNT(rm.id)')
			->where('rm.is_read = :read and rm.aboutUser = :user')
			->setParameters(array(
					'read' => 'false',
                                        'user' => $user
			))
			->getQuery();
        
        $query1 = $this->createQueryBuilder('rm')
			->select('COUNT(rm.id)')
			->where('rm.is_read_status = :read and rm.aboutUser = :user')
			->setParameters(array(
					'read' => 'false',
                                        'user' => $user
			))
			->getQuery();
	
	
	return $query->getSingleScalarResult() + $query1->getSingleScalarResult();
    }
    public function updateRequestMessage(User $user)
    {
        $query = $this->createQueryBuilder('rm')
		->update()
		->set('rm.is_read_status', 'true')
		->where('rm.aboutUser = :user')
		->getQuery();

		$query->setParameter('user', $user);

		return $query->execute();
    }
    public function updateRequestMessage1(User $user)
    {
        $query = $this->createQueryBuilder('rm')
		->update()
		->set('rm.is_read', 'true')
		->where('rm.aboutUser = :user')
		->getQuery();

		$query->setParameter('user', $user);

		return $query->execute();
    }
    
    public function countUnreadMessageInGroup($groupId)
    {
        $query = $this->createQueryBuilder('r')
			->select('COUNT(r.id)')
			->where('r.is_read = :read and r.aboutUserGroup = :usergroup')
			->setParameters(array(
					'read' => 'false',
                                        'usergroup' => $groupId
			))
			->getQuery();
	
	
	return $query->getSingleScalarResult();
    }
    
    public function updateRequestMessageInGroup($groupId)
    {
        $query = $this->createQueryBuilder('rm')
		->update()
		->set('rm.is_read', 'true')
		->where('rm.aboutUserGroup = :usergroup')
		->getQuery();

		$query->setParameter('usergroup', $groupId);

		return $query->execute();
    }
}