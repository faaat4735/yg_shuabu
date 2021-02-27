<?php

// 发放邀请的奖励
// 每日1：00执行一次
require_once __DIR__ . '/../init.inc.php';

$db = \Core\Db::getDbInstance();

$sql = 'DELETE FROM t_gold WHERE user_id = 16 AND gold_source = "sport"';
$db->exec($sql);
$sql = 'DELETE FROM t_activity_sport WHERE user_id = 16';
$db->exec($sql);

echo 'done';