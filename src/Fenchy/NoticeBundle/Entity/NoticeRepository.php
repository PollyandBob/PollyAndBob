<?php

namespace Fenchy\NoticeBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query\Expr;

use Fenchy\UserBundle\Entity\User;

class NoticeRepository extends EntityRepository
{
    /**
     * Returns draft for given user (if has one).
     * 
     * @param \Fenchy\UserBundle\Entity\User $user
     * @return Fenchy\NoticeBundle\Entity\Notice | NULL
     */
    public function findDraft(User $user) {
        
        return $this->createQueryBuilder('n')
                ->select('n, t, v, p')
                ->leftJoin('n.type', 't')
                ->leftJoin('n.values', 'v')
                ->leftJoin('v.property', 'p')
                ->where('n.user = :user')
                ->andWhere('n.draft = :draft')
                ->setParameters(array(
                    'draft' => TRUE,
                    'user'  => $user
                ))
                ->getQuery()
                ->getOneOrNullResult();
    }
    
    /**
     * Returns of notices with full detailed information from related entities.
     * @param \Fenchy\NoticeBundle\Entity\NoticesFilter $filter
     * @return array
     * 
     * @uses AdminBundle
     */
    public function getFullDetailedList($filter = NULL) {

        if(!($filter instanceof \Fenchy\AdminBundle\Entity\NoticesFilter)) {
            
            return $this->createQueryBuilder('n')
                    ->select("n, p, v, t, u, l, s, su")
                    ->leftJoin('n.values', 'v')
                    ->leftJoin('v.property', 'p')
                    ->join('n.user', 'u')
                    ->join('n.location', 'l')
                    ->leftJoin('n.type', 't')
                    ->leftJoin('n.stickers', 's', Expr\Join::WITH, 's.discarded_at IS NULL')
                    ->leftJoin('s.reported_by', 'su')
                    ->getQuery()
                    ->getResult();
        } 

        $sub = $this->getEntityManager()->createQuery("
                        SELECT count(s2.id) FROM FenchyUtilBundle:Sticker s2
                        WHERE s2.notice = n AND s2.discarded_at IS NULL"
                    );
        
        $query = $this->createQueryBuilder('n')
                    ->select("n, p, v, t, u, l, s, su, (".$sub->getDQL().") as HIDDEN stickersQ")
                    ->leftJoin('n.values', 'v')
                    ->leftJoin('v.property', 'p')
                    ->join('n.user', 'u')
                    ->join('n.location', 'l')
                    ->leftJoin('n.type', 't');
        
        if($filter->reported_only) {
            $query->join('n.stickers', 's', Expr\Join::WITH, 's.discarded_at IS NULL')
                ->leftJoin('s.reported_by', 'su');
        } else {
            $query->leftJoin('n.stickers', 's', Expr\Join::WITH, 's.discarded_at IS NULL')
                ->leftJoin('s.reported_by', 'su');
        }
        
        if($filter->title) {
            $query->where('n.title like :title')
                    ->setParameter('title', '%'.$filter->title.'%');
        }
        
        if($filter->created_after) {
            $query->andWhere('n.created_at > :after')
                    ->setParameter('after', $filter->created_after);
        }
        
        if($filter->created_before) {
            $query->andWhere('n.created_at < :before')
                    ->setParameter('before', $filter->created_before);
        }
        
        if($filter->sort === 'stickersQ') {
            return $query
                    ->orderBy($filter->sort, $filter->order)
                    ->getQuery()
                    ->getResult();
        }
        
        return $query
                    ->orderBy('n.'.$filter->sort, $filter->order)
                    ->getQuery()
                    ->getResult();
    }
    
