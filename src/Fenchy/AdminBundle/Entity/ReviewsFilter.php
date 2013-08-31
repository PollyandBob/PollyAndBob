<?php

namespace Fenchy\AdminBundle\Entity;
/**
 * Description of UsersFilter
 *
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class ReviewsFilter {
    
    public $reported_only;
    public $text;
    public $type;
    public $created_after;
    public $created_before;
    public $author;
    public $target;
    public $order;
    public $sort;

    public function __construct() {
        
        $this->reported_only = FALSE;
        $this->order = 'id';
        $this->sort = 'asc';
        $this->type = \Fenchy\NoticeBundle\Entity\Review::TYPE_NEGATIVE;
        
    }
    
}

?>
