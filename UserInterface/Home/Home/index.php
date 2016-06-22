<?php
defined('APP_DIR') or exit();
/** @var $this \Zodream\Domain\Response\View */
$this->extend(array(
    'layout' => array(
        'head',
        'navbar'
    )), array(
    )
);
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <div class="list-group menu">
                <a href="<?php $this->url('');?>" class="list-group-item active">
                    <span class="zd_all"></span>全部文件
                </a>
                <a href="<?php $this->url('');?>" class="list-group-item">
                    <span class="zd_img"></span>图片
                </a>
                <a href="<?php $this->url('');?>" class="list-group-item">
                    <span class="zd_doc"></span>文档
                </a>
                <a href="<?php $this->url('');?>" class="list-group-item">
                    <span class="zd_video"></span>视频
                </a>
                <a href="<?php $this->url('');?>" class="list-group-item">
                    <span class="zd_bt"></span>种子
                </a>
                <a href="<?php $this->url('');?>" class="list-group-item">
                    <span class="zd_music"></span>音乐
                </a>
                <a href="<?php $this->url('');?>" class="list-group-item">
                    <span class="zd_other"></span>其它
                </a>
                <a href="<?php $this->url('');?>" class="list-group-item">
                    <span class="zd_share"></span>我的分享
                </a>
                <a href="<?php $this->url('');?>" class="list-group-item">
                    <span class="zd_trash"></span>回收站
                </a>
            </div>
            <div class="progress">
                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
                    <span class="sr-only">40% Complete (success)</span>
                </div>
            </div>
        </div>
        <div class="col-md-10">
            <div class="row">
                <div class="dropdown col-md-2">
                    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <span class="glyphicon glyphicon-open"></span>
                        上传
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li><a href="#">上传文件</a></li>
                        <li><a href="#">上传文件夹</a></li>
                    </ul>
                </div>
                <button class="btn btn-default col-md-2"><span class="glyphicon glyphicon-plus"></span>新建文件夹</button>
                <div class="col-md-offset-6 col-md-2">
                    <button><span class="glyphicon glyphicon-th-list"></span></button>
                    <button><span class="glyphicon glyphicon-th-large"></span></button>
                </div>
            </div>
            <ol class="breadcrumb">
                <li><a href="#">Home</a></li>
                <li><a href="#">Library</a></li>
                <li class="active">Data</li>
            </ol>
            <div class="row">

            </div>
        </div>
    </div>
</div>

<?php
$this->extend(array(
    'layout' => array(
        'foot'
    )), array(
        '!js require(["home/home"]);'
    )
);
?>