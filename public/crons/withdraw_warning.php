<?php

require_once __DIR__ . '/../init.inc.php';

//$endHour = date('H');
//
//if ($endHour > 6) {
//    $last = 1;
//} elseif ($endHour == 6) {
//    $last = 6;
//} elseif ($endHour == 0) {
//    $last = 1;
//} else {
//    exit;
//}

//$startHour = date('H:00', strtotime('-' . $last . ' hours'));
//$endHour = date('H:00');
//$startTime = date('Y-m-d H:00:00', strtotime('-' . $last . ' hour'));
//$endTime = date('Y-m-d H:00:00');
$startTime = date('Y-m-d 00:00:00', strtotime('-1 day'));
$endTime = date('Y-m-d 23:59:59', strtotime('-1 day'));

$html = '<html lang="en">
<head>
<style>
    table,table tr th, table tr td { border:1px solid #000000; }
    table { width: 200px; min-height: 25px; line-height: 25px; text-align: center; border-collapse: collapse;}
</style>
</head>
<body>';
$str = '<table>
    <tr><th>待处理提现笔数</th><th>待处理提现笔数金额</th></tr>
    <tr><td>%d</td><td>%f</td></tr>
</table>';
$db = \Core\Db::getDbInstance();

$sql = 'SELECT COUNT(withdraw_id) count, SUM(withdraw_amount) sum FROM t_withdraw WHERE create_time >= ? AND create_time < ? AND withdraw_status = "pending"';
$total = $db->getRow($sql, $startTime, $endTime);

$html .= sprintf($str, $total['count'] ?: 0, $total['sum'] ?: 0);

$html .= '</body>
</html>';
$mail = new \Core\Mail();
$mail->send(array('zjf580@163.com'), (ENV_PRODUCTION ? '' : '测试-') . date('Y-m-d', strtotime('-1 day')) . '定时提现邮件', $html);
echo 'done' . PHP_EOL;

