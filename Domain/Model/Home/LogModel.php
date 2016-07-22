<?php
namespace Domain\Model\Home;

use Domain\Model\Model;
/**
 * Class LogModel
 * @property integer $id
 * @property string $ip
 * @property string $url
 * @property string $user
 * @property string $event
 * @property string $data
 * @property integer $create_at
 */
class LogModel extends Model {
	public static $table = 'log';

	protected function rules() {
		return array (
		  'ip' => 'required|string:3-20',
		  'url' => '|string:3-255',
		  'user' => 'required|string:3-30',
		  'event' => 'required|string:3-20',
		  'data' => '',
		  'create_at' => 'required|int',
		);
	}

	protected function labels() {
		return array (
		  'id' => 'Id',
		  'ip' => 'Ip',
		  'url' => 'Url',
		  'user' => 'User',
		  'event' => 'Event',
		  'data' => 'Data',
		  'create_at' => 'Create At',
		);
	}

	/**
	 * 添加纪录
	 * @param $data
	 * @param $action
	 * @return int
	 */
	public static function addLog($data, $action) {
		return (new static)->add(array(
			'event' => $action,
			'data' => is_string($data) ? $data : json_encode($data),
			'url' => Url::to(),
			'ip' => Request::ip(),
			'create_at' => time(),
			'user' => Auth::guest() ? null : Auth::user()['name']
		));
	}

	/**
	 * 判断是否存在记录
	 * @param $action
	 * @param string|integer $data 如果为null则不判断数据
	 * @return bool
	 */
	public static function hasLog($action, $data = null) {
		$sql = "ip = '".Request::ip()."'";
		if (!Auth::guest()) {
			$sql = "({$sql} or user = '".Auth::user()['name']."')";
		}
		$sql .= " AND event = '{$action}'";
		if (!is_null($data)) {
			$sql .= " AND data = '".(is_string($data) ? $data : json_encode($data))."'";
		}
		return empty(static::findOne($sql));
	}
}