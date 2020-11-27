<?php

namespace Controller;

use Core\Controller;

class ActionController extends Controller
{
    /**
     * 领取奖励
     */
    public function awardAction () {
        if (!isset($this->inputData['count']) || !isset($this->inputData['num']) || !isset($this->inputData['type'])) {
            return 202;
        }
        $taskClass = new \Core\Task($this->userId);
        return $taskClass->receiveAward($this->inputData);
    }

    /**
     * 绑定微信
     * @return array|int
     */
    public function wechatAction () {
        if (!isset($this->inputData['unionid'])) {
            return 202;
        }
        $sql = 'SELECT wechat_unionid FROM t_user WHERE user_id = ?';
        $wechatInfo = $this->db->getOne($sql, $this->userId);
        if ($wechatInfo) {
            return 304;
        }
        $sql = 'SELECT COUNT(*) FROM t_user WHERE wechat_unionid = ?';
        $unionInfo = $this->db->getOne($sql, $this->inputData['unionid']);
        if ($unionInfo) {
            return 305;
        }
        $sql = 'UPDATE t_user SET wechat_openid = ?, nickname = ?, language = ?, sex = ?, province = ?, city = ?, country = ?, headimgurl = ?, wechat_unionid = ? WHERE user_id = ?';
        $this->db->exec($sql, $this->inputData['openid'] ?? '', $this->inputData['nickname'] ?? '', $this->inputData['language'] ?? '', $this->inputData['sex'] ?? 0, $this->inputData['province'] ?? '', $this->inputData['city'] ?? '', $this->inputData['country'] ?? '', $this->inputData['headimgurl'] ?? '', $this->inputData['unionid'], $this->userId);
        $return = array();
        $sql = 'SELECT * FROM t_gold WHERE gold_source = ? AND user_id = ?';
        $awardInfo = $this->db->getOne($sql, 'wechat', $this->userId);

        if (!$awardInfo) {
            $sql = 'SELECT award_min FROM t_award_config WHERE config_type = "wechat"';
            $gold = $this->db->getOne($sql);
            $this->model->gold->insert(array('user_id' => $this->userId, 'gold_amount' => $gold, 'gold_source' => 'wechat', 'gold_count' => '1'));
            $return['award'] = $gold;
        }

        return $return;
    }

    /**
     * 绑定邀请码
     * @return array|int
     */
    public function invitedAction () {
        if (!isset($this->inputData['invitedCode'])) {
            return 202;
        }
        // 您已填写过邀请码
        $sql = 'SELECT COUNT(*) FROM t_user_invited WHERE invited_id = ?';
        $invitedInfo = $this->db->getOne($sql, $this->userId);
        if ($invitedInfo) {
            return 306;
        }
        // 邀请码无效，请重新输入
        $sql = 'SELECT user_id, create_time FROM t_user WHERE invited_code = ?';
        $userInfo = $this->db->getRow($sql, $this->inputData['invitedCode']);
        if (!$userInfo) {
            return 307;
        }
        // 验证码无效，请填写比您先注册的用户的邀请码
        $sql = 'SELECT create_time, unionid FROM t_user WHERE user_id = ?';
        $invitedUserInfo = $this->db->getOne($sql, $this->userId);
        if (strtotime($invitedUserInfo['create_time']) <= strtotime($userInfo['create_time'])) {
            return 308;
        }
        // 请先绑定微信后，再填写邀请码
        if (!$invitedUserInfo['wechat_unionid']) {
            return 309;
        }
        $sql = 'INSERT INTO t_user_invited SET user_id = ?, invited_id = ?';
        $this->db->exec($sql, $userInfo['user_id'], $this->userId);

        $return = array();
        $sql = 'SELECT * FROM t_gold WHERE gold_source = ? AND user_id = ?';
        $awardInfo = $this->db->getOne($sql, 'invited', $this->userId);
        if (!$awardInfo) {
            $sql = 'SELECT award_min FROM t_award_config WHERE config_type = "invited"';
            $invitedGold = $this->db->getOne($sql);
            $this->model->gold->insert(array('user_id' => $this->userId, 'gold_amount' => $invitedGold, 'gold_source' => 'invited', 'gold_count' => '1'));
            $return['award'] = $invitedGold;

            $sql = 'SELECT award_min FROM t_award_config WHERE config_type = "do_invite"';
            $gold = $this->db->getOne($sql);
            $sql = 'SELECT IFNULL(COUNT(id), 0) FROM t_user_invited WHERE user_id = ?';
            $userInvitedCount = $this->db->getOne($sql, $userInfo['user_id']);
            $this->model->gold->insert(array('user_id' => $userInfo['user_id'], 'gold_amount' => $gold, 'gold_source' => 'do_invite', 'gold_count' => $userInvitedCount + 1));
        }
        return $return;
    }

    public function requestWithdrawAction () {
        if (!isset($this->inputData['amount']) || !in_array($this->inputData['amount'], array(0.3, 20, 50, 100, 150, 200))) {
            return 202;
        }
        $sql = 'SELECT wechat_unionid, wechat_openid, user_status FROM t_user WHERE user_id = ?';
        $payInfo = $this->db->getRow($sql, $this->userId);
        if (!$payInfo['user_status']) {
            return 310;
        }
        if (!$payInfo['wechat_unionid']) {
            return 311;
        }
        //todo 添加支付宝实名认证

        $withdrawalGold = $this->inputData['amount'] * 10000;
        $currentGold = $this->model->gold->total($this->userId, 'current');
        if ($currentGold < $withdrawalGold) {
            return 312;
        }
        if (0.3 == $this->inputData['amount']) {
            $sql = 'SELECT COUNT(*) FROM t_withdraw WHERE user_id = ? AND withdraw_amount = 0.3 AND (withdraw_status = "pending" OR withdraw_status = "success")';
            if ($this->db->getOne($sql, $this->userId)) {
                return 313;
            }
        }
        //todo 高并发多次插入记录问题 加锁解决
//        $sql = 'INSERT INTO t_withdraw (user_id, withdraw_amount, withdraw_gold, withdraw_status, withdraw_method, wechat_openid) SELECT :user_id, :withdraw_amount,:withdraw_gold, :withdraw_status, :withdraw_method, :wechat_openid FROM DUAL WHERE NOT EXISTS (SELECT withdraw_id FROM t_withdraw WHERE user_id = :user_id AND withdraw_amount = :withdraw_amount AND withdraw_status = :withdraw_status)';
//        $this->db->exec($sql, array('user_id' => $this->userId, 'withdraw_amount' => $this->inputData['amount'], 'withdraw_gold' => $withdrawalGold, 'withdraw_method' => 'wechat', 'withdraw_status' => 'pending', 'wechat_openid' => $payInfo['wechat_openid']));
        $sql = 'INSERT INTO t_withdraw SET user_id = :user_id, withdraw_amount = :withdraw_amount, withdraw_gold = :withdraw_gold, withdraw_status = :withdraw_status, withdraw_account = :withdraw_account';
        $this->db->exec($sql, array('user_id' => $this->userId, 'withdraw_amount' => $this->inputData['amount'], 'withdraw_gold' => $withdrawalGold, 'withdraw_status' => 'pending', 'withdraw_account' => $payInfo['wechat_openid']));
        return array();
    }

}