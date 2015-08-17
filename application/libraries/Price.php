<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 价格格式化model
 * @author yeduo
 */
class Price{
	/**
	 * 把元转换为万元
	 * Enter description here ...
	 * @param unknown_type $money 单位：元
	 */
    public function doFormatMoney($money){
    	$money = trim($money);
    	if(!is_numeric($money)){
    		return false;
    	}
    	$arr = str_split($money);
    	$str="";
    	$num = count($arr);
    	if($num>4){
    		//万元以上
	    	foreach ($arr as $key => $value){
	    		$str.=$value;
	    		if($num-$key==5){
	    			$str.=".";
	    		}
	    	}
	    	$str = trim($str,"0");
	    	$str = trim($str,".");
	    	return $str;
    	}else{
    		$money = trim($money,"0");
    		//不够万元，前面补0
    		for($i=0;$i<4-$num;$i++){
    			$money = "0".$money;
    		}
    		return "0.".$money;
    	}
    }
}

