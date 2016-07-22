<?php
namespace Domain\Model\Home;

use Domain\Model\Model;
/**
 * Class RoleModel
 * @property integer $id
 * @property string $name
 * @property integer $parent_id
 * @property string $data
 */
class RoleModel extends Model {
	public static $table = 'role';


	protected function rules() {
		return array (
		  'name' => 'required|string:3-45',
		  'parent_id' => '|int',
		  'data' => '',
		);
	}

	protected function labels() {
		return array (
		  'id' => 'Id',
		  'name' => 'Name',
		  'parent_id' => 'Parent Id',
		  'data' => 'Data',
		);
	}
}