<?php

namespace Core\Task;

use Core\Task;

class Walk extends Task
{
    protected function _getInfo() {

        return array('list' => array(array('count' => 1, 'type' => 'walk', 'num' => 100), array('count' => 2, 'type' => 'walk', 'num' => 100), array('count' => 3, 'type' => 'walk', 'num' => 100), array('count' => 4, 'type' => 'walk', 'num' => 100)), 'receiveTime' => 1000, 'serverTime' => 2000, 'restCount' => 100, 'walkCount' => 10000);
    }
}