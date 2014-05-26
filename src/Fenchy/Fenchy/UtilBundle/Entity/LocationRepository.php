<?php

namespace Fenchy\UtilBundle\Entity;
use Doctrine\ORM\EntityRepository;

class LocationRepository extends EntityRepository
{
    /**
     * find KNN ( K nearest neighbours )
     * 
     * Based on PostGIS's "<->" operator. Retruns APPROXIMATE results ( based
     * on cartesian coordinates ).
     * Method created for DQL functions testing - can't tell for now if will
     * be used at all.
     * 
     */
    public function findKNN(Location $refLocation) {
        $latitude = $refLocation->getLatitude();
        $longitude = $refLocation->getLongitude();
        
        $query = $this->createQueryBuilder('l')
            ->select("l, (postGIS_KNN_Operator(({$longitude}), ({$latitude}), l.pgisGeom)) AS HIDDEN knni")
            ->orderBy('knni', 'ASC');
        return $query->getQuery()->getResult();
    }
    
    /**
     * Find location points within given radius
     * 
     * Based on PostGIS's  ST_DWithin() function. Returns EXACT results ( based
     * on ellipsoidal coordinates ).
     * Method created for DQL functions testing - can't tell for now if will
     * be used at all.
     */    
    public function findWithin(Location $refLocation, $radius) {

        $latitude = $refLocation->getLatitude();
        $longitude = $refLocation->getLongitude();
        
        $query = $this->createQueryBuilder('l')
            ->select("l, postGIS_ST_Distance(({$longitude}), ({$latitude}), l.pgisGeog) AS HIDDEN distance")
            ->where("postGIS_ST_DWithin(({$longitude}), ({$latitude}), l.pgisGeog, ({$radius})) = TRUE ")
            ->orderBy('distance', 'ASC');
        return $query->getQuery()->getResult();
    }
    
    /**
     * Find locations and return them sorted by distance to given location point.
     * 
     * Based on PostGIS's ST_Distance() function. Returns EXACT results ( based
     * on ellipsoidal coordinages ).
     * Method created for DQL functions testing - can't tell for now if will
     * be used at all.
     */
    public function findLocationsOBD(Location $refLocation) {

        $latitude = $refLocation->getLatitude();
        $longitude = $refLocation->getLongitude();
        
        $query = $this->createQueryBuilder('l')
            ->select("l, postGIS_ST_Distance(({$longitude}), ({$latitude}), l.pgisGeog) AS HIDDEN distance")
//                ->leftJoin('n.values', 'v')
//                ->leftJoin('v.property', 'p')
//                ->join('n.user', 'u')
//                ->join('n.location', 'l')
//                ->where('n.draft != true')
//                ->andWhere('DATE(n.start_date) <= :date')
//                ->andWhere('DATE(n.end_date) >= :date')
                ->orderBy('distance', 'ASC')
//                ->setParameters(array(
//                    'date'      => $date
//                ))
                ;

//        if($first || $max) {
//            $query->setFirstResult($first)
//                    ->setMaxResults($max);
//                    
//            return new Paginator($query, TRUE);
 //       }
        
        return $query->getQuery()
                    ->getResult();
    }
}