<?php
namespace Service\Home;

use Domain\Model\DiskModel as FactoryModel;
use Zodream\Domain\Authentication\Auth;
use Zodream\Domain\Response\Download;
use Zodream\Domain\Upload\UploadInput;
use Zodream\Infrastructure\FileSystem;
use Zodream\Infrastructure\Request;
use Domain\Model\Home\DiskModel;

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
        $data = FactoryModel::query('disk')
            ->findAll(['user_id' => Auth::user()['id'], 
                'parent_id' => $id]);
        $this->ajaxReturn([
            'status' => 'success',
            'data' => $data
        ]);
    }

    function deleteAction() {
        $data = Request::post('id');
        if (empty($data)) {
            $this->ajaxFailure('不能为空！');
        }
        $result = FactoryModel::query('disk')->delete([
            'id' => [(array)$data, 'or', 'and'],
            'user_id' => Auth::user()['id']
        ]);
        $this->ajaxSuccess($result);
    }

    function shareAction() {
        $data = Request::post('id,');
        if (empty($data)) {
            $this->ajaxFailure('不能为空！');
        }
        $result = FactoryModel::query('disk_share')->addValues([
            'id' => (array)$data,
            'user_id' => Auth::user()['id']
        ]);
        $this->ajaxSuccess($result);
    }

    /**
     * 增加文件夹
     */
    function createAction() {
        $data = Request::post('name,parent_id 0');
        $data['create_at'] = $data['update_at'] = time();
        $data['is_dir'] = 1;
        $result = FactoryModel::query('disk')->save([
            'name' => 'required|string:4-100',
            'parent_id' => 'required|int',
            'is_dir' => null,
            'user_id' => null,
            'create_at' => null,
            'update_at' => null
        ], $data);
        if (empty($result)) {
            $this->ajaxFailure('添加失败！');
        }
        $data['id'] = $result;
        $this->ajaxSuccess($data);
    }

    function renameAction() {
        $data = Request::post('id,name');
        $data['update_at'] = time();
        $result = FactoryModel::query('disk')->save([
            'name' => 'required|string:4-100',
            'id' => 'required|int',
            'update_at' => null
        ], $data);
        if (empty($result)) {
            $this->ajaxFailure('添加失败！');
        }
        $this->ajaxReturn([
            'status' => 'success',
            'update_at' => $data['update_at']
        ]);
    }

    function checkAction() {
        $data = Request::post('md5,name,parent_id');
        if (empty($data['md5']) || empty($data['name'])) {
            $this->ajaxFailure('不能为空！');
        }
        $result = FactoryModel::query('disk')->findOne(['md5' => $data['md5']]);
        if (empty($result)) {
            $this->ajaxFailure('MD5 Error', 2);
        }
        $data['extension'] = FileSystem::getExtension($data['name']);
        $data['size'] = $result['size'];
        $data['location'] = $result['location'];
        $model = new DiskModel();
        $data['create_at'] = $data['update_at'] = time();
        $id = $model->fill($data);
        if (empty($id)) {
            return $this->ajaxFailure('添加失败', 3);
        }
        $data['id'] = $id;
        unset($data['localhost']);
        return $this->ajaxSuccess($data);
    }

    function uploadAction() {
        set_time_limit(0);
        $upload = new UploadInput();
        $file = $this->config['cache'].$upload->getName();
        $result = $upload->save($file);
        if (!$result) {
            $this->ajaxFailure($upload->getError());
        }
        $this->ajaxReturn([
            'status' => 'success',
            'name' => $upload->getName(),
            'size' => $upload->getSize(),
            'type' => $upload->getType()
        ]);
    }

    function addAction() {
        $data = Request::post('name,md5,size,parent_id 0,type,temp');
        $file = $this->config['cache'].$data['temp'];
        if (!is_file($file) || filesize($file) != $data['size']) {
            $this->ajaxFailure('FILE ERROR!');
        }
        $data['location'] = md5($data['name'].time()).FileSystem::getExtension($data['name'], true);
        if (!FileSystem::moveFile($file, $this->config['cache'].$data['location'])) {
            $this->ajaxFailure('MOVE FILE ERROR!');
        }
        $model = new DiskModel();
        $data['create_at'] = $data['update_at'] = time();
        $id = $model->fill($data);
        die(var_dump($model->getError()));
        if (empty($id)) {
            return $this->ajaxFailure('添加失败');
        }
        $data['id'] = $id;
        unset($data['location']);
        return $this->ajaxSuccess($data);
    }

    public function downloadAction($id) {
        $data = FactoryModel::query('disk')->findById($id);
        if (empty($data)) {
            $this->ajaxFailure('ID ERROR!');
        }
        $file = $this->config['folder'].$data['location'];
        if (!is_file($file)) {
            $this->ajaxFailure('FILE ERROR!');
        }
        Download::make($file, $data['name']);
    }
}