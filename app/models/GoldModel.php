<?php

namespace Model;
use Core\Model;

class GoldModel extends Model
{
    /**
     * 已领取金币详情
     * @param $userId
     * @param $type
     * @return mixed
     */
    public function receiveGold ($userId, $type) {
        $sql = 'SELECT count(gold_id) count, MAX(create_time) maxTime FROM t_gold WHERE user_id = ? AND change_date = ? AND gold_source = ?';
        return $this->db->getRow($sql, $userId, date('Y-m-d'), $type);
    }

    /**
     * 用户金币改变
     * @param $data
     * $data['user_id'] 用户id
     * $data['gold_count'] 领取金币次数
     * $data['gold_amount'] 领取金币金额
     * $data['gold_source'] 领取金币来源
     */
    public function insert ($data) {
//        $userState = $this->model->user2->userInfo($params['user_id'], 'user_status');
//        if (!$userState) {
//            return new ApiReturn('', 203, '抱歉您的账户已被冻结');
//        }
        if (isset($data['isDouble']) && $data['isDouble']) {
            // 很多类型不能双倍 加上判断 todo
            if (!in_array($data['gold_source'], array('newer', 'walk_stage'))) {
                $data['gold_amount'] = $data['gold_amount'] * 2;
            }
        }
        if (isset($data['isFive']) && $data['isFive']) {
            // 很多类型不能双倍 加上判断 todo
            if (!in_array($data['gold_source'], array('newer', 'walk_stage'))) {
                $data['gold_amount'] = $data['gold_amount'] * 5;
            }
        }
        $sql = "SELECT COUNT(*) FROM t_gold WHERE user_id = ?";
        $goldCount = $this->db->getOne($sql, $data['user_id']);
        if ($goldCount == 2) {
            $sql = "SELECT * FROM t_user WHERE user_id = ?";
            $userInfo = $this->db->getRow($sql, $data['user_id']);
            $callbackUrl = $this->callback($userInfo['OAID'] ?? '', $userInfo['IMEI'] ?? '', $userInfo['AndroidId'] ?? '', $userInfo['MAC'] ?? '');
            if ($callbackUrl) {
                file_get_contents($callbackUrl);
            }
        }
        $insertData = array('user_id' => $data['user_id'], 'gold_amount' => $data['gold_amount'], 'gold_source' => $data['gold_source'], 'gold_count' => $data['gold_count'], 'change_date' => date('Y-m-d'));
        $sql = 'INSERT INTO t_gold (user_id, gold_count, gold_amount, gold_source, change_date) SELECT :user_id, :gold_count, :gold_amount, :gold_source, :change_date FROM DUAL WHERE NOT EXISTS (SELECT gold_id FROM t_gold WHERE user_id = :user_id AND gold_count = :gold_count AND gold_source = :gold_source AND change_date = :change_date)';
        return $this->db->exec($sql, $insertData);
    }

    /**
     * 用户金币信息
     * @param $userId
     * @param string $type
     * @return array|mixed
     */
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

    /**
     * 验证奖励是否领取过和按照顺序领取的
     * @param $userId
     * @param $data
     * @return bool|int
     */
    public function verify ($userId, $data) {
        $sql = 'SELECT gold_id FROM t_gold WHERE user_id = ? AND gold_count = ? AND gold_source = ? AND change_date = ?';
        $hasReceive = $this->db->getOne($sql, $userId, $data['count'], $data['type'], date('Y-m-d'));
        if ($hasReceive) {
            return 302;
        }
        // 验证按照顺序领取
        if (!in_array($data['type'], array('walk', 'walk_stage', 'drink', 'sign', 'sport'))) {
            $sql = 'SELECT MAX(gold_count) FROM t_gold WHERE user_id = ? AND gold_source = ? AND change_date = ?';
            $maxCount = $this->db->getOne($sql, $userId, $data['type'], date('Y-m-d'));
            if ($data['count'] != $maxCount + 1) {
                return 303;
            }
        }
        return TRUE;
    }

    /**
     * 最近7天金币明细
     * @param $userId
     * @return mixed
     */
    public function details ($userId) {
        $sql = 'SELECT g.gold_amount amount, UNIX_TIMESTAMP(g.create_time) * 1000 time, a.activity_name name FROM t_gold g LEFT JOIN t_activity a ON g.gold_source = a.activity_type WHERE g.user_id = ? AND g.create_time >= ? ORDER BY g.gold_id DESC';
        return $this->db->getAll($sql, $userId, date('Y-m-d', strtotime('-7 days')));
    }

    public function goldTotal ($userId) {
        $sql = "SELECT COUNT(*) FROM t_gold WHERE user_id = ?";
        return $this->db->getOne($sql, $userId);
    }

    public function goldDetails ($userId, $limit) {
        $sql = 'SELECT g.gold_amount amount, g.create_time, a.activity_name name FROM t_gold g LEFT JOIN t_activity a ON g.gold_source = a.activity_type WHERE g.user_id = ? ORDER BY g.gold_id DESC LIMIT ' . $limit;
        return $this->db->getAll($sql, $userId);
    }

    public function todayGold ($userId) {
        $sql = 'SELECT IFNULL(SUM(gold_amount), 0) FROM t_gold WHERE user_id = ? AND change_date = ?';
        return $this->db->getOne($sql, $userId, date('Y-m-d'));
    }

    /**
     * @param $imei
     * @param $androidid
     * @param $mac
     * @return array
     */
    public function callback ($oaid, $imei, $androidid, $mac) {
        $callback = '';
        if ($oaid) {
            $sql = 'SELECT callback FROM t_ocean_monitor WHERE oaid = ? ORDER BY log_id DESC';
            $callback = $this->db->getOne($sql, $oaid);
        }
        if ($imei) {
            $sql = 'SELECT callback FROM t_ocean_monitor WHERE imei_md5 = ? ORDER BY log_id DESC';
            $callback = $this->db->getOne($sql, md5($imei));
        }
        if ($callback) {
            return $callback;
        }
        if ($androidid) {
            $sql = 'SELECT callback FROM t_ocean_monitor WHERE androidid_md5 = ? ORDER BY log_id DESC';
            $callback = $this->db->getOne($sql, md5($androidid));
        }
        if ($callback) {
            return $callback;
        }
        if ($mac) {
            $sql = 'SELECT callback FROM t_ocean_monitor WHERE mac_md5 = ? ORDER BY log_id DESC';
            $callback = $this->db->getOne($sql, md5(str_replace(':', '', $mac)));
        }
        if ($callback) {
            return $callback;
        }
        return $callback;
    }
}