    /**
     * Returns query which is ready for search page, but without conditions.
     * 
     * @return \Doctrine\ORM\QueryBuilder
     * @uses Fenchy\NoticeBundle\Services\ListFilter
     */
    public function getFindAllWithOwnerQuery() {
        
        return $this->createQueryBuilder('n')
                    ->select('n, o, ru, ng, og, ngi, ogi, v, p, l')
                    ->join('n.user', 'o')
                    ->join('o.user_regular', 'ru')
                    ->join('ru.gallery', 'og')
                    ->leftJoin('og.images', 'ogi')
                    ->join('n.gallery', 'ng')
                    ->leftJoin('ng.images', 'ngi')
                    ->leftJoin('n.values', 'v')
                    ->leftJoin('v.property', 'p')
                    ->join('n.location', 'l')
                    ->where('n.draft = false');
    }
    
    
    /**
     * Returns query which is ready for search page result count, but without conditions.
     * 
     * @return \Doctrine\ORM\QueryBuilder
     * @uses Fenchy\NoticeBundle\Services\ListFilter
     */
    public function getCountWithOwnerQuery() {
        
        return $this->createQueryBuilder('n')
                    ->select('n.id')->distinct()
                    ->join('n.user', 'o')
                    ->join('o.user_regular', 'ru')
                    ->join('ru.gallery', 'og')
                    ->leftJoin('og.images', 'ogi')
                    ->join('n.gallery', 'ng')
                    ->leftJoin('ng.images', 'ngi')
                    ->leftJoin('n.values', 'v')
                    ->leftJoin('v.property', 'p')
                    ->join('n.location', 'l')
                    ->where('n.draft = false');
    }
    
