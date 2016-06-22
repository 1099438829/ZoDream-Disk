<?php
namespace Service\Home;

class AccountController extends Controller {
    function indexAction() {
        $this->show([
            'title' => '登录'
        ]);
    }
}