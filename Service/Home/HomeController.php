<?php
namespace Service\Home;

use Domain\Model\DiskModel;
use Zodream\Domain\Authentication\Auth;

class HomeController extends Controller {
    protected function rules() {
        return [
            '*' => '@'
        ];
    }

    function indexAction() {
        $this->show(array(
            'title' => '首页'
        ));
    }
    
    function listAction($id = 0) {
        $data = DiskModel::query('disk')->findAll(['user_id' => Auth::user()['id'], 'parent_id' => $id]);
        $this->ajaxReturn([
            'status' => 'success',
            'data' => $data
        ]);
    }
}