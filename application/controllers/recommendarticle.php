<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Recommendarticle extends CI_Controller{

    public function __construct(){
        parent::__construct();
    }
    /**
     * 推荐阅读文章页面
     */
    public function index(){
    	$this->load->model("content/recommendarticlesmodel",'recommend');
    	$ArticleID = 633;
    	$data['content'] = $this->recommend->getCommendArticleFromHistory($ArticleID,10);
    	$data['base_url']=$this->config->item('base_url');
		$this->load->view("user/test_recommend_article.html",$data);
    }
    
    /**
     * 根据文章ID查询记录
     * Enter description here ...
     */
    public function getarticlelog(){
    	if(!$this->input->is_ajax_request()){
            show_404();
        }
        /**
         * 加载json类
         */
        $this->load->library('Services_JSON',array('use' =>16));
        $this->load->model("content/recommendarticlesmodel",'recommend');
        
        $ArticleID = $this->input->post('ArticleID');
        
        $data = $this->recommend->getAriticleInfo($ArticleID);
        echo $this->services_json->encode($data);
    }
    
    /**
     * 根据阅读的文章ID推荐其他用户读过的文章
     * Enter description here ...
     */
    public function getcommendarticle(){
    	if(!$this->input->is_ajax_request()){
            show_404();
        }
        /**
         * 加载json类
         */
        $this->load->library('Services_JSON',array('use' =>16));
        $this->load->model("content/recommendarticlesmodel",'recommend');
        
        $ArticleID = $this->input->post('ArticleID');
        
        $data = $this->recommend->getCommendArticleFromHistory($ArticleID,10);
        echo $this->services_json->encode($data);
    }
    
    /**
     * 获取当天的历史记录
     * Enter description here ...
     */
    public function gethistory(){
    	if(!$this->input->is_ajax_request()){
            show_404();
        }
        /**
         * 加载json类
         */
        $this->load->library('Services_JSON',array('use' =>16));
        $this->load->model("content/recommendarticlesmodel",'recommend');
        
        $data = $this->recommend->getHistories();
        echo $this->services_json->encode($data);
    }
    
    /**
     * 获取收藏记录
     * Enter description here ...
     */
    public function getfavorite(){
    	if(!$this->input->is_ajax_request()){
            show_404();
        }
        /**
         * 加载json类
         */
        $this->load->library('Services_JSON',array('use' =>16));
        $this->load->model("content/recommendarticlesmodel",'recommend');
        
        $data = $this->recommend->getFavorite();
        echo $this->services_json->encode($data);
    }
    
}