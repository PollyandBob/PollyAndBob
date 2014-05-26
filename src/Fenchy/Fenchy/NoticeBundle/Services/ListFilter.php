<?php

namespace Fenchy\NoticeBundle\Services;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine,
    Symfony\Component\HttpFoundation\Request,
    Doctrine\ORM\Query\Expr;
use Fenchy\UserBundle\Entity\User;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Description of ListFilter
 *
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class ListFilter {
    
    public $count = 0;
    private $container;
    
    /**
     * Doctrine registry instance
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;
    
    /**
     * Request instance
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;
    
    /**
     * Title to Content similarity ratio.
     * @var Float
     */
    private $TCRatio = 1;
    
    /**
     * Minimal similarity level required to pass notice when "where" field in 
     * search has been filled in.
     * @var Float 
     */
    private $minSimilarity = 0;
    
    private $validFilter = array(
            'distance'  => NULL,
            'postDate'  => NULL,
            'types'     => array(),
            'what'      => NULL,
            'location'  => NULL,
            'sort'      => 'time',
            'range'     => array( 'low' => 0, 'high' => 10)
        );
    
    
    private $allowedPostDates = array('today', 'yesterday', 'thisWeek', 'lastWeek', 'pastMonth');
    private $allowedSorts = array('rel','dist','time');
    /**
     * Array of types
     * @var Array od Fenchy\NoticeBundle\Entity\Type
     */
    private $types = array();

    private $validationErrors = array();
    
    private $currentUser = NULL;
    
    /**
     * Dictionary service
     * @var type 
     */
    private $dictionary;
    
    public function __construct(Container $container, Doctrine $doctrine, Request $request, $TCRatio = 1, $minSimilarity = 0, $dictionary) {
        
        $this->container = $container;
        $this->doctrine = $doctrine;
        $this->request = $request;
        $this->TCRatio = $TCRatio;
        $this->minSimilarity = $minSimilarity;
        $this->validFilter['distance'] = $this->container->getParameter('distance_slider_default');
        $this->dictionary = $dictionary;
        $this->pagination = $this->container->getParameter('listings_pagination');
    }
    
    public function setCurrentUser(\Fenchy\UserBundle\Entity\User $user) {
        $this->currentUser = $user;
    }
    
    
    /**
     * Returns  notices
     * @return string
     */
    public function getList() {
        $elements = $this->getElements();
        $this->count = $elements->count();
        return $this->prepareList($elements);
    }
    
    public function getListKNN( $knn ) {
        $elements = $this->getElementsKNN( $knn );
        return $this->prepareList($elements);
    }
    
    protected function prepareList( $elements ) 
    {
        $result = array();
        foreach($elements as $element) {
            if($element instanceof \Fenchy\NoticeBundle\Entity\Notice) {
                $data = $element->toJsonArray();
                $data['url'] = $this->container->get('router')
                        ->generate('fenchy_notice_show_slug', array(
                                    'slug' => $element->getSlug(),
                                    'year' => $element->getStartDate()->format('Y'),
                                    'month' => $element->getStartDate()->format('m'),
                                    'day' => $element->getStartDate()->format('d')
                                ));
                $data['icon'] = $element->getIcon();
                $data['owner']['url'] = $this->container->get('router')
                        ->generate('fenchy_regular_user_user_profilev2', array('userId' => $element->getUser()->getId()));
                $result[] = $data;
            }
            elseif($element instanceof \Fenchy\UserBundle\Entity\User) {
                $data = $element->toJsonArray();
                $data['url'] = $this->container->get('router')
                        ->generate('fenchy_regular_user_user_profilev2', array('userId' => $element->getId()));
                $data['icon'] = '';
                $result[] = $data;
            }
        }
        
        return $result;
    }
    
    
    public function getFilter() {
        
        $counters = $this->doctrine->getManager()->getRepository('FenchyNoticeBundle:Notice')->getFilterCount();
        $timeCounters = $this->doctrine->getManager()->getRepository('FenchyNoticeBundle:Notice')->getFilterTimeCount();
        $filter = array(
            'categories' => array(), 
            'postDate' => array(
                array(
                    'id' => 'today',
                    'name' => $this->container->get('translator')->trans('notice.filter.today'),
                    'count' => $timeCounters['today']
                ),
                array(
                    'id' => 'yesterday',
                    'name' => $this->container->get('translator')->trans('notice.filter.yesterday'),
                    'count' => $timeCounters['yesterday']
                ),
                array(
                    'id' => 'thisWeek',
                    'name' => $this->container->get('translator')->trans('notice.filter.this_week'),
                    'count' => $timeCounters['thisWeek']
                ),
                array(
                    'id' => 'lastWeek',
                    'name' => $this->container->get('translator')->trans('notice.filter.last_week'),
                    'count' => $timeCounters['lastWeek']
                ),
                array(
                    'id' => 'pastMonth',
                    'name' => $this->container->get('translator')->trans('notice.filter.past_month'),
                    'count' => $timeCounters['pastMonth']
                )
            ),
            'what'     => NULL,
            'where'    => NULL,
            'location' => NULL,
            );
        
        $this->loadTypes();
        $category = array();
        foreach($this->types as $type) {
            
            $subcat = array();
            foreach($type->getProperties() as $property) {
                if($property->isSelectable()) {
                    $options = $property->getOptions();
                    $subcat2 = array();
                    if(is_array($options) && !empty($options)) {
                        foreach($options as $k => $v) {
                            $subcat2[] = array(
                                'name'      => !empty($v)?$this->container->get('translator')->trans('notice.subcategory.'.$v):$v,
                                'id'        => $k,
                                'checked'   => $this->isValidFilterTypeChecked($type->getId(), $property->getId(), $k),
                                'count'     => 0,
                                'subcategories' => array()
                            );
                        }
                    }
                    $subcat[] = array(
                        'name'      => '',
                        'id'        => $property->getId(),
                        'checked'   => $this->isValidFilterTypeChecked($type->getId(), $property->getId(), NULL),
                        'count'     => 0,
                        'display'   => FALSE,
                        'subcategories' => $subcat2
                    );
                }
            }
            
            $catname = $type->getName();
            
            $category[] = array(
                'id'            => $type->getId(),
                'name'          => !empty($catname)?$this->container->get('translator')->trans('notice.category.'.$catname):$catname,
                'checked'       => $this->isValidFilterTypeChecked($type->getId(), NULL, NULL),
                'count'         => 0,
                'subcategories' => $subcat
            );
        }

        $filter['categories'] = $this->mergeCategoryWithCounters($category, $counters);
        
        return $filter;
    }
    
    private function isValidFilterTypeChecked( $type, $ptype=NULL, $value=NULL ) {
        
        if ( empty($this->validFilter['types']) ) 
            return FALSE;
        $isChecked = FALSE;
        foreach ( $this->validFilter['types'] as $typeData) {
            if ( $typeData['id'] != $type ) continue;
            if ( $ptype === NULL ) {
                if ($typeData['checked'] ) $isChecked = TRUE;
                break;
            }
            else {
                foreach ( $typeData['subcategories'] as $propData ) {
                    if ( $propData['id'] != $ptype) continue;
                    if ( $value === NULL ) {
                        if ($propData['checked']) $isChecked = TRUE;
                        break;
                    }
                    else {
                        foreach( $propData['subcategories'] as $valData ) {
                            if ( $valData['id'] != $value ) continue;
                            if ( $valData['checked'] ) $isChecked = TRUE;
                            break;
                        }
                    }
                }
            }
        }
        return $isChecked;
    }
    
    private function getKeyValidFilter( $type, $ptype=NULL, $value=NULL ) {
        $key = NULL;
        //$subcategories = NULL;
        
        foreach ( $this->validFilter['types'] as $typeKey => $typeData) {
            if ( $typeData['id'] != $type ) continue;
            if ( $ptype === NULL ) {
                $key = $typeKey;
                break;
            }
            else {
                foreach ( $typeData['subcategories'] as $propKey => $propData ) {
                    if ( $propData['id'] != $ptype) continue;
                    if ( $value === NULL ) {
                        $key = $propKey;
                        break;
                    }
                    else {
                        foreach( $propData['subcategories'] as $valKey => $valData ) {
                            if ( $valData['id'] != $value ) continue;
                            $key = $valKey;
                            break;
                        }
                    }
                }
            }
        }
        return $key;
    }
    
    private function mergeCategoryWithCounters($category, $counters) {
        
        foreach($counters as $counter) {
            
            if(!is_numeric($counter['type_id'])) continue;
            
            foreach($category as &$type) {
                if($type['id'] == $counter['type_id']) {
                    if(!empty($type['subcategories'])) {
                        foreach($type['subcategories'] as &$property) {
                            if($property['id'] == $counter['property_id']) {
                                $property['count'] += $counter['count'];

                                foreach($property['subcategories'] as &$value) {
                                    if($value['id'] == $counter['value']) {
                                        $value['count'] = $counter['count'];
                                    }
                                }
                            }
                        }
                    } else {
                        $type['count'] = $counter['count'];
                    }
                }
            }
        }
        
        // We needs to get counter from subcategories. Otherwise counted value 
        // will be multiplied by number of properties.
        foreach($category as &$type) {
            if(!empty($type['subcategories'])) {
                $anySub = reset($type['subcategories']);
                $type['count'] = $anySub['count'];
            }
        }
        return $category;
    }
   
    
    /**
     * Loads types. Do nothing if types are already loaded.
     * 
     * @return NULL
     */
    private function loadTypes() {
        
        if(!empty($this->types)) return;
        
        $this->types = $this->doctrine
                        ->getManager()
                        ->getRepository('FenchyNoticeBundle:Type')
                        ->getFilterTypes();
    }
    
    /**
     * Returns Type with given $id if it exists in $this->types.
     * Loads types automatically.
     * 
     * @param Integer $id
     * @return null|Type
     */
    private function getType($id) {
        
        $this->loadTypes();
        
        foreach ($this->types as $type) {
            
            if ($type->getId() === $id) return $type;
        }
        
        return NULL;
    }
   
    protected function applyFilterCriteria( $query ) {
        if ( $this->validFilter['distance'] && $this->validFilter['location'] ) {
            // distance withing specified location
            $lng = $this->validFilter['location']['lng'];
            $lat = $this->validFilter['location']['lat'];
            $radius = $this->validFilter['distance'];
            $query->addSelect("postGIS_ST_Distance(({$lng}), ({$lat}), l.pgisGeog) AS HIDDEN pgis_distance");
            $query->andWhere("postGIS_ST_DWithin(({$lng}), ({$lat}), l.pgisGeog, ({$radius})) = TRUE ");
        } else if ($this->validFilter['distance']) {
            // distance within user's location
            if( $this->currentUser ) {
                $cULocation = $this->currentUser->getLocation();
                $lat = $cULocation->getLatitude();
                $lng = $cULocation->getLongitude();
            }
            else {
                $lat = $this->container->getParameter('filter_default_lat');
                $lng = $this->container->getParameter('filter_default_lng');               
            }
            $radius = $this->validFilter['distance'];
            $query->addSelect("postGIS_ST_Distance(({$lng}), ({$lat}), l.pgisGeog) AS HIDDEN pgis_distance");
            $query->andWhere("postGIS_ST_DWithin(({$lng}), ({$lat}), l.pgisGeog, ({$radius})) = TRUE ");
        }
        
        if($this->validFilter['what']) {
            $what = str_replace(' ', '|', $this->validFilter['what']);
            $query->andWhere(
                "(similarity(n.content, :simWhat) * :simRatio + similarity(n.title, :simWhat)) >= :simMin")
                ->setParameter('simWhat', $what)
                ->setParameter('simRatio', $this->TCRatio)
                ->setParameter('simMin', $this->minSimilarity);
        }

        if($this->validFilter['postDate']) {
            list($dateStart, $dateEnd) = $this->getPostDateRange();

            $query->andWhere(new Expr\Orx(array(
                        new Expr\Andx(array('n.start_date >= :poststart', 'n.start_date <= :postend')),
                        new Expr\Andx(array('n.end_date >= :poststart', 'n.end_date <= :postend')),
                        new Expr\Andx(array('n.start_date < :poststart', 'n.end_date > :postend'))
                    )))
                    ->setParameter('poststart', $dateStart)
                    ->setParameter('postend', $dateEnd);
        } // end if postDate
        
        if($this->validFilter['types'] && !$this->allUnchecked($this->validFilter['types'])) {

            list($noticesQuery, $parameters) = $this->getTypesFilterSubquery();
            $query->andWhere($query->expr()->in('n', $noticesQuery->getDQL()));
            foreach($parameters as $k => $v)
                $query->setParameter($k, $v);
            
        } // if not all unchecked
        
        if(isset($this->validFilter['userId'])) {
            $query->andWhere('o.id = :userId')
                    ->setParameter('userId', $this->validFilter['userId']);
        }

        switch($this->validFilter['sort']) {
            case 'rel':
                $query->orderBy('similar', 'desc');
                break;
            case 'dist':
                $query->orderBy('pgis_distance', 'asc');
                break;
            case 'time':
                $query->orderBy('n.created_at', 'desc');
                break;
        }
        
        return $query;
    }

    /**
     * Returns filtered notices
     * @return array of Notice objects
     */
    public function getElements() {
        $query = $this->doctrine
            ->getManager()
            ->getRepository('FenchyNoticeBundle:Notice')
            ->getFindAllWithOwnerQuery();
        $query = $this->applyFilterCriteria($query);
        $query
            ->setFirstResult( $this->validFilter['range']['low'] )
            ->setMaxResults( $this->pagination + 1);
        return new Paginator($query, \TRUE);
    }
    
    public function getElementsKNN( $knn ) {
        $query = $this->doctrine
            ->getManager()
            ->getRepository('FenchyNoticeBundle:Notice')
            ->getFindAllWithOwnerQuery();
        
        if ( $this->validFilter['location'] ) {
            // distance withing specified location
            $lng = $this->validFilter['location']['lng'];
            $lat = $this->validFilter['location']['lat'];
        } else {
            // distance within user's location
            if( $this->currentUser ) {
                $cULocation = $this->currentUser->getLocation();
                $lat = $cULocation->getLatitude();
                $lng = $cULocation->getLongitude();
            }
            else {
                $lat = $this->container->getParameter('filter_default_lat');
                $lng = $this->container->getParameter('filter_default_lng');               
            }
        }

        $query
            ->select("postGIS_KNN_Operator(({$lng}), ({$lat}), l.pgisGeom) AS pgis_knn, n.id")
            ->orderBy('pgis_knn', 'asc')
            ->setFirstResult( 0 )->setMaxResults( 5 );
            foreach($query->getQuery()->getResult() as $row) $ids[] = $row['id'];
        $q2 = $this->doctrine
            ->getManager()
            ->getRepository('FenchyNoticeBundle:Notice')
            ->getFindAllWithOwnerQuery()
            ->andWhere($query->expr()->in('n', $ids));
            
       return new Paginator($q2, TRUE);
     }
    
    public function getElementsCount() {
        $query = $this->doctrine
            ->getManager()
            ->getRepository('FenchyNoticeBundle:Notice')
            ->getCountWithOwnerQuery();
        
        $query = $this->applyFilterCriteria($query); 
        $query->select('count(n)');
        $result = $query->getQuery()->getResult();
        
        return $result[0][1];
    }
    
    /**
     * 
     * @return type
     * @throws Exception
     * @todo If we decide to use this function we have to escape all variables!!!
     */
    protected function getElementsWithUsers() {
        
        throw new Exception("ListFilter::getElementsWithUsers - Feature not implemented. No pagination");
        
        $noticesSQL = "
            FROM notice__notices AS notice
            JOIN location ON location.notice_id = notice.id
            ";
        $noticesSelect = array('notice.id', "'notice' as type", 'notice.start_date as startdate');
        $noticesWhere = array('draft = false');
        
        $usersSQL = "
            FROM user__regular AS user
            JOIN location ON location.user_id = user.id
            ";
        $usersSelect = array('user.id', "'user' as type", 'user.last_login');
        $usersWhere = array('enabled = true', 'locked = false', 'expired = false');
        
        if($this->validFilter['userId']) {
            list($dateStart, $dateEnd) = $this->getPostDateRange();
            
            $noticesWhere[] = 'notice.user_id = '.$this->validFilter['userId'];
            $usersWhere[] = 'user.id = '.$this->validFilter['userId'];
        }
        
        if($this->validFilter['distance'] && $this->validFilter['location'] ) {
            $lng = $this->validFilter['location']['lng'];
            $lat = $this->validFilter['location']['lat'];
            $radius = $this->validFilter['distance'];
            array_push($noticesSelect, "ST_Distance( location.pgisgeog, POINT({$lng} {$lat}) ) AS pgis_distance");
            array_push($noticesWhere, "ST_DWithin( POINT({$lng} {$lat}), location.pgisgeog, {$radius})");
            array_push($usersSelect, "ST_Distance( location.pgisgeog, POINT({$lng} {$lat}) ) AS pgis_distance");
            array_push($usersWhere, "ST_DWithin( POINT({$lng} {$lat}), location.pgisgeog, {$radius})");
        } else if ($this->validFilter['distance']) {    
            if( $this->currentUser ) {
                $cULocation = $this->currentUser->getLocation();
                $lat = $cULocation->getLatitude();
                $lng = $cULocation->getLongitude();
            }
            else {
                $lat = $this->container->getParameter('filter_default_lat');
                $lng = $this->container->getParameter('filter_default_lng');               
            }
            array_push($noticesSelect, "ST_Distance( location.pgisgeog, POINT({$lng} {$lat}) ) AS pgis_distance");
            array_push($noticesWhere, "ST_DWithin( POINT({$lng} {$lat}), location.pgisgeog, {$radius})");
            array_push($usersSelect, "ST_Distance( location.pgisgeog, POINT({$lng} {$lat}) ) AS pgis_distance");
            array_push($usersWhere, "ST_DWithin( POINT({$lng} {$lat}), location.pgisgeog, {$radius})");
        }
        
        if($this->validFilter['what'] || $this->validFilter['sort'] = 'revelant') {
            $what = str_replace(' ', '|', $this->validFilter['what']);
            $usersSelect[] = "(similarity(n.content, '$what') * {$this->TCRatio} + similarity(n.title, '$what')) as similar";
            $noticesSelect[] = "(similarity(n.content, '$what') * {$this->TCRatio} + similarity(n.title, '$what')) as similar";
        }
        
        if($this->validFilter['what']) {
                $usersWhere[] = "similar >= {$this->minSimilarity}";
                $noticesWhere[] = "similar >= {$this->minSimilarity}";
        }
        
        if($this->validFilter['postDate']) {
            list($dateStart, $dateEnd) = $this->getPostDateRange();
            
            $noticesWhere[] = 'notice.created_at >= '.$dateStart->format('Y-m-d H:i:s');
            $noticesWhere[] = 'notice.created_at <= '.$dateEnd->format('Y-m-d H:i:s');
            $usersWhere[] = 'user.last_login >= '.$dateStart->format('Y-m-d H:i:s');
            $usersWhere[] = 'user.last_login <= '.$dateEnd->format('Y-m-d H:i:s');

        } // end if postDate
        
        if($this->validFilter['types'] && !$this->allUnchecked($this->validFilter['types'])) {

            $noticesWhere[] = 'notice.id IN ('.$this->getTypesFilterSubquery(TRUE).')';
            
        } // if not all unchecked
        
        switch($this->validFilter['sort']) {
            case 'rel':
                $order = 'ORDER BY similar DESC';
                break;
            case 'dist':
                $order = 'ORDER BY pgis_distance ASC';
                break;
            case 'time':
                $order = 'ORDER BY posdate DESC';
                break;
        }

        $noticesSelect  = implode(', ', $noticesSelect);
        $noticesWhere   = implode(' AND ', $noticesWhere);
        $usersSelect    = implode(', ', $usersSelect);
        $usersWhere     = implode(' AND ', $usersWhere);
        $sql = "($noticesSelect $noticesWhere) UNION ($usersSelect $usersWhere) $order";
        $stmt = $this->doctrine
                    ->getManager()
                    ->getConnection()
                    ->prepare($sql);
        $stmt->execute();
        $elements = $stmt->fetchAll();
        
        $uIds = array();
        $nIds = array();
        foreach($elements as $e) {
            if($e['type'] === 'notice') $nIds[] = $e['id'];
            else $uIds[] = $e['id'];
        }
        if(!empty($nIds)) {
            $query = $this->doctrine
                        ->getManager()
                        ->getRepository('FenchyNoticeBundle:Notice')
                        ->getFindAllWithOwnerQuery()
                        ->where('n.id IN ('.implode(', ', $nIds).')');
            $notices = $query->getQuery()->getResult();
        }
        else {
            $notices = array();
        }
        
        if(!empty($uIds)) {
            $query = $this->doctrine
                        ->getManager()
                        ->getRepository('FenchyUserBundle:User')
                        ->getFindAllQuery()
                        ->where('u.id IN ('.implode(', ', $uIds).')');
            $users = $query->getQuery()->getResult();
        }
        else {
            $users = array();
        }
        
        foreach($elements as $i => $e) {
            if($e['type'] === 'notice') {
                foreach($notices as $k => $notice) {
                    if($e['id'] == $notice->getId()) {
                        $elements[$i] = $notice;
                        unset($notices[$k]);
                        break;
                    }
                }
            }
            else {
                foreach($users as $k => $user) {
                    if($e['id'] == $user->getId()) {
                        $elements[$i] = $user;
                        unset($users[$k]);
                        break;
                    }
                }
            }
        }
        
        return $elements;
    }
    
    /**
     * Checks if all elements of given array are array and has not 'checked' => TRUE pair.
     * 
     * @param Array $arr
     * @return boolean
     */
    private function allUnchecked($arr) {

        foreach($arr as $data) {
            if(is_array($data) && array_key_exists('checked', $data) && $data['checked']) return FALSE;
        }
        
        return TRUE;
    }
    
    /**
     * Returns array with two elements $dateStart and $dateEnd.
     * Dates value depends on filter['postDate'].
     * Use list() to split result into 2 \DateTime objects.
     * 
     * @return Array
     */
    private function getPostDateRange() {
        
        switch($this->validFilter['postDate']) {
            case 'today' :
                $dateStart = new \DateTime();
                $dateStart->setTime(0, 0, 1);
                $dateEnd = new \DateTime();
                $dateEnd->setTime(23, 59, 59);
                break;

            case 'yesterday':
                $dateStart = new \DateTime('yesterday');
                $dateStart->setTime(0, 0, 1);
                $dateEnd = new \DateTime('yesterday');
                $dateEnd->setTime(23, 59, 59);
                break;

            case 'thisWeek':
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
                break;

            case 'lastWeek':
                $dateStart = new \DateTime();
                $dateStart->setTime(0, 0, 1);
                $dateStart->sub(new \DateInterval('P7D'));
                $dateEnd = new \DateTime();
                $dateEnd->setTime(23, 59, 59);
                break;

            case 'pastMonth':
                $dateStart = new \DateTime();
                $dateStart->setTime(0, 0, 1);
                $dateStart->sub(new \DateInterval('P1M'));
                $dateEnd = new \DateTime();
                $dateEnd->setTime(23, 59, 59);
                break;
            default :
                $dateStart = new \DateTime($this->validFilter['postDate']);
                $dateStart->setTime(0, 0, 1);
                $dateEnd = new \DateTime($this->validFilter['postDate']);
                $dateEnd->setTime(23, 59, 59);
                break;
        } // end of switch
        
        return array($dateStart, $dateEnd);
    }
    
    /**
     * Returns query which selects notices which matches to filtred types.
     * Or its ids when $sql flag set to true.
     * 
     * @param boolean $sql If set to true return sql string, Query otherwise.
     * @return Doctrine\ORM\QueryBuilder | string query
     */
    private function getTypesFilterSubquery($sql = FALSE) {
        
        $this->loadTypes();
        $noticesQuery = $this->doctrine
                ->getEntityManager()
                ->getRepository('FenchyNoticeBundle:Notice')
                ->createQueryBuilder('n2')
                ->select($sql ? 'n2.id' : 'n2')
                ->leftJoin('n2.type', 't2')
                ->leftJoin('n2.values', 'v2')
                ->leftJoin('t2.properties', 'p2');

        $typeAlternatives = array();
        $parameters = array();
        foreach($this->types as $type) {

            if($this->isValidFilterTypeChecked($type->getId(), NULL, NULL)) {

                $typeAnd = array('n2.type = :condType'.$type->getId());
                $parameters['condType'.$type->getId()] = $type;

                // if type has subcategories/properties
                $typeKey = $this->getKeyValidFilter($type->getId(), NULL, NULL);                 
                if(
                    array_key_exists('subcategories', $this->validFilter['types'][$typeKey]) && 
                    is_array($this->validFilter['types'][$typeKey]['subcategories']) &&
                    !empty($this->validFilter['types'][$typeKey]['subcategories'])
                ) {

                    foreach($this->validFilter['types'][$typeKey]['subcategories'] as $pId => $prop) {
                        // if type has this property
                        if( $property = $type->hasPropertyOfId($prop['id']) ) {
                            $values = array();
                            if(!$this->allUnchecked($prop['subcategories'])) {
                                
                                foreach($prop['subcategories'] as $val => $valData) {
                                    // if value is selected
                                    if(is_array($valData) && array_key_exists('checked', $valData) && $valData['checked']) {
                                        $values[] = $valData['id'];
                                    }
                                }
                            }
                            if(!empty($values)) {
                                // If there is more than one property for type, then we have to 
                                // join conditions for all of them by 'AND'
                                // To do that we need to additionaly join each value separately.
                                if(count($this->validFilter['types'][$typeKey]['subcategories']) > 1) {
                                    $noticesQuery->leftJoin('n2.values', 'v2'.$prop['id'], Expr\Join::WITH, 'v2'.$prop['id'].'.property = '.$prop['id']);
                                    $typeAnd[] = 'v2'.$prop['id'].'.value in (:values'.$prop['id'].')';
                                    $parameters['values'.$prop['id']] = $values;
                                }
                                else {
                                    $typeAnd[] = 'v2.value in (:values'.$prop['id'].')';
                                    $parameters['values'.$prop['id']] = $values;
                                }
                            }
                        } // if type has property
                    } // foreach properties
                } // if type has properties
                if(!empty($typeAnd)) {
                    $typeAlternatives[] = new Expr\Andx($typeAnd);
                }
            } // if type in filter
        } // foreach type

        if(!empty($typeAlternatives))
            $noticesQuery->andWhere(new Expr\Orx($typeAlternatives));
        if($sql)
            return $noticesQuery->getQuery()->getSQL();
        else return array($noticesQuery, $parameters);
    }
    
    
    

    
    
    
    
    public function buildValidateFilter($clientFilter) {

        if ( empty($clientFilter) ) return;
        
        // DISTANCE
        if ( array_key_exists('distance', $clientFilter) ) {
            $intDistance = (int) $clientFilter['distance'];
            if ( $intDistance >= $this->container->getParameter('filter_min_distance')
              && $intDistance <= $this->container->getParameter('filter_max_distance') 
                    - $this->container->getParameter('distance_slider_snap')) {
                $this->validFilter['distance'] = $intDistance;
            } else if ( $intDistance >= $this->container->getParameter('filter_max_distance') ) {
                $this->validFilter['distance'] = \NULL;
            }
        } 
        
        // POST DATE
        if( array_key_exists('postDate', $clientFilter) 
                && (
                    in_array($clientFilter['postDate'], $this->allowedPostDates) 
                    || date('Y-m-d', strtotime($clientFilter['postDate'])) == $clientFilter['postDate']
                ) 
        ) {
            $this->validFilter['postDate'] = $clientFilter['postDate'];
        }
        
        // CATEGORIES ( TYPES )
        if( array_key_exists('categories', $clientFilter) ) {
            
            $this->loadTypes();
            $clientTypes = $clientFilter['categories'];
            $typesAreValid = TRUE;  
            foreach($clientTypes as $typeId => $typeData) {
                $type = $this->getType($typeData['id']);
                if(NULL === $type) {
                    if ( $typeData['id'] != 'users') {
                        $this->validationErrors[] = 'type_value_'.$typeId;
                        $typesAreValid = FALSE;
                    }
                } else {
                    foreach($typeData['subcategories'] as $propId => $propData) {
                        $property = $type->hasPropertyOfId($propData['id']);
                        if(FALSE === $property) {
                            $this->validationErrors[] = $type.'_has_no_property_'.$propId;
                            $typesAreValid = FALSE;
                        } else {
                            foreach ( $propData['subcategories'] as $valId => $valData ) {
                                if( !$property->isValueValid($valData['id']) ) {
                                    $this->validationErrors[] = 'property_values';
                                    $typesAreValid = FALSE;
                                }
                            }
                        }
                    }
                }
            }
            if ( $typesAreValid )
                $this->validFilter['types'] = $clientTypes;
        }
        
        // SEARCH PHRASE:
        if( array_key_exists('what', $clientFilter) ) {
            $this->validFilter['what'] = $clientFilter['what'];
            $this->dictionary->store($clientFilter['what']);
        }
        
        if( array_key_exists('location', $clientFilter) &&
            is_array($clientFilter['location']) &&
            array_key_exists('lat', $clientFilter['location']) &&    
            array_key_exists('lng', $clientFilter['location']) &&
            is_numeric($clientFilter['location']['lat']) &&
            is_numeric($clientFilter['location']['lng']) ) {
            
            $clientLat = (float) $clientFilter['location']['lat'];
            $clientLng = (float) $clientFilter['location']['lng'];
            
            if ( $clientLat >  -90 && $clientLat <  90 &&
                 $clientLng > -180 && $clientLng < 180) {
                $this->validFilter['location'] = array(
                    'lat' => $clientLat,
                    'lng' => $clientLng
                );
                if (array_key_exists('where', $clientFilter['location']))
                    $this->validFilter['location']['where'] = $clientFilter['location']['where'];
            }
        }
        
        if( array_key_exists('sort', $clientFilter) ) {
            if ( in_array($clientFilter('sortBy'), $this->allowedSorts) )
                $this->validFilter['sort'] = $clientFilter['sortBy'];
        }
        
        if( array_key_exists('range', $clientFilter) &&
            is_array($clientFilter['range']) &&
            array_key_exists('low', $clientFilter['range']) &&
            array_key_exists('high', $clientFilter['range']) &&
            is_numeric($clientFilter['range']['low']) &&
            is_numeric($clientFilter['range']['high']) ) {
            
            $rangeLow = (int) $clientFilter['range']['low'];
            $rangeHigh = (int) $clientFilter['range']['high'];
            
            $this->validFilter['range'] = array(
                'low' =>  $clientFilter['range']['low'],
                'high' =>  $clientFilter['range']['high']
            );
        }
        
        // user
        if(array_key_exists('userId', $clientFilter)) {
            $this->validFilter['userId'] = $clientFilter['userId'];
        }
        
    }
}