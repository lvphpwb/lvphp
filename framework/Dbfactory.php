<?php
class frm_Dbfactory extends frm_Baseobject{
    public static $db = null;

    public static function factory($config){
        if(!frm_Dbfactory::$db[$config['name']]){
            $dbClass = new ReflectionClass ( 'frm_Medoo' );
			frm_Dbfactory::$db[$config['name']]['master'] = $dbClass->newInstance($config['master']);
            frm_Dbfactory::$db[$config['name']]['slave'] = $dbClass->newInstance($config['slave']);
        }
        return frm_Dbfactory::$db[$config['name']];
    }
}