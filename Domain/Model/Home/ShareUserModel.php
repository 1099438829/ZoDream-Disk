<?php
namespace Domain\Model\Home;

use Domain\Model\Model;
/**
 * Class ShareUserModel
 * @property integer $share_id
 * @property integer $user_id
 */
class ShareUserModel extends Model {
	public static $table = 'share_user';

	protected $primaryKey = array (
	);

	protected function rules() {
		return array (
		  'share_id' => 'required|int',
		  'user_id' => 'required|int',
		);
	}

	protected function labels() {
		return array (
		  'share_id' => 'Share Id',
		  'user_id' => 'User Id',
		);
	}
}