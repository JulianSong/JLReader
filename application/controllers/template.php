<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 车款相关控制器
 * @author julian
 */
class Template extends CI_Controller {
    public function index(){
        $this->templist();
    }
    /**
     * 获得车系先关内容
     * @param  $sid 车系id
     */
    public function templist(){
        /**
         * 加载model，类库
         * SeriesModel 车系model
         * cookie   cookie类
         */
        $this->load->model('content/TemplateModel','temps');
        $data=array();
        $data['pages']=$this->temps->getAllParsedTemplate(400,300);
        $this->load->view('admin/template.html',$data);
    }
    public function editiplate(){
        if(!$this->input->is_ajax_request()){
            show_404();
        }
        $plate=array();
        $plate['TemplateID']=$this->input->post('ti');
        $plate['CanUse']=$this->input->post('cu');
        $this->load->model('content/TemplateModel','temps');
        $data['pages']=$this->temps->editPlate($plate);
        die("s");
    }
    public function addplate(){
        $this->load->view('admin/addplate.html');
    }
    public function add(){
       if(!$this->input->is_ajax_request()){
            show_404();
        }
        $plate=array();
        $plate['BlockAmount']=$this->input->post('ba');
        $plate['PlateInfo']=htmlspecialchars(preg_replace("/\s/","",$this->input->post('tinfo')));
        $this->load->model('content/TemplateModel','temps');
        $plate['TemplateID']=$this->temps->addTemplate($plate['BlockAmount']);
        $plate['PlateInfo']=$plate['TemplateID'].','.$plate['PlateInfo'];
        $this->temps->editPlate($plate);
        die("s");
    }
}