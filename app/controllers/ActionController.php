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

}