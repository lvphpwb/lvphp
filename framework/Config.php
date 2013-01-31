<?php

class frm_Config extends frm_Baseobject{
    private static $conf = array();

	static function load(){
		$conffile = APP_PATH . '/application/config/config.php';
		if(file_exists($conffile)){
			require_once $conffile;
			self::$conf = $systemconfig;
		}
	}

    static function getConfig( $key ){
		return self::$conf[$key];
	}
}