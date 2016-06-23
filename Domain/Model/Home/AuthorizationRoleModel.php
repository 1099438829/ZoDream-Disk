<?php
namespace Domain\Model\Home;

use Domain\Model\Model;
class AuthorizationRoleModel extends Model {
	protected $table = 'authorization_role';
	
	protected $fillAble = array(
		'role_id',
		'authorization_id'
	);
}