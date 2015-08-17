<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * AutoReader显示程序
 * @author julian
 */
class Reader extends CI_Controller {
    /**
     * 返回的错误信息
     */
    private $ERROR_NO_DATA=1;//没有符合要求的数据
    private $ERROR_WRONG_BATCH=2;//数据批次错误
    private $ERROR_WRONG_SID=3;//车系id错误
    private $ERROR_WRONG_CT=4;//内容类型错误
    private $ERROR_WRONG_S=5;//内容类型错误
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * 显示文章列表
     */
    public function articles($sid=0,$contentType=0){
         show_404();
        /**
         * 校验输入数据
         */
        $sid=intval($sid);
        if(!$sid){
            show_404();
        }
        $this->output->cache(420);
        $contentType=intval($contentType);
        if(!$contentType){
            show_404();
        }
        /*
         * 加载模型，类库
         */
        $this->load->model('content/ArticleModel','articles');
        $this->load->model('content/ContentTypeModel','contentType');
        $this->load->model('content/TemplateModel','temps');
        $this->load->model('common/SeriesModel','series');
        $this->load->library('Services_JSON',array('use' =>16));
        /**
         * 准备数据
         * @var $data 页面输出数据
         */
        $data=array();

        $data=$this->articles->getArticles($sid,$contentType);
        if($data['length']==0){
            show_404();
        }
        if($data['length']==1){
            $this->load->helper('url');
            redirect("reader/articlecontent/".$data['list'][0]['ArticleID']);
        }
        $data['seriesArticlesNum']=$this->articles->getArticlesNumByScs($sid,$contentType);
        $data['seriesInfo']=$this->series->getSeriesInfo($sid);
        $data['contentType']=$this->contentType->getContentType($contentType);
        $data['tempJson']=$this->services_json->encode($this->temps->getAllTemplate());
        $data['sid']=$sid;
        $data['ct']=$contentType;
        $data['csids']="";
        $data['title']=$data['seriesInfo']['cat_name']."——".$data['contentType']['name'];
        $data['base_url']=$this->config->item('base_url');
        $this->load->view("user/reader.html",$data);
    }
    public function qarticles(){
         
        /**
         * 校验输入数据
         */
        $sid=intval($this->input->post('sid'));
        if(!$sid){
            show_404();
        }
        $contentType=1;
        $csids=$this->input->post('csids');
        if(empty($csids)){
            show_404();
        }
        $query=implode(",",$csids);
        /*
         * 加载模型，类库
         */
        $this->load->model('content/ArticleModel','articles');
        $this->load->model('content/ContentTypeModel','contentType');
        $this->load->model('content/TemplateModel','temps');
        $this->load->model('common/SeriesModel','series');
        $this->load->library('Services_JSON',array('use' =>16));
        /**
         * 准备数据
         * @var $data 页面输出数据
         */
        $data=array();

        $data=$this->articles->serachArticle($sid,$contentType,$query);
        if($data['length']==0){
            show_404();
        }
        if($data['length']==1){
            $this->load->helper('url');
            redirect("reader/articlecontent/".$data['list'][0]['ArticleID']);
        }else{
            $data['seriesArticlesNum']=$this->articles->getSerachArticleNumb($sid,$contentType,$query);
            $data['seriesInfo']=$this->series->getSeriesInfo($sid);
            $data['contentType']=$this->contentType->getContentType($contentType);
            $data['tempJson']=$this->services_json->encode($this->temps->getAllTemplate());
            $data['sid']=$sid;
            $data['ct']=$contentType;
            $data['csids']=$query;
            $data['title']=$data['seriesInfo']['cat_name']."—".$data['contentType']['name'];
            $data['base_url']=$this->config->item('base_url');
            $this->load->view("user/reader.html",$data);
        }
    }
    /**
     * 显示经过treesaver排版的文章内页
     */
    public function iframearticlecontent($aid=0){
        /**
         * 校验输入数据
         */
        $aid=intval($aid);
        if(!$aid){
            show_404();
        }
        $this->output->cache(420);
        $this->load->model('content/ArticleModel','articles');
        $this->load->model('content/recommendarticlesmodel','recommend');
        $data['article']=$this->articles->getArticleContent($aid);
        if(!isset($data['article']['ArticleID'])){
            show_404();//如果文章不存在则转到404页面
        }
        //$data["recommend"]=$this->recommend->getCommendArticleFromHistory($aid,10);//推荐的10篇文章
        $data['title']=$data['article']['Title'];
        $data['base_url']=$this->config->item('base_url');

        $uid = $this->session->userdata('uid');
        if(empty($uid)){
            $uid=0;
        }
        $data['uid'] = $uid;
        $this->load->view("treesaver/article_content_nohead.html",$data);
    }
    /**
     * iframe显示经过treesaver排版的文章内页
     * by yeduo 2011.12.05
     */
    public function articlecontent($aid=0){
        /**
         * 校验输入数据
         */
        $aid=intval($aid);
        if(!$aid){
            show_404();
        }
        $this->output->cache(420);
        $this->load->model('content/ArticleModel','articles');
        $this->load->model('content/recommendarticlesmodel','recommend');
        $this->load->model('common/SeriesModel','series');
        $data['article']=$this->articles->getArticleContent($aid);
        if(!isset($data['article']['ArticleID'])){
            show_404();//如果文章不存在则转到404页面
        }
        $data['title']=$data['article']['Title'];
        $data['base_url']=$this->config->item('base_url');

        $uid = $this->session->userdata('uid');
        if(empty($uid)){
            $uid=0;
        }
        $data['uid'] = $uid;
        $data['aid'] = $aid;
        $data['SeriesID']=$data['article']['SeriesID'];
        $data['seriesInfo']=$this->series->getSeriesInfo($data['SeriesID']);
        $data['showSeriesInfo']=true;
        $this->load->view("treesaver/article_content_iframe.html",$data);
    }
    /**
     * 获得更多的文章
     */
    public function morepages(){
        /**
         * 如果不是ajax请求侧直接返回404错误页面
         */
        if(!$this->input->is_ajax_request()){
            show_404();
        }
        /**
         * 加载模型类库
         * NoteModel 用户笔记模型
         * Services_JSON json 类
         */
        $this->load->model('content/ArticleModel','articles');
        $this->load->model('content/TemplateModel','temps');
        /**
         * 校验输入数据
         */
        $batch=intval($this->input->post('b'));
        if(empty($batch)){
            echo($this->ERROR_WRONG_BATCH);
            exit;
        }
        $sid=intval($this->input->post('sid'));
        if(empty($sid)){
            echo($this->ERROR_WRONG_SID);
            exit;
        }
        $contentType=htmlspecialchars(preg_replace("/\s/"," ",$this->input->post('ct')));
        if(empty($contentType)){
            echo ($this->ERROR_WRONG_CT);
            exit;
        }
        $csids=htmlspecialchars(preg_replace("/\s/"," ",$this->input->post('csids')));
        if(empty($csids)){
            $data=$this->articles->getArticles($sid,$contentType,$batch);
        }else{
            $data=$this->articles->serachArticle($sid,$contentType,$csids,$batch);
        }
        /**
         * 设置数据
         * @var $data['batch']  批次
         * @var $data['loadDataNum']  当前已加载的文章数量
         * @var $data['pageLength']页码
         */
        
        if($data['length']==0){
            echo $this->ERROR_NO_DATA;
            exit;
        }
        $data['batch']=$batch;
        $data['loadDataNum']=($batch-1)*30+$data['length'];
        $this->load->view("user/lib/data_bind.html",$data);
    }
    
	/**
     * 显示没有经过treesaver排版的文章内页
     * by yeduo 2011.12.05
     */
    public function newarticlecontent($aid=0){
        /**
         * 校验输入数据
         */
        $aid=intval($aid);
        if(!$aid){
            show_404();
        }
      	$this->output->cache(420);
        $this->load->model('content/ArticleModel','articles');
        $this->load->model('content/recommendarticlesmodel','recommend');
        $this->load->model('common/SeriesModel','series');
        $data['article']=$this->articles->getArticleContent($aid);
        if(!isset($data['article']['ArticleID'])){
            show_404();//如果文章不存在则转到404页面
        }
        $data['title']=$data['article']['Title'];
        $data['base_url']=$this->config->item('base_url');

        $uid = $this->session->userdata('uid');
        if(empty($uid)){
            $uid=0;
        }
        $data['uid'] = $uid;
        $data['aid'] = $aid;
        $data['SeriesID']=$data['article']['SeriesID'];
        $data['seriesInfo']=$this->series->getSeriesInfo($data['SeriesID']);
        $data['showSeriesInfo']=true;print_r($data['article']['Summary']);
        $this->load->view("treesaver/article_content_two.html",$data);
    }
}
?>