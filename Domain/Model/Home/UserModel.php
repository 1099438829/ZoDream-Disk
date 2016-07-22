<?php
namespace Domain\Model\Home;

use Domain\Model\Model;
use Zodream\Domain\Filter\DataFilter;
use Zodream\Infrastructure\Cookie;
use Zodream\Infrastructure\ObjectExpand\Hash;
use Zodream\Infrastructure\ObjectExpand\StringExpand;
use Zodream\Infrastructure\Request;

/**
 * Class UserModel
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $sex
 * @property string $avatar
 * @property string $token
 * @property integer $login_num
 * @property string $update_ip
 * @property integer $update_at
 * @property string $previous_ip
 * @property integer $previous_at
 * @property string $create_ip
 * @property integer $create_at
 */
class UserModel extends Model {
	public static $table = 'user';

	protected $primaryKey = array (
	  	'id',
	  	'name',
	  	'email',
	);

	protected function rules() {
		return array (
		  'name' => 'required|string:3-30',
		  'email' => '|string:3-100',
		  'password' => 'required|string:3-64',
		  'sex' => '',
		  'avatar' => '|string:3-200',
		  'token' => '|string:3-60',
		  'login_num' => '|int',
		  'update_ip' => '|string:3-20',
		  'update_at' => '|int',
		  'previous_ip' => '|string:3-20',
		  'previous_at' => '|int',
		  'create_ip' => '|string:3-20',
		  'create_at' => '|int',
		);
	}

	protected function labels() {
		return array (
		  'id' => 'Id',
		  'name' => 'Name',
		  'email' => 'Email',
		  'password' => 'Password',
		  'sex' => 'Sex',
		  'avatar' => 'Avatar',
		  'token' => 'Token',
		  'login_num' => 'Login Num',
		  'update_ip' => 'Update Ip',
		  'update_at' => 'Update At',
		  'previous_ip' => 'Previous Ip',
		  'previous_at' => 'Previous At',
		  'create_ip' => 'Create Ip',
		  'create_at' => 'Create At',
		);
	}

	/**
	 * @param $name
	 * @return static
	 */
	public static function findByName($name) {
		return static::findOne(['name' => $name]);
	}
	
	public function setPassword($password) {
		return $this->password = Hash::make($password);
	}
	
	public function validatePassword($password) {
		return Hash::verify($password, $this->password);
	}

	public function login() {
		if (!$this->has()) {
			$this->load();
		}
		if (!$this->validate(array(
			'username'    => 'required',
			'password' => 'required|string:3-30'
		))) {
			return false;
		}
		$user = static::findByName($this->name);
		if (empty($user)) {
			$this->setError('name', '用户不存在！');
			return false;
		}
		if ($user->validatePassword($this->password)) {
			$this->setError('password', '密码错误！');
			return false;
		}
		$user['previous_ip'] = $user['update_ip'];
		$user['previous_at'] = $user['update_at'];
		$user['login_num'] = intval($user['login_num']) + 1;
		$user['update_ip'] = Request::ip();
		$user['update_at'] = time();
		if (Request::post('remember') != null) {
			$token = StringExpand::random(10);
			$user['token'] = $token;
			Cookie::set('token', $token, 3600 * 24 * 30);
		}else {
			$user['token'] = null;
		}
		if (empty($user->update())) {
			return false;
		}
		
		Factory::session()->set('user', $user);
		return true;
	}

	/**
	 * 注册
	 * @return bool
	 */
	public function register() {
		if (!$this->has()) {
			$this->load();
		}
		if (!$this->validate(array(
			'name'     => 'required|string:2-20',
			'email'    => 'required|email',
			'password' => 'required|confirm:repassword|string:3-30',
			'agree'    => 'required'
		))) {
			return false;
		}
		unset($data['repassword'], $data['agree']);
		$data['password'] = Hash::make(($data['password']));
		$data['create_at'] = time();
		$data['avatar'] = '/assets/images/avatar/'.random_int(0, 48).'.png';
		$data['create_ip'] = Request::ip();
		return !empty(DiskModel::query('user')->add($data));
	}
}