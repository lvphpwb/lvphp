<?php
class frm_Baseobject {
    public function __set($property_name, $value){
        $this->$property_name = $value;
    }

    public function __get($property_name){
        if(isset($this->$property_name)){
            return($this->$property_name);
        }else{
            return(NULL);
        }
    }

    public function __isset($property_name){
        return isset($this->$property_name);
    }

    public function __unset($property_name){
        unset($this->$property_name);
    }

    public function __call($function_name, $args){
        print "你所调用的函数：$function_name(参数：";
        print_r($args);
        print ")不存在！<br>\n";
        exit;
    }
}
