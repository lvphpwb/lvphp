<?php
class frm_Uri extends frm_Baseobject{
    public $controllername = 'IndexController';
    public $functionname = 'indexAction';


    public function __construct(){
        if(strpos($_SERVER['REQUEST_URI'], '?') === FALSE){
            $request_uri = explode("/",$_SERVER['REQUEST_URI']);
        }else{
            $request_uri_info = explode('?', $_SERVER['REQUEST_URI']);
            $request_uri = explode("/",$request_uri_info[0]);
        }
        if(empty($request_uri[1]) && empty($request_uri[2])){
            $this->controllername = 'IndexController';
            $this->functionname = "indexAction";
        }else{
            $this->controllername = strtolower($request_uri[1]) == 'index.php' ? 'IndexController' : ucfirst($request_uri[1]).'Controller';
            $this->functionname = empty($request_uri[2]) ? 'indexAction' : $request_uri[2] . 'Action';
        }
        if(count($request_uri) > 2){
            for($i=3;$i<count($request_uri);$i++){
                if(!empty($request_uri[$i])){
                    frm_Request::setVar('urlpath_' . ($i-3), urldecode($request_uri[$i]), 'get');
                }
            }
        }
    }

    public function Route(){
        //note 实例化控制器
        if(file_exists(APP_PATH . "/application/controller/{$this->controllername}.php")){
            $controlClass = new ReflectionClass ( 'app_controller_' . $this->controllername );
            $controllerObj = $controlClass->newInstance();
            if(method_exists($controllerObj, $this->functionname)){
                frm_Request::setVar('controllername', $this->controllername, 'system');
                frm_Request::setVar('functionname', $this->functionname, 'system');
                return $controllerObj;
            }else{
                exit("控制器{$this->controllername}的{$this->functionname}不存在！");
            }
        }else{
            exit("控制器{$this->controllername}不存在！");
        }
    }
}