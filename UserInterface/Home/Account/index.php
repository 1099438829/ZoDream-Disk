<?php
defined('APP_DIR') or exit();
use Zodream\Domain\Html\Bootstrap\FormWidget;
/** @var $this \Zodream\Domain\Response\View */
$this->extend(array(
    'layout' => array(
        'head'
    )), array(
        'login.css'
    )
);
$message = $this->get('message');
$html = <<<HTML
<div class="login">
    <div class="col-lg-10">
        <div class="input-group m-top20">
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            <input type="text" name="username" id="userName" placeholder="请输入用户名" class="logininput form-control input-lg">
        </div>
        <div class="input-group m-top20">
            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
            <input type="password" name="password" id="passWord" placeholder="请输入密码" class="logininput form-control input-lg">
        </div>
    </div>
    <div class="col-lg-10">
        <div class="checkbox">
            <label id="rem">
                <input type="checkbox" id="remember" name="remember" value="1" checked="checked"> 自动登录
            </label>
        </div>
    </div>
    <p class="text-danger text-center">{$message}</p>
    <div class="col-lg-10">
        <button class="btn btn-info" type="submit" id="loginbtn">登录</button>
    </div>
</div>
HTML;

echo FormWidget::begin()->html($html)->end();

$this->extend(array(
    'layout' => array(
        'foot'
    )), [
    ]
);
