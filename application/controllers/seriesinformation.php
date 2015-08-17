<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 车系参数页面相关控制器
 * @author yeduo
 */
class Seriesinformation extends CI_Controller {
	public function __construct(){
        parent::__construct();
    }
    /**
     * 车系参数页面
     */
    public function index(){
    	show_404();
    }
    /**
     * 车系参数页面
     * 
     */
    public function information($SeriesID=""){
    	if($SeriesID==""||!is_numeric($SeriesID)){
    		show_404();
    	}
    	$this->load->model("common/SeriesModel",'seriesInfo');
    	$this->load->model("content/SeriesInformationModel",'information');
    	$data=array();
    	$data['allattributes']=$this->information->getAllAttributes();
    	$data['attributes']=$this->information->getSeriesInformation($SeriesID);
    	$data['carCount']=count($data['attributes']);
        $data['seriesInfo']=$this->seriesInfo->getSeriesInfo($SeriesID);
        $data['showSeriesInfo']=true;
        $data['title']="车系参数-".$data['seriesInfo']['cat_name'];
        $data['base_url']=$this->config->item('base_url');
        $this->load->view("user/series_information.html",$data);
    }
}