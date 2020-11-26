<?php

namespace Core\Ad;

use Core\Ad;

class TaskNewer extends Ad
{
    protected function _getInfo() {
        $sql = 'SELECT award_min FROM t_award_config WHERE config_type = "wechat"';
        $wechatAward = $this->db->getOne($sql);
        return array(array('name' => '绑定微信', 'desc' => '让每一步更有价值', 'type' => 'task', 'url' => 'wechat', 'award' => $wechatAward, 'isComplete' => 1), array('name' => '填写邀请码', 'desc' => '让每一步更有价值', 'type' => 'task', 'url' => 'invitedCode', 'award' => 2000, 'isComplete' => 0));
    }
}