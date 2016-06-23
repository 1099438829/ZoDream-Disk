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
        <div id="content" class="col-md-10">
            <div class="row">
                <div class="dropdown col-md-2">
                    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <span class="glyphicon glyphicon-open"></span>
                        上传
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li><a href="#" class="uploadFile">上传文件</a></li>
                        <li><a href="#">上传文件夹</a></li>
                    </ul>
                </div>
                <button class="btn btn-default col-md-2"><span class="glyphicon glyphicon-plus"></span>新建文件夹</button>
                <div class="col-md-offset-6 col-md-2 view-mode">
                    <button v-bind:class="{'active': isList}" v-on:click="setList(true)"><span class="glyphicon glyphicon-th-list"></span></button>
                    <button v-bind:class="{'active': !isList}" v-on:click="setList(false)"><span class="glyphicon glyphicon-th-large"></span></button>
                </div>
            </div>
            <ol class="breadcrumb">
                <li><a href="#">全部文件</a></li>
                <li><a href="#">Library</a></li>
                <li class="active">Data</li>
            </ol>
            <div class="header">
                <div v-show="isList && checkCount < 1" class="row">
                    <div class="col-md-1">
                        <span class="checkbox" v-on:click="checkAll" v-bind:class="{'checked': isAllChecked}"></span>
                    </div>
                    <div class="col-md-6"  v-on:click="setOrder('name')">
                        <span>文件名</span>
                        <span v-show="orderKey == 'name' && order > 0" class="glyphicon glyphicon-arrow-up"></span>
                        <span v-show="orderKey == 'name' && order < 0" class="glyphicon glyphicon-arrow-down"></span>
                    </div>
                    <div class="col-md-2" v-on:click="setOrder('size')">
                        <span>大小</span>
                        <span v-show="orderKey == 'size' && order > 0" class="glyphicon glyphicon-arrow-up"></span>
                        <span v-show="orderKey == 'size' && order < 0" class="glyphicon glyphicon-arrow-down"></span>
                    </div>
                    <div class="col-md-3" v-on:click="setOrder('update_at')">
                        <span>修改时间</span>
                        <span v-show="orderKey == 'update_at' && order > 0" class="glyphicon glyphicon-arrow-up"></span>
                        <span v-show="orderKey == 'update_at' && order < 0" class="glyphicon glyphicon-arrow-down"></span>
                    </div>
                </div>
                <div v-show="!isList && checkCount < 1" class="row">
                    <div class="col-md-1">
                        <span class="checkbox" v-on:click="checkAll" v-bind:class="{'checked': isAllChecked}"></span>
                    </div>
                </div>
                <div v-show="checkCount > 0" class="row">
                    <div class="col-md-1">
                        <span class="checkbox" v-on:click="checkAll" v-bind:class="{'checked': isAllChecked}"></span>
                    </div>
                    <div class="col-md-3">
                        已选中 {{ checkCount }} 个文件/文件夹
                    </div>
                    <div class="col-md-8">
                        <button class="btn btn-default">
                            <span class="glyphicon glyphicon-share"></span>
                            分享
                        </button>
                        <button class="btn btn-default">
                            <span class="glyphicon glyphicon-download-alt"></span>
                            下载
                        </button>
                        <button v-on:click="deleteAll" class="btn btn-default">
                            <span class="glyphicon glyphicon-trash"></span>
                            删除
                        </button>
                        <button class="btn btn-default">
                            <span class="glyphicon glyphicon-copy"></span>
                            复制到
                        </button>
                        <button class="btn btn-default">
                            <span class="glyphicon glyphicon-move"></span>
                            移动到
                        </button>
                        <button class="btn btn-default">
                            <span class="glyphicon glyphicon-pencil"></span>
                            重命名
                        </button>
                    </div>
                </div>
            </div> <!-- END HEADER -->
            <div class="body">
                <div v-show="isList" class="zd_list">
                    <div v-for="item in files | orderBy orderKey order " v-on:click="check(item)" class="row">
                        <div class="col-md-1">
                            <span class="checkbox" v-bind:class="{'checked': item.checked}"></span>
                        </div>
                        <div class="col-md-6">
                            <span v-bind:class="{'zd_s_dir': item.type == 0, 'zd_s_file': item.type == 1}"></span>
                            <span>{{item.name}}</span>
                        </div>
                        <div class="col-md-2">
                            <span>{{item.size | size}}</span>
                        </div>
                        <div class="col-md-3">
                            <span>{{item.update_at | time}}</span>
                            <div class="zd_tool">
                                <span class="glyphicon glyphicon-share"></span>
                                <span class="glyphicon glyphicon-download-alt"></span>
                                <span class="glyphicon glyphicon-move"></span>
                                <span class="glyphicon glyphicon-copy"></span>
                                <span class="glyphicon glyphicon-pencil"></span>
                                <span v-on:click="delete(item)" class="glyphicon glyphicon-trash"></span>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
                <div v-show="!isList" class="zd_grid">
                    <div class="row">
                        <div v-for="item in files" v-on:click="check(item)" class="col-md-2">
                            <div  v-bind:class="{'zd_dir': item.type == 0, 'zd_file': item.type == 1}">
                                <span class="checkbox" v-bind:class="{'checked': item.checked}"></span>
                            </div>
                            <div class="zd_name">
                                <a href="#">{{item.name}}</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="loadEffect">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>

        </div>
    </div>
</div>

<div id="upload" class="zd_upload" v-bind:class="{'min': mode == 1, 'max': mode == 2}">
    <div class="head">
        <span>{{title}}</span>
        <span v-show="mode != 2" v-on:click="mode = 2" class="glyphicon glyphicon-resize-full"></span>
        <span v-show="mode != 1" v-on:click="mode = 1" class="glyphicon glyphicon-resize-small"></span>
        <span v-on:click="mode = 0" class="glyphicon glyphicon-remove"></span>
    </div>
    <div class="body">
        <div class="row">
            <div class="col-md-4">
                文件(夹)名
            </div>
            <div class="col-md-2">
                大小
            </div>
            <div class="col-md-2">
                上传目录
            </div>
            <div class="col-md-2">
                状态
            </div>
            <div class="col-md-2">
                操作
            </div>
        </div>
        <div v-for="item in files" class="row">
            <div class="col-md-4">
                {{item.name}}
            </div>
            <div class="col-md-2">
                {{item.size | size}}
            </div>
            <div class="col-md-2">
                {{item.dir}}
            </div>
            <div class="col-md-2">
                {{item.status}}
            </div>
            <div class="col-md-2">
                <span v-show="!item.status" v-on:click="delete(item)" class="glyphicon glyphicon-trash"></span>
            </div>
        </div>
    </div>
</div>

<?php
$this->extend(array(
    'layout' => array(
        'foot'
    )), array(
        'vue',
        'zodream'
    )
);
?>