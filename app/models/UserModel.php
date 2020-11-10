<?php

namespace Model;
use Core\Model;

class UserModel extends Model
{
    public function get () {
        $sql = 'SHOW TABLES';
        return $this->db->getAll($sql);
        return '11sdfasdf';
    }
}