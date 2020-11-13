<?php

namespace Model;
use Core\Model;

class GoldModel extends Model
{

    public function receiveGold ($userId, $type) {
        $sql = 'SELECT count(gold_id) count, MAX(create_time) maxTime FROM t_gold WHERE user_id = ? AND change_date = ? AND gold_source = ?';
        return $this->db->getRow($sql, $userId, date('Y-m-d'), $type);
    }
}