    /**
     * Returns counted number of notices within all available groups
     * @return array
     * @author Michał Nowak <mnowak@pgs-soft.com>
     */
    public function getFilterCount() {
        
        $sql  = '
                SELECT notice__types.id as type_id, notice__property_types.id as property_id, notice__values.value, count(*)
                FROM notice__notices
                LEFT JOIN notice__values on notice__values.notice_id = notice__notices.id
                LEFT JOIN notice__property_types on notice__property_types.id = notice__values.property_id
                LEFT JOIN notice__types on notice__types.id = notice__notices.type_id
                WHERE notice__notices.draft = false
                GROUP BY notice__types.id, notice__property_types.id, notice__values.value
                ';
            
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Returns listings other than given one but belongs to same user.
     * 
     * @param integer $num limit of returned elements
     * @param \Fenchy\NoticeBundle\Entity\Notice $notice
     * @return array
     * @author Mateusz Krowiak <mkrowiak@pgs-soft.com>
     */
    public function getUserRecentListings($num, Notice $notice) {
        
        $query = $this->createQueryBuilder('n')
                ->select("n, l, u, ur, g, i")
                ->join('n.gallery', 'g')
                ->leftJoin('g.images', 'i')
                ->join('n.user', 'u')
                ->join('u.user_regular', 'ur')
                ->join('n.location', 'l')
                ->where('n.draft = false and u.id = :user and n.id != :notice')
                ->setMaxResults($num)
                ->orderBy('n.created_at', "DESC")
                ->getQuery();
        $query->setParameter('notice', $notice->getId());
        $query->setParameter('user', $notice->getUser());
        
        return $query->getResult();
                
    }
    
    /**
     * Returns listings similar to given one.
     * 
     * @param integer $num limit of returned elements
     * @param \Fenchy\NoticeBundle\Entity\Notice $notice
     * @return array
     * @author Mateusz Krowiak <mkrowiak@pgs-soft.com>
     */
    public function getSimilarListings($num, Notice $notice) {
        
        $tags = $notice->getDictionaries();
        if(0 === $tags->count()) return array();

        $tagIds = array();
        foreach($tags as $tag) $tagIds[] = $tag->getId();
        
        $subQ = $this->createQueryBuilder('n2')
                ->select('n2.id, count(n2) AS HIDDEN c')
                ->join('n2.dictionaries', 'd')
                ->where('n2 != :notice')
                ->andWhere('d IN (:tags)')
                ->andWhere('n2.draft = false')
                ->groupBy('n2')
                ->orderBy('c')
                ->setMaxResults($num);
        $subQ->setParameters(array(
            'tags' => $tagIds,
            'notice' => $notice
        ));
        $noticeIds = array();
        $subResults = $subQ->getQuery()->getResult();
        
        if(empty($subResults)) return array();
        foreach($subResults as $res) $noticeIds[] = $res['id'];
        
        $query = $this->createQueryBuilder('n');
        $query->select('n, l, g, i')
                ->join('n.user', 'u')
                ->join('n.location', 'l')
                ->join('n.gallery', 'g')
                ->leftJoin('g.images', 'i')
                ->where($query->expr()->in('n', $noticeIds));
        
        return $query->getQuery()->getResult();
        
    } 
   
    /**
     * Counts number of notices for periods.
     * @return Array
     */
    public function getFilterTimeCount() {
        
        $columns = array();
        $params = array();

        // TODAY
        $dateStart = new \DateTime();
        $dateStart->setTime(0, 0, 1);
        $dateEnd = new \DateTime();
        $dateEnd->setTime(23, 59, 59);
        $columns[] = "
            SUM(
                CASE 
                    WHEN 
                        n.start_date >= :todayStart AND n.start_date <= :todayEnd 
                        OR
                        n.end_date >= :todayStart AND n.end_date <= :todayEnd 
                        OR
                        n.start_date < :todayStart AND n.end_date > :todayEnd 
                    THEN 1 
                    ELSE 0 
                END
            ) as today";
        $params['todayStart'] = $dateStart;
        $params['todayEnd'] = $dateEnd;

        // YESTERDAY
        $dateStart = new \DateTime('yesterday');
        $dateStart->setTime(0, 0, 1);
        $dateEnd = new \DateTime('yesterday');
        $dateEnd->setTime(23, 59, 59);
        $columns[] = "
            SUM(
                CASE 
                    WHEN 
                        n.start_date >= :yesterdayStart AND n.start_date <= :yesterdayEnd 
                        OR
                        n.end_date >= :yesterdayStart AND n.end_date <= :yesterdayEnd 
                        OR
                        n.start_date < :yesterdayStart AND n.end_date > :yesterdayEnd 
                    THEN 1 
                    ELSE 0 
                END
            ) as yesterday";
        $params['yesterdayStart'] = $dateStart;
        $params['yesterdayEnd'] = $dateEnd;

        // THIS WEEK
        $today = new \DateTime();
        if('Sunday' == $today->format('l')) {
            $dateStart = clone $today;
            $dateStart->modify('Monday last week');
            $dateStart->setTime(0, 0, 1);
            $dateEnd = $today;
            $dateEnd->setTime(23, 59, 59);
        } else {
            $dateStart = clone $today;
            $dateStart->modify('Monday this week');
            $dateStart->setTime(0, 0, 1);
            $dateEnd = clone $today;
            $dateEnd->modify('Sunday this week');
            $dateEnd->setTime(23, 59, 59);
        }
        $columns[] = "
            SUM(
                CASE 
                    WHEN 
                        n.start_date >= :thisWeekStart AND n.start_date <= :thisWeekEnd 
                        OR
                        n.end_date >= :thisWeekStart AND n.end_date <= :thisWeekEnd 
                        OR
                        n.start_date < :thisWeekStart AND n.end_date > :thisWeekEnd 
                    THEN 1 
                    ELSE 0 
                END
            ) as thisWeek";
        $params['thisWeekStart'] = $dateStart;
        $params['thisWeekEnd'] = $dateEnd;

        // LAST WEEK
        $dateStart = new \DateTime();
        $dateStart->setTime(0, 0, 1);
        $dateStart->sub(new \DateInterval('P7D'));
        $dateEnd = new \DateTime();
        $dateEnd->setTime(23, 59, 59);
        $columns[] = "
            SUM(
                CASE 
                    WHEN 
                        n.start_date >= :lastWeekStart AND n.start_date <= :lastWeekEnd 
                        OR
                        n.end_date >= :lastWeekStart AND n.end_date <= :lastWeekEnd 
                        OR
                        n.start_date < :lastWeekStart AND n.end_date > :lastWeekEnd 
                    THEN 1 
                    ELSE 0 
                END
            ) as lastWeek";
        $params['lastWeekStart'] = $dateStart;
        $params['lastWeekEnd'] = $dateEnd;
        
        // PAST MONTH
        $dateStart = new \DateTime();
        $dateStart->setTime(0, 0, 1);
        $dateStart->sub(new \DateInterval('P1M'));
        $dateEnd = new \DateTime();
        $dateEnd->setTime(23, 59, 59);
        $columns[] = "
            SUM(
                CASE 
                    WHEN 
                        n.start_date >= :pastMonthStart AND n.start_date <= :pastMonthEnd 
                        OR
                        n.end_date >= :pastMonthStart AND n.end_date <= :pastMonthEnd 
                        OR
                        n.start_date < :pastMonthStart AND n.end_date > :pastMonthEnd 
                    THEN 1 
                    ELSE 0 
                END
            ) as pastMonth";
        $params['pastMonthStart'] = $dateStart;
        $params['pastMonthEnd'] = $dateEnd;

        return $this->createQueryBuilder('n')
                ->select(implode(', ', $columns))
                ->where('n.draft = false')
                ->setParameters($params)
                ->getQuery()
                ->getOneOrNullResult();
    }
    
    /**
     * Returns notice with given id with all related data:
     * - gallery with images
     * - tmg gallery with images
     * - regular user
     * - regular user gallery with images
     * - user
     * - notice reviews
     * 
     * @param integer $id
     * @return Notice
     */
    public function findFullDetailed($id) {
        
        return $this->getDetailedQuery()
                ->where('n.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
    }
    
    /**
     * Returns notice with given slug with all related data:
     * - gallery with images
     * - tmg gallery with images
     * - regular user
     * - regular user gallery with images
     * - user
     * - notice reviews
     * 
     * @param string $slug
     * @return Notice
     */
    public function findFullDetailedWithSlug($slug) {
        
        return $this->getDetailedQuery()
                ->select('n, t, v, pt')
                ->join('n.type', 't')
                ->leftJoin('n.values', 'v')
                ->leftJoin('v.property', 'pt')
                ->where('n.slug = :slug')
                ->setParameter('slug', $slug)
                ->getQuery()
                ->getOneOrNullResult();
    }    
    
    /**
     * Returns query which selecting full detailed notice. Joined data:
     * - gallery with images
     * - tmg gallery with images
     * - regular user
     * - regular user gallery with images
     * - user
     * - notice reviews
     * 
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getDetailedQuery() {
        
        return $this->createQueryBuilder('n')
                ->select('n, ng, ngi, ntg, ntgi, ru, rug, rugi, u, r')
                ->join('n.gallery', 'ng')
                ->leftJoin('ng.images', 'ngi')
                ->leftJoin('ng.tmp', 'ntg')
                ->leftJoin('ntg.images', 'ntgi')
                ->join('n.user', 'u')
                ->join('u.user_regular', 'ru')
                ->join('ru.gallery', 'rug')
                ->leftJoin('rug.images', 'rugi')
                ->leftJoin('n.reviews', 'r');
    }
    
    /**
     * 
     * Returns notices with flag on_dashboard => true
     * Additionally join user, gallery for notice, location, type, gallery for user
     * 
     * @return Array
     */
    public function getDashboardNotices() {
        
        return $this->createQueryBuilder('n')
                ->select('n, u, ur, l, ng, ngi, ug, ugi, t, v, pt')
                ->join('n.user', 'u')
                ->join('u.user_regular', 'ur')
                ->join('n.gallery', 'ng')
                ->join('n.location', 'l')
                ->join('n.type', 't')
                ->leftJoin('ng.images', 'ngi')
                ->leftJoin('u.gallery', 'ug')
                ->leftJoin('ug.images', 'ugi')
                ->leftJoin('n.values', 'v')
                ->leftJoin('v.property', 'pt')
                ->where('n.on_dashboard = true')
                ->orderBy('n.created_at', 'DESC')
                ->getQuery()
                ->getResult();
        
    }
    
    public function findCount( $criteria ) {
        $query =  $this->createQueryBuilder('n')
                ->select('COUNT(n.id)')
                ->where('n.draft = false');
        
        if ( array_key_exists('user', $criteria) ) {
            $query->andWhere('n.user = '.$criteria['user']);
        }
                
        return $query->getQuery()->getSingleScalarResult();
    }
    
    /**
     * Returns notice with its owner.
     * @param integer $id
     * @return Notice
     */
    public function getWithUser($id) {
        
        return $this->createQueryBuilder('n')
                ->select('n, u')
                ->join('n.user', 'u')
                ->where('n.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
    }
    
    /**
     * retrieves user notices from db
     * 
     * @param \Fenchy\UserBundle\Entity\User $user
     * @return Array 
     */
    public function getUserNotices(User $user) {
        
        return $this->createQueryBuilder('n')
                ->select('n, g, i, l, v, t')
                ->where('n.user = :user')
                ->join('n.gallery', 'g')
                ->join('n.location', 'l')
                ->join('n.type', 't')
                ->leftJoin('n.values', 'v')
                ->leftJoin('g.images', 'i')
                ->setParameter('user', $user)
                ->orderBy('n.created_at', 'DESC')
                ->getQuery()
                ->getResult();
    }
    
}