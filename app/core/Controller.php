<?php

namespace Core;

class Controller
{
    protected $inputData;
    protected $userId;

    public function __construct()
    {
        $this->inputData = json_decode(file_get_contents("php://input"), TRUE);
    }

    public function init () {
        $userId = $this->checkHeader();
        if (!$userId) {
            return 203;
        }
        $this->userId = $userId;
        return TRUE;
    }

    public function __get ($name) {
        if (!isset($this->$name)) {
            switch ($name) {
                case 'db':
                    $temp = Db::getDbInstance();
                    break;
                case 'model':
                    $temp = new Model();
                    break;
                default:
                    //todo 报错
            }
            $this->$name = $temp;
        }
        return $this->$name;
    }

    public function checkHeader($checkToken = TRUE) {
//        return TRUE;// todo debug删除
        // HTTP_VERSION_CODE
        // HTTP_SOURCE
        // HTTP_TIME
        // HTTP_SECRET
        // 加密 md5(时间戳 + 'ygsecert1007') 前8位
        if (!isset($_SERVER['HTTP_VERSION_CODE']) || !isset($_SERVER['HTTP_SOURCE']) || !isset($_SERVER['HTTP_TIME']) || !isset($_SERVER['HTTP_SECRET'])) {
            return FALSE;
        }
        // 验证时间戳在当前时间的误差范围内
        if ($_SERVER['HTTP_SECRET'] !== substr(md5($_SERVER['HTTP_TIME'] . 'ygsecert1007'), 0,8)) {
            return FALSE;
        }
        if ($checkToken) {
            // 检查token合法
            $sql = 'SELECT user_id FROM t_user WHERE access_token = ?';
            $userId = $this->db->getOne($sql, $_SERVER['HTTP_ACCESS_TOKEN']);
            return $userId;
        }
        return TRUE;
    }
}