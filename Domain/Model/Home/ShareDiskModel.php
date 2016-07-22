<?php
namespace Domain\Model\Home;

use Domain\Model\Model;
/**
 * Class ShareDiskModel
 * @property integer $share_id
 * @property integer $disk_id
 */
class ShareDiskModel extends Model {
	public static $table = 'share_disk';

	protected $primaryKey = array (
	);

	protected function rules() {
		return array (
		  'share_id' => 'required|int',
		  'disk_id' => 'required|int',
		);
	}

	protected function labels() {
		return array (
		  'share_id' => 'Share Id',
		  'disk_id' => 'Disk Id',
		);
	}
}