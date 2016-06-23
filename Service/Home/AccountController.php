<?php
namespace Service\Home;

use Domain\Form\DiskForm;
use Domain\Model\DiskModel;
use Zodream\Domain\Response\Redirect;
use Zodream\Infrastructure\Cookie;
use Zodream\Infrastructure\Request;
use Zodream\Infrastructure\Session;

class AccountController extends Controller {
    protected function rules() {
        return array(
            'logout' => '@',
            'check' => '*',
            '*' => '?'
        );
    }

    function indexAction() {
        $this->show(array(
            'title' => '登录'
        ));
    }

    function indexPost() {
        $result = DiskForm::start()->login();
        DiskModel::query()->addLoginLog(Request::post('email'), $result);
        if (!$result) {
            return;
        }
        $url = Request::get('ReturnUrl', 'index.php');
        Redirect::to($url);
    }

    function logoutAction() {
        DiskModel::query('user')->updateById(Auth::user()['id'], array(
            'token' => null
        ));
        Session::getInstance()->clear();
        Cookie::delete('token');
        Redirect::to('account');
    }
}