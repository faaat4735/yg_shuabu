<?php

namespace Controller;

use Core\Controller;

class UserController extends Controller
{
    public function init () {
        if (!$this->checkHeader(FALSE)) {
            return 203;
        }
        return TRUE;
    }

    public function infoAction () {
        // 检查用户是否存在
        $userInfo = $this->model->user->getUserInfoByDeviceId($this->inputData['deviceId']);
        if ($userInfo) {
            // 存在 返回用户信息
            return $userInfo;
        } else {
            // 不存在 创建用户，返回用户信息
            return $this->model->user->createUser($this->inputData['deviceId'], $this->inputData['userDeviceInfo']);
        }
    }

}