<?php
namespace Domain\Model\Home;

use Domain\Model\Model;
class DiskModel extends Model {
	protected $table = 'disk';
	
	protected $fillAble = array(
		'name',
		'extsion',
		'icon',
		'size',
		'md5',
		'location',
		'is_dir',
		'parent_id',
		'user_id',
		'delete_at',
		'update_at',
		'create_at'
	);
}