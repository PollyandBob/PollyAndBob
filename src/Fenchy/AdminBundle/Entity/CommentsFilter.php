<?php

namespace Fenchy\AdminBundle\Entity;
/**
 * Description of UsersFilter
 *
 * @author Bhumi Gothi
 */
class CommentsFilter {    
    
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
