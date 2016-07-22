<?php
defined('APP_DIR') or exit();
use Zodream\Domain\Routing\Url;
use Zodream\Domain\Html\Bootstrap\Html;
use Infrastructure\Tree;

/** @var $this \Zodream\Domain\Response\View */
$this->extend(array(
    'layout' => array(
        'head',
        'navbar'
    )), array(
    )
);
?>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <ul class="list-group">
                <li class="list-group-item">
                    <a href="<?=Url::to(['manage'])?>">部门管理</a>
                </li>
                <li class="list-group-item">
                    <a href="<?=Url::to(['manage/user'])?>">用户管理</a>
                </li>
                <li class="list-group-item">Morbi leo risus</li>
                <li class="list-group-item">Porta ac consectetur ac</li>
                <li class="list-group-item">Vestibulum at eros</li>
            </ul>
        </div>

        <div class="col-md-8">
            <div>
                <button class="btn btn-primary" data-toggle="modal" data-target="#addModal">增加</button>
            </div>
            <div class="tree">
                <?=Tree::makeUl($this->get('data', array()), 0, function ($item) {
                    return Html::tag('div', Html::a('添加',
                            ['create', 'id' => $item['id'], 'kind' => $item['kind']]).Html::a('编辑',
                            ['update', 'id' => $item['id']]). Html::a('删除',
                            ['delete', 'id' => $item['id']]));
                })?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="addModalLabel">增加结构</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">
                        名称
                    </div>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="name"  placeholder="名称">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        上级
                    </div>
                    <div class="col-sm-10">
                        <select id="parent">
                            <?=Tree::makeOption($this->get('data', array()))?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        数据
                    </div>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="data"  placeholder="数据"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary">保存</button>
            </div>
        </div>
    </div>
</div>


<?php
$this->extend(array(
    'layout' => array(
        'foot'
    )), array(
    )
);
?>
