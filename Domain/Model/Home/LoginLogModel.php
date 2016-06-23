<?php
namespace Domain\Model\Home;

use Domain\Model\Model;
class LoginLogModel extends Model {
	protected $table = 'login_log';
	
	protected $fillAble = array(
		'ip',
		'user',
		'status',
		'mode',
		'create_at'
	);
}