<?php

namespace Core\Task;

use Core\Task;

class Box extends Task
{
    protected function _getInfo() {
        return array('receiveTime' => 1000, 'serverTime' => 1000, 'count' => 1, 'num' => 10,'type' => 'box');
    }
}