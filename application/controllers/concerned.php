<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 关注车系相关控制器
 * @author julian
 */
class Concerned extends CI_Controller {
    private $ERROR_WRONG_SID=1;//ID错误
    private $ERROR_USER_NOT_LOGIN=2;//用户未登录错误
    private $ERROR_CONCEREND_EXSIT=3;//关注已存在
    private $ERROR_SYS_ERROR=4;//系统错误
    private $SUCCESS=0;//成功
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * 添加关联车系
     */
    public function add(){
        /**
         * 如果不是ajax请求侧直接返回404错误页面
         */
        if(!$this->input->is_ajax_request()){
            show_404();
        }
        /**
         * 加载模型类库
         */
        $this->load->model("user/ConcernedModel",'concerned');
        $this->load->library('Services_JSON',array('use' =>16));
        $uid=$this->session->userdata("uid");
        if(!$uid){
            $result['error']=$this->ERROR_USER_NOT_LOGIN;
            die($this->services_json->encode($result));
        }
        /**
         * 设置数据
         * @var $result
         */
        $result=array();
        $result['error']=0;
        if($this->input->post("sid")==0){
            $result['error']=$this->ERROR_WRONG_SID;
            echo $this->services_json->encode($result);
            exit;
        }
        /**
         * 返回json数据
         */
        if($this->concerned->existConcerned($this->input->post("sid"),$uid)){
            $result['error']=$this->ERROR_CONCEREND_EXSIT;
        }else{
            if($this->concerned->addConcerned($this->input->post("sid"),$uid)){
                $result['error']=$this->SUCCESS;
            }else{
                $result['error']=$this->ERROR_SYS_ERROR;
            }
        }
        echo $this->services_json->encode($result);
         
    }
    /**
     * 删除关联车系
     */
    public function del(){
        /**
         * 如果不是ajax请求侧直接返回404错误页面
         */
        if(!$this->input->is_ajax_request()){
            show_404();
        }
        /**
         * 加载模型类库
         */
        $this->load->model("user/ConcernedModel",'concerned');
        $this->load->library('Services_JSON',array('use' =>16));
        $uid=$this->session->userdata("uid");
        if(!$uid){
            $result['error']=$this->ERROR_USER_NOT_LOGIN;
            die($this->services_json->encode($result));
        }
        /**
         * 设置数据
         * @var $result
         */
        $result=array();
        $result['error']=0;
        /**
         * 返回json数据
         */
        if($this->concerned->delConcerned($this->input->post("sid"),1)){
            $result['error']=$this->SUCCESS;
        }else{
            $result['error']=$this->ERROR_SYS_ERROR;
        }
        echo $this->services_json->encode($result);
    }
    /**
     * 获得用户关注的车系
     */
    public function getuserconcered(){
        $uid=$this->session->userdata('uid');
        $uid=$uid?$uid:0; 
        $data=array();
        $data['userConcerned']=$this->concerned->getUserConcerned($uid);

    }
}