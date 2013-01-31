<?php

class frm_Response extends frm_Baseobject{
    private static $reponse = array();

	static function getReponse(){
		return frm_Response::$reponse;
	}

    static function setResponse( $value ){
		frm_Response::$reponse  = $value;
	}

	static function set($key,$value){
		frm_Response::$reponse[$key] = $value;
	}

	static function get($key){
		return frm_Response::$reponse[$key];
	}
}