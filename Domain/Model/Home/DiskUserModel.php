<?php
namespace Domain\Model\Home;

use Domain\Model\Model;
class DiskUserModel extends Model {
	protected $table = 'disk_user';
	
	protected $fillAble = array(
		'share_id',
		'user_id'
	);
}