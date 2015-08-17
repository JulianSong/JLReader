<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 系统首页
 * @author julian
 */
class Index extends CI_Controller {

    public function __construct(){
        parent::__construct();
    }
     
    public function index(){
       $uid=$this->session->userdata('uid');
        /*if($uid){
            $this->load->helper('url');
            redirect('user');
        }
        /**
         * 加载model，类库
         */
        $this->load->model('common/SeriesModel','series');
        $this->load->model('user/ConcernedModel','concerned');
        $this->load->helper('cookie');
        /**
         * 设置数据
         * @var $data
         */
        $sidCookie=$this->input->cookie('_uhsid', TRUE);
        $data=array();
        $data['mobile_phone']=$this->session->userdata('mobile_phone');
        $data['logoutViewedSeries']=$this->series->getLogoutViewedSeries($sidCookie);
        //$this->series->addRecoment();
        $data['recommend']=$this->series->getRecommend();
        //$this->series->generateKeywords();
        //$data['hotConcerned']=$this->series->getHotConcerned();
        $data['uid']=$this->session->userdata("uid");
        $data['title']="车型研究";
        $data['index']=true;
        $data['c_name']="_uhsid";
        $data['base_url']=$this->config->item('base_url');
        /**
         * 加载页面
         */
        $this->load->view('user/index.html',$data);
    }
}