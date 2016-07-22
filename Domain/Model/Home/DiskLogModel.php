<?php
namespace Domain\Model\Home;

use Domain\Model\Model;
/**
 * Class DiskLogModel
 * @property integer $id
 * @property integer $disk_id
 * @property string $event
 * @property integer $user_id
 * @property integer $create_at
 */
class DiskLogModel extends Model {
	public static $table = 'disk_log';

	protected function rules() {
		return array (
		  'disk_id' => 'required|int',
		  'event' => 'required|string:3-255',
		  'user_id' => '|int',
		  'create_at' => '|int',
		);
	}

	protected function labels() {
		return array (
		  'id' => 'Id',
		  'disk_id' => 'Disk Id',
		  'event' => 'Event',
		  'user_id' => 'User Id',
		  'create_at' => 'Create At',
		);
	}
}