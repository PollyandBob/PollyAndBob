<?php
namespace Fenchy\AdminBundle\Entity;
/**
 * Description of UsersFilter
 *
 * @author Bhumi Gothi 
 */
class IdentityVerificationFilter {

	public $username;
	public $status;
	public $order;
    public $sort;
	
	public function __construct() {

		$this->order = 'asc';
        $this->sort = 'id';
	}

}

?>