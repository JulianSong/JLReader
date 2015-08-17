<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class History extends CI_Controller{

    public function __construct(){
        parent::__construct();
    }
    /**
     * 历史记录测试页面
     */
    public function index(){
		$this->load->view("user/test_history.html");
    }
    
    /**
     * Ajax请求，打开文章内页时，记录打开时间，之前无记录的话就创建新记录，有记录的话只更新打开时间
     * @author yeduo 2011.10.19
     */
    public function setstart(){
    	if(!$this->input->is_ajax_request()){
            show_404();
        }else{
        	$params = array('use' =>16);
	        $this->load->library('Services_JSON',$params);
	        $result = array();
        	$uid = $this->session->userdata('uid');
        	if($uid!=''){
        		//用户已登录
	            $this->load->model("user/historymodel",'history');
	            $ArticleID = $this->input->post('ArticleID');
	            $duration=$this->history->setStartHistory($uid,$ArticleID);
	            $result['result']=1;
	            $result['duration']=$duration;
        	}else{
        		//用户没有登录
        		$result['result']=0;
        	}
        	echo $this->services_json->encode($result);
        }
    }
    
    /**
     * Ajax请求，关闭文章内页时，根据打开时间，计算持续时间，并记录
     * @author yeduo 2011.10.19
     */
    public function setend(){
    	if(!$this->input->is_ajax_request()){
            show_404();
        }else{
        	$uid = $uid = $this->session->userdata('uid');
        	if($uid!=''){
	            $params = array('use' =>16);
	            $this->load->library('Services_JSON',$params);
	            $this->load->model("user/historymodel",'history');
	            $result = array();
	            $ArticleID = $this->input->post('ArticleID');
	            $act=$this->history->setEndHistory($uid,$ArticleID);
	            $result['result']=$act;
	            echo $this->services_json->encode($result);
        	}
        }
    }
    
	/**
     * Ajax请求，获取当天的历史记录
     * @author yeduo 2011.10.19
     */
    public function gethistories(){
    if(!$this->input->is_ajax_request()){
            show_404();
        }else{
            $params = array('use' =>16);
            $this->load->library('Services_JSON',$params);
            $this->load->model("user/historymodel",'history');
            $result = array();
            $uid = $this->input->post('uid');//有漏洞注意修改！任何时候用户id都应该从session中取出
            $limit = $this->input->post('limit');
            $data=$this->history->getHistories($uid,$limit);
            if(empty($data)){
            	$result['result']=1;//没有数据
            }else{
            	$result['result']=0;//有数据
            	$result['data']=$data;
            }
            echo $this->services_json->encode($result);
        }
    }
    
    /**
     * 监听并处理阅读时间
     * Enter description here ...
     */
    public function actiontime(){
    	if(!$this->input->is_ajax_request()){
            show_404();
        }else{
        	$params = array('use' =>16);
            $this->load->library('Services_JSON',$params);
            $result = array();
        	$uid=$this->session->userdata('uid');
        	if($uid!=""){
        		$ArticleID = $this->input->post('ArticleID');
        		$count_time = $this->input->post('count_time');
        		$this->load->model("user/historymodel",'history');
        		$duration = $this->history->setDuration($uid,$ArticleID,$count_time);
        		$result['result'] = 1;
        		$result['duration'] = $duration;
        	}else{
        		$result['result'] = 0;//没登陆
        	}
        	echo $this->services_json->encode($result);
        }
    }
}