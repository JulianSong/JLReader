<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 关注车系model
 * @author julian
 */
class SeriesModel extends CI_Model{
    public function __construct(){
        parent::__construct();
        $this->load->model('content/ContentTypeModel','contentType');
        $this->load->model('content/ArticleModel','article');
        $this->load->model("user/ConcernedModel",'concerned');
        $this->load->model("content/ArticleSitesModel",'articleSites');
        $this->load->library('Pinyin');
        $this->load->library('Price');
    }
    /**
     * 增加车系热度
     * @param  $sid 车系id
     */
    public function addHot($sid){
        $sql="SELECT SeriesID FROM event_series_hot WHERE SeriesID = ?";
        $query=$this->db->query($sql,array($sid));
        if(!$query->num_rows()){
            $sqlInsert="INSERT INTO  event_series_hot VALUES( ?, 1)";
            $this->db->query($sqlInsert,array($sid));
        }else{
            $sqlUpdate="UPDATE event_series_hot SET hot=hot+1 WHERE SeriesID = ?";
            $this->db->query($sqlUpdate,array($sid));
        }
    }
    /**
     * 高级搜素
     * 首先直接使用关键字搜索，无结果则使用拼音搜索
     * 如果仍无结果则搜索品牌
     * @param  $key 关键字
     */
    public function advanceSerach($key){
        $result=array();
        $result=$this->searchSeries($key);
        if(!$result){
            $result=$this->series->searchSeries($this->pinyin->getPinyin($key,"UTF-8"));
        }
        return $result;
    }
    /**
     * 获得所有频道
     */
    public function getAllChannel(){
        return $this->contentType->getAllContentType();
    }
    /**
     * 获得所有可以关注的车系
     */
    public function getCanReaderSeries(){
        $sql="SELECT ec.cat_id,ec.cat_name, ec.series_img,ec.spellfirstchar
            FROM event_category AS ec
            RIGHT  JOIN event_spider_articles AS esa
            ON ec.cat_id = esa.SeriesID
            GROUP BY  ec.cat_id
            ORDER BY ec.spellfirstchar";
        $query=$this->db->query($sql);

        return $query->result_array();
    }
    /**
     *获得有文章可阅读的车系id并存入session
     */
    public function getCanReaderSeriesID(){
        $resultArray=array();
        if($this->session->userdata("can_reader_series")){
            return $this->session->userdata("can_reader_series");
        }else{
            $arr=$this->getCanReaderSeries();
            foreach($arr as $row){
                array_push($resultArray,$row['cat_id']);
            }
            $this->session->set_userdata("can_reader_series",$resultArray);
            return $resultArray;
        }
    }
    /**
     * 添加推荐
     */
    public function addRecoment(){
        $sql="INSERT INTO event_recommend_series VALUES(?,?)";
        $arr=$this->getCanReaderSeriesID();
        foreach($arr as $row){
            $this->db->query($sql,array($row,1));
        }
    }
    /**
     * 获得车系各个频道信息
     * @param $sid
     */
    public function getChannels($sid){
        $channel=array();
        $arr=$this->getAllChannel();
        foreach($arr as $contentType){
            $contentType['numb']=$this->article->getArticlesNumByScs($sid,$contentType['id']);
            if($contentType['numb']!=0)
            array_push($channel,$contentType);
        }
        return $channel;
    }
    /**
     * 获得车系的评论
     * @param unknown_type $sid
     */
    public function getComments($sid){
        $sql="SELECT title,content,type,AngleOfView,URL,'From',SeriesID,Author FROM event_spider_comments WHERE SeriesID = ". $sid;
        $query=$this->db->query($sql);
        return $query->result_array();
    }
    /**
     *获得指定定数量的最受关注的车系
     * @param  $limit 数量
     */
    public function getHotConcerned($limit=10){
        $sql="SELECT es.cat_id,es.cat_name, es.series_img,es.spellfirstchar,esh.hot
              FROM event_category AS es
              RIGHT  JOIN event_series_hot AS esh
              ON es.cat_id = esh.SeriesID  
              ORDER BY esh.hot DESC
              LIMIT  ?";
        $query=$this->db->query($sql,array($limit));
        return $this->processingData($query);
    }
    /**
     *获得推荐的车系
     * @param  $limit 数量
     */
    public function getRecommend($limit=8){
        $sql="SELECT es.cat_id,es.cat_name, es.series_img,es.spellfirstchar,ers.weight
              FROM event_category AS es
              RIGHT  JOIN event_recommend_series AS ers
              ON es.cat_id = ers.SeriesID  
              ORDER BY ers.weight DESC
              LIMIT  ?";
        $query=$this->db->query($sql,array($limit));
        return $this->processingData($query);
         
    }
    /**
     * 通过互相关联的文章获得竞争车系
     * @param  $sid 车系id
     */
    public function getCompeteSeries($sid,$contentType=0){
        $sql="SELECT ec.cat_id,ec.cat_name,a.SeriesID,esa.ArticleID
              FROM  event_article_series AS a
              JOIN  event_article_series AS b
              ON a.ArticleID=b.ArticleID
              LEFT JOIN event_spider_articles AS esa
              ON esa.ArticleID=a.ArticleID
              LEFT JOIN event_category AS ec
              ON ec.cat_id=a.SeriesID
              WHERE b.SeriesID=? AND a.SeriesID<> ? AND esa.contentType = 1
              GROUP BY ec.cat_id  ";
        $query=$this->db->query($sql,array($sid,$sid));
        $arr=$query->result_array();
        return $arr; 
    }
    /**
     * 获得车系的基础信息
     * @param  $sid 车系id
     */
    public function getSeriesInfo($sid){
        $sql="SELECT cat_id,cat_name,cat_desc, series_img ,price_zone_min,price_zone_max FROM event_category WHERE cat_id = ". $sid;
        $query=$this->db->query($sql);
        $result=$this->processingData($query);
        
        if(isset($result[0])){
        	$result[0]["price_zone_min"]=$this->price->doFormatMoney($result[0]["price_zone_min"]);
        	$result[0]["price_zone_max"]=$this->price->doFormatMoney($result[0]["price_zone_max"]);
            return $result[0];
        }else{
            return false ;
        }
    }
    /**
     *根据用户浏览器存放的cookie获得用户浏览过的车系
     * @param  $sidCookiArray
     */
    public function getLogoutViewedSeries($sidCookie){
        $sidArray=array_reverse(explode(",",$sidCookie));
        $series=array();
        foreach($sidArray as $row){
            $sid=intval($row);
            if($sid!=0){
                array_push($series,$this->getSeriesInfo($sid));
            }
        }
        return $series;
    }
    /**
     * 根据车系名称生成包含拼音字母的搜索关键字
     */
    public function generateKeywords(){
        $sql="SELECT cat_id,cat_name,keywords  FROM event_category ";
        $query=$this->db->query($sql);
        $arr=$query->result_array();
        foreach($arr as $row){
            if(empty($row['keywords'])){
                $keywords=$row['cat_name'];
                $keywords.=$this->pinyin->getPinyin($row['cat_name'],"UTF-8");
                $sql2="UPDATE  event_category SET keywords = ? WHERE cat_id = ?";
                $this->db->query($sql2,array($keywords,$row['cat_id']));
            }
        }
    }
    /**
     * 处理查询所得数据结果
     * is_concerned 是否被当前用户关注
     * can_reader   车系是否有文章可阅读
     * @param  $query CI的数据库查询结果对象
     */
    public function processingData($query){
        $uid=$this->session->userdata("uid");
        if($uid){
            $userConcernedSid=$this->concerned->getUserConcernedSid($uid);
        }
        $canReaderSeries=$this->getCanReaderSeriesID();
        $resultArray=array();
        $arr=$query->result_array();
        foreach($arr as $row){
            if($uid&&in_array($row['cat_id'],$userConcernedSid)){
                $row['is_concerned']=1;
            }else{
                $row['is_concerned']=0;
            }
            if(in_array($row['cat_id'],$canReaderSeries)){
                $row['can_reade']=1;
            }else{
                $row['can_reade']=0;
            }
            $row["series_img"]=empty($row["series_img"])?"no_picture.gif":$row["series_img"];
            $row['articles_num']=$this->article->getArticlesNumByScs($row['cat_id']);
            if($row['articles_num']!=0){
            	array_push($resultArray,$row);
            }
        }
        return $resultArray;
    }
    /**
     * 获得车系下的所有车款
     * @param  $sid
     */
    public function getCars($sid){
        $subSeriesSql="SELECT  cat_id, cat_name  FROM event_category WHERE parent_id= ? ORDER BY cat_name DESC";
        $querySubSeries=$this->db->query($subSeriesSql,array($sid));
        $result=array();
        $arr=$querySubSeries->result_array();
        foreach($arr as $row){
            $carsSql="SELECT goods_id,goods_name  FROM goods WHERE cat_id= ? ORDER BY goods_id DESC";
            $queryCars=$this->db->query($carsSql,array($row['cat_id']));
            $row['cars']=$queryCars->result_array();
            if($queryCars->num_rows()!=0){
                array_push($result,$row);
            }
        }
        return $result;
    }
    /**
     * 搜素车系
     */
    public function searchSeries($key,$numb="all"){
        $sql="SELECT sub_s.cat_id,sub_s.cat_name,sub_s.cat_desc, sub_s.series_img
              FROM event_category AS sub_s ,event_category AS s LEFT JOIN event_category AS b
              ON s.parent_id=b.cat_id
              WHERE  b.parent_id= 1307 AND sub_s.parent_id=s.cat_id AND (sub_s.keywords LIKE '%$key%' OR b.keywords LIKE '%$key%' )
              ";
        if($numb!="all")
        $sql.=" LIMIT ".$numb;//die($sql);
        $query=$this->db->query($sql);
        return $this->processingData($query);
    }

}

