<?php

namespace Fenchy\RegularUserBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Fenchy\UserBundle\Entity\User;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class UserRegularRepository extends EntityRepository
{
    /**
     * Returns list of neighbours (join: UserRegular, User, Notices, Galleries, Images)
     * 
     * @param USER $user actual user
     * @param String $search word to be seached in firstname and address
     * @param Integer $minDistance minimal distance from user to neighbour
     * @param Integer $maxDistance maximal distance from user to neighbour
     * @param Boolean $contactsOnly if set to true only user contact will be selected
     * @param Integer $first pagination first
     * @param Integer $max Pagination elements on page
     */
    public function findNeighbours(User $user, $search, $minDistance, $maxDistance, $contactsOnly = FALSE, $first = 0, $max = 0) {
        
        $select = 'ru, u, n, rg, ri, ug, ui';
        $qb = $this->createQueryBuilder('ru')
                ->select($select)
                ->where('ru.user != :user')
                ->join('ru.user', 'u')
                ->leftJoin('ru.gallery', 'rg')
                ->leftJoin('rg.images', 'ri')
                ->leftJoin('u.gallery', 'ug')
                ->leftJoin('ug.images', 'ui')
                ->leftJoin('u.notices', 'n', Expr\Join::WITH, 'n.draft != TRUE AND n.type IS NULL')
                ->setParameter('user', $user)
                ->orderBy('u.activity', 'DESC');
        
        if ($search) {

            $select .= ", similarity(concatSB(concatSB(ru.firstname, ru.city), ru.district), '$search') as HIDDEN sim";
            $qb->orderBy('sim', 'DESC')
                ->addOrderBy('u.activity', 'DESC');
        }
        
        if ($contactsOnly) {
            
            $qb->join('ru.friendsWithMe', 'fr', Expr\Join::WITH, 'fr.user = :user');
        }
        
        if (NULL != $minDistance || NULL != $maxDistance) {
            
            $minDistanceKm = $minDistance;
            $maxDistanceKm = $maxDistance;
            
            $minDistance = $minDistance * 0.009000900090009;
            $maxDistance = $maxDistance * 0.009000900090009;
            
            $longitude = $user->getRegularUser()->getLongitude();
            $latitude = $user->getRegularUser()->getLatitude();
            NULL === $longitude && $longitude = 0;
            NULL === $latitude && $latitude = 0;
            
            if (NULL != $minDistance) {
                if (NULL == $maxDistance) {
                    $qbRange = $this->createQueryBuilder('ru2')
                            ->select('ru2.id')
                            ->where("abs(ru2.longitude - {$longitude}) > $minDistance")
                            ->andWhere("abs(ru2.latitude - {$latitude}) > $minDistance")
                            ->groupBy('ru2.id')
                            ->having("geoDistance(({$longitude}), ({$latitude}), ru2.longitude, ru2.latitude) > ".$minDistanceKm);
                } else {
                    $qbRange = $this->createQueryBuilder('ru2')
                            ->select("ru2.id")
                            ->where("(abs(ru2.longitude - ({$longitude})) > $minDistance
                                AND abs(ru2.latitude - ({$latitude})) > $minDistance
                                OR abs(ru2.longitude - ({$longitude})) < $maxDistance
                                AND abs(ru2.latitude - ({$latitude})) < $maxDistance)")
                            
                            ->groupBy('ru2.id')
                            ->having("geoDistance(({$longitude}), ({$latitude}), ru2.longitude, ru2.latitude) > $minDistanceKm
                                    OR geoDistance(({$longitude}), ({$latitude}), ru2.longitude, ru2.latitude) < $maxDistanceKm");
                }
            } elseif (NULL != $maxDistance) {
                $qbRange = $this->createQueryBuilder('ru2')
                            ->select('ru2.id')
                            ->where("abs(ru2.longitude - ({$longitude})) < $maxDistance")
                            ->andWhere("abs(ru2.latitude - ({$latitude})) < $maxDistance")
                            ->groupBy('ru2.id')
                            ->having("geoDistance(({$longitude}), ({$latitude}), ru2.longitude, ru2.latitude) < ".$maxDistanceKm);
            }
            $qb->andWhere($qb->expr()->in('ru.id', $qbRange->getDQL()));
        }

        if($first || $max) {
            $qb->select($select)
                    ->setFirstResult($first)
                    ->setMaxResults($max);
                    
            return new Paginator($qb, TRUE);
            
        } else {
            
            return $qb->select($select)
                    ->getQuery()
                    ->getResult();
        }
    }

    public function getWithReviews($id) {
        
        return $this->createQueryBuilder('ru')
                ->select('ru, u, ur, n, nr')
                ->join('ru.user', 'u')
                ->leftJoin('u.reviews', 'ur')
                ->leftJoin('u.notices', 'n')
                ->leftJoin('n.reviews', 'nr')
                ->where('ru.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
    }
    
    public function getManagerType(User $user)
    {
    	
    	$users = $this->createQueryBuilder('ru')
		    	->select('ru, u')
		    	->join('ru.user', 'u')
		    	->getQuery()
                ->getResult();
    	
        
    	$location = $user->getLocation();
    	$lat2 = $location->getLatitude();
    	$log2 = $location->getLongitude();    	 
    	
    	$distanceArray = array();
    	$count = 0;
    	if($users)
    	{
    		foreach ($users as $u)
    		{
    			$location = $u->getUser()->getLocation();
    			$lat = $location->getLatitude();
    			$log = $location->getLongitude();
    	
    			$theta = $log - $log2;
    			// Find the Great Circle distance
    			$distance = rad2deg(acos((sin(deg2rad($lat)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)))));
    			$distance = $distance * 60 * 1.1515;
    			$distance = round(($distance * 1609.34), 0);//miles to meter rounded to 0
    	
    			if($u->getId() != $user->getId())
    			{
    				if($distance < 30000 && $distance >= 0)
    				{
    					$count++;
    				}
    			}
    		}   		
    	}
    	
    	$managertype = "";
    	$managertype_alpha = " ";
    	$classColor = "red";
    	
    	if($count < 500 && $user->getActivity() < 500 && $user->getManagertype()!=1)
    	{
    		$managertype = "pioneer";
    		$managertype_alpha = "P";
    		$classColor = "red";
    	}
    	else if($user->getActivity() >= 500 && $user->getActivity() < 1000 && $user->getManagerType()!=1)
    	{
    		$managertype = "community_m";
    		$managertype_alpha = "C";
    		$classColor = "blue";
    	}
    	elseif($user->getActivity() >= 1000 && $user->getManagerType()!=1)
    	{
    		$managertype = "neighbor";
    		$managertype_alpha = "N";
    		$classColor = "green";
    	}
    	elseif($user->getManagerType()==1)
    	{
    		$managertype = "house_m";
    		$managertype_alpha = "H";
    		$classColor = "orange";
    	}
    	else
    	{
    		$managertype = "";
    		$managertype_alpha = " ";
    		$classColor = "violet";
    	}
    	$managertype_array = array();
    	$managertype_array[0] = $managertype;
    	$managertype_array[1] = $managertype_alpha;
    	$managertype_array[2] = $classColor;
    	
    	return $managertype_array;
    }
}