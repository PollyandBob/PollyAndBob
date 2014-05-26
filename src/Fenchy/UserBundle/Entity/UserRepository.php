<?php

namespace Fenchy\UserBundle\Entity;

use Doctrine\ORM\EntityRepository,
        Doctrine\ORM\Query\ResultSetMapping,
    Doctrine\ORM\Query\Expr;

use Fenchy\AdminBundle\Entity\UsersFilter;

class UserRepository extends EntityRepository
{
    
    
    public function findByEmailChangeConfirmationToken($token)
    {
        return $this->createQueryBuilder('u')
            ->join('u.email_change_request','r')
            ->where('r.confirmation_token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    
    public function findAllWithStickersWithDate($filter = NULL,$when,$now) {
    
    		$date = explode('to',$when);
	    	$startdate = date('Y-m-d H:i:s',$date[0]);
	    	$enddate = date('Y-m-d H:i:s',$date[1]);
	    	 
	    	if($now)
	    	{
	    		$dateStart = new \DateTime();
	    		$dateStart->setTime(0, 0, 1);
	    		$startdate = $dateStart;
	    		$dateEnd = new \DateTime();
	    		$dateEnd->setTime(23, 59, 59);
	    		$enddate = $dateEnd;
	    		 
	    	}
    	
    	if($filter instanceof \Fenchy\AdminBundle\Entity\UsersFilter) {
    
    		if($filter->sort === 'stickersQ') {
    
    			$sub = $this->getEntityManager()->createQuery("
                        SELECT count(s2.id) FROM FenchyUtilBundle:Sticker s2
                        WHERE s2.user = u AND s2.discarded_at IS NULL"
    			);
    
    			$query = $this->createQueryBuilder('u')
    			->select('u, ru, s, su, ('.$sub->getDQL().') as HIDDEN stickersQ')
    			->join('u.user_regular', 'ru')
    			;
    
    		} else {
    			$query = $this->createQueryBuilder('u')
    			->select('u, ru, s, su')
    			->join('u.user_regular', 'ru')
    			;
    		}
    
    		if($filter->reported_only) {
    			$query->join('u.stickers', 's', Expr\Join::WITH, 's.discarded_at IS NULL')
    			->leftJoin('s.reported_by', 'su');
    		} else {
    			$query->leftJoin('u.stickers', 's', Expr\Join::WITH, 's.discarded_at IS NULL')
    			->leftJoin('s.reported_by', 'su');
    		}
    
    		if($filter->name) {
    			$query->andWhere('ConcatSB(ru.firstname, ru.lastname) like :name')
    			->setParameter('name', '%'.$filter->name.'%');
    		}
    		if($filter->created_after) {
    			$query->andWhere('u.created_at > :after')
    			->setParameter('after', $filter->created_after);
    		}
    		if($filter->created_before) {
    			$query->andWhere('u.created_at < :before')
    			->setParameter('before', $filter->created_before);
    		}
    
    		if($filter->sort === 'stickersQ') {
    			$query->orderBy($filter->sort, $filter->order);
    		} elseif($filter->sort === 'lastname') {
    			$query->orderBy('ru.'.$filter->sort, $filter->order);
    		} else {
    			$query->orderBy('u.'.$filter->sort, $filter->order);
    		}
    	} else {
    		$query = $this->createQueryBuilder('u')
    		->select('u, ru, s, su')
    		->join('u.user_regular', 'ru')
    		->leftJoin('u.stickers', 's', Expr\Join::WITH, 's.discarded_at IS NULL')
    		->leftJoin('s.reported_by', 'su')
    		->where('u.created_at >= :start AND u.created_at <= :end')
       		->setParameter('start', $startdate)
       		->setParameter('end', $enddate);
    	}
        //echo $query->getQuery()->getSQL();
    	return $query->orderBy('u.id','DESC')
    	->getQuery()
    	->getResult();
    }
    
    /**
     * 
     * @param UsersFilter $filter
     * @return array
     */
    public function findAllWithStickers($filter = NULL) {
        
        
        if($filter instanceof \Fenchy\AdminBundle\Entity\UsersFilter) {
            
            if($filter->sort === 'stickersQ') {
                
                $sub = $this->getEntityManager()->createQuery("
                        SELECT count(s2.id) FROM FenchyUtilBundle:Sticker s2
                        WHERE s2.user = u AND s2.discarded_at IS NULL"
                    );
                
                $query = $this->createQueryBuilder('u')
                    ->select('u, ru, s, su, ('.$sub->getDQL().') as HIDDEN stickersQ')
                    ->join('u.user_regular', 'ru')
                    ;
                
            } else {
                $query = $this->createQueryBuilder('u')
                    ->select('u, ru, s, su')
                    ->join('u.user_regular', 'ru')
                    ;
            }
            
            if($filter->reported_only) {
                $query->join('u.stickers', 's', Expr\Join::WITH, 's.discarded_at IS NULL')
                    ->leftJoin('s.reported_by', 'su');
            } else {
                $query->leftJoin('u.stickers', 's', Expr\Join::WITH, 's.discarded_at IS NULL')
                    ->leftJoin('s.reported_by', 'su');
            }
            
            if($filter->name) {
                $query->andWhere('ConcatSB(ru.firstname, ru.lastname) like :name')
                        ->setParameter('name', '%'.$filter->name.'%');
            }
            if($filter->created_after) {
                $query->andWhere('u.created_at > :after')
                        ->setParameter('after', $filter->created_after);
            }
            if($filter->created_before) {
                $query->andWhere('u.created_at < :before')
                        ->setParameter('before', $filter->created_before);
            }

            if($filter->sort === 'stickersQ') {
                $query->orderBy($filter->sort, $filter->order);
            } elseif($filter->sort === 'lastname') {
                $query->orderBy('ru.'.$filter->sort, $filter->order);
            } else {
                $query->orderBy('u.'.$filter->sort, $filter->order);
            }
        } else {
            $query = $this->createQueryBuilder('u')
                ->select('u, ru, s, su')
                ->join('u.user_regular', 'ru')
                ->leftJoin('u.stickers', 's', Expr\Join::WITH, 's.discarded_at IS NULL')
                ->leftJoin('s.reported_by', 'su');
        }
        
        return $query->orderBy('u.id','DESC')
                ->getQuery()
                ->getResult();
    }
    /**
     * 
     * @param UsersFilter $filter
     * @return array
     */
    public function findAllWithSticker($filter = NULL) {
        
        if($filter instanceof \Fenchy\AdminBundle\Entity\UsersFilter) {
            
            if($filter->sort === 'stickersQ') {
                
                $sub = $this->getEntityManager()->createQuery("
                        SELECT count(s2.id) FROM FenchyUtilBundle:Sticker s2
                        WHERE s2.user = u AND s2.discarded_at IS NULL"
                    );
                
                $query = $this->createQueryBuilder('u')
                    ->select('u, ru, s, su, ('.$sub->getDQL().') as HIDDEN stickersQ')
                    ->join('u.user_regular', 'ru')
                    ;
                
            } else {
                $query = $this->createQueryBuilder('u')
                    ->select('u, ru, s, su')
                    ->join('u.user_regular', 'ru')
                    ;
            }
            
            if($filter->reported_only) {
                $query->join('u.stickers', 's', Expr\Join::WITH, 's.discarded_at IS NULL')
                    ->leftJoin('s.reported_by', 'su');
            } else {
                $query->leftJoin('u.stickers', 's', Expr\Join::WITH, 's.discarded_at IS NULL')
                    ->leftJoin('s.reported_by', 'su');
            }
            
            if($filter->first_name) {
                $query->andWhere('LOWER(ru.firstname) like :name')
                        ->setParameter(':name', strtolower('%'.$filter->first_name.'%'));
            }
            if($filter->last_name) {
                $query->andWhere('LOWER(ru.lastname) like :lastname')
                        ->setParameter('lastname', strtolower('%'.$filter->last_name.'%'));
            }   
            
            if($filter->location) {
                $query->join('u.location','l');
                $query->andWhere('LOWER(l.location) like :location')
                        ->setParameter('location', strtolower('%'.$filter->location.'%'));
            }
            
            if($filter->postcode) {
                $query->andWhere('LOWER(ru.postcode) like :postcode')
                        ->setParameter('postcode', strtolower('%'.$filter->postcode.'%'));
            }  
            
            if($filter->sort === 'stickersQ') {
                $query->orderBy($filter->sort, $filter->order);
            }
            elseif($filter->sort === 'firstname') {
                $query->orderBy('ru.'.$filter->sort, $filter->order); 
            }
            elseif($filter->sort === 'lastname') {
                $query->orderBy('ru.'.$filter->sort, $filter->order);
            } 
            else {
                $query->orderBy('u.'.$filter->sort, $filter->order);
            }
        } else {
            $query = $this->createQueryBuilder('u')
                ->select('u, ru, s, su')
                ->join('u.user_regular', 'ru')
                ->leftJoin('u.stickers', 's', Expr\Join::WITH, 's.discarded_at IS NULL')
                ->leftJoin('s.reported_by', 'su');
        }
        
        return $query
                ->getQuery()
                ->getResult();
    }
    /**
     *
     * @param UsersFilter $filter
     * @return array
     */
    public function searchUser($searchWord) {

    	if(strlen($searchWord)>=3)
    	{
	    	$query = $this->createQueryBuilder('u')
	    			->select('u, ru')
	    			->join('u.user_regular', 'ru');
	
	    	$query->where('LOWER(ru.firstname) like :firstname')
	    			->setParameter('firstname', strtolower('%'.$searchWord.'%'));
	    	
	    	$query->orWhere('LOWER(ru.lastname) like :lastname')
	    			->setParameter(':lastname', strtolower('%'.$searchWord.'%'));
	    	    	
	    	return $query
			    	->getQuery()
			    	->getResult();
    	}
		return null;    
    }
    
    /**
     *
     * @param UsersFilter $filter
     * @return array
     */
    public function searchUserNeighbors($searchWord) {
    
    	if(strlen($searchWord)>=3)
    	{
    		$query = $this->createQueryBuilder('u')
    		->select('u, ru')
    		->join('u.user_regular', 'ru');
    
    		return $query
    		->getQuery()
    		->getResult();
    	}
    	return null;
    }
    
    public function getAllData($id) {
        
        return $this->createQueryBuilder('u')
                ->select('u, ru, rug, rugi, n, r, owr, c')
                ->join('u.user_regular', 'ru')
                ->join('ru.gallery', 'rug')
                ->leftJoin('rug.images' , 'rugi')
                ->leftJoin('u.notices', 'n')
                ->leftJoin('u.reviews', 'r')
                ->leftJoin('u.ownReviews', 'owr')
                ->leftJoin('ru.myFriends', 'c')
                ->where('u = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
    }
    
    public function getFindAllQuery() {
        
        return $this->createQueryBuilder('u')
                    ->select('u, ru, og, ogi, l')
                    ->join('u.user_regular', 'ru')
                    ->join('ru.gallery', 'og')
                    ->leftJoin('og.images', 'ogi')
                    ->join('u.location', 'l');
    }
    
    public function getUserByEmail($email)
    {
    	$query = $this->createQueryBuilder('u')
    		->select('u')
    		->where('u.email = :email')
    		->setParameter('email', $email)
    		->getQuery();
    	$userwithEmail = $query->getOneOrNullResult();
    	return $userwithEmail;
    }
    
    public function getFirstRecord()
    {
        return $this->createQueryBuilder('u')
    	->select("u")
    	->orderBy('u.created_at', "ASC")
    	->getQuery()
    	->getResult();
    }
    
    public function getLastRecord()
    {
        return $this->createQueryBuilder('u')
    	->select("u")
    	->orderBy('u.created_at', "DESC")
    	->getQuery()
    	->getResult();
    }
    
    /**
     * 
     * @param Users $postcode
     * @return array
     */
    public function findByPostalCode($postcode = NULL) {
        
        if($postcode!= NULL)
        {
            $query = $this->createQueryBuilder('u')
                ->select('u, ru, s, su')
                ->join('u.user_regular', 'ru')
                ->leftJoin('u.stickers', 's', Expr\Join::WITH, 's.discarded_at IS NULL')
                ->leftJoin('s.reported_by', 'su')
                ->where('ru.postcode =:postcode')
                ->setParameter('postcode', $postcode);
            
            return $query->orderBy('u.id','DESC')
                    ->getQuery()
                    ->getResult();
        }
        $sql = "SELECT u.postcode, 
                count(*) AS count "
            ."FROM user__regular as u "
            ."GROUP BY postcode;";


            $rsm = new ResultSetMapping;
            $rsm->addScalarResult('postcode', 'postcode');
            $rsm->addScalarResult('firstname', 'firstname');
            $rsm->addScalarResult('count', 'count');
            $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
            return $query->getResult();

            
    }
    
    /**
     * 
     * @param Users $postcode
     * @return array
     */
    public function findByAge($age = NULL) {
        
        if($age!= NULL)
        {          
            $agelimit = explode('-', $age);
         
            if(strcasecmp($agelimit[0], 'without age declaration') != 0 && strcasecmp($agelimit[0], '90 and older') != 0) 
            {
                $query = $this->createQueryBuilder('u')
                        ->select('u, ru, s, su')
                        ->join('u.user_regular', 'ru')
                        ->leftJoin('u.stickers', 's', Expr\Join::WITH, 's.discarded_at IS NULL')
                        ->leftJoin('s.reported_by', 'su')
                        ->where('ru.userAge >= :agelowerlimit AND ru.userAge <= :ageupperlimit')
                        ->setParameter('agelowerlimit', $agelimit[0])
                        ->setParameter('ageupperlimit', $agelimit[1]);
            }
            else if(strcasecmp($agelimit[0], '90 and older') ==0 )
            {
                $query = $this->createQueryBuilder('u')
                        ->select('u, ru, s, su')
                        ->join('u.user_regular', 'ru')
                        ->leftJoin('u.stickers', 's', Expr\Join::WITH, 's.discarded_at IS NULL')
                        ->leftJoin('s.reported_by', 'su')
                        ->where('ru.userAge >= :agelowerlimit')
                        ->setParameter('agelowerlimit', 90);                        
            }
            else {
     
                $query = $this->createQueryBuilder('u')
                        ->select('u, ru, s, su')
                        ->join('u.user_regular', 'ru')
                        ->leftJoin('u.stickers', 's', Expr\Join::WITH, 's.discarded_at IS NULL')
                        ->leftJoin('s.reported_by', 'su')
                        ->where('ru.userAge IS NULL');
                        
            }
            return $query->orderBy('u.id','DESC')
                    ->getQuery()
                    ->getResult();
        }
           
        $sql = "SELECT
                COUNT(*),
                CASE
                  WHEN ru.userage >= 14 AND ru.userage <= 19 THEN '14-19'
                  WHEN ru.userage >=20 AND ru.userage <=29 THEN '20-29'
                  WHEN ru.userage >=30 AND ru.userage <=39 THEN '30-39'
                  WHEN ru.userage >=40 AND ru.userage <=49  THEN '40-49'
                  WHEN ru.userage >=50 AND ru.userage <=59 THEN '50-59'
                  WHEN ru.userage >=60 AND ru.userage <=69 THEN '60-69'
                  WHEN ru.userage >=70 AND ru.userage <=79 THEN '70-79'
                  WHEN ru.userage >=80 AND ru.userage <=89 THEN '80-89'
                  WHEN ru.userage >=90 THEN '90 and older'
                  WHEN ru.userage is null THEN 'without age declaration'
                END AS ageband
              FROM user__users u INNER JOIN user__regular ru ON u.id = ru.id
              GROUP BY ageband";


            $rsm = new ResultSetMapping;
            $rsm->addScalarResult('ageband', 'ageband');
            $rsm->addScalarResult('count', 'count');
            $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
            return $query->getResult();
    }
}