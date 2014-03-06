<?php
class frm_Model extends frm_Baseobject{
    protected $masterdb = null;
    protected $slavedb = null;
    public $tablename = null;
    public $key = null;

    public function __construct(){}

    public function select($fields, $where, $master = false){
        $query_db = $master ? $this->masterdb : $this->slavedb;
        return $query_db->select($this->tablename, $fields, $where);
    }

    public function insert($data){
        return $this->masterdb->insert($this->tablename, $data);
    }

    public function update($data, $where){
        return $this->masterdb->update($this->tablename, $data, $where);
    }

    public function delete($where){
        return $this->masterdb->delete($this->tablename, $where);
    }

    public function get($fields, $where, $master = false){
        $query_db = $master ? $this->masterdb : $this->slavedb;
        return $query_db->get($this->tablename, $fields, $where);
    }

    public function has($where, $master = false){
        $query_db = $master ? $this->masterdb : $this->slavedb;
        return $query_db->has($this->tablename, $where);
    }

    public function count($where, $master = false){
        $query_db = $master ? $this->masterdb : $this->slavedb;
        return $query_db->count($this->tablename, $where);
    }

    public function query($sql, $master = false){
        $query_db = $master ? $this->masterdb : $this->slavedb;
        return $query_db->query($sql);
    }

    public function selectSQL($sql, $master = false){
        $query_db = $master ? $this->masterdb : $this->slavedb;
        return $query_db->query($sql)->fetchAll();
    }

    public function last_query($master = false){
        $query_db = $master ? $this->masterdb : $this->slavedb;
        return $query_db->last_query();
    }

    public function error(){
        $query_db = $master ? $this->masterdb : $this->slavedb;
        return $query_db->error();
    }
}