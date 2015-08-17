<?php
/**
 * 
 * 用户model
 * @author yeduo
 *
 */
class Usermodel extends CI_Model{
	/**
	 * 通过手机号检测用户是否存在，返回结果为true表示可以注册，返回结果为false表示用户已存在
	 * @author yeduo 2011.10.10
	 * 
	 * @param unknown_type $mobile_phone 手机号码
	 */
	public function checkUserInfo($mobile_phone){
		$sql = "SELECT mobile_phone FROM event_user WHERE mobile_phone=".$mobile_phone;
		$queryTemplates=$this->db->query($sql);
		$result = true;
		$templateArray = array();
		foreach ($queryTemplates->result_array() as $row) {
			array_push($templateArray, $row);
		}
		if(!empty($templateArray)){
			$result = false;
		}
		return $result;
	}
	
	/**
	 * 添加新用户
	 * @author yeduo 2011.10.10
	 * 
	 * @param unknown_type $mobile_phone
	 * @param unknown_type $email
	 * @param unknown_type $password
	 * @param unknown_type $sign_time
	 * @param unknown_type $sign_ip
	 */
	public function addUser($mobile_phone,$email,$password,$sign_time,$sign_ip){
		$data = array(
				'mobile_phone' 	=> $mobile_phone,
				'email'			=> $email,
				'password'		=> $password,
				'sign_time'		=> $sign_time,
				'sign_ip'		=> $sign_ip
		);
		$this->db->insert('event_user',$data);
	}
	
	/**
	 * 校验手机号
	 * @author yeduo 2011.10.10
	 */
	public function isMobilePhone($mobile_phone)
	{
		$pattern = '/^1[3|5|8|0][0-9]\d{8}$/';
		return preg_match($pattern, $mobile_phone);
	}
	
	/**
	 * 校验email
	 * @author yeduo 2011.10.10
	 */
	public function isEmail($email)
	{
		$chars = "/^([.a-zA-Z0-9_ -])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/";
	    if (strpos($email, '@') !== false && strpos($email, '.') !== false)
	    {
	        if (preg_match($chars, $email))
	        {
	            return true;
	        }
	        else
	        {
	            return false;
	        }
	    }
	    else
	    {
	        return false;
	    }
	}
	
	/**
	 * 校验密码
	 * @author yeduo 2011.10.10
	 */
	public function checkPassword($password)
	{
		$result = true;
		if(strlen($password)>5&&strlen($password)<16)
			$result = true;
		else $result = false;
		return $result;
	}
	
	/**
	 * 根据手机号码获取用户信息
	 * @author yeduo 2011.10.10
	 * @param unknown_type $mobile_phone
	 */
	public function getUserInfoByMobile($mobile_phone){
		$sql = "SELECT * FROM event_user WHERE mobile_phone=".$mobile_phone;
        $query=$this->db->query($sql);
        $result = $query->row_array();
        $result['sign_time'] = date('Y-m-d H:i:s',$result['sign_time']);
        return $result;
	}
	
	/**
	 * 根据uid获取用户信息
	 * @author yeduo 2011.10.20
	 * @param unknown_type $mobile_phone
	 */
	public function getUserInfoByUid($uid){
		$sql = "SELECT * FROM event_user WHERE uid=".$uid;
        $query=$this->db->query($sql);
        $result = $query->row_array();
        if(!empty($result)){
        	$result['sign_time'] = date('Y-m-d H:i:s',$result['sign_time']);
        }
        return $result;
	}
	
	/**
	 * 根据手机号码获取密码
	 * Enter description here ...
	 * @param unknown_type $mobile_phone
	 */
	public function getPasswordByMobile($mobile_phone){
		$sql = "SELECT password FROM event_user WHERE mobile_phone=".$mobile_phone;
		$queryPassword = $this->db->query($sql);
		$password = 0;
		foreach ($queryPassword->result_array() as $row) {
			$password = $row['password'];
        }
		return $password;
	}
	
	/**
	 * 根据uid获取密码
	 * Enter description here ...
	 * @param unknown_type $mobile_phone
	 */
	public function getPasswordByUid($uid){
		$sql = "SELECT password FROM event_user WHERE uid=".$uid;
		$queryPassword = $this->db->query($sql);
		$password = 0;
		foreach ($queryPassword->result_array() as $row) {
			$password = $row['password'];
        }
		return $password;
	}
	
