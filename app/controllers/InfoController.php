<?php

namespace Controller;

use Core\Controller;

class InfoController extends Controller
{
    /**
     * 首页信息
     * @return array
     */
    public function startAction () {
        $taskClass = new \Core\Task($this->userId);
        $adClass = new \Core\Ad($this->userId);
        // 更新步数
        if (isset($this->inputData['totalWalk'])) {
            $this->model->walk->updateTotal($this->userId, $this->inputData['totalWalk']);
        }
        // 金币
        // 宝箱
        // receiveTime serverTime currentCount award
        // 中间任务
        // 底部任务
        return array('walkInfo' => $taskClass->getInfo('walk'), 'boxInfo' => $taskClass->getInfo('box'), 'taskMid' => $adClass->getInfo('startMid'), 'taskBot' => $adClass->getInfo('startBot'));
    }

    /**
     * 步数奖励任务信息
     * @return array
     */
    public function walkAction () {
        $taskClass = new \Core\Task($this->userId);
        return $taskClass->getInfo('walk');
    }

    /**
     * 大转盘活动信息
     * @return array
     */
    public function lotteryAction () {
        $taskClass = new \Core\Task($this->userId);
        return array('lotteryInfo' => $taskClass->getInfo('lottery'), 'awardRoll' => array('游客20201117 抽到100金币', '游客20221117 抽到200金币', '游客20201119 抽到50金币', '游客20181117 抽到100金币'));
    }

    /**
     * 任务页面信息
     * @return array
     */
    public function taskAction () {
        $taskClass = new \Core\Task($this->userId);
        $adClass = new \Core\Ad($this->userId);
        return array('signInfo' => $taskClass->getInfo('sign'), 'taskTop' => $adClass->getInfo('taskTop'), 'taskDaily' => $adClass->getInfo('taskDaily'), 'taskNewer' => $adClass->getInfo('taskNewer'));
    }

    /**
     * 步数阶段奖励信息
     * @return array
     */
    public function walkStageAction () {
        $taskClass = new \Core\Task($this->userId);
        return array('list' => $taskClass->getInfo('walkStage'));
    }

    /**
     * 喝水打卡活动信息
     * @return array
     */
    public function drinkAction () {
        $taskClass = new \Core\Task($this->userId);
        return array('list' => $taskClass->getInfo('drink'));
    }

    /**
     * 金币明细
     * @return array
     */
    public function goldDetailsAction () {
        return array('list' => $this->model->gold->details($this->userId));
    }

    /**
     * 提现页面信息
     * @return array
     */
    public function withdrawAction () {
        $sql = 'SELECT wechat_unionid, alipay_account FROM t_user WHERE user_id = ?';
        $bindInfo = $this->db->getRow($sql, $this->userId);
        $withdrawArr = array(0.5, 5, 20, 50, 100, 200);
        foreach ($withdrawArr as $amount) {
            // 0.5提现只能一次。
            if (0.5 == $amount) {
                $sql = 'SELECT COUNT(*) FROM t_withdraw WHERE user_id = ? AND withdraw_amount = ? AND (withdraw_status = "pending" OR withdraw_status = "success")';
                if ($this->db->getOne($sql, $this->userId, $amount)) {
                    continue;
                }
            }
            $withdrawList[] = array('amount' => $amount, 'gold' => $amount * 10000);
        }
        return array('isBindWechat' => ($bindInfo && $bindInfo['wechat_unionid']) ? 1 : 0, 'withdrawList' => $withdrawList, 'isBindAlipay' => ($bindInfo && $bindInfo['alipay_account']) ? 1 : 0);
    }

    /**
     * 提现明细
     * @return array
     */
    public function withdrawDetailsAction () {
        $sql = 'SELECT withdraw_amount amount, CASE withdraw_status WHEN "pending" THEN \'审核中\' WHEN "failure" THEN \'审核失败\' ELSE \'审核成功\' END status, "支付宝" method, UNIX_TIMESTAMP(create_time) * 1000 time, withdraw_remark reason FROM t_withdraw WHERE user_id = ? ORDER BY withdraw_id DESC';
        $withdrawList = $this->db->getAll($sql, $this->userId);
        return array('list' => $withdrawList);
    }

    /**
     *
     */
    public function adStatus () {
        $sql = 'SELECT ad_status FROM t_version_ad WHERE version_id = ? AND app_name = ?';
        $adStatus = $this->db->getOne($sql, $_SERVER['HTTP_VERSION_CODE'] ?? 0, $_SERVER['HTTP_SOURCE'] ?? '') ?: 0;
        return array('adStatus' => $adStatus);
    }
}