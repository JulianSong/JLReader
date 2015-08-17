<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 用户笔记搜索内容到车型页面显示
 * @author yeduo
 */
class SeriesCommentModel extends CI_Model{
    /**
     * 根据用户id，车系id和标签获得用户的一条笔记
     * @param  $uid 用户ID，逗号隔开
     * @param  $SeriesID 车系ID
     * @param  $tag 标签
     */
    public function getOneNoteByTag($uid,$SeriesID,$tag){
    	$str_uid=implode(',', $uid);//用逗号分隔的字符串
        $sql="SELECT ArticleID, content , note_tag
            FROM event_user_note 
            WHERE uid IN(".$str_uid.") AND SeriesID = ".$SeriesID." AND note_tag LIKE '".$tag."' ORDER BY time DESC LIMIT 1";
        $query=$this->db->query($sql);
    	if(!$query->num_rows()){
            return false;
        }
        $resultArray=$query->first_row("array");
        return $resultArray;
    }
    
    /**
     * 根据用户id，车系id和不搜索的标签获得用户的一条笔记
     * @param  $uid 用户ID，逗号隔开
     * @param  $SeriesID 车系ID
     * @param  $tag 不搜索的标签
     * @param  $limit 搜索的数量
     */
    public function getAllNoteByTag($uid,$SeriesID,$tag_arr,$limit){
    	$str_uid=implode(',', $uid);//用逗号分隔的字符串
    	$str_tag=implode(',', $tag_arr);//用逗号分隔的字符串
        $sql="SELECT ArticleID, content , note_tag
            FROM event_user_note 
            WHERE uid IN(".$str_uid.") AND SeriesID = ".$SeriesID." AND note_tag NOT IN(".$str_tag.") ORDER BY time DESC ";
        if($limit!=''){
        	$sql=$sql." LIMIT ".$limit;
        }
        $query=$this->db->query($sql);
        if(!$query->num_rows()){
            return false;
        }
        $resultArray=$query->result_array();
        return $resultArray;
    }
    
}
