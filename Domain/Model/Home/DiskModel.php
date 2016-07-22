<?php
namespace Domain\Model\Home;

use Domain\Model\Model;
/**
 * Class DiskModel
 * @property integer $id
 * @property string $name
 * @property string $extension
 * @property string $icon
 * @property integer $size
 * @property string $md5
 * @property string $location
 * @property integer $is_dir
 * @property integer $parent_id
 * @property integer $user_id
 * @property integer $delete_at
 * @property integer $update_at
 * @property integer $create_at
 */
class DiskModel extends Model {
	public static $table = 'disk';

	protected function rules() {
		return array (
		  'name' => 'required|string:3-200',
		  'extension' => '|string:3-45',
		  'icon' => '|string:3-100',
		  'size' => '|int',
		  'md5' => 'string:31-32',
		  'location' => 'required|string:3-255',
		  'is_dir' => '|int:0-1',
		  'parent_id' => '|int',
		  'user_id' => 'required|int',
		  'delete_at' => '|int',
		  'update_at' => '|int',
		  'create_at' => '|int',
		);
	}

	protected function labels() {
		return array (
		  'id' => 'Id',
		  'name' => 'Name',
		  'extension' => 'Extension',
		  'icon' => 'Icon',
		  'size' => 'Size',
		  'md5' => 'Md5',
		  'location' => 'Location',
		  'is_dir' => 'Is Dir',
		  'parent_id' => 'Parent Id',
		  'user_id' => 'User Id',
		  'delete_at' => 'Delete At',
		  'update_at' => 'Update At',
		  'create_at' => 'Create At',
		);
	}
}