	/**
	 * 密码匹配成功后，记录用户信息到session
	 * @author yeduo 2011.10.11
	 * @param unknown_type $mobile_phone
	 */
	public function setUserSession($mobile_phone){
		$this->db->select('uid, mobile_phone, email');
		$query=$this->db->get_where('event_user',array('mobile_phone'=>$mobile_phone));
		foreach ($query->result_array() as $row) {
			$uid = $row['uid'];
			$email = $row['email'];
        }
        $this->session->set_userdata('uid',$uid);
        $this->session->set_userdata('mobile_phone',$mobile_phone);
        $this->session->set_userdata('email',$email);
	}
	
	/**
	 * 记录用户的操作信息
	 * @author yeduo 2011.10.24
	 * @param unknown_type $mobile_phone 手机号码
	 * @param unknown_type $action_type 行为类型，行为类型（1登录,2修改信息，3，找回密码）
	 * @param unknown_type $comments 备注
	 */
	public function setUserAction($mobile_phone,$action_type,$comments){
		$this->db->select('uid');
		$query=$this->db->get_where('event_user',array('mobile_phone'=>$mobile_phone));
		foreach ($query->result_array() as $row) {
			$uid = $row['uid'];
        }
        $str = $_SERVER['HTTP_USER_AGENT'];//客户端类型 1pc机 2移动设备
        if(strstr($str, "Windows")){
        	$client_type = 1;//PC机
        }else{
        	$client_type = 2;//移动设备
        }
        $action_data = array(
            'time'			=>	$_SERVER['REQUEST_TIME'],//发生时间
        	'uid'			=>	$uid,
            'ip'			=>	$_SERVER['REMOTE_ADDR'],//用户ip地址
            'client_type'	=>	$client_type,
            'action_type'	=>	$action_type,
            'comments'		=>	$comments
            );
        $this->db->insert('event_user_action',$action_data);
	}
	
	/**
	 * 更新用户资料
	 * @author yeduo 2011.10.13
	 * @param unknown_type $mobile_phone
	 * @param unknown_type $userinfo
	 */
	public function updateUserInfo($mobile_phone,$userinfo){
		$this->db->where('mobile_phone',$mobile_phone);
		$this->db->update('event_user',$userinfo);
	}
	
	/**
	 * 修改密码
	 * @author yeduo 2011.10.19
	 * @param $old_psw
	 * @param $new_psw
	 */
	public function updatePassword($old_psw,$new_psw){
		$uid = $this->session->userdata('uid');
		$mobile_phone = $this->session->userdata('mobile_phone');
		if($uid==""||$mobile_phone==""){
			return $result=1;//登录过期，需重新登录
		}
		$md5_password = md5($mobile_phone.$old_psw);
        $sav_password = $this->getPasswordByUid($uid);
        
		if($md5_password==$sav_password){
			$md5_newpsw=md5($mobile_phone.$new_psw);
			$userinfo=array('password'=>$md5_newpsw);
			$this->db->where('uid',$uid);
			$this->db->update('event_user',$userinfo);
        	return $result=0;//修改成功
        }else{
        	return $result=2;//原密码不正确
        }

	}
	
	/**
	 * 根据手机号和邮件地址判断是不是存在此人
	 * @author yeduo 2011.10.20
	 * @param unknown_type $mobile_phone
	 * @param unknown_type $email
	 */
	public function isUser($mobile_phone,$email){
		$sql = "SELECT uid FROM event_user WHERE mobile_phone='".$mobile_phone."' AND email='".$email."';";
        $query=$this->db->query($sql);
		$num=$query->num_rows();
        if($num==1){
        	return $num;//有此人
        }else if($num==0){
			return $num;//手机号和电子邮件地址不匹配，请重新输入！
        }
	}
	
