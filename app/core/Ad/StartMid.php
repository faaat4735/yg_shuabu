<?php

namespace Core\Ad;

use Core\Ad;

class StartMid extends Ad
{
    protected function _getInfo() {

        return array(array('img' => OSS_HOST . 'ad/startMid1.png', 'type' => 'interior', 'url' => 'sport'), array('img' => OSS_HOST . 'ad/startMid2.png', 'type' => 'interior', 'url' => 'invited'), array('img' => OSS_HOST . 'ad/startMid3.png', 'type' => 'interior', 'url' => 'task'));
    }
}