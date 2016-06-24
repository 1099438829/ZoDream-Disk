<?php
namespace Service\Home;

use Domain\Model\DiskModel;
use Zodream\Domain\Authentication\Auth;
use Zodream\Infrastructure\Request;

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

    function deleteAction() {
        $data = Request::post('id');
        if (empty($data)) {
            $this->ajaxReturn([
                'status' => 'failure',
                'error' => '不能为空！'
            ]);
        }
        $result = DiskModel::query('disk')->delete([
            'id' => (array)$data,
            'user_id' => Auth::user()['id']
        ]);
        $this->ajaxReturn([
            'status' => 'success',
            'data' => $result
        ]);
    }

    /**
     * 增加文件夹
     */
    function addAction() {
        $data = Request::post('name,parent_id 0');
        $result = DiskModel::query('disk')->save([
            'name' => 'required|string:4-100',
            'parent_id' => 'required|int',
            'user_id' => null,
            'create_at' => null,
            'update_at' => null
        ], $data);
        if (empty($result)) {
            $this->ajaxReturn([
                'status' => 'failure',
                'error' => '添加失败！'
            ]);
        }
        $data['id'] = $result;
        $this->ajaxReturn([
            'status' => 'success',
            'data' => $data
        ]);
    }

    function checkAction() {
        $data = Request::post('md5,name');
        if (empty($data['md5']) || empty($data['name'])) {
            $this->ajaxReturn([
                'status' => 'failure',
                'error' => '不能为空！'
            ]);
        }
        $result = DiskModel::query('disk')->findOne(['md5' => $data['md5']]);
        if (empty($result)) {
            $this->ajaxReturn([
                'status' => 'success',
                'data' => null
            ]);
        }
    }
}