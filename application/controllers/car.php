<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 车款相关控制器
 * @author julian
 */
class Car extends CI_Controller {
    /**
     * 获得车系先关内容
     * @param  $sid 车系id
     */
    public function attributes(){
        /**
         * 安全性检测，数据转换,校验
         */
        if(!$this->input->is_ajax_request()){
            show_404();
        }
        $cid=intval($this->input->post('c'));
        if($cid==0){
            die("");
        }
        /**
         * 加载model，类库
         * SeriesModel 车系model
         * cookie   cookie类
         */
        $this->load->model('common/CarModel','car');
        $data=array();
        $data['attributes']=$this->car->getCarsAttribute($cid);
        $data['car_price']=array_shift($data['attributes']);
        $data['base_url']=$this->config->item('base_url');
        
        $this->load->view('user/lib/car_attributes.html',$data);
    }
}