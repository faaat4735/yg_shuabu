<?php

namespace Model;
use Core\Model;

class GoldModel extends Model
{

    public function receiveGold ($userId, $type) {
        $sql = 'SELECT count(gold_id) count, MAX(create_time) maxTime FROM t_gold WHERE user_id = ? AND change_date = ? AND gold_source = ?';
        return $this->db->getRow($sql, $userId, date('Y-m-d'), $type);
    }

    /**
     * array('user_id' => $this->userId, 'gold_count' => $this->inputData['count'], 'gold_amount' => $this->inputData['num'], 'gold_source' => $this->inputData['type'])
     * @param $data
     */
    public function insert ($data) {
        //$userState = $this->model->user2->userInfo($params['user_id'], 'user_status');
        //        if (!$userState) {
        //            return new ApiReturn('', 203, '抱歉您的账户已被冻结');
        //        }
        $data['change_date'] = date('Y-m-d');
        $sql = 'INSERT INTO t_gold (user_id, gold_count, gold_amount, gold_source, change_date) SELECT :user_id, :gold_count, :gold_amount, :gold_source, :change_date FROM DUAL WHERE NOT EXISTS (SELECT gold_id FROM t_gold WHERE user_id = :user_id AND gold_count = :gold_count AND gold_source = :gold_source AND change_date = :change_date)';
        return $this->db->exec($sql, $data);
    }

    public function total ($userId, $type = '') {
        //获取当前用户可用金币
        $totalArr = array('total' => 0, 'current' => 0, 'bolcked' => 0);
        $sql = 'SELECT IFNULL(SUM(gold_amount), 0) FROM t_gold WHERE user_id = ?';
        $totalArr['total'] =$this->db->getOne($sql, $userId);
        $sql = 'SELECT IFNULL(SUM(withdraw_gold), 0) FROM t_withdraw WHERE user_id = ? AND withdraw_status = "pending"';
        $totalArr['bolcked'] = $this->db->getOne($sql, $userId);
        $totalArr['current'] = $totalArr['total'] - $totalArr['bolcked'];
        if (!$type) {
            return $totalArr;
        } else {
            return $totalArr[$type];
        }
    }


    public function verify ($userId, $data) {
        $sql = 'SELECT gold_id FROM t_gold WHERE user_id = ? AND gold_count = ? AND gold_source = ? AND change_date = ?';
        $hasReceive = $this->db->getOne($sql, $userId, $data['count'], $data['type'], date('Y-m-d'));
        if ($hasReceive) {
            return 302;
        }
        // 验证按照顺序领取
        if ('walk' != $data['type']) {
            $sql = 'SELECT MAX(gold_count) FROM t_gold WHERE user_id = ? AND gold_source = ? AND change_date = ?';
            $maxCount = $this->db->getOne($sql, $userId, $data['type'], date('Y-m-d'));
            if ($data['count'] != $maxCount + 1) {
                return 303;
            }
        }
        return TRUE;
    }
}