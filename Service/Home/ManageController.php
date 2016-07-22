<?php
namespace Service\Home;

/**
 * Created by PhpStorm.
 * User: zx648
 * Date: 2016/6/29
 * Time: 15:35
 */
use Domain\Model\DiskModel;

class ManageController extends Controller {
    function indexAction() {
        $data = DiskModel::query('role')->findAll();
        $this->show([
            'title' => '组织结构',
            'data' => $data
        ]);
    }
    
    function userAction() {
        $this->show([
            'title' => '用户管理'
        ]);
    }
}