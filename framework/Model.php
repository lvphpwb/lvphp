<?php
class frm_Model extends frm_Baseobject{
    protected $masterdb = null;
    protected $slavedb = null;
    public $tablename = null;
    public $key = null;

    public function __construct(){
        
    }

    public function getOne($sql, $master = false){
        $query_db = $master ? $this->masterdb : $this->slavedb;
        return $query_db->getOne($sql);
    }

    public function getByKey($key, $master = false){
        $query_db = $master ? $this->masterdb : $this->slavedb;
        return $query_db->select('*', $this->tablename, "{$this->key}='{$key}'", '0,1');
    }

    public function getRow($sql, $master = false){
        $query_db = $master ? $this->masterdb : $this->slavedb;
        return $query_db->getRow($sql);
    }

    public function getAll($sql, $master = false){
        $query_db = $master ? $this->masterdb : $this->slavedb;
        return $query_db->getAll($sql);
    }

    public function getAffectedRows(){
        return $this->masterdb->getAffectedRows();
    }

    public function lastId(){
        return $this->masterdb->lastId();
    }

    public function select($fields, $where = '', $arg = array('order'=>'', 'limit'=>'', 'group'=>''), $master = false){
        $query_db = $master ? $this->masterdb : $this->slavedb;
        $order_by = empty($arg['order']) ? "{$this->key} DESC" : $arg['order'];
        return $query_db->select($fields, $this->tablename, $where, $arg['limit'], $order_by, $arg['group']);
    }

    public function selectOne($fields, $where = '', $master = false){
        $query_db = $master ? $this->masterdb : $this->slavedb;
        return $query_db->select($fields, $this->tablename, $where, '0,1');
    }

    public function count($where = '1', $master = false){
        $query_db = $master ? $this->masterdb : $this->slavedb;
        return $query_db->count($this->tablename, $where);
    }

    public function insert($data){
        return $this->masterdb->insert($this->tablename, $data);
    }

    public function insertMulti($data){
        return $this->masterdb->insert($this->tablename, $data);
    }

    public function update($data, $where = ''){
        return $this->masterdb->update($this->tablename, $data, $where);
    }

    public function updateByKey($data, $key){
        return $this->masterdb->update($this->tablename, $data, "{$this->key}='{$key}'");
    }

    public function replace($data){
        return $this->masterdb->replace($this->tablename, $data);
    }

    public function delete($where){
        return $this->masterdb->delete($this->tablename, $where);
    }

    public function deleteByKey($key){
        return $this->masterdb->delete($this->tablename, "{$this->key}='{$key}'");
    }

    public function execute($sql){
        return $this->masterdb->execute($sql);
    }

    public function close(){
        return $this->masterdb->close();
        return $this->slavedb->close();
    }
}