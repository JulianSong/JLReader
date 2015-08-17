<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 关注车系相关控制器
 * @author julian
 */
class Series extends CI_Controller {
    /**
     * 获得车系相关内容
     * @param  $sid 车系id
     */
    public function contents($sid=0){
        /**
         * 安全性检测，数据转换,校验
         */
        $sid=intval($sid);
        if($sid==0){
            show_404();
        }
        $this->session->set_userdata('current_sid',$sid);
        /**
         * 加载model，类库
         * SeriesModel 车系model
         * cookie   cookie类
         */
        $this->load->model('common/SeriesModel','series');
        $this->load->model("user/ConcernedModel",'concerned');
        $this->load->helper('cookie');
        /**
         * 获得cookie
         */
        $_sid_cookie=$this->input->cookie('_uhsid', TRUE);
        $_sid_cookie_array=empty($_sid_cookie)? array(): explode(",",$_sid_cookie);
        /**
         * 设置数据
         * @var $data
         * @var $data['mobile_phone']    用户手机号
         * @var $data['seriesInfo']      车系信息
         * @var $data['seriesComments']  车系平评论
         * @var $data['channels']        车系频道——文章类型
         * @var $data['sites']           车系文章网站类型
         * @var $data['title']           页面标题
         * @var $data['base_url']        站点地址
         */
        $data=array();
        $data['mobile_phone']=$this->session->userdata('mobile_phone');
        $data['seriesInfo']=$this->series->getSeriesInfo($sid);
        if(!$data['seriesInfo']){
            show_404();
        }
        $data['cars']=$this->series->getCars($sid);
        $data['seriesComments']=$this->series->getComments($sid);
        $data['channels']=$this->series->getChannels($sid);
        $data['competeSeries']=$this->series->getCompeteSeries($sid);
        $data['csLength']=count($data['competeSeries']);
        $data['title']= $data['seriesInfo']['cat_name'];
        $data['base_url']=$this->config->item('base_url');
        /**
         * 如果用户登录则将此车系保存到用户关注，并存入cookie，增加车系关注度
         * 未登录则只保存到cookie，增加车系关注度
         */
        $uid=$this->session->userdata('uid');
        if($uid){
            if(!$this->concerned->existConcerned($sid,$uid)){
                $this->concerned->addConcerned($sid,$uid);
                $this->series->addHot($sid);
            }
            if(!in_array($sid,$_sid_cookie_array)){
                array_push($_sid_cookie_array,$sid);
            }
        }else{
            if(!in_array($sid,$_sid_cookie_array)){
                $this->series->addHot($sid);
                array_push($_sid_cookie_array,$sid);
            }
        }
        /**
         * 设置cookie
         * _uhsid用户访问过的车系id以","号分隔
         * 时长一个月
         */
        $cookie = array(
                   'name'   => '_uhsid',
                   'value'  => implode(",",$_sid_cookie_array),
                   'expire' => '2592000',
                   'domain' => '',
                   'path'   => '/',
                   'prefix' => '',
        );
        $this->input->set_cookie($cookie);
        /**
         * 加载页面
         * series_contents.html 车系内容页面
         */
        $this->load->view('user/series_contents.html',$data);
    }
    /**
     * 文章搜素
     * @param  $sid 车系id
     */
    public function competeseries($sid=0){
        /**
         * 安全性检测，数据转换,校验
         */
        $sid=intval($sid);
        if($sid==0){
            die('车系不存在或者已经删除！');
        }
        $this->session->set_userdata('current_sid',$sid);
        /**
         * 加载model，类库
         * SeriesModel 车系model
         * cookie   cookie类
         */
        $this->load->model('common/SeriesModel','series');
        /**
         * 设置数据
         * @var $data
         * @var $data['mobile_phone']    用户手机号
         * @var $data['seriesInfo']      车系信息
         * @var $data['seriesComments']  车系平评论
         * @var $data['channels']        车系频道——文章类型
         * @var $data['sites']           车系文章网站类型
         * @var $data['title']           页面标题
         * @var $data['base_url']        站点地址
         */
        $data=array();
        $data['seriesInfo']=$this->series->getSeriesInfo($sid);
        if(!$data['seriesInfo']){
            die('车系不存在或者已经删除！');
        }
        $data['competeSeries']=$this->series->getCompeteSeries($sid);
        $data['csLength']=count($data['competeSeries']);
        $data['base_url']=$this->config->item('base_url');
        /**
         * 加载页面
         * series_contents.html 车系内容页面
         */
        $this->load->view('user/compete_series.html',$data);
    }
    public function info($sid=0){
        /**
         * 校验输入数据
         */
        $sid=intval($sid);
        if(!$sid){
            show_404();
        }
        $this->session->set_userdata('current_sid',$sid);
        $contentType="2,3,4";
        /*
         * 加载模型，类库
         */
        $this->load->model('content/ArticleModel','articles');
        $this->load->model('content/ContentTypeModel','contentType');
        $this->load->model('content/TemplateModel','temps');
        $this->load->model('common/SeriesModel','series');
        $this->load->library('Services_JSON',array('use' =>16));
        $this->load->helper('cookie');
        /**
         * 获得cookie
         */
        $_sid_cookie=$this->input->cookie('_uhsid', TRUE);
        $_sid_cookie_array=empty($_sid_cookie)? array(): explode(",",$_sid_cookie);
        /**
         * 如果用户登录则将此车系保存到用户关注，并存入cookie，增加车系关注度
         * 未登录则只保存到cookie，增加车系关注度
         */
        $uid=$this->session->userdata('uid');
        if($uid){
            if(!$this->concerned->existConcerned($sid,$uid)){
                $this->concerned->addConcerned($sid,$uid);
                $this->series->addHot($sid);
            }
            if(!in_array($sid,$_sid_cookie_array)){
                array_push($_sid_cookie_array,$sid);
            }
        }else{
            if(!in_array($sid,$_sid_cookie_array)){
                $this->series->addHot($sid);
                array_push($_sid_cookie_array,$sid);
            }
        }

        /**
         * 设置cookie
         * _uhsid用户访问过的车系id以","号分隔
         * 时长一个月
         */
        $cookie = array(
                   'name'   => '_uhsid',
                   'value'  => implode(",",$_sid_cookie_array),
                   'expire' => '2592000',
                   'domain' => '',
                   'path'   => '/',
                   'prefix' => '',
        );
        $this->input->set_cookie($cookie);
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
        $data['tempJson']=$this->services_json->encode($this->temps->getAllTemplate());
        $data['sid']=$sid;
        $data['ct']=$contentType;
        $data['csids']="";
        $data['title']=$data['seriesInfo']['cat_name'];
        $data['base_url']=$this->config->item('base_url');
        $this->load->view("user/reader.html",$data);
    }
    /**
     * 显示车系搜索框
     */
    public function showSerachBox($key=''){
        /**
         * 安全性检测，数据转换,校验
         */
        if(!$this->input->is_ajax_request()){
            show_404();
        }
        $data=array();
        $data['base_url']=$this->config->item('base_url');
        $data['key']=$key;
        $this->load->view('user/lib/serach_series_box.html',$data);
    }
    /**
     * 获得搜索的竞争车系的文章的数量
     */
    public function getsan(){
        /**
         * 校验输入数据
         */
        $sid=intval($this->input->post('sid'));
        if(!$sid){
            echo '0';
            exit;
        }
        $csids=$this->input->post('csids');
        if(empty($csids)){
            echo '0';
            exit;
        }
        /*
         * 加载模型，类库
         */
        $this->load->model('content/ArticleModel','articles');
        /**
         * 返回数据
         */
        echo $this->articles->getSerachArticleNumb($sid,1,$csids);
    }
    /**
     * 搜索车系
     */
    public function serach(){
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
        $this->load->view('user/lib/series_serach_result.html',$data);
    }
}