<?php
namespace Domain\Model\Home;

use Domain\Model\Model;
class UserModel extends Model {
	protected $table = 'user';
	
	protected $fillAble = array(
		'name',
		'email',
		'password',
		'sex',
		'avatar',
		'token',
		'login_num',
		'update_ip',
		'update_at',
		'previous_ip',
		'previous_at',
		'create_ip',
		'create_at'
	);
}