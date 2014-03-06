<?php
class frm_Request extends frm_Baseobject{
	private static $system = array ();

	static public function getVar($name, $hash = 'default', $default = null) {
		$hash = strtoupper ( $hash );
		$hash === 'METHOD' && $hash = strtoupper ( $_SERVER ['REQUEST_METHOD'] );
		switch ($hash) {
			case 'GET' : $input =$_GET;break;
			case 'POST' : $input = $_POST;break;
			case 'FILES' : $input = $_FILES;break;
			case 'COOKIE' : $input = $_COOKIE;break;
			case 'SERVER' : $input = $_SERVER;break;
			case 'SYSTEM' : $input = self::$system;break;
			default : $input = $_REQUEST;break;
		}
        return (isset ($input[$name]) ? $input[$name] : $default);
	}

	static public function setVar($name, $value = null, $hash = 'method') {
		$hash = strtoupper ( $hash );
		$hash === 'METHOD' && $hash = strtoupper( $_SERVER['REQUEST_METHOD'] );
		switch ($hash) {
			case 'GET' : $_GET[$name] = $value;$_REQUEST[$name] = $value;break;
			case 'POST' : $_POST[$name] = $value;$_REQUEST[$name] = $value;break;
			case 'COOKIE' : $_COOKIE[$name] = $value;$_REQUEST[$name] = $value;break;
			case 'FILES' : $_FILES[$name] = $value;break;
			case 'SERVER' : $_SERVER['name'] = $value;break;
			case 'SYSTEM' : self::$system[$name]=$value;break;
            default : $_REQUEST[$name] = $value;break;
		}
	}
}