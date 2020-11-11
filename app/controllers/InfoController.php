<?php

namespace Controller;

use Core\Controller;

class InfoController extends Controller
{
    public function startAction () {
        $userId = $this->checkHeader();
        if (!$userId) {
            return 203;
        }
        $taskClass = new \Core\Task($userId);
        $adClass = new \Core\Ad($userId);
        // 金币
        // 宝箱
        // receiveTime serverTime currentCount award
        // 中间任务
        // 底部任务
        return array('walkInfo' => $taskClass->getInfo('walk'), 'boxInfo' => $taskClass->getInfo('box'), 'taskMid' => $adClass->getInfo('startMid'), 'taskBot' => $adClass->getInfo('startBot'));
    }
}