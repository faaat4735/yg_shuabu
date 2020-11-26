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

    public function drinkAction () {
        $taskClass = new \Core\Task($this->userId);
        return array('list' => $taskClass->getInfo('drink'));
    }
}