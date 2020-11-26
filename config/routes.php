<?php

use NoahBuscher\Macaw\Macaw;

Macaw::any('/(:all)/(:all)', function($controller, $action) {
    $controllerName = '\\Controller\\' .ucfirst($controller) . 'Controller';
    $controllerClass = new $controllerName();
    $actionName = $action . 'Action';
    if (!method_exists($controllerClass, $actionName)) {
        $return = '未找到';
        echo $return;
        exit;
    } else {
        $result = $controllerClass->init();
        if (TRUE === $result) {
            $result = $controllerClass->$actionName();
        }
    }
    // 返回数据
    if (is_array($result)) {
        if ($result == array()) {
            $result = (object)array();
        }
        $return = array('code' => 200, 'data' => $result, 'msg' => '');
    } else {
    // 返回错误码
        //opt 返回错误码msg列表
        //201 token 错误  202 访问错误  203 认证失败
        $array = array(201 => '无效token', 202 => '访问失败，请稍后再试', 203 => '认证失败，请稍后再试', 204 => '抱歉您的账户已被冻结', 301 => '验证失败，无法领取', 302 => '验证失败，无法领取', 303 => '验证失败，无法领取', 304 => '用户已绑定微信', 305 => '不能重复绑定', 306 => '您已填写过邀请码', 307 => '邀请码无效，请重新输入', 308 => '验证码无效，请填写比您先注册的用户的邀请码', 309 => '请先绑定微信后，再填写邀请码');
        $return = array('code' => $result, 'data' => (object) array(), 'msg' => $array[$result] ?? '');
    }
    if (DEBUG_MODE) {
        //add api log
        $logFile = LOG_DIR;
        if (!is_dir($logFile)) {
            mkdir($logFile, 0755, true);
        }
        file_put_contents($logFile . 'access.log', date('Y-m-d H:i:s') . '|' . ($_SERVER['HTTP_VERSION_CODE'] ?? ' ') . '|' . ($_SERVER['HTTP_SOURCE'] ?? ' ') . '|' . ($_SERVER['HTTP_TIME'] ?? ' ') . '|' . ($_SERVER['HTTP_SECRET'] ?? ' ') . '|' . file_get_contents("php://input") . '|' . json_encode($return) . PHP_EOL, FILE_APPEND);
    }
    echo json_encode($return);
    exit;
});

Macaw::get('(:all)', function($fu) {
    //todo 404
    echo '未匹配到路由<br>'.$fu;
});

Macaw::dispatch();