	/**
	 * 用户进行密码找回操作时，发送一封确认邮件
	 * @author yeduo 2011.10.20
	 * @param unknown_type $mobile_phone
	 * @param unknown_type $email
	 */
	public function sendGetPasswordEmail($mobile_phone,$email){
		/**
		 * 如果未发送过 app_get_psw_time==0
		 * ，或者过期 app_get_psw_time<strtotime("-1 week") 注：时间小于一周前的时间则表示过期
		 * ，则发送
		 */
		$userinfo = $this->getUserInfoByMobile($mobile_phone);
		if($userinfo['app_get_psw_time']==0||$userinfo['app_get_psw_time']<strtotime("-1 week")){
			$app_time = $_SERVER['REQUEST_TIME'];
			$uid = $userinfo['uid'];
	
			
			
			$key = md5($uid.$userinfo['mobile_phone'].$userinfo['sign_time'].$app_time);//密钥
			$url = $this->config->item('base_url')."user/setpassword/".$uid."/".$key;
			
			$this->load->library('email');
					
			$config['protocol'] = 'smtp';
			$config['smtp_host'] = 'ssl://smtp.gmail.com';
			$config['smtp_user'] = 'autoreader2011@gmail.com';
			$config['smtp_pass'] = 'Cybercare.cn';
			$config['smtp_port'] = '465';
			$config['smtp_timeout'] = '5';
			$config['newline'] = "\r\n";
			$config['crlf'] = "\r\n";
			$this->email->initialize($config);
			
	
			$this->email->from('autoreader2011@gmail.com', 'autoreader');
			$this->email->to($email); 
			
			$message=$mobile_phone."，您好！\r\n\r\n".
			"您在车行研究申请了重设密码，请点击以下链接重设密码:\r\n".$url."\r\n".
			"如果您没有提交过类似申请，请勿点击该链接 \r\n\r\n".
			"autoreader\r\n".date('Y-m-d');
	
			$this->email->subject('密码找回');
			$this->email->message($message); 
			$this->email->send();
			
			//更新申请密码重置时间
			$data=array('app_get_psw_time'=>$app_time);
			$this->db->where('uid',$uid);
			$this->db->update('event_user',$data);
			
			return 1;
		}else{
			return 2;
		}
	}
	
	/**
	 * 根据重置密码的链接判断有效性
	 * Enter description here ...
	 * @param $uid
	 * @param $key
	 */
	public function checkSetPassword($uid,$key){
		if(empty($uid) || empty($key)){
			return 1;//链接无效:参数不足
		}
		$userinfo = $this->getUserInfoByUid($uid);
		if(empty($userinfo)){
			return 1;//链接无效:用户名不存在
		}
		$new_key = md5($uid.$userinfo['mobile_phone'].$userinfo['sign_time'].$userinfo['app_get_psw_time']);//密钥
		if($key!=$new_key){
			return 1;//链接无效:密钥不对
		}
		if($userinfo['app_get_psw_time']>strtotime("-1 week")){
			return 0;//该链接有效
		}else{
			return 2;//该链接过期，请重新发送找回密码申请
		}
	}
	
	/**
	 * 重置密码
	 * Enter description here ...
	 * @param $uid
	 * @param $key
	 */
	public function resetPassword($uid,$key,$new_psw){
		if(empty($uid) || empty($key)|| empty($new_psw)){
			return 1;//链接无效:参数不足
		}
		$userinfo = $this->getUserInfoByUid($uid);
		if(empty($userinfo)){
			return 1;//链接无效:用户名不存在
		}
		$new_key = md5($uid.$userinfo['mobile_phone'].$userinfo['sign_time'].$userinfo['app_get_psw_time']);//密钥
		if($key!=$new_key){
			return 1;//链接无效:密钥不对
		}
		if($userinfo['app_get_psw_time']>strtotime("-1 week")){
			$md5_password = md5($userinfo['mobile_phone'].$new_psw);
			//更新密码和申请密码重置时间
			$data=array(
				'app_get_psw_time'	=>	0,
				'password'			=>	$md5_password
			);
			$this->db->where('uid',$uid);
			$this->db->update('event_user',$data);
			/**
        	 * 重置密码成功，记录用户操作
        	 * @param unknown_type $mobile_phone 手机号码
			 * @param unknown_type $action_type 行为类型，行为类型（1登录,2修改信息，3，找回密码）
			 * @param unknown_type $comments 备注
        	 */
            $this->setUserAction($userinfo['mobile_phone'],3,'重置密码');
			return 0;//该链接有效,更新成功
		}else{
			return 2;//该链接过期，请重新发送找回密码申请
		}
	}
	
}