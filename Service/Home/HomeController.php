<?php
namespace Service\Home;

use Domain\Model\DiskModel;
use Zodream\Domain\Authentication\Auth;
use Zodream\Domain\Upload\UploadInput;
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
            'id' => [(array)$data, 'or', 'and'],
            'user_id' => Auth::user()['id']
        ]);
        $this->ajaxReturn([
            'status' => 'success',
            'data' => $result
        ]);
    }

    function shareAction() {
        $data = Request::post('id,');
        if (empty($data)) {
            $this->ajaxReturn([
                'status' => 'failure',
                'error' => '不能为空！'
            ]);
        }
        $result = DiskModel::query('disk_share')->addValues([
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
    function createAction() {
        $data = Request::post('name,parent_id 0');
        $data['create_at'] = $data['update_at'] = time();
        $data['is_dir'] = 1;
        $result = DiskModel::query('disk')->save([
            'name' => 'required|string:4-100',
            'parent_id' => 'required|int',
            'is_dir' => null,
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

    function renameAction() {
        $data = Request::post('id,name');
        $data['update_at'] = time();
        $result = DiskModel::query('disk')->save([
            'name' => 'required|string:4-100',
            'id' => 'required|int',
            'update_at' => null
        ], $data);
        if (empty($result)) {
            $this->ajaxReturn([
                'status' => 'failure',
                'error' => '添加失败！'
            ]);
        }
        $this->ajaxReturn([
            'status' => 'success',
            'update_at' => $data['update_at']
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

    function uploadAction() {
        set_time_limit(0);
        $upload = new UploadInput();
        $file = __DIR__.'/'.$upload->getName();
        $result = $upload->save($file);
        if (!$result) {
            $this->ajaxReturn([
                'status' => 'failure',
                'error' => $upload->getError()
            ]);
        }
        $this->ajaxReturn([
            'status' => 'success',
            'name' => $upload->getName(),
            'size' => $upload->getSize(),
            'file' => $file,
            'type' => $upload->getType()
        ]);
    }
}