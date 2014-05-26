<?php

namespace Fenchy\AdminBundle\Entity;
/**
 * Description of UsersFilter
 *
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class ReviewsFilter {    
    
    public $text;
    public $author;
    public $receiver;
    public $target;
    public $order;
    public $sort;

    public function __construct() {
        
        $this->order = 'id';
        $this->sort = 'asc';        
    }
    
}

?>
