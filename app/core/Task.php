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

    /**
     * 获取任务信息
     * @param $type
     * @return array
     */
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

    /**
     * 领取奖励
     */
    public function receiveAward ($data) {
        // 验证是否领取过
        $verifyGold = $this->model->gold->verify($this->userId, $data);
        if (TRUE !== $verifyGold) {
            return $verifyGold;
        }
        // 领取
        $className = '\\Core\\Task\\' . ucfirst($data['type']);
        if (class_exists($className)) {
            $this->className = new $className($this->userId);
            $this->className->type = $data['type'];
        } else {
            $this->type = $data['type'];
            $this->className = $this;
        }
        // 验证金额是否符合规范
        $verifyActivity = $this->className->_verify($data);
        if (TRUE !== $verifyActivity) {
            return $verifyActivity;
        }
        return $this->className->_receiveAward($data);

    }

    protected function _verify($data) {
        // 验证最大次数 可领取时间
        // 移动sql 到model中
        $sql = 'SELECT award_min, award_max FROM t_award_config WHERE config_type = ? AND counter <= ? ORDER BY counter DESC LIMIT 1';
        $awardRange = $this->db->getRow($sql, $this->type, $data['count']);
        if ($awardRange['award_min'] <= $data['num'] && $awardRange['award_max'] >= $data['num']) {
            return TRUE;
        }
        return 301;
    }

    protected function _getInfo() {
        // todo
        return array($this->type);
    }

    protected function _receiveAward ($data) {
        $this->model->gold->insert(array('user_id' => $this->userId, 'gold_count' => $data['count'], 'gold_amount' => $data['num'], 'gold_source' => $data['type']));
        return $this->_getInfo();
    }
}