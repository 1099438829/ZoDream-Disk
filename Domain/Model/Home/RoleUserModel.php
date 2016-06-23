<?php
namespace Domain\Model\Home;

use Domain\Model\Model;
class RoleUserModel extends Model {
	protected $table = 'role_user';
	
	protected $fillAble = array(
		'user_id',
		'role_id'
	);
}