<?php
namespace Domain\Form;
/**
 * Created by PhpStorm.
 * User: zx648
 * Date: 2016/4/5
 * Time: 10:54
 */
use Domain\Model\DiskModel;
use Zodream\Domain\Authentication\Auth;
use Zodream\Domain\Form;
use Zodream\Infrastructure\Cookie;
use Zodream\Infrastructure\Factory;
use Zodream\Infrastructure\ObjectExpand\Hash;
use Zodream\Infrastructure\ObjectExpand\StringExpand;
use Zodream\Infrastructure\Request;

class DiskForm extends Form {

    /**
     * 设置验证规则
     * @param array $rules
     */
    public function setRules(array $rules) {
        $this->rules = $rules;
    }

    public function login() {
        $data = Request::post('username,password');
        if (!$this->validate($data, array(
            'username'    => 'required',
            'password' => 'required|string:3-30'
        ))) {
            $this->send('message', '验证失败！');
            return false;
        }
        $model =  DiskModel::query('user');
        $user = $model->findOne(['name' => $data['username']]);
        if (empty($user)) {
            $this->send('message', '用户不存在！');
            return false;
        }
        if (!Hash::verify($data['password'], $user['password'])) {
            $this->send('message', '密码错误！');
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
        $model->updateById($user['id'], array(
            'previous_ip' => $user['previous_ip'],
            'previous_at' => $user['previous_at'],
            'login_num' => $user['login_num'],
            'update_ip' => $user['update_ip'],
            'update_at' => $user['update_at'],
            'token' => $user['token']
        ));
        $user['roles'] = DiskModel::query('role_user r')->findAll(array(
            'right' => array(
                'authorization_role ar',
                'r.role_id = ar.role_id'
            ),
            'left' => array(
                'authorization a',
                'a.id = ar.authorization_id'
            ),
            'where' => 'r.user_id = '.$user['id']
        ), array(
            'id' => 'a.id',
            'name' => 'a.name'
        ));
        Factory::session()->set('user', $user);
        return true;
    }

    /**
     * 注册
     * @return bool
     */
    public function register() {
        $data = Request::post('name,email,password,repassword,agree');
        if (!$this->validate($data, array(
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

    public function resetPassword() {
        $data = Request::post('password,repassword,oldpassword');
        if (!$this->validate($data, array(
            'oldpassword'     => 'required|string:3-30',
            'password' => 'required|confirm:repassword|string:3-30'
        ))) {
            return false;
        }
        if (!Hash::verify($data['oldpassword'], Auth::user()['password'])) {
            return false;
        }
        $password = Hash::make($data['password']);
        $row = DiskModel::query('user')->updateById(Auth::user()['id'], array('password' => $password));
        if (empty($row)) {
            return false;
        }
        return true;
    }

    public function addRole() {
        $id = intval(Request::post('id'));
        $name = Request::post('name');
        if (empty($name)) {
            return false;
        }
        $auth = Request::post('auth', array());
        if (empty($id)) {
            $id = DiskModel::query('role')->add(array(
                'name' => $name
            ));
            if (empty($id)) {
                return false;
            }
        } else {
            DiskModel::query('authorization_role')->delete('role_id = '.$id);
        }
        $sql = [];
        foreach ($auth as $item) {
            $sql[] = [$id, intval($item)];
        }
        DiskModel::query('authorization_role')->addValues(['role_id', 'authorization_id'], $sql);
        return true;
    }

    /**
     * 后台添加用户
     * @return bool
     */
    public function addUser() {
        $id = intval(Request::post('id'));
        $data = Request::post('name,email,password,repassword');
        if (empty($id)) {
            if (!$this->validate($data, array(
                'name'     => 'required|string:2-20',
                'email'    => 'required|email',
                'password' => 'required|confirm:repassword|string:3-30'
            ))) {
                return false;
            }
            unset($data['repassword']);
            $data['password'] = Hash::make(($data['password']));
            $data['create_at'] = time();
            $data['create_ip'] = Request::ip();
            $id = DiskModel::query('user')->add($data);
            if (empty($id)) {
                return false;
            }
        } else {
            if (empty($data['password']) || $data['password'] != $data['repassword']) {
                unset($data['password']);
            } else {
                $data['password'] = Hash::make(($data['password']));
            }
            unset($data['repassword']);
            DiskModel::query('user')->updateById($id, $data);
            DiskModel::query('role_user')->delete('user_id = '.$id);
        }
        $role = Request::post('role');
        if (!empty($role)) {
            DiskModel::query('role_user')->add(array(
                'user_id' => $id,
                'role_id' => $role
            ));
        }
        return true;
    }


    /**
     * 表单开始
     * @param array $rules
     * @return static
     */
    public static function start($rules = array()) {
        $instance = new static();
        if (!empty($rules)) {
            $instance->setRules($rules);
        }
        return $instance;
    }
}