<?php
class frm_Form extends frm_Baseobject{

    public $error = null;

    public $items = null;

    protected $allcheck = false;

    protected $checkitems = null;

    public function __construct() {

	}

    protected function checkItem($key, $formitem){
        $itemvalue = $this->isPost() ? (frm_Request::getVar($key, 'POST')) : (frm_Request::getVar($key, 'GET'));
        $this->items[$key] = $itemvalue;
        foreach ($formitem as $checkunit) {
            if($checkunit['required'] !== false || !empty($itemvalue)){
                $err = $this->checkFun($itemvalue, $checkunit);
                if($err){
                    if($this->allcheck){
                        $this->error[$key] = $err;
                    }else{
                        $this->error = $err;
                    }
                    break;
                }
            }
        }
	}

    protected function checkFun($value, $checkunit){
        return call_user_func_array(array($this, $checkunit['checkfun']), array($value, $checkunit['preg'], $checkunit['param'], $checkunit['errmessage']));
    }

    //检验
	public function submit() {
		if(!empty($this->checkitems['items'])){
			foreach ($this->checkitems['items'] as $key => $formitem){
                if($formitem){
                    $this->checkItem($key, $formitem);
                    if(!$this->allcheck){
                        if($this->error){
                            break;
                        }
                    }
                }
			}
            return ($this->error ? false : true);
		}
        return true;
	}

    public function isPost(){
        return (empty($_POST) ? false : true);
    }

    public function isEmpty($str, $preg = "", $param = array(), $errmessage = "内容不能为空！"){
        if($str == null || (is_array($str) && count($str) == 0) || strlen($str) == 0){
            return $errmessage;
        }else{
            return "";
        }
    }

    public function isSame($str, $preg, $param = array(), $errmessage = "两次输入的内容不相同！"){
        if($str != $param[0]){
            return $errmessage;
        }else{
            return "";
        }
    }
    /**
     * 验证数据是否是一个电子邮件
     * @param string $email 需要验证的email地址
     * @return bool;
     */
    public function isEmail($str, $preg, $param = array(), $errmessage = "输入的邮箱格式错误！"){
        $res = $this->checkMatch($str, "/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}$/s", $param, $errmessage);
        return $res;
    }

    /**
     * 验证数据是否全是数字+字母
     * @param string $str 需要验证的数据;
     * @return bool
     */
    public function isLetnum($str, $preg, $param = array(), $errmessage = "请输入字母或数字！"){
        $res = $this->checkMatch($str, "/^[a-zA-Z0-9_]+$/", $param, $errmessage);
        return $res;
    }

    /**
     * 验证数据是否是qq号
     * @param $str
     */
    public function isQq($str, $preg, $param = array(), $errmessage = "输入的QQ号格式错误！"){
        $res = $this->checkMatch($str, "/^[1-9]{1}[0-9]{3,9}$/", $param, $errmessage);
        return $res;
    }

    /**
     * 验证数据是否是数字
     * @param string $str 需要验证的数据
     */
    public function isNumber($str, $preg, $param = array(), $errmessage = "请输入纯数字！"){
        $res = $this->checkMatch($str, "/^[0-9]+$/", $param, $errmessage);
        return $res;
    }

    /**
     * 验证数据是否是字母
     * @param string $str 需要验证的数据
     */
    public function isLetter($str, $preg, $param = array(), $errmessage = "请输入纯字母！"){
        $res = $this->checkMatch($str, "/^[a-zA-Z_]+$/", $param, $errmessage);
        return $res;
    }

    /**
     * 验证数据是否是YYYY-MM-DD格式的日期
     * @param string $str 需要验证的数据
     */
    public function isDate($str, $preg, $param = array(), $errmessage = "输入的日期格式错误！"){
        $res = $this->checkMatch($str, "/^[1-2][0-9]{3}-(0|1)[0-9]-[0-3][0-9]$/", $param, $errmessage);
        return $res;
    }

    /**
     * 验证数据是否是手机号
     * @param string @str 需要验证的号码
     */
    public function isPhone($str, $preg, $param = array(), $errmessage = "输入的手机号格式错误！"){
        $res = $this->checkMatch($str, "/^1[3|4|5|8][0-9]{9}$/", $param, $errmessage);
        return $res;
    }

    /**
     * 验证数据是否是电话号码
     * @param string @str 需要验证的号码
     */
    public function isTel($str, $preg, $param = array(), $errmessage = "输入的电话号码格式错误！"){
        $res = $this->checkMatch($str, "/^(0[\d]{2,3}-)?[\d]{7,8}(-[\d]{3,5})?$/", $param, $errmessage);
        return $res;
    }

    public function lenBetween($str, $preg = "", $param = array(), $errmessage = ""){
        $leng = strlen( $str );
        if ( $leng < $param['min'] || $leng > $param['max']){
            return $errmessage == "" ? "输入的长度应在{$param['min']}-{$param['max']}之间！" : $errmessage;
        }else{
            return "";
        }
    }

    public function checkMatch($str, $preg, $param = array(), $errmessage = "输入错误！"){
        if (preg_match($preg, $str)){
            return "";
        }else{
            return $errmessage;
        }
    }
}