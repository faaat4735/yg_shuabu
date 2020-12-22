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
        $sql = 'SELECT create_time, wechat_unionid FROM t_user WHERE user_id = ?';
        $invitedUserInfo = $this->db->getRow($sql, $this->userId);
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

    /**
     * 申请提现
     * @return array|int
     */
    public function requestWithdrawAction () {
        if (!isset($this->inputData['amount']) || !in_array($this->inputData['amount'], array(0.3, 20, 50, 100, 150, 200))) {
            return 202;
        }
        $sql = 'SELECT wechat_unionid, wechat_openid, user_status, alipay_account, alipay_name FROM t_user WHERE user_id = ?';
        $payInfo = $this->db->getRow($sql, $this->userId);
        if (!$payInfo['user_status']) {
            return 310;
        }
//        if (!$payInfo['wechat_unionid']) {
//            return 311;
//        }
        // 20201217 微信提现转支付宝提现
        if (!$payInfo['alipay_account']) {
            return 316;
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
        $sql = 'INSERT INTO t_withdraw SET user_id = :user_id, withdraw_amount = :withdraw_amount, withdraw_gold = :withdraw_gold, withdraw_status = :withdraw_status, withdraw_account = :withdraw_account, withdraw_name = :withdraw_name, withdraw_method = :withdraw_method';
        $this->db->exec($sql, array('user_id' => $this->userId, 'withdraw_amount' => $this->inputData['amount'], 'withdraw_gold' => $withdrawalGold, 'withdraw_status' => 'pending', 'withdraw_account' => $payInfo['alipay_account'], 'withdraw_name' => $payInfo['alipay_name'], 'withdraw_method' => 'alipay'));
        return array();
    }

    /**
     * 用户反馈
     * @return array|int
     */
    public function feedbackAction () {
        if (!isset($this->inputData['content']) && $this->inputData['content']) {
            return 202;
        }
        //判断多次提交需要超过多久
        $sql = 'SELECT create_time FROM t_user_feedback WHERE user_id = ? ORDER BY feedback_id DESC';
        $lastUpload = $this->db->getOne($sql, $this->userId);
        if ($lastUpload && (time() - strtotime($lastUpload) < 600)) {
            return 314;
        }

        $image1 = $image2 = $image3 = FALSE;
        foreach (array('image1', 'image2', 'image3') as $image) {
            if (isset($this->inputData[$image]) && $this->inputData[$image]) {
                $$image = $this->uploadImage($this->inputData[$image]);
                if (FALSE === $$image) {
                    continue;
                }
            }
        }

        $sql = 'INSERT INTO t_user_feedback SET user_id = :user_id, content = :content, phone = :phone, image_1 = :image_1, image_2 = :image_2, image_3 = :image_3';
        $this->db->exec($sql, array(
            'user_id' => $this->userId,
            'content' => $this->inputData['content'],
            'phone' => $this->inputData['phone'] ?? 0,
            'image_1' => $image1 ?: '',
            'image_2' => $image2 ?: '',
            'image_3' => $image3 ?: '',
        ));
        return array();
    }

    /**
     * 绑定支付宝账号
     * @return array|int
     */
    public function alipayAction () {
        if (!isset($this->inputData['name']) || !isset($this->inputData['account'])) {
            return 202;
        }
        $sql = 'SELECT alipay_account FROM t_user WHERE user_id = ?';
        $aliInfo = $this->db->getOne($sql, $this->userId);
        if ($aliInfo) {
            return 317;
        }
        $sql = 'SELECT user_id FROM t_user WHERE alipay_account = ?';
        $bindInfo = $this->db->getOne($sql, $this->inputData['account']);
        if ($bindInfo) {
            return 318;
        }
        $sql = 'UPDATE t_user SET alipay_account = ?, alipay_name = ? WHERE user_id = ?';
        $this->db->exec($sql, $this->inputData['account'], $this->inputData['name'], $this->userId);
        return array();
    }

    /**
     * 保存用户上传图片
     * @param $code base64
     * @return bool|string
     */
    protected function uploadImage ($code) {
//        strlen($code);
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $code, $result)){
            $ext = strtolower($result[2]);
//            if (!in_array($ext, array('jpg','jpeg', 'png', 'gif', 'bmp'))) {
//                return new ApiReturn('', 313,'上传图片格式不正确');
//            }

            $length = strlen($code) - strlen($result[1]);
            $size = $length - $length / 4;
            if ($size > 1024 * 1024) {
                return FALSE;
            }
            $saveFile = (ENV_PRODUCTION ? '' : 'test-') . substr(md5(substr($code, 20)), 10) . time() . '.' . strtolower($ext);

            file_put_contents('/tmp/' . $saveFile, base64_decode(str_replace($result[1], '', $code)));

            $oss = new \Core\Oss();
            $uploadReturn = $oss->upload('upload/' . $saveFile, '/tmp/' . $saveFile);
            if ($uploadReturn !== TRUE) {
                return FALSE;
            }
            return 'upload/' . $saveFile;

//            $saveFile = date('Ymd') . '/';
//            if (!is_dir(UPLOAD_IMAGE_DIR . $saveFile)) {
//                $a = mkdir(UPLOAD_IMAGE_DIR . $saveFile, 0755, true);
//            }
//
//            if (file_put_contents(UPLOAD_IMAGE_DIR . $saveFile, base64_decode(str_replace($result[1], '', $code)))) {
//                return 'upload/image/' . $saveFile;
//            } else {
//                return new ApiReturn('', 314,'上传失败');
//            }
        }else{
            echo 111;
            return FALSE;
        }
    }


}