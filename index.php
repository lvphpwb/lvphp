<?php
header( 'Content-Type:   text/html;   charset=utf-8 ');
error_reporting( E_ALL ^ E_NOTICE);
define("APP_PATH", dirname(__FILE__) );
include APP_PATH . '/framework/Application.php';

$app = new frm_Application();
$app->run();