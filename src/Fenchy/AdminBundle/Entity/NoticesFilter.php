<?php

namespace Fenchy\AdminBundle\Entity;
/**
 * Description of UsersFilter
 *
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class NoticesFilter {
    
    public $reported_only;
    public $title;
    public $created_before;
    public $created_after;
    public $order;
    public $sort;

    public function __construct() {
        
        $this->order = 'id';
        $this->sort = 'asc';
    }
    
}

?>
