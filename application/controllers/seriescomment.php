<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 车系点评页面相关控制器
 * @author yeduo
 */
class Seriescomment extends CI_Controller {
	public function __construct(){
        parent::__construct();
    }
    /**
     * 车系点评页面
     */
    public function index(){
    	show_404();
    }
    /**
     * 车系点评页面
     * $uid 获取笔记的用户id
     * $limit=10;//获取的数量
     */
    public function comment($SeriesID=""){
    	if($SeriesID==""||!is_numeric($SeriesID)){
    		show_404();
    	}
    	$this->load->model("content/SeriesCommentModel",'seriesnote');
    	$this->load->model('common/SeriesModel','series');
    	$data=array();
    	$uid=array(11,13);//设置管理员用户id
    	$tag="外观特色";
    	$wg = $this->seriesnote->getOneNoteByTag($uid,$SeriesID,$tag);
    	if($wg){
    		$data['waiguantese']=$wg;
    	}else{
    		$data['waiguantese']=array('content'=>'暂时无内容','ArticleID'=>0);
    	}
    	
    	$tag="动力性能";
    	$dl = $this->seriesnote->getOneNoteByTag($uid,$SeriesID,$tag);
    	if($dl){
    		$data['donglixingneng']=$dl;
    	}else{
    		$data['donglixingneng']=array('content'=>'暂时无内容','ArticleID'=>0);
    	}
    	
    	$tag="配置特色";
    	$ps = $this->seriesnote->getOneNoteByTag($uid,$SeriesID,$tag);
    	if($ps){
    		$data['peizhitese']=$ps;
    	}else{
    		$data['peizhitese']=array('content'=>'暂时无内容','ArticleID'=>0);
    	}
    	
    	$tag_arr=array("'外观特色'","'动力特色'","'配置特色'");
    	$limit=10;//获取的数量
    	$dp = $this->seriesnote->getAllNoteByTag($uid,$SeriesID,$tag_arr,$limit);
    	$data['dianping']=$dp;
    	
        $data['base_url']=$this->config->item("base_url");
        $data['seriesInfo']=$this->series->getSeriesInfo($SeriesID);
        $data['showSeriesInfo']=true;
        $data['title']="车系点评-".$data['seriesInfo']['cat_name'];
        $this->load->view("user/series_comment.html",$data);
    }
}