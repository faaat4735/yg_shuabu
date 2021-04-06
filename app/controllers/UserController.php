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
        // 存在 返回用户信息
        if (!$userInfo) {
            // 不存在 创建用户，返回用户信息
            $userInfo = $this->model->user->createUser($this->inputData['deviceId'], $this->inputData['userDeviceInfo']);
        }
        $withdrawCount = $this->__withdrawCount();
        $userInfo['adClick'] = 8;
        if ($withdrawCount >= 8) {
            $userInfo['adClick'] = 1;
        } elseif ($withdrawCount >= 6) {
            $userInfo['adClick'] = 2;
        } elseif ($withdrawCount >= 4) {
            $userInfo['adClick'] = 4;
        } elseif ($withdrawCount >= 2) {
            $userInfo['adClick'] = 6;
        }
        return $userInfo;
    }

    /**
     * 广告屏蔽
     * @return array
     */
    public function adStatusAction () {
        $sql = 'SELECT ad_status FROM t_version_ad WHERE version_id = ? AND app_name = ?';
        $adStatus = $this->db->getOne($sql, $_SERVER['HTTP_VERSION_CODE'] ?? 0, $_SERVER['HTTP_SOURCE'] ?? '') ?: 0;
        return array('adStatus' => $adStatus);
    }

}