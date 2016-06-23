<?php
namespace Domain\Model\Home;

use Domain\Model\Model;
class DiskLogModel extends Model {
	protected $table = 'disk_log';
	
	protected $fillAble = array(
		'disk_id',
		'event',
		'user_id',
		'create_at'
	);
}