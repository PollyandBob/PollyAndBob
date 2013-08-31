<?php

namespace Fenchy\AdminBundle\Entity;
/**
 * Description of UsersFilter
 *
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class UsersFilter {
    
    public $reported_only;
    public $name;
    public $created_before;
    public $created_after;
    public $order;
    public $sort;

    public function __construct() {
        
        $this->reported_only = FALSE;
        $this->order = 'id';
        $this->sort = 'asc';
    }
    
}

?>
