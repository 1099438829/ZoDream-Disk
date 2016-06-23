<?php
namespace Domain\Model\Home;

use Domain\Model\Model;
class DiskShareModel extends Model {
	protected $table = 'disk_share';
	
	protected $fillAble = array(
		'disk_id',
		'save_count',
		'down_count',
		'view_count',
		'mode',
		'password',
		'create_at',
		'end_at'
	);
}