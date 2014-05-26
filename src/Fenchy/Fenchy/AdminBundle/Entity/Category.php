<?php

namespace Fenchy\AdminBundle\Entity;
/**
 * Description of UsersFilter
 *
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class Category {
    
	public $id;
    public $name;
    public $sequence;
    public $order;
	public $sort;

    public function __construct() {
    	$this->id = null;
    	$this->name = null;
    	$this->sequence = null;
        $this->order = 'id';
        $this->sort = 'asc';
    }
    
}

?>
