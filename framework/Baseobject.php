<?php
class frm_Baseobject {
    public function __set($name, $value){
        $this->$name = $value;
    }
    public function __get($name){
        return isset($this->$name) ? $this->name : null;
    }
    public function __isset($name){
        return isset($this->$name);
    }
    public function __unset($name){
        unset($this->$name);
    }
    public function __call($fun_name, $args){
        print "你所调用的函数：{$fun_name}(参数：";
        print_r($args);
        print ")不存在！<br>\n";
        exit;
    }
}
