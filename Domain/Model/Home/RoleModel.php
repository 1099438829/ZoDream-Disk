<?php
namespace Domain\Model\Home;

use Domain\Model\Model;
class RoleModel extends Model {
	protected $table = 'role';
	
	protected $fillAble = array(
		'name'
	);
}