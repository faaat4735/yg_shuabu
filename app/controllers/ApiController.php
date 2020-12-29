<?php

namespace Controller;

class ApiController
{

    public function init() {

    }

    public function monitorAction () {
        if ($_GET) {
            $sql = 'INSERT INTO t_ocean_monitor SET imei_md5 = ?, oaid = ?, androidid_md5 = ?, mac_md5 = ?, ad_id = ?, params = ?';
            $this->db->exec($sql, $_GET['imei'] ?? '', $_GET['oaid'] ?? '', $_GET['androidid'] ?? '', $_GET['mac'] ?? '', $_GET['adid'] ?? '', json_encode($_GET));
        }
        $return = array('code' => '200', 'msg' => '保存成功');
        echo json_encode($return);
        exit;
    }


}