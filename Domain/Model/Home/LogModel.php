<?php
namespace Domain\Model\Home;

use Domain\Model\Model;
class LogModel extends Model {
	protected $table = 'log';
	
	protected $fillAble = array(
		'ip',
		'url',
		'user',
		'event',
		'data',
		'create_at'
	);
}