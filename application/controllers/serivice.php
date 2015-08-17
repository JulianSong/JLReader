<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 车款相关控制器
 * @author julian
 */
class Serivice extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('content/TemplateModel','temps');
	}
    public function template(){
    	$this->load->model('content/TemplateModel','temps');
    	$id=$this->input->post('id');
    	echo json_encode($this->temps->find($id));
    }
    public function article(){
    	$this->load->model('content/ArticleModel','article');
    	$data=$this->article->getArticles(648,1,1);
 		echo json_encode($data['list']);   	
    }
    public function prossPic(){
    	
    	$this->load->model('content/ContentTypeModel','content');
//    	$this->content->prossPic(FCPATH."images/0/",0);
    	$this->content->prossPic(FCPATH."images/1/",1);
    }
 public function article_test(){
    	$this->load->model('content/ArticleModel','article');
    	$data=$this->article->getArticles(406,1,1,30);
    	header("Context-type=text/plian");
    	print_r($data);
 		echo json_encode($data);   	
    }
}