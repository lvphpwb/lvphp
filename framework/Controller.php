<?php
class frm_Controller extends frm_Baseobject{
    public $templatefile = "";
    public function __construct(){
        $this->SetTemplate();
    }

    public function GetFilterList($function){
        return array('in'=>array(),'out'=>array());
    }

    public function success($msg, $url='/', $timeout=3){
        exit("<html><body style='padding:0px;margin:0px;'><div style='width:100%;height:100%;background-color:#ddd;'><div style='position: fixed;display:block;top:40%;left:45%;color: #468847;border-radius: 4px 4px 4px 4px;background-color: #DFF0D8;border: 1px solid #D6E9C6;padding: 8px 14px;text-align: center;'>" . $msg . " 正在跳转...</div></div></body></html><script>setTimeout(function(){location.href='{$url}';}, " . $timeout*1000 . ");</script>");
    }

    public function error($msg, $url='/', $timeout=3){
        exit("<html><body style='padding:0px;margin:0px;'><div style='width:100%;height:100%;background-color:#ddd;'><div style='position: fixed;display:block;top:40%;left:45%;color: #B94A48;border-radius: 4px 4px 4px 4px;background-color: #F2DEDE;border: 1px solid #EED3D7;padding: 8px 14px;text-align: center;'>" . $msg . " 正在跳转...</div></div></body></html><script>setTimeout(function(){location.href='{$url}';}, " . $timeout*1000 . ");</script>");
    }

    public function jump($url){
        $url = $url ?: '/';
        echo "<script>location.href='{$url}';</script>";
        exit;
    }

    public function close($message = ''){
        echo "<p><font color='red'>网站已关闭</font></p>";
        exit;
    }

    public function Display($template = ''){
        $template_file = APP_PATH . "/extend/Template.php";
        $templateObj = file_exists($template_file) ? ( new extend_Template() ) : ( new frm_Template() );
        $templateObj->setTemplateDir(APP_PATH . "/application/template/");
        $templateObj->setTemplateCacheDir(APP_PATH . "/application/template_c/");
        $templateObj->Display( $template ? $template : $this->templatefile );
    }

    public function SetTemplate($template = ''){
        $this->templatefile = $template ? $template : strtolower(frm_Request::getVar('controllername','system') . "_" . frm_Request::getVar('actionname','system') . ".html");
    }
}