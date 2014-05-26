<?php

namespace Fenchy\AdminBundle\Entity;
/**
 * Description of UsersFilter
 *
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class DictionaryFilter {
    
    public $text;
    public $search_quantity_less;
    public $search_quantity_more;
    public $tag_quantity_less;
    public $tag_quantity_more;
    public $search_activated;
    public $tag_activated;
    public $tag;
    public $disabled;
    public $order;
    public $sort;

    public function __construct() {
        
        $this->sort = 'id';
        $this->order = 'asc';
    }
    
}

?>
