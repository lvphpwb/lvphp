<?php

class frm_Template extends frm_Baseobject{
    //note 模板文件夹路径
    public $templateDir = '';
    //note 模板缓存文件夹路径
    public $templatecacheDir = '';
    //note 模板文件
	private $templateFile = null;
	//note 模板缓存文件
	private $templateTPL = null;

	//note 正则表达式
	private $var_regexp = "\@?\\\$[a-zA-Z_][\\\$\w]*(?:\[[\w\-\.\"\'\[\]\$]+\])*";
	private $vtag_regexp = "\<\?php echo (\@?\\\$[a-zA-Z_][\\\$\w]*(?:\[[\w\-\.\"\'\[\]\$]+\])*)\;\?\>";
	private $const_regexp = "\{([\w]+)\}";

    public function __construct(){
    	$this->templateDir = frm_Config::getConfig('template_dir') ? frm_Config::getConfig('template_dir') : APP_PATH . "/application/template/";
    	$this->templatecacheDir = frm_Config::getConfig('templatecache_dir') ? frm_Config::getConfig('templatecache_dir') : APP_PATH . "/application/template_c/";
    }

    public function setTemplateDir($dir){
        $this->templateDir = $dir;
    }

    public function setTemplateCacheDir($dir){
        $this->templatecacheDir = $dir;
    }

    public function Display($templatefile){
		$this->templateFile = $this->templateDir . $templatefile;
		$this->templateTPL = $this->templatecacheDir . $templatefile;
		//note 判断缓存文件是否存在或过期，并创建
		if(!file_exists($this->templateTPL) || @filemtime($this->templateTPL) < @filemtime($this->templateFile)){
			$this->creatTemplateTPL();
		}
		$sys_response = frm_Response::getReponse();
        if($sys_response){
            foreach ($sys_response as $key=>$value){
                $$key = $value;
            }
        }
		include_once $this->templateTPL;
	}

    private function creatTemplateTPL(){
		$templateContent = file_get_contents($this->templateFile);
		$templateTPLContent = $this->makeTPL($templateContent);
		$this->makeDir(dirname($this->templateTPL));
		$this->writeFile($this->templateTPL, $templateTPLContent, 'w');
	}

    private function makeTPL($templateContent){
		$templateContent = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $templateContent);//去除html注释符号<!---->
		$templateContent = preg_replace("/\{($this->var_regexp)\}/", "<?php echo \\1;?>", $templateContent);//替换带{}的变量
		$templateContent = preg_replace("/\{($this->const_regexp)\}/", "<?php echo \\1;?>", $templateContent);//替换带{}的常量
		$templateContent = preg_replace("/(?<!\<\?php echo |\\\\)$this->var_regexp/", "<?php echo \\0;?>", $templateContent);//替换重复的<?php echo
		$templateContent = preg_replace("/\{php (.*?)\}/ies", "\$this->stripvTag('<?php \\1?>')", $templateContent);//替换php标签
		$templateContent = preg_replace("/\{for (.*?)\}/ies", "\$this->stripvTag('<?php for(\\1) {?>')", $templateContent);//替换for标签
		$templateContent = preg_replace("/\{elseif\s+(.+?)\}/ies", "\$this->stripvTag('<?php } elseif (\\1) { ?>')", $templateContent);//替换elseif标签
		for($i=0; $i<3; $i++) {
			$templateContent = preg_replace("/\{loop\s+$this->vtag_regexp\s+$this->vtag_regexp\s+$this->vtag_regexp\}(.+?)\{\/loop\}/ies", "\$this->loopSection('\\1', '\\2', '\\3', '\\4')", $templateContent);
			$templateContent = preg_replace("/\{loop\s+$this->vtag_regexp\s+$this->vtag_regexp\}(.+?)\{\/loop\}/ies", "\$this->loopSection('\\1', '', '\\2', '\\3')", $templateContent);
		}
		$templateContent = preg_replace("/\{if\s+(.+?)\}/ies", "\$this->stripvTag('<?php if(\\1) { ?>')", $templateContent);//替换if标签
		$templateContent = preg_replace("/\{include\s+(.*?)\}/is", "<?php include \\1; ?>", $templateContent);//替换include标签
		$templateContent = preg_replace("/\{template\s+(.*?)\}/is", '<?php $this->Layout("\\1"); ?>', $templateContent);//替换template标签
        $templateContent = preg_replace("/\{pluginid\s+id=(\d+?)\}/is", '<?php $this->ShowPlugin("\\1"); ?>', $templateContent);//替换插件标签
		$templateContent = preg_replace("/\{else\}/is", "<?php } else { ?>", $templateContent);//替换else标签
		$templateContent = preg_replace("/\{\/if\}/is", "<?php } ?>", $templateContent);//替换/if标签
		$templateContent = preg_replace("/\{\/for\}/is", "<?php } ?>", $templateContent);//替换/for标签
		$templateContent = preg_replace("/$this->const_regexp/", "<?php echo \\1;?>", $templateContent);//note {else} 也符合常量格式，此处要注意先后顺??
		$templateContent = preg_replace("/(\\\$[a-zA-Z_]\w+\[)([a-zA-Z_]\w+)\]/i", "\\1'\\2']", $templateContent);//将二维数组替换成带单引号的标准模式
		$templateContent = "<?php if(!defined('APP_PATH')) exit('Access Denied');?>\r\n$templateContent";
		return $templateContent;
	}

    private function loopSection($arr, $key, $v, $content) {
		$arr = preg_replace("/$this->vtag_regexp/is", "\\1", str_replace("\\\"", '"', $arr));
		$key = preg_replace("/$this->vtag_regexp/is", "\\1", str_replace("\\\"", '"', $key));
		$v = preg_replace("/$this->vtag_regexp/is", "\\1", str_replace("\\\"", '"', $v));
		$content = str_replace("\\\"", '"', $content);
		return $key ? "<?php foreach((array)$arr as $key=>$v) {?>$content<?php }?>" : "<?php foreach((array)$arr as $v) {?>$content<?php } ?>";
	}

	private function stripvTag($s){
		return preg_replace("/$this->vtag_regexp/is", "\\1", str_replace("\\\"", '"', $s));
	}

    private function writeFile($fileName, $data, $mod = 'w'){
		$handle = fopen($fileName, $mod);
		if ($handle) {
			if (fwrite($handle, $data) !== false) {
				fclose($handle);
				return true;
			}else 
				return false;
		}else{
			return false;
		}
	}

    private function Layout($templateName){
        $this->Display(strtolower($templateName . ".html"));
    }

    private function ShowPlugin( $pluginID ){
        extend_Plugin::dispatch( $pluginID );
    }

    private function makeDir($dir){
		return is_dir($dir) or mkdir($dir, 0777);
	}
}
