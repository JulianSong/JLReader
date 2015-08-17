<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reviewarticle extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->helper(array('form', 'url'));
    }
    /**
     * review文章页面
     */
    public function index(){
    	$data['title']="review文章";
    	$data['base_url']=$this->config->item("base_url");
		$this->load->view("admin/review_article.html",$data);
    }
    
    /**
     * 左边管理页面
     * Enter description here ...
     */
    public function reviewarticlelist(){
    	$data['base_url']=$this->config->item("base_url");
		$this->load->view("admin/review_article_list.html",$data);
    }
    
    /**
     * 右上方编辑页面
     * Enter description here ...
     */
    public function editarticlelist($ArticleID=0){
    	$this->load->model('content/articlemodel','article');
    	$data=$this->article->getArticleByAid($ArticleID);
    	$data['base_url']=$this->config->item("base_url");
    	$sql = "SELECT type FROM event_article_review WHERE ArticleID = ".$ArticleID;
    	$query=$this->db->query($sql);
    	$result=array();
        foreach ($query->result_array() as $row)
		{
		   array_push($result, $row['type']);
		}
		$data['del']=false;
		$data['picture']=false;
		$data['recommend']=false;
		$data['edit']=false;
		if(is_numeric(array_search("1",$result))){
			$data['del']=true;
		}
		if(is_numeric(array_search("2",$result))){
			$data['picture']=true;
		}
		if(is_numeric(array_search("3",$result))){
			$data['recommend']=true;
		}
		if(is_numeric(array_search("4",$result))){
			$data['edit']=true;
		}
		
		$this->load->view("admin/review_article_edit.html",$data);
    }
    
	/**
     * 搜索车系
     */
    public function search(){
        /**
         * 安全性检测，数据转换,校验
         */
        if(!$this->input->is_ajax_request()){
            show_404();
        }
        $key=htmlspecialchars(preg_replace("/\s/","",$this->input->post("key")));
        if(empty($key)){
            die("请输入搜索条件");
        }
        /**
         * 加载model，类库
         */
        $this->load->model('common/SeriesModel','series');
        $this->load->library('Pinyin');
        /**
         * 设置数据
         * @var $data
         * @var $data['serachResult']  搜索结果
         * @var $data['base_url']      站点地址
         */
        $data=array();
        $serachResult=$this->series->advanceSerach($key);
        $data['serachResult']=$serachResult;
        $data['numb']=count($data['serachResult']);
        $data['base_url']=$this->config->item('base_url');
        /**
         * 加载页面
         * series_contents.html 车系内容页面
         */
        $this->load->view('admin/review_series_serach_result.html',$data);
    }
    
	/**
     *
     * @param  $seriesID 车系id
     * @param  $size 每次获得的数据数量
     */
    public function getArticles() {
    	$seriesID = $this->input->post('cat_id');
    	
        $sql = "SELECT esa.ArticleID,esa.Title
                FROM event_spider_articles AS esa
                JOIN event_article_series AS eas
                ON esa.ArticleID = eas.ArticleID";
        $where=" ";
        if($seriesID!=0){
            $where.=" WHERE  eas.SeriesID=".$seriesID." AND esa.contentType=1";
        }
        $order=" ORDER BY esa.ArticleDate DESC";
        $sql.=$where.$order;
        $query=$this->db->query($sql);
		$result=$query->result_array();
        $data['articles']=$result;
        $data['numb']=count($data['articles']);
    	
        $sql = "SELECT esa.ArticleID,esa.Title
                FROM event_spider_articles AS esa
                JOIN event_article_series AS eas
                ON esa.ArticleID = eas.ArticleID";
        $where=" ";
        if($seriesID!=0){
            $where.=" WHERE  eas.SeriesID=".$seriesID." AND esa.contentType=2";
        }
        $order=" ORDER BY esa.ArticleDate DESC";
        $sql.=$where.$order;
        $query=$this->db->query($sql);
		$result=$query->result_array();
        $data['articles_2']=$result;
        $data['numb_2']=count($data['articles_2']);
    	
        $sql = "SELECT esa.ArticleID,esa.Title
                FROM event_spider_articles AS esa
                JOIN event_article_series AS eas
                ON esa.ArticleID = eas.ArticleID";
        $where=" ";
        if($seriesID!=0){
            $where.=" WHERE  eas.SeriesID=".$seriesID." AND esa.contentType=3";
        }
        $order=" ORDER BY esa.ArticleDate DESC";
        $sql.=$where.$order;
        $query=$this->db->query($sql);
		$result=$query->result_array();
        $data['articles_3']=$result;
        $data['numb_3']=count($data['articles_3']);
    	
        $sql = "SELECT esa.ArticleID,esa.Title
                FROM event_spider_articles AS esa
                JOIN event_article_series AS eas
                ON esa.ArticleID = eas.ArticleID";
        $where=" ";
        if($seriesID!=0){
            $where.=" WHERE  eas.SeriesID=".$seriesID." AND esa.contentType=4";
        }
        $order=" ORDER BY esa.ArticleDate DESC";
        $sql.=$where.$order;
        $query=$this->db->query($sql);
		$result=$query->result_array();
        $data['articles_4']=$result;
        $data['numb_4']=count($data['articles_4']);
        
        $data['base_url']=$this->config->item("base_url");
        
        /**
         * 加载页面
         * series_contents.html 文章内容页面
         */
        $this->load->view('admin/review_article_serach_result.html',$data);
    }
    
    /**
     * 编辑文章质量
     * Enter description here ...
     */
    public function editarticle(){
    	$ArticleID = $this->input->post('ArticleID');
    	$type = $this->input->post('type');
    	$action = $this->input->post('action');
    	if($action){
    	//选中，则插入
    		$data = array(
				'ArticleID'		=> $ArticleID,
				'type'		=> $type
			);
			$this->db->insert('event_article_review',$data);
			echo("1");
    	}else{
    	//未选中，删除
    		$this->db->delete('event_article_review', array('ArticleID' => $ArticleID,'type' => $type));
    		echo("0");
    	}
    }
    
	/**
     * 弹出层上传文件
     */
    public function showUploadBox($cat_id="",$cat_name=""){
        /**
         * 安全性检测，数据转换,校验
         */
        if(!$this->input->is_ajax_request()){
            show_404();
        }
        $data['error']='';
        $data['cat_id']=$cat_id;
        $data['cat_name']=$cat_name;
        $data['base_url']=$this->config->item('base_url');
        $this->load->view('admin/upload_form.html',$data);
    }
    
	public function do_upload(){
		if(!$this->input->is_ajax_request()){
            show_404();
        }
        $cat_id = $this->input->post('cat_id');
        $field_name = $this->input->post('field_name');
		$params = array('use' =>16);
        $this->load->library('Services_JSON',$params);
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size'] = '100';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';
		
		$this->load->library('upload', $config);
		if ( ! $this->upload->do_upload())
		{
		  $error = $this->upload->display_errors();
		  $result['result']=0;//失败
		  $result['error']=$error;
		} 
		else
		{
		  $data = array('upload_data' => $this->upload->data());
		  $result['result']=1;//成功
		  $result['error']=$data;print_r($data);
		}
		echo $this->services_json->encode($result);
	} 
}