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
        return 'Login failure';
    }
    
    public function menuAction() {
        return  array('list' => array(
            array('id' => '1', 'resName' => '计步宝', 'resKey'=> 'menu_stepcounter', 'resIcon'=> 'xtxg', 'children' => array(
                array( 'resName' => '首页', 'resKey'=> 'index'),
                array( 'resName' => '运营位管理', 'resKey'=> 'ad'),
                array( 'id' => '1-1', 'resName' => '用户管理', 'resKey'=> 'menu_stepcounter_user', 'children' => array(
                    array( 'resName' => '用户明细', 'resKey'=> 'user'),
                    array( 'resName' => '用户提现', 'resKey'=> 'withdraw'),
                    array( 'resName' => '用户反馈', 'resKey'=> 'feedback'),
                    array( 'resName' => '用户邀请', 'resKey'=> 'invited'),
                )),
                array( 'id' => '1-2', 'resName' => '系统管理', 'resKey'=> 'menu_stepcounter_system', 'children' => array(
                    array( 'resName' => '版本升级', 'resKey'=> 'version'),
                    array( 'resName' => '广告频闭', 'resKey'=> 'version-ad'),
                    array( 'resName' => '三方错误码', 'resKey'=> 'sdk-error'),
                )),
            )),
            array('id' => '2', 'resName' => '狗狗世界', 'resKey'=> 'menu_dogsworld', 'resIcon'=> 'moduleManage', 'children' => array(
                array( 'id' => '2-1', 'resName' => '用户管理', 'resKey'=> 'menu_dogsworld_user', 'children' => array(
                    array( 'resName' => '内部用户', 'resKey'=> 'dogs-interior'),
                    array( 'resName' => '用户提现', 'resKey'=> 'dogs-withdraw'),
                    array( 'resName' => '用户列表', 'resKey'=> 'dogs-user'),
                )),
                array( 'id' => '2-2', 'resName' => '系统管理', 'resKey'=> 'menu_dogsworld_system', 'children' => array(
                    array( 'resName' => '版本升级', 'resKey'=> 'dogs-version'),
                    array( 'resName' => '广告频闭', 'resKey'=> 'dogs-version-ad'),
                )),
            )),
            array('id' => '3', 'resName' => '广告配置', 'resKey'=> 'menu_ad', 'resIcon'=> 'xtxg', 'children' => array(
                array( 'resName' => '开发中...', 'resKey'=> 'ad-user'),
            )),
        ));
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