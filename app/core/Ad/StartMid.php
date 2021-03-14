<?php

namespace Core\Ad;

use Core\Ad;

class StartMid extends Ad
{
    protected function _getInfo() {


        return array(array('img' => OSS_HOST . 'ad/start_ydz.png', 'type' => 'interior', 'url' => 'sport'), array('img' => OSS_HOST . 'ad/start_yq.png', 'type' => 'sdk', 'url' => 'dl_game'), array('img' => OSS_HOST . 'ad/start_mrfl.png', 'type' => 'sdk', 'url' => 'op_novel'));
//        return array(array('img' => OSS_HOST . 'ad/start_ydz.png', 'type' => 'interior', 'url' => 'sport'), array('img' => OSS_HOST . 'ad/start_yq.png', 'type' => 'interior', 'url' => 'invited'), array('img' => OSS_HOST . 'ad/start_mrfl.png', 'type' => 'interior', 'url' => 'task'));
    }
}