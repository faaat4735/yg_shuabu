<?php

namespace Admin;

use Core\Controller;

Class BaseController extends Controller {
    
    public function loginAction() {
        if (isset($_POST['username']) && isset($_POST['password'])) {
            if (ADMIN_USER == $_POST['username']) {
                if (md5(ADMIN_PASSWORD) == $_POST['password']) {
                    return array();
                }
            }
        }
        $return = array('status' => 'error', 'data' => '', 'msg' => '登录失败');
        echo json_encode($return);;
        exit;
    }
    
    public function menuAction() {
        return array('id' => '1', 'resName' => '走路多多', 'resKey'=> 'menu_zou', 'resIcon'=> 'xtxg', 'children' => array(
            array( 'resName' => '首页', 'resKey'=> 'zou-index'),
            array( 'resName' => '运营位管理', 'resKey'=> 'zou-ad'),
            array( 'resName' => '广告频闭', 'resKey'=> 'zou-version-ad'),
            array( 'id' => '1-1', 'resName' => '用户管理', 'resKey'=> 'menu_zou_user', 'children' => array(
                array( 'resName' => '用户明细', 'resKey'=> 'zou-user'),
                array( 'resName' => '用户提现', 'resKey'=> 'zou-withdraw'),
                array( 'resName' => '用户反馈', 'resKey'=> 'zou-feedback'),
                array( 'resName' => '用户邀请', 'resKey'=> 'zou-invited')
            ))));
    }
    
    public function userInfoAction () {
        return array('id' => 1);
    }
    
    public function logoutAction() {
        return array();
    }
    
    public function uploadAction() {
        header('Access-Control-Allow-Headers:x-requested-with');
        if ($_FILES) {
            $uploadFile = $_FILES['file'];
            switch ($uploadFile['type']) {
                case 'image/png':
                case 'image/jpg':
                case 'image/gif':
                    $result = move_uploaded_file($uploadFile['tmp_name'], IMG_DIR . $uploadFile['name']);
                    break;
                case 'application/vnd.android.package-archive':
                    $result = move_uploaded_file($uploadFile['tmp_name'], APP_DIR . $uploadFile['name']);
                    break;
            }
            return array($_FILES);
        }
    }
}