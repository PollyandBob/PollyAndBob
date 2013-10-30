<?php

namespace Fenchy\AdminBundle\Entity;
/**
 * Description of UsersFilter
 *
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class CategoriesFilter {
    
	
    public $name;
    public $sequence;
    public $order;
	public $sort;

    public function __construct() {
        $this->name = null;
        $this->order = 'id';
        $this->sort = 'asc';
    }
    
}

?>
