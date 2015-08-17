<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 关注车系model
 * @author julian
 */
class ConcernedModel extends CI_Model{
    /**
     * 类的初始化方法加载其他model及类库
     */
    public function __construct(){
        parent::__construct();
        $this->load->model('content/ArticleModel','article');
    }
    /**
     * 添加一个concerned
     * @param  $sid 车系id
     * @param  $uid 用户id
     */
    public function addConcerned($sid,$uid){
        $sql="INSERT INTO event_concerned_series VALUES(?,?)";
        if(!$this->existConcerned($sid,$uid))
        return $this->db->query($sql, array($uid,$sid));
    }
    /**
     * 删除一个concerned
     * @param  $sid 车系id
     * @param  $uid 用户id
     */
    public function delConcerned($sid,$uid){
        $sql="DELETE FROM   event_concerned_series WHERE uid= ? AND SeriesID= ?";
        return $this->db->query($sql, array($uid,$sid));
    }
    /**
     * 关注是否存在
     * @param  $sid 车系id
     * @param  $uid 用户id
     */
    public function existConcerned($sid,$uid){
        $query=$this->getConcerned($sid,$uid);
        return $query->num_rows();
    }
    /**
     * 获得用户关注的车系
     * @param  $uid 用户id
     */
    public function getUserConcerned($uid){
        $sql="SELECT ec.cat_id,ec.cat_name, ec.series_img,ec.spellfirstchar
            FROM event_category AS ec
            RIGHT  JOIN event_concerned_series AS ecs
            ON ec.cat_id = ecs.SeriesID
            WHERE uid=?
            GROUP BY  ec.cat_id
            ORDER BY ec.spellfirstchar";
        $query=$this->db->query($sql, array($uid));
        $result=array();
        foreach($query->result_array() as $row){
            $row['articles_num']=$this->article->getArticlesNumByScs($row['cat_id']);
            if(empty($row['series_img'])){
              $row['series_img']="no_picture.gif";
            }
            array_push($result,$row);
        }
        return $result;
    }
    /**
     * 获得用户关注的车系的id
     * @param  $uid 用户id
     */
    public function getUserConcernedSid($uid){
        $sql="SELECT SeriesID  FROM   event_concerned_series WHERE uid= ? ";
        $query=$this->db->query($sql, array($uid));
        $result=array();
        foreach($query->result_array() as $row){
            array_push($result,$row['SeriesID']);
        }
        return $result;
    }
    /**
     * 获得一个concerned
     * @param  $sid 车系id
     * @param  $uid 用户id
     */
    public function getConcerned($sid,$uid){
        $sql="SELECT * FROM  event_concerned_series WHERE uid=? AND SeriesID=?";
        $query=$this->db->query($sql, array($uid,$sid));
        return $query;
    }
}