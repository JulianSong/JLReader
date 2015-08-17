<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 笔记相关
 * @author julian
 */
class Note extends CI_Controller {
    /**
     * 返回的错误码
     */
    private $ERROR_USER_NOT_LOGIN=1;//用户未登录错误
    private $ERROR_WRONG_AID=2;//文章ID错误
    private $ERROR_WRONG_SID=3;//车系ID错误
    private $ERROR_WRONG_NID=4;//笔记ID错误
    private $ERROR_WRONG_CONTENT=5;//笔记内容不符合要求
    private $ERROR_SYS_ERROR=6;//系统错误
    private $ERROR_WRONG_TAG=7;//标签错误
    private $ERROR_NOTE_NUMB_OUT=8;//用户当天添加的标签超出数量
    private $ERROR_WRONG_DATE=9;//日期错误
    private $SUCCESS=0;//成功
    /**
     * 配置量
     */
    private $DAILY_NOTES_LIMIT=50;//用户单日能添加的笔记最大数量
    /**
     * 添加笔记
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
         * NoteModel 用户笔记模型
         * Services_JSON json 类
         */
        $this->load->model('user/NoteModel','note');
        $this->load->library('Services_JSON',array('use' =>16));
        /**
         * 设置返回结果数据
         * @var $result
         */
        $result=array();
        /**
         * 校验输入数据
         */
        $uid=$this->session->userdata("uid");
        if(!$uid){
            $result['error']=$this->ERROR_USER_NOT_LOGIN;
            die($this->services_json->encode($result));
        }
        $todyNoteNumb=$this->note->getUserDailyNotesNumb($uid);
        if(($todyNoteNumb++)>$this->DAILY_NOTES_LIMIT){
            $result['error']=$this->ERROR_NOTE_NUMB_OUT;
            die($this->services_json->encode($result));
        }
        $type=intval($this->input->post("t"));
        if(empty($type)){
            $result['error']=$this->ERROR_WRONG_CONTENT;
            die($this->services_json->encode($result));
        }
        $aid=intval($this->input->post("a"));
        if(empty($aid)&&$type!=2){
            $result['error']=$this->ERROR_WRONG_AID;
            die($this->services_json->encode($result));
        }
        $sid=intval($this->input->post("s"));
        if(empty($sid)){
            $result['error']=$this->ERROR_WRONG_SID;
            die($this->services_json->encode($result));
        }
        $noteContent=$this->input->post("nc");
        $noteContent=htmlspecialchars(preg_replace("/\s/","",$this->input->post("nc")));
        if(empty($noteContent)){
            $result['error']=$this->ERROR_WRONG_CONTENT;
            die($this->services_json->encode($result));
        }
        /**
         * 执行操作
         */
        if($this->note->addNote($uid,$aid,$sid,$noteContent,$type)){
            $result['error']=$this->SUCCESS;
        }else{
            $result['error']=$this->ERROR_SYS_ERROR;
        }
        echo $this->services_json->encode($result);
    }
    public function edit(){
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
        $this->load->model('user/NoteModel','note');
        $this->load->library('Services_JSON',array('use' =>16));
        /**
         * 设置返回结果数据
         * @var $result
         */
        $result=array();
        /**
         * 校验输入数据
         */
        $uid=$this->session->userdata("uid");
        if(!$uid){
            $result['error']=$this->ERROR_USER_NOT_LOGIN;
            die($this->services_json->encode($result));
        }
        $nid=intval($this->input->post("n"));
        if(empty($nid)){
            $result['error']=$this->ERROR_WRONG_NID;
            die($this->services_json->encode($result));
        }
        $noteContent=htmlspecialchars(preg_replace("/\s/","",$this->input->post("nc")));
        if(empty($noteContent)){
            $result['error']=$this->ERROR_WRONG_CONTENT;
            die($this->services_json->encode($result));
        }
        if(!$this->note->getUserNote($uid,$nid)){
            $result['error']=$this->ERROR_WRONG_NID;
            die($this->services_json->encode($result));
        }
        /**
         * 执行操作
         */
        if($this->note->editNoteContent($uid,$nid,$noteContent)){
            $result['error']=$this->SUCCESS;
        }else{
            $result['error']=$this->ERROR_SYS_ERROR;
        }
        echo $this->services_json->encode($result);
    }
    public function edittag(){
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
        $this->load->model('user/NoteModel','note');
        $this->load->library('Services_JSON',array('use' =>16));
        /**
         * 设置返回结果数据
         * @var $result
         */
        $result=array();
        /**
         * 校验输入数据
         */
        $uid=$this->session->userdata("uid");
        if(!$uid){
            $result['error']=$this->ERROR_USER_NOT_LOGIN;
            die($this->services_json->encode($result));
        }
        $nid=intval($this->input->post("n"));
        if(empty($nid)){
            $result['error']=$this->ERROR_WRONG_NID;
            die($this->services_json->encode($result));
        }
        $noteTag=htmlspecialchars(preg_replace("/\s/","",$this->input->post("nt")));
        if(empty($noteTag)){
            $result['error']=$this->ERROR_WRONG_TAG;
            die($this->services_json->encode($result));
        }
        if(!$this->note->getUserNote($uid,$nid)){
            $result['error']=$this->ERROR_WRONG_NID;
            die($this->services_json->encode($result));
        }
        /**
         * 执行操作
         */
        if($this->note->editNoteTag($uid,$nid,$noteTag)){
            $result['error']=$this->SUCCESS;
        }else{
            $result['error']=$this->ERROR_SYS_ERROR;
        }
        echo $this->services_json->encode($result);
    }
    /**
     * 刪除笔记
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
         * NoteModel 用户笔记模型
         * Services_JSON json 类
         */
        $this->load->model('user/NoteModel','note');
        $this->load->library('Services_JSON',array('use' =>16));
        /**
         * 设置返回结果数据
         * @var $result
         */
        $result=array();
        /**
         * 校验输入数据
         */
        $uid=$this->session->userdata("uid");
        if(!$uid){
            $result['error']=$this->ERROR_USER_NOT_LOGIN;
            die($this->services_json->encode($result));
        }
        $nids=trim($this->input->post("nids"));
        if(empty($nids)){
            $result['error']=$this->ERROR_WRONG_NID;
            die($this->services_json->encode($result));
        }
        $nidsArray=explode(",",$nids);
        foreach($nidsArray as $nid){
            if(!$this->note->getUserNote($uid,$nid)){
                $result['error']=$this->ERROR_WRONG_NID;
                die($this->services_json->encode($result));
            }
            if($this->note->delUserNote($uid,$nid)){
                $result['error']=$this->SUCCESS;
            }else{
                die($result['error']=$this->ERROR_SYS_ERROR);
            }
        }
        $result['del_notes']=$nidsArray;
        echo $this->services_json->encode($result);
    }
    /**
     * 获得用户笔记
     */
    public function usernotes(){
        /**
         * 未登录则直接跳转到登录页面
         */
        $uid=$this->session->userdata('uid');
        if(!$uid){
            $this->load->helper('url');
            redirect('user');
        }else{
            /**
             * 加载模型类库
             * NoteModel 用户笔记模型
             */
            $this->load->model("user/NoteModel",'note');
            /**
             * 设置数据
             * @var $data['userNotes'] 用户笔记
             * @var $data['title']页面标题
             * @var $data['base_url'] 站点地址
             * @var $data['mobile_phone'] 用户手机号
             */
            $data=array();
            //$timeRange=array("start"=>strtotime("today"),"end"=>0);
            $condition=array(
                            "uid"=>$uid
            );
            $data['userNotes']=$this->note->getNotes($condition);
            $data['title']="我的笔记-".$this->session->userdata('mobile_phone');
            $data['base_url']=$this->config->item("base_url");
            $data['mobile_phone']=$this->session->userdata('mobile_phone');
            $this->load->view('user/notes.html',$data);
        }
    }
    /**
     *
     */
    public function calendar(){
        /**
         * 如果不是ajax请求侧直接返回404错误页面
         */
        if(!$this->input->is_ajax_request()){
            show_404();
        }
        /**
         * 校验输入数据
         */
        $uid=$this->session->userdata("uid");
        if(!$uid){
            die($this->ERROR_USER_NOT_LOGIN);
        }
        $year=intval($this->input->post("y"));
        if(empty($year)){
            $year=date("Y",time());
        }
        $month=intval($this->input->post("m"));
        if(empty($month)){
            $month=date("m",time());
        }
        /**
         * 加载模型类库
         * NoteModel 用户笔记模型
         */
        $this->load->model("user/NoteModel",'note');
        /**
         * 设置数据
         * @var $data['base_url'] 站点地址
         */
        $data=array();
        $data['year']=$year;
        $data['month']=$month;
        $data['base_url']=$this->config->item("base_url");
        $data['calMonth']=$this->note->getNoteProfileOfMonth($uid,$year,$month);
        if($data['calMonth']){
            $this->load->view('user/lib/calendar.html',$data);
        }else{
            die($this->ERROR_SYS_ERROR);
        }
    }
    /**
     * 按日期获得某天的笔记
     */
    public function dailynotes(){
        /**
         * 如果不是ajax请求侧直接返回404错误页面
         */
        if(!$this->input->is_ajax_request()){
            show_404();
        }
        /**
         * 加载模型类库
         * NoteModel 用户笔记模型
         */
        $this->load->model('user/NoteModel','note');
        /**
         * 校验输入数据
         */
        $uid=$this->session->userdata("uid");
        if(!$uid){
            echo($this->ERROR_USER_NOT_LOGIN);
            exit;
        }
        $year=intval($this->input->post("y"));
        if(empty($year)){
            echo($this->ERROR_WRONG_DATE);
            exit;
        }
        $month=intval($this->input->post("m"));
        if(empty($month)){
            echo($this->ERROR_WRONG_DATE);
            exit;
        }
        $date=intval($this->input->post("d"));
        if(empty($date)){
            echo($this->ERROR_WRONG_DATE);
            exit;
        }
        /**
         * 执行操作
         */
        $time=mktime(0,0,0,$month,$date,$year);
        $data=array();
        $data['year']=$year;
        $data['month']=$month;
        $timeRange=array("start"=>0,"end"=>0);
        $condition=array(
                        "uid"=>$uid,
                        "start"=>$time,
                        "end"=>$time+24*60*60
        );
        $data['userNotes']=$this->note->getNotes($condition);
        $data['base_url']=$this->config->item("base_url");
        if($data['userNotes']){
            $this->load->view('user/lib/daily_notes.html',$data);
        }else{
            die($this->ERROR_SYS_ERROR);
        }
    }
    public function tags(){
        /**
         * 如果不是ajax请求侧直接返回404错误页面
         */
        if(!$this->input->is_ajax_request()){
            show_404();
        }
        /**
         * 校验输入数据
         */
        $uid=$this->session->userdata("uid");
        if(!$uid){
            echo($this->ERROR_USER_NOT_LOGIN);
            exit;
        }
        /**
         * 加载模型类库
         * NoteModel 用户笔记模型
         */
        $this->load->model('user/NoteModel','note');
        $data=array();
        $data['tags']=$this->note->getNoteTags($uid);
        $this->load->view('user/lib/note_tags.html',$data);
    }
    /**
     *通过标签获得笔记
     */
    public function tagnotes(){
        /**
         * 如果不是ajax请求侧直接返回404错误页面
         */
        if(!$this->input->is_ajax_request()){
            show_404();
        }
        /**
         * 加载模型类库
         * NoteModel 用户笔记模型
         */
        $this->load->model('user/NoteModel','note');
        /**
         * 校验输入数据
         */
        $uid=$this->session->userdata("uid");
        if(!$uid){
            echo($this->ERROR_USER_NOT_LOGIN);
            exit;
        }
        $tag=htmlspecialchars(preg_replace("/\s/","",$this->input->post("t")));
        if(empty($tag)){
            echo($this->ERROR_WRONG_TAG);
            exit;
        }
        /**
         * 执行操作
         */
        $data=array();
        $data['base_url']=$this->config->item("base_url");
        $condition=array(
                        "uid"=>$uid,
                        "tag"=>$tag
        );
        $data['userNotes']=$this->note->getNotes($condition);
        if($data['userNotes']){
            $this->load->view('user/lib/daily_notes.html',$data);
        }else{
            die($this->ERROR_SYS_ERROR);
        }
    }
    /**
     * 分享笔记
     */
    public function sharenotes(){


    }
    /**
     * 打印笔记
     */
    public function printnotes(){
        /**
         * 未登录则直接跳转到登录页面
         */
        $uid=$this->session->userdata('uid');
        if(!$uid){
            $this->load->helper('url');
            redirect('user');
        }else{
            $notes_id=$this->input->post("notes_id");
            if(empty($notes_id)){
                show_404();
            }
            $ids=implode(",",$notes_id);
            /**
             * 加载模型类库
             * NoteModel 用户笔记模型
             */
            $this->load->model("user/NoteModel",'note');
            /**
             * 设置数据
             * @var $data['userNotes'] 用户笔记
             * @var $data['title']页面标题
             * @var $data['base_url'] 站点地址
             * @var $data['mobile_phone'] 用户手机号
             */
            $data=array();
            $timeRange=array("start"=>0,"end"=>0);
            $condition=array(
                            "uid"=>$uid,
                            "ids"=>$ids
            );
            $data['userNotes']=$this->note->getNotes($condition);
            $data['title']="打印预览";
            $data['base_url']=$this->config->item("base_url");
            $this->load->view('user/print_notes.html',$data);
        }
    }
    /**
     * 合并所选笔记
     */
    public function merge(){
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
        $this->load->model('user/NoteModel','note');
        $this->load->library('Services_JSON',array('use' =>16));
        /**
         * 校验输入数据
         */
        $uid=$this->session->userdata("uid");
        if(!$uid){
            $result['error']=$this->ERROR_USER_NOT_LOGIN;
            die($this->services_json->encode($result));
        }
        $nids=trim($this->input->post("nids"));
        if(empty($nids)){
            $result['error']=$this->ERROR_WRONG_NID;
            die($this->services_json->encode($result));
        }
        if($result=$this->note->mergeNotes($uid,$nids)){
            $result['error']=$this->SUCCESS;
            die($this->services_json->encode($result));
        }else{
            $result['error']=$this->ERROR_SYS_ERROR;
            die($this->services_json->encode($result));
             
        }
    }
    
    /**
     * 修改笔记的相关车系
     * Enter description here ...
     */
    public function setseries(){
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
        $this->load->model('user/NoteModel','note');
        $this->load->library('Services_JSON',array('use' =>16));
        /**
         * 校验输入数据
         */
        $uid=$this->session->userdata("uid");
        if(!$uid){
        	echo('0');//没有登录
        }
        
        $notes_id=$this->input->post("nid");
        $series_id=$this->input->post("seriesid");
        $data=$this->note->editSeries($uid,$notes_id,$series_id);
        if($data){
        	echo('1');//设置成功
        }else{
        	echo('2');//设置失败
        }
    }
}