<?php

namespace Core\Ad;

use Core\Ad;

class TaskDaily extends Ad
{
    protected function _getInfo() {
        // todo 修改文案
        return array(array('name' => '步数累计任务', 'desc' => '让每一步更有价值', 'type' => 'interior', 'url' => 'walkStage', 'award' => 2000), array('name' => '每日打卡', 'desc' => '让每一步更有价值', 'type' => 'interior', 'url' => 'clockIn', 'award' => 2000), array('name' => '邀请好友', 'desc' => '让每一步更有价值', 'type' => 'interior', 'url' => 'invited', 'award' => 2000));
    }
}