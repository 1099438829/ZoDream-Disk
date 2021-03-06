<?php
namespace Domain\Model\Home;

use Domain\Model\Model;
/**
 * Class RoleUserModel
 * @property integer $user_id
 * @property integer $role_id
 */
class RoleUserModel extends Model {
	public static $table = 'role_user';

	protected $primaryKey = array (
	);

	protected function rules() {
		return array (
		  'user_id' => 'required|int',
		  'role_id' => 'required|int',
		);
	}

	protected function labels() {
		return array (
		  'user_id' => 'User Id',
		  'role_id' => 'Role Id',
		);
	}
}