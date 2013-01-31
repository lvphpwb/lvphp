<?php
defined('APP_PATH') || exit("NO APP_PATH!");
define("FRAMEWORK_PATH", dirname(__FILE__) );
//note 自动加载机制
function __autoload($class_name) {
    $class_info = explode('_', $class_name);
    if(in_array($class_info[0], array('frm', 'app', 'extend'))){
        $class_type = $class_info[0];
        unset($class_info[0]);
        if($class_type == 'frm'){
            $file_path = FRAMEWORK_PATH . '/' . implode('/', $class_info) . '.php';
        }else if($class_type == 'app'){
            $file_path = APP_PATH . '/application/' . implode('/', $class_info) . '.php';
        }else if($class_type == 'extend'){
            $file_path = APP_PATH . '/extend/' . implode('/', $class_info) . '.php';
        }
        require_once ($file_path);
        unset($class_name, $class_info, $class_type, $file_path);
    }else{
        exit("加载的类定义错误:{$class_name}！");
    }
}

class frm_Application extends frm_Baseobject{
    protected $controller = null;
    protected $filterList = array('in'=>array(), 'out'=>array());

    public function __construct() {
        //note 加载配置
        $this->LoadConfig();
        //note 路由解析
        $this->Route();
        //note 加载Filter
        $this->GetFilterList();
    }

    private function LoadConfig(){
        $confclass_file = APP_PATH . "/extend/Config.php";
        file_exists($confclass_file) ? (extend_Config::load()) : (frm_Config::load());
    }

    private function Route(){
        //note 获取url解析对象实例化
        $uri_file = APP_PATH . "/extend/Uri.php";
        $uriObj = file_exists($uri_file) ? ( new extend_Uri() ) : ( new frm_Uri() );
        $this->controller = $uriObj->Route();
    }

    public function Run() {
        //note 进行Filter
        if($this->filterList['in']){
            foreach ( $this->filterList['in'] as $filter ) {
                $filter->DealFilter();
            }
        }
        call_user_func( array($this->controller, frm_Request::getVar('functionname', 'system')) );
        //note 显示前处理数据
        if($this->filterList['out']){
            foreach ( $this->filterList['out'] as $filter ) {
                $filter->DealFilter();
            }
        }
        //note 渲染模板
        $template_file = APP_PATH . "/extend/Template.php";
        $templateObj = file_exists($template_file) ? ( new extend_Template() ) : ( new frm_Template() );
        $templateObj->Display( $this->controller->GetTemplate() );
    }

    private function GetFilterList(){
        $filter_info = $this->controller->GetFilterList( frm_Request::getVar('functionname', 'system') );
        if($filter_info['in']){
            foreach ($filter_info['in'] as $filter){
                $filterClass = new ReflectionClass ( 'app_filter_' . $filter . "InFilter" );
                $this->filterList['in'][] = $filterClass->newInstance();
            }
        }
        if($filter_info['out']){
            foreach ($filter_info['out'] as $filter){
                $filterClass = new ReflectionClass ( 'app_filter_' . $filter . "OutFilter" );
                $this->filterList['out'][] = $filterClass->newInstance();
            }
        }
    }
}