<?php
class frm_Uri extends frm_Baseobject{
    public $controllername = 'IndexController';
    public $actionname = 'indexAction';

    public function __construct(){
        if(strpos($_SERVER['REQUEST_URI'], '?') === FALSE){
            $request_uri = explode("/",$_SERVER['REQUEST_URI']);
        }else{
            $request_uri_info = explode('?', $_SERVER['REQUEST_URI']);
            $request_uri = explode("/",$request_uri_info[0]);
        }
        if(empty($request_uri[1]) && empty($request_uri[2])){
            $this->controllername = 'IndexController';
            $this->actionname = "indexAction";
        }else{
            $this->controllername = strtolower($request_uri[1]) == 'index.php' ? 'IndexController' : ucfirst($request_uri[1]).'Controller';
            $this->actionname = empty($request_uri[2]) ? 'indexAction' : $request_uri[2] . 'Action';
        }
        frm_Request::setVar('controllername', $this->controllername, 'system');
        frm_Request::setVar('actionname', $this->actionname, 'system');
        //note 处理path
        if(count($request_uri) > 2){
            $urlargs = array();
            for($i=3;$i<count($request_uri);$i++){
                if($request_uri[$i]){
                    $urlargs[] = urldecode($request_uri[$i]);
                }
            }
            frm_Request::setVar('urlargs', $urlargs, 'get');
        }
    }

    public function Route(){
        //note 实例化控制器
        if(file_exists(APP_PATH . "/application/controller/{$this->controllername}.php")){
            $controlClass = new ReflectionClass ( 'app_controller_' . $this->controllername );
            $controllerObj = $controlClass->newInstance();
            if(method_exists($controllerObj, $this->actionname)){
                return $controllerObj;
            }else{
                exit("控制器{$this->controllername}的{$this->actionname}不存在！");
            }
        }else{
            exit("控制器{$this->controllername}不存在！");
        }
    }
}