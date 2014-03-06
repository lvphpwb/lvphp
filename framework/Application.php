<?php
defined('APP_PATH') || exit("NO APP_PATH!");
define("FRAMEWORK_PATH", dirname(__FILE__) );
//note 自动加载机制
function __autoload($class_name) {
    $class_info = explode('_', $class_name);
    $class_type = $class_info[0];
    $type_arr = array('frm'=> FRAMEWORK_PATH . '/', 'app'=>APP_PATH . '/application/', 'extend'=>APP_PATH . '/extend/');
    if($type_arr[$class_type]){
        unset($class_info[0]);
        include $type_arr[$class_type] . implode('/', $class_info) . '.php';
        unset($class_name, $class_info, $class_type, $type_arr);
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
        file_exists(APP_PATH . "/extend/Config.php") ? (extend_Config::load()) : (frm_Config::load());
    }

    private function Route(){
        //note 获取url解析对象实例化
        $uriObj = file_exists(APP_PATH . "/extend/Uri.php") ? ( new extend_Uri() ) : ( new frm_Uri() );
        $this->controller = $uriObj->Route();
    }

    public function Run() {
        //note 进行Filter
        if($this->filterList['in']){
            foreach ( $this->filterList['in'] as $filter ) {
                $filter->DealFilter($this->controller);
            }
        }
        call_user_func( array($this->controller, frm_Request::getVar('actionname', 'system')) );
        //note 显示前处理数据
        if($this->filterList['out']){
            foreach ( $this->filterList['out'] as $filter ) {
                $filter->DealFilter($this->controller);
            }
        }
        //note 渲染模板
        $this->controller->Display();
    }

    private function GetFilterList(){
        $filter_info = $this->controller->GetFilterList( frm_Request::getVar('actionname', 'system') );
        if($filter_info['in']){
            foreach ($filter_info['in'] as $filter){
                $filterClass = new ReflectionClass ( 'app_' . $filter . "InFilter" );
                $this->filterList['in'][] = $filterClass->newInstance();
            }
        }
        if($filter_info['out']){
            foreach ($filter_info['out'] as $filter){
                $filterClass = new ReflectionClass ( 'app_' . $filter . "OutFilter" );
                $this->filterList['out'][] = $filterClass->newInstance();
            }
        }
    }
}