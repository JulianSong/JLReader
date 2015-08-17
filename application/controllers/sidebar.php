<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 关注车系相关控制器
 * @author julian
 */
class SideBar extends CI_Controller {
    public function topbar(){
        $sid=intval($this->input->post('sid'));
        $sid=$this->session->userdata('current_sid',$sid);
        $data=array();
        if($sid){
            $this->load->model('common/SeriesModel','series');
            $data['seriesInfo']=$this->series->getSeriesInfo($sid);
            $data['showSeriesInfo']=true;
        }
        $data['base_url']=$this->config->item('base_url');
        $this->load->view("user/lib/page_header.html",$data);
    }
}
