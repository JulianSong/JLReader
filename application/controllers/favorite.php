<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Favorite extends CI_Controller{

    public function __construct(){
        parent::__construct();
    }
    /**
     * 收藏测试页面
     */
    public function index(){
		$this->load->view("admin/upload_form.html");
//		$this->load->view("user/test_favorite.html");
    }
	
    /**
     * Ajax请求，添加收藏
     * @author yeduo 2011.10.18
     */
    public function addfavorite(){
    	if(!$this->input->is_ajax_request()){
            show_404();
        }else{
        	$params = array('use' =>16);
	        $this->load->library('Services_JSON',$params);
	        $this->load->model("user/favoritemodel",'favorite');
        	$uid = $this->session->userdata('uid');
        	$ArticleID = $this->input->post('ArticleID');
        	if($uid!=''){
        		//已经登录，写数据库
	            $result = array();
	            $act=$this->favorite->addFavorite($uid,$ArticleID);
	            $result['result']=$act;
        	}else{
        		//未登录，写cookie
        		$act=$this->favorite->addFavoriteCookie($ArticleID);
        		$result['result']=$act;//未登录 3超过10个收藏，4添加Cookie成功
        	}
        	echo $this->services_json->encode($result);
        }
    }
	
    /**
     * Ajax请求，个人中心中移除收藏
     * @author yeduo 2011.10.18
     */
    public function delfavorite(){
    	if(!$this->input->is_ajax_request()){
            show_404();
        }else{
            $params = array('use' =>16);
            $this->load->library('Services_JSON',$params);
            $uid = $this->session->userdata('uid');
        	if($uid!=''){
	            $this->load->model("user/favoritemodel",'favorite');

	            $ArticleID = $this->input->post('ArticleID');
	            $num = $this->favorite->delFavorite($uid,$ArticleID);
	            $favorite_limit = '';//获取收藏的数量，空表示全部获取
	            $data['favorite']=$this->favorite->getAllFavorite($uid,$favorite_limit,"DESC");
	            $data['favorite_count']=$this->favorite->getNumFavorite($uid);
	            $data['favorite_page_num']=5;//设置用户收藏文章的每页数量
	            $data['favorite_point']=
	            "温馨提示：这是一个收藏夹，你只要在浏览文章的时候点击收藏，就可以把喜欢的文章收藏在这里，方便你随时查阅，最多可以收藏50篇文章，点击删除小图标就可以移除收藏。";
	            $data['base_url']=$this->config->item("base_url");
	            $this->load->view('user/favorite_result.html',$data);
        	}else{
        		$result=0;
        		echo $result;
        	}
        }
    }
	
    /**
     * Ajax请求，文章列表中移除收藏
     * @author yeduo 2011.10.18
     */
    public function delfavoritefromarticle(){
    	if(!$this->input->is_ajax_request()){
            show_404();
        }else{
            $params = array('use' =>16);
            $this->load->library('Services_JSON',$params);
            $this->load->model("user/favoritemodel",'favorite');
            $uid = $uid = $this->session->userdata('uid');
            $ArticleID = $this->input->post('ArticleID');
        	if($uid!=''){
	            $num = $this->favorite->delFavorite($uid,$ArticleID);
	            if($num==1){
	            	echo $num;//移除成功
	            }else if($num==0){
	            	echo $num;//移除失败
	            }
        	}else{
        		$result=2;//未登录
        		$this->favorite->delFavoriteCookie($ArticleID);
        		echo $num=1;//移除成功
        	}
        }
    }
    
    /**
     * Ajax请求，目录页获取收藏列表
     * @author yeduo 2011.10.18
     */
    public function getallfavorite(){
    	if(!$this->input->is_ajax_request()){
            show_404();
        }
        $params = array('use' =>16);
        $this->load->library('Services_JSON',$params);
    	$uid = $this->session->userdata('uid');
        if(!$uid){
           $result['result']=0;//没有登录
           $this->load->model("user/favoritemodel",'favorite');
           $data=$this->favorite->getAllFavoriteCookie();
        	if(empty($data)){
            	$result['result']=1;//没有数据
            }else{
	            $result['result']=2;//有数据
	            $result['favorite_list']=$data;
	            $result['base_url']=$this->config->item("base_url");
            }
        }else{
            $this->load->model("user/favoritemodel",'favorite');
            $result = array();
            $favorite_limit = '';//获取收藏的数量，空表示全部获取
            $data=$this->favorite->getAllFavorite($uid,$favorite_limit,"ASC");
            if(empty($data)){
            	$result['result']=1;//没有数据
            }else{
	            $result['result']=2;//有数据
	            $result['favorite_list']=$data;
	            $result['base_url']=$this->config->item("base_url");
            }
        }
        $this->load->view('user/favorite_list.html',$result);
        //echo $this->services_json->encode($result);
    }
    /**
     * 获得用户关注的所有的文章id
     */
    public function getfavoriteaids(){
        if(!$this->input->is_ajax_request()){
            show_404();
        }
        $uid = $this->session->userdata('uid');
        $this->load->model("user/favoritemodel",'favorite');
        $data=$this->favorite->getAllFavoriteArticleID($uid);
        echo $data;
        exit;
    }
    
    /**
     * 检测Cookie中是否有收藏记录
     * Enter description here ...
     */
    public function checkfavoritecookie(){
    	if(!$this->input->is_ajax_request()){
            show_404();
        }
        $uid = $this->session->userdata('uid');
        if($uid==""){
        	echo("0");
        }
        $this->load->model("user/favoritemodel",'favorite');
        $num=$this->favorite->checkFavoriteCookie($uid);
		if($num==0){
			echo("0");//Cookie没有需要更新的数据
		}else{
			echo($num);//有需要更新的数据
    	}
    }
    
    /**
     * 合并Cookie中的收藏到我的收藏中
     * Enter description here ...
     */
    public function combinefavorite(){
    	if(!$this->input->is_ajax_request()){
            show_404();
        }
        $uid = $this->session->userdata('uid');
        if($uid==""){
        	echo("0");
        }
        $this->load->model("user/favoritemodel",'favorite');
        $OK_num=$this->favorite->combineFavoriteCookie($uid);
		echo($OK_num);
    }
}