<?php

namespace Core;

use Core\Controller;

class Task extends Controller
{
    protected $userId;
    protected $className;
    protected $type;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function getInfo ($type) {
        $className = '\\Core\\Task\\' . ucfirst($type);
        if (class_exists($className)) {
            $this->className = new $className($this->userId);
        } else {
            $this->type = $type;
            $this->className = $this;
        }
        return $this->className->_getInfo();
    }

    protected function _getInfo() {
        return array($this->type);
    }
}