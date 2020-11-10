<?php

namespace Controller;

use Core\Controller;

class UserController extends Controller
{
    public function indexAction () {
        return $this->model->user->get();
    }

    public function infoAction () {
        if (!$this->checkHeader(FALSE)) {
            return 203;
        }
        // 检查用户是否存在
        $userInfo = $this->model->user->getUserInfoByDeviceId($this->inputData['deviceId']);
        if ($userInfo) {
            // 存在 返回用户信息
            return array('accessToken' => 'testToken', 'nickname' => 'testName', 'headimgurl' => 'http://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJdAAGVU1MRfSOl4z7y0LUwUG2atbQUxBDVTpkod8Zdf5OEhDMyeibpZ4icxETwjFpZ4Gv9BOBOsSFQ/132', 'currentGold' => 1000);
        } else {
            // 不存在 创建用户，返回用户信息
            $this->model->user->createUser();
            return array('accessToken' => 'testToken', 'nickname' => 'testName', 'headimgurl' => 'http://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJdAAGVU1MRfSOl4z7y0LUwUG2atbQUxBDVTpkod8Zdf5OEhDMyeibpZ4icxETwjFpZ4Gv9BOBOsSFQ/132', 'currentGold' => 1000);
        }

    }
}