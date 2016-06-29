<?php
namespace Service\Home;
use Domain\Model\DiskModel;
use Zodream\Domain\Authentication\Auth;

/**
 * Created by PhpStorm.
 * User: zx648
 * Date: 2016/6/29
 * Time: 15:39
 */
class ShareController extends Controller {
    function indexAction() {
        $data = DiskModel::query('disk_share s')->findAll([
            'left' => [
                'disk d',
                'd.id = s.disk_id',
                'user u',
                'd.user_id = u.id'
            ],
            'where' => [
                's.mode' => 0
            ]
        ], [
            'id' => 'd.id',
            'name' => 'd.name',
            'avatar' => 'u.avatar',
            'user_id' => 'u.id',
            'user_name' => 'u.name',
            'create_at' => 's.create_at',
        ]);
        $this->show([
            'title' => '分享广场',
            'data' => $data
        ]);
    }
    
    function myAction() {
        $data = DiskModel::query('disk_share s')->findAll([
            'left' => [
                'disk d',
                'd.id = s.disk_id',
            ],
            'where' => [
                'd.user_id' => Auth::user()['id']
            ]
        ], [
            'id' => 'd.id',
            'share_id' => 's.id',
            'name' => 'd.name',
            'create_at' => 's.create_at'
        ]);
        $this->show([
            'title' => '我的分享',
            'data' => $data
        ]);
    }

    function deleteAction($id) {
        $row = DiskModel::query('disk_share')->deleteById($id);
        if (empty($row)) {
            $this->ajaxFailure('ERROR');
        }
        $this->ajaxSuccess();
    }

    function userAction($id) {
        $data = DiskModel::query('disk_share s')->findAll([
            'left' => [
                'disk d',
                'd.id = s.disk_id',
            ],
            'where' => [
                's.mode' => 0,
                'd.user_id' => intval($id)
            ]
        ], [
            'id' => 'd.id',
            'name' => 'd.name',
            'create_at' => 's.create_at',
        ]);
        $user = DiskModel::query('user')->findById($id);
        $this->show([
            'title' => $user['name'].'的分享',
            'data' => $data,
            'user' => $user
        ]);
    }
    
    function viewAction($id) {
        $this->show([
            'title' => '查看分享'
        ]);
    }
}