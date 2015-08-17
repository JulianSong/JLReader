<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller{

    public function __construct(){
        parent::__construct();
    }
    /**
     * 用户中心页面
     */
    public function index(){
        //exit;
        /**
         * 未登录则直接跳转到登录页面
         */
        $uid=$this->session->userdata('uid');
        if(!$uid){
            $this->login();
        }else{
            /**
             * 加载模型类库
             * ConcernedModel 用户关注车系模型
             * favorite 收藏模型
             * history 历史记录模型
             */
            $this->load->model("user/ConcernedModel",'concerned');
            $this->load->model("user/NoteModel",'note');
            $this->load->model("user/favoritemodel",'favorite');
            $this->load->model("user/historymodel",'history');
            /**
             * 设置数据
             * @var $data['userConcerned'] 用户关注的车系
             * @var $data['base_url'] 站点地址
             * @var $data['favorite'] 用户收藏的文章
             * @var $data['favorite_count'] 用户收藏文章的数量
             * @var $data['favorite_page_num'] 设置用户收藏文章的每页数量
             * @var $data['history'] 用户当天浏览过的文章
             * @var $data['history_count'] 用户当天浏览过的文章的数量
             * @var $data['history_page_num'] 设置用户当天浏览过的文章的每页数量
             */
            $data=array();
            $data['userConcerned']=$this->concerned->getUserConcerned($uid);
            $data['userNotes']=$this->note->getNotes(array('uid'=>$uid));
            $data['title']="用户中心—".$this->session->userdata('mobile_phone');
            $data['base_url']=$this->config->item("base_url");
            $data['mobile_phone']=$this->session->userdata('mobile_phone');
            
            $favorite_limit = '';//获取收藏的数量，空表示全部获取
            $data['favorite']=$this->favorite->getAllFavorite($uid,$favorite_limit,"DESC");
            $data['favorite_count']=$this->favorite->getNumFavorite($uid);
            $data['favorite_page_num']=5;//设置用户收藏文章的每页数量
            $data['favorite_point']=
            "温馨提示：这是一个收藏夹，你只要在浏览文章的时候点击收藏，就可以把喜欢的文章收藏在这里，方便你随时查阅，最多可以收藏50篇文章，点击删除小图标就可以移除收藏。";
            
            $history_limit = '';//获取历史记录的数量，空表示全部获取
            $data['history']=$this->history->getHistories($uid,$history_limit);
            $data['history_count']=$this->history->getNumHistory($uid);
            $data['history_page_num']=5;//设置用户当天浏览过的文章的每页数量
            $data['history_point']="温馨提示：这是一个浏览记录，自动记录你当天的浏览历史，方便你随时回顾看过的文章。";
            /**
	         * 加载页面
	         */
            $this->load->view('user/user.html',$data);
        }
    }
    /**
     * 注册页面
     * @author yeduo 2011.10.10
     */
    function sign()
    {
        /**
         * 加载验证码辅助类
         * 参考http://codeigniter.org.cn/user_guide/helpers/captcha_helper.html
         */
    	$this->load->helper('captcha');
        $vals = array(
			'word' => '',//验证码字符串，空表示随机产生
			'word_length' => '5',//设置验证码的长度
			'img_path' => 'images/captcha/',//验证码的图片目录，参数是必须的
			'img_url' => $this->config->item("base_url").'images/captcha/',//验证码的图片目录，参数是必须的
			'img_width' => '150',//验证码图片显示的宽度
			'img_height' => 40,//验证码图片显示的高度
			'expiration' => 7200//(秒) 指定了验证码图片的超时删除时间. 默认是2小时
        );
        /**
         * 设置数据
         * @var $data['cap'] 验证码的数据，包括image、word、time
         * @var $data['base_url'] 站点地址
         */
        $data=array();
        $data['cap']= create_captcha($vals);
        $data['base_url']=$this->config->item('base_url');
        /**
         * 把验证码记录到session中，用于比对
         */
        $cap_word = $data['cap']['word'];
        $cap_word = strtolower($cap_word);
        $this->session->set_userdata('cap_word',$cap_word);
        /**
         * 加载页面
         */
        $this->load->view('user/sign.html',$data);
    }
    /**
     * 登录页面
     * @author yeduo 2011.10.11
     */
    function login()
    {
        if($this->input->is_ajax_request()){
            $this->load->view('user/lib/login_box.html');
        }else{
        	/**
             * 设置数据
             * @var $data['base_url'] 站点地址
             */
            $data=array();
            $data['base_url']=$this->config->item('base_url');
            /**
	         * 加载页面
	         */
            $this->load->view('user/login.html',$data);
        }
    }
    
	/**
     * 弹出层登录
     */
    public function showLoginBox(){
        /**
         * 安全性检测，数据转换,校验
         */
        if(!$this->input->is_ajax_request()){
            show_404();
        }
        $data=array();
        $data['base_url']=$this->config->item('base_url');
        $this->load->view('user/login_box.html',$data);
    }
    
    /**
     * 个人资料页面
     * @author yeduo 2011.10.13
     */
    function userinfo()
    {
        /**
         * 获取session中的手机号码，判断是否登录
         */
    	$mobile_phone = $this->session->userdata('mobile_phone');
        if($mobile_phone!=""){
        	/**
        	 * 加载用户模型
        	 */
            $this->load->model("user/usermodel",'user');
            /**
             * 设置数据
             * @var $data['user_info']用户资料
             * @var $data['base_url'] 站点地址
             */
            $data['title']="个人资料—".$this->session->userdata('mobile_phone');
        	$data['mobile_phone']=$this->session->userdata('mobile_phone');
            $data['user_info'] = $this->user->getUserInfoByMobile($mobile_phone);
            $data['base_url']=$this->config->item('base_url');
            /**
	         * 加载页面
	         */
            $this->load->view('user/userinfo.html',$data);
        }else{
            $this->login();
        }
    }
    /**
     * 密码页面
     * @author yeduo 2011.10.18
     */
    function password()
    {
        $uid = $this->session->userdata('uid');
        if($uid!=''){
        	$data['title']="修改密码—".$this->session->userdata('mobile_phone');
        	$data['mobile_phone']=$this->session->userdata('mobile_phone');
        	$data['base_url']=$this->config->item('base_url');
            $this->load->view('user/edit_password.html',$data);
        }else{
            $this->login();
        }
    }
    
    /**
     * 注册邮件找回密码页面
     * @author yeduo 2011.10.20
     */
    function getpassword()
    {
    	$data['title']='找回密码';
    	$data['base_url']=$this->config->item('base_url');
    	$this->load->view('user/get_password.html',$data);
    }
    
    /**
     * 重设密码页面
     * @author yeduo 2011.10.20
     */
    function setpassword($uid='',$key='')
    {
    	$this->load->model("user/usermodel",'user');
    	$result = $this->user->checkSetPassword($uid,$key);
    	if($result==1){
    		echo '该重置密码的链接无效，请重新申请找回密码';
    		$this->getpassword();
    	}else if($result==2){
    		echo '该重置密码的链接已过期，请重新申请找回密码';
    		$this->getpassword();
    	}else if($result==0){
    		$userinfo = $this->user->getUserInfoByUid($uid);
    		$data=array(
    			'uid'	=> $userinfo['uid'],
    			'key'	=> $key
    		);
    		$data['title']='重设密码';
    		$data['base_url']=$this->config->item('base_url');
    		$this->load->view('user/set_password.html',$data);
    	}
    }

    /**
     * Ajax请求，检测用户是否存在
     * @author yeduo 2011.10.10
     */
    function checkuserinfo()
    {
        if(!$this->input->is_ajax_request()){
            show_404();
        }else{
            $params = array('use' =>16);
            $this->load->library('Services_JSON',$params);
            $this->load->model("user/usermodel",'user');
            $result = array();
            if($this->user->checkUserInfo($this->input->post('phone'))){
                $result['error']=1;
                $result['msg']='手机号可用';
            }else{
                $result['error']=2;
                $result['msg']='手机号已被注册';
            }
            echo $this->services_json->encode($result);
        }
    }

    /**
     * Ajax请求，注册新用户
     * @author yeduo 2011.10.10
     */
    function adduser()
    {
        if(!$this->input->is_ajax_request()){
            show_404();
        }else{
            $mobile_phone = $this->input->post('mobile_phone');
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $input_captcha = $this->input->post('input_captcha');
            $get_captcha = $this->session->userdata('cap_word');
            $this->load->model("user/usermodel",'user');
            $this->load->helper('email');
            if(!$this->user->isMobilePhone($mobile_phone))
            {
                die("1");//请正确填写手机号
            }
            if(!valid_email($email))
            {
                die("2");//请正确填写邮箱
            }
            if(!$this->user->checkPassword($password))
            {
                die("3");//密码格式不正确
            }
            if($get_captcha==""){
                die("4");//验证码过期，请刷新页面后再注册
            }
            if($input_captcha!=$get_captcha){
                die("5");//验证码错误，请重新注册
            }
            $md5_password = md5($mobile_phone.$password);
            $sign_time = $_SERVER['REQUEST_TIME'];
            $sign_ip = $_SERVER['REMOTE_ADDR'];

            $this->user->addUser($mobile_phone,$email,$md5_password,$sign_time,$sign_ip);
            die("0");//注册成功
        }
    }

    /**
     * Ajax请求，用户登录
     * @author yeduo 2011.10.11
     */
    function userlogin()
    {
        if(!$this->input->is_ajax_request()){
            show_404();
        }
        /**
         * 加载json类和用户模版
         */
        $this->load->library('Services_JSON', array('use' =>16));
        $this->load->model("user/usermodel",'user');
        
        /**
         * 获取Ajax传来的参数
         * Enter description here ...
         * @var unknown_type
         */
        $mobile_phone = $this->input->post('mobile_phone');
        $password = $this->input->post('password');
        
        /**
         * 校验登录信息
         * @var unknown_type
         */
        $result=array();
        if(!$this->user->isMobilePhone($mobile_phone))
        {  $result['error']='1';
        die($this->services_json->encode($result));//请正确填写手机号
        }
        if(!$this->user->checkPassword($password))
        {
            $result['error']='2';
            die($this->services_json->encode($result));//密码格式不正确
        }
        $md5_password = md5($mobile_phone.$password);
        $sav_password = $this->user->getPasswordByMobile($mobile_phone);
        if($sav_password==""){
            $result['error']='3';
            die($this->services_json->encode($result));//手机号不存在，请重新登录
        }else if($md5_password!=$sav_password){
            $result['error']='4';
            die($this->services_json->encode($result));//密码不正确，请重新登录！
        }else{
        	/**
        	 * 登录成功，记录登录操作
        	 * @param unknown_type $mobile_phone 手机号码
			 * @param unknown_type $action_type 行为类型，行为类型（1登录,2修改信息，3，找回密码）
			 * @param unknown_type $comments 备注
        	 */
            $this->user->setUserAction($mobile_phone,1,'登录');
            /**
             * 设置用户session
             */
            $this->user->setUserSession($mobile_phone);
            $result['mobile_phone']=$mobile_phone;
            $result['error']='0';
            die($this->services_json->encode($result));//登录成功
        }
    }


    /**
     * 用户登出
     * @author yeduo 2011.10.11
     */
    function userlogout()
    {
        $this->session->sess_destroy();
        $this->load->helper('url');
        redirect('index');
    }

    /**
     * Ajax请求，刷新验证码
     * @author yeduo 2011.10.13
     */
    function updatecaptcha()
    {
        if(!$this->input->is_ajax_request()){
            show_404();
        }else{
        	/**
        	 * 加载json类和验证码类
        	 */
            $this->load->library('Services_JSON',array('use' =>16));
            $this->load->helper('captcha');
            /**
	         * 设置数据
	         */
            $vals = array(
				'word' => '',
				'word_length' => '5',
				'img_path' => 'images/captcha/',
				'img_url' => $this->config->item("base_url").'images/captcha/',
				'img_width' => '150',
				'img_height' => 40,
				'expiration' => 7200
            );
            $cap = create_captcha($vals);
            $cap_word = $cap['word'];
            $cap_word = strtolower($cap_word);
            $this->session->set_userdata('cap_word',$cap_word);
            $result=array(
            	'image'=>$cap['image']
            );
            echo $this->services_json->encode($result);
        }
    }

    /**
     *  Ajax请求，更新个人信息
     * @author yeduo 2011.10.13
     */
    function updateuserinfo()
    {
        if(!$this->input->is_ajax_request()){
            show_404();
        }else{
        	/**
        	 * 加载json类和用户模型类
        	 */
            $params = array('use' =>16);
            $this->load->library('Services_JSON',$params);
            $this->load->model("user/usermodel",'user');
            $this->load->helper('email');
            /**
             * 获取页面请求参数
             */
            $mobile_phone = $this->input->post('mobile_phone');
            $email = $this->input->post('email');
            /**
             * 设置数据
             */
            $result=array();
            
            if(valid_email($email))
            {
                $userinfo = array();
                $userinfo['email']=$email;
                $this->user->updateUserInfo($mobile_phone,$userinfo);
                
                /**
	        	 * 修改个人资料成功，记录修改信息
	        	 * @param unknown_type $mobile_phone 手机号码
				 * @param unknown_type $action_type 行为类型，行为类型（1登录,2修改信息，3，找回密码）
				 * @param unknown_type $comments 备注
	        	 */
	            $this->user->setUserAction($mobile_phone,2,'修改个人资料');
            
                $result['error']=1;
            }else{
                $result['error']=2;
            }
            echo $this->services_json->encode($result);
        }
    }

    /**
     *  Ajax请求，修改密码
     * @author yeduo 2011.10.18
     */
    function editpassword()
    {
        if(!$this->input->is_ajax_request()){
            show_404();
        }else{
        	/**
        	 * 加载json类和用户模型类
        	 */
            $params = array('use' =>16);
            $this->load->library('Services_JSON',$params);
            $this->load->model("user/usermodel",'user');
            /**
             * 获取页面请求参数
             */
            $old_psw = $this->input->post('old_psw');
            $new_psw = $this->input->post('new_psw');
            /**
             * 设置数据
             */
            $result=array();
            if(!$this->user->checkPassword($old_psw))
            {
                $result['result']='1';
                die($this->services_json->encode($result));//旧密码格式不正确
            }
            if(!$this->user->checkPassword($new_psw))
            {
                $result['result']='2';
                die($this->services_json->encode($result));//新密码格式不正确
            }
            $num=$this->user->updatePassword($old_psw,$new_psw);
            if($num==1){
                $result['result']='3';
                die($this->services_json->encode($result));//页面过期，需重新登录
            }
            if($num==2){
                $result['result']='4';
                die($this->services_json->encode($result));//原密码不正确
            }
            if($num==0){
                
                /**
	        	 * 修改密码成功，记录修改操作
	        	 * @param unknown_type $mobile_phone 手机号码
				 * @param unknown_type $action_type 行为类型，行为类型（1登录,2修改信息，3，找回密码）
				 * @param unknown_type $comments 备注
	        	 */
            	$mobile_phone = $this->session->userdata('mobile_phone');
	            $this->user->setUserAction($mobile_phone,2,'修改密码');
	            
	            $result['result']='0';
                die($this->services_json->encode($result));//修改成功
            }
            echo $this->services_json->encode($result);
        }
    }
    
    /**
     *  Ajax请求，注册邮件找回密码
     * @author yeduo 2011.10.20
     */
	function getpasswordbyemail(){
		if(!$this->input->is_ajax_request()){
            show_404();
        }else{
        	/**
        	 * 加载json类和用户模型类
        	 */
        	$params = array('use' =>16);
            $this->load->library('Services_JSON',$params);
            $this->load->model("user/usermodel",'user');
            $this->load->helper('email');
            /**
             * 获取页面请求参数
             */
            $mobile_phone = $this->input->post('mobile_phone');
            $email = $this->input->post('email');
            /**
             * 设置数据
             */
            $result=array();
			if(!$this->user->isMobilePhone($mobile_phone))
			{
				$result['result'] = 1;//请正确填写手机号
				die($this->services_json->encode($result));
			}
			if(!valid_email($email))
			{
				$result['result'] = 2;//请正确填写邮箱
				die($this->services_json->encode($result));
			}
			$act = $this->user->isUser($mobile_phone,$email);
			if($act==1){//发送重置密码邮件
				$state = $this->user->sendGetPasswordEmail($mobile_phone,$email);
				if($state==1){
					$result['result'] = 0;
					/**
		        	 * 登录成功，记录登录操作
		        	 * @param unknown_type $mobile_phone 手机号码
					 * @param unknown_type $action_type 行为类型，行为类型（1登录,2修改信息，3，找回密码）
					 * @param unknown_type $comments 备注
		        	 */
		            $this->user->setUserAction($mobile_phone,3,'申请找回密码');
				}else{
					$result['result'] = 4;//已经申请过，不能重复申请
				}
				die($this->services_json->encode($result));
			}else{
				$result['result'] = 3;//手机号和电子邮件地址不匹配，请重新输入！
				die($this->services_json->encode($result));
			}
            echo $this->services_json->encode($result);
        }
	}
	
	/**
     *  Ajax请求，重置密码
     * @author yeduo 2011.10.20
     */
	function resetpassword(){
		if(!$this->input->is_ajax_request()){
            show_404();
        }else{
        	/**
        	 * 加载json类和用户模型类
        	 */
        	$params = array('use' =>16);
            $this->load->library('Services_JSON',$params);
            $this->load->model("user/usermodel",'user');
            /**
             * 获取页面请求参数
             */
            $uid = $this->input->post('uid');
            $key = $this->input->post('key');
            $new_psw = $this->input->post('new_psw');
            /**
             * 设置数据
             */
            $result=array();
            if(empty($uid)||empty($key)||empty($new_psw)){
            	$result['result']=1;//无效
            	die($this->services_json->encode($result));
            }
        	if(!$this->user->checkPassword($new_psw))
	        {
	            $result['result']='2';
	            die($this->services_json->encode($result));//新密码格式不正确
	        }
	        $act = $this->user->resetPassword($uid,$key,$new_psw);
	        if($act==1){
	        	$result['result']=1;//无效
            	die($this->services_json->encode($result));
	        }
        	if($act==2){
	        	$result['result']=3;//该链接过期，请重新发送找回密码申请
            	die($this->services_json->encode($result));
	        }
        	if($act==0){
	        	$result['result']=0;//重置密码成功
            	die($this->services_json->encode($result));
	        }
	        
            echo $this->services_json->encode($result);
        }
	}
	
	function check_email(){
		$email = $this->input->post('email');
		$this->load->helper('email');
		if(valid_email($email)){
			die("1");//邮件格式正确
		}else{
			die("0");//邮件格式不正确
		}
	}
}