<?php

// 处理提现
require_once __DIR__ . '/../init.inc.php';

$db = \Core\Db::getDbInstance();
$model = new \Core\Model();
$sql = 'SELECT variable_value FROM t_variable WHERE variable_name = "withdraw_max"';
$withdrawMax = $db->getOne($sql);
$wechatPay = new \Core\Wxpay();

while (true) {
    $sql = 'SELECT withdraw_id, withdraw_amount, withdraw_account, user_id, withdraw_gold FROM t_withdraw WHERE withdraw_status = "pending" AND withdraw_method = "wechat" AND withdraw_amount = 0.5';
    $withdrawList = $db->getAll($sql);
    if ($withdrawList) {
        $sql = 'SELECT COUNT(withdraw_id) FROM t_withdraw WHERE withdraw_status = "success" AND withdraw_method = "wechat" AND withdraw_amount = 0.5 AND change_time >= ?';
        $count = $db->getOne($sql, date('Y-m-d'));
        if ($count < $withdrawMax) {
            foreach ($withdrawList as $withdrawInfo) {
                $returnStatus = $wechatPay->transfer($withdrawInfo['withdraw_amount'], $withdrawInfo['withdraw_account']);
                if (TRUE === $returnStatus) {
                    $sql = 'SELECT COUNT(*) FROM t_withdraw WHERE user_id = ?';
                    $withdrawCount = $db->getOne($sql, $withdrawInfo['user_id']) ?: 0;
                    $model->gold->insert(array('user_id' => $withdrawInfo['user_id'], 'gold_amount' => 0 - $withdrawInfo['withdraw_gold'], 'gold_source' => "withdraw", 'gold_count' => $withdrawCount + 1));
                    $return = $model->withdraw->updateStatus(array('withdraw_status' => 'success', 'withdraw_id' => $withdrawInfo['withdraw_id']));
                } else {
                    //to do failure reason from api return
                    $return = $model->withdraw->updateStatus(array('withdraw_status' => 'failure', 'withdraw_remark' => $returnStatus, 'withdraw_id' => $withdrawInfo['withdraw_id']));
                }
            }
        }
    }

    sleep(3);
}

echo 'done';