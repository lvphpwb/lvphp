<?php
class frm_Validation extends frm_Baseobject{
	var $error = null;
	var $iserror = false;
	public function isEmpty($str, $preg = "", $param = array(), $errmessage = "内容不能为空！"){
		if($str == null || strlen($str) == 0 || (is_array($str) && count($str) == 1 && empty($str[0]))){
			$this->error = $errmessage;
			$this->iserror = true;
		}else{
			$this->iserror = false;
		}
	}

	public function isEqual($str, $preg, $param = array(), $errmessage = "两次输入的内容不相同！"){
		if($str != $param[0]){
			$this->error = $errmessage;
			$this->iserror = true;
		}else{
			$this->iserror = false;
		}
	}
	/**
	 * 验证数据是否是一个电子邮件
	 * @param string $email 需要验证的email地址
	 * @return bool;
	 */
	public function isEmail($str, $preg, $param = array(), $errmessage = "输入的邮箱格式错误！")
	{
		$this->checkEreg($str, "^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}$", $param, $errmessage);
	}

	/**
	 * 验证数据是否全是数字+字母
	 * @param string $str 需要验证的数据;
	 * @return bool
	 */
	public function isAlnum($str, $preg, $param = array(), $errmessage = "请输入字母或数字！")
	{
		$this->checkEreg($str, "^[a-zA-Z0-9_]+$", $param, $errmessage);
	}

	/**
	 * 验证数据是否是qq号
	 * @param $str
	 */
	public function isQq($str, $preg, $param = array(), $errmessage = "输入的QQ号格式错误！")
	{
		$this->checkEreg($str, "^[1-9]{1}[0-9]{3,9}$", $param, $errmessage);
	}

	/**
	 * 验证数据是否是数字
	 * @param string $str 需要验证的数据
	 */
	public function isInternet($str, $preg, $param = array(), $errmessage = "请输入纯数字！")
	{
		$this->checkEreg($str, "^[0-9]+$", $param, $errmessage);
	}

	/**
	 * 验证数据是否是字母
	 * @param string $str 需要验证的数据
	 */
	public function isAlpha($str, $preg, $param = array(), $errmessage = "请输入纯字母！")
	{
		$this->checkEreg($str, "^[a-zA-Z_]+$", $param, $errmessage);
	}

	/**
	 * 验证数据是否是YYYY-MM-DD格式的日期
	 * @param string $str 需要验证的数据
	 */
	public function isDate($str, $preg, $param = array(), $errmessage = "输入的日期格式错误！")
	{
		$this->checkEreg($str, "^[1-2][0-9]{3}-(0|1)[0-9]-[0-3][0-9]$", $param, $errmessage);
	}

	/**
	 * 验证数据是否是手机号
	 * @param string @str 需要验证的号码
	 */
	public function isPhoneNum($str, $preg, $param = array(), $errmessage = "输入的手机号格式错误！")
	{
		$this->checkEreg($str, "^1[3|5|8][0-9]{9}$", $param, $errmessage);
	}

	/**
	 * 验证数据是否是电话号码
	 * @param string @str 需要验证的号码
	 */
	public function isTelNum($str, $preg, $param = array(), $errmessage = "输入的电话号码格式错误！"){
		$this->checkEreg($str, "^(0[\d]{2,3}-)?[\d]{7,8}(-[\d]{3,5})?$", $param, $errmessage);
	}

	/**
	 * 验证数据长度
	 * @param string $str 所要验证的数据
	 * @param int $min 数据最小长度
	 * @param int $max 数据最大长度
	 */
	public function between($str, $preg = "", $param = array(), $errmessage = "")
	{
		$leng = strlen( $str );
		if ( $leng < $param['min'] || $leng > $param['max']){
			$this->error = $errmessage == "" ? "输入的长度应在{$param['min']}-{$param['max']}之间！" : $errmessage;
			$this->iserror = true;
		}else{
			$this->iserror = false;
		}
	}

	public function checkEreg($str, $preg, $param = array(), $errmessage = "输入错误！"){
		if (ereg($preg,$str)){
			$this->iserror = false;
		}else{
			$this->error = $errmessage;
			$this->iserror = true;
		}
	}

    public function upload($str, $preg, $param = array(), $errmessage = "上传错误！"){
        if(empty($str)){
            $this->error = "请选择上传文件！";
			$this->iserror = true;
        }else{
            //note 暂时不做过滤
            $this->iserror = false;
        }
    }
}