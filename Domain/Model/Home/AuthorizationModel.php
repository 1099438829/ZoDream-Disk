<?php
namespace Domain\Model\Home;

use Domain\Model\Model;
class AuthorizationModel extends Model {
	protected $table = 'authorization';
	
	protected $fillAble = array(
		'name'
	);
}