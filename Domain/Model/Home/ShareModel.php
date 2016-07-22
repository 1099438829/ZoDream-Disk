<?php
namespace Domain\Model\Home;

use Domain\Model\Model;
/**
 * Class ShareModel
 * @property integer $id
 * @property integer $save_count
 * @property integer $down_count
 * @property integer $view_count
 * @property integer $mode
 * @property string $password
 * @property integer $end_at
 * @property integer $create_at
 */
class ShareModel extends Model {
	public static $table = 'share';

	protected function rules() {
		return array (
		  'save_count' => '|int',
		  'down_count' => '|int',
		  'view_count' => '|int',
		  'mode' => '|int',
		  'password' => '|string:3-6',
		  'end_at' => '|int',
		  'create_at' => '|int',
		);
	}

	protected function labels() {
		return array (
		  'id' => 'Id',
		  'save_count' => 'Save Count',
		  'down_count' => 'Down Count',
		  'view_count' => 'View Count',
		  'mode' => 'Mode',
		  'password' => 'Password',
		  'end_at' => 'End At',
		  'create_at' => 'Create At',
		);
	}
}