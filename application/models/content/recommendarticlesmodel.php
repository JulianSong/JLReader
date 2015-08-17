<?php
/**
 * 
 * 推荐文章model
 * @author yeduo
 *
 */
class Recommendarticlesmodel extends CI_Model{
	/**
	 * 根据ArticleID获取读过这篇文章有60s的所有用户id
	 * @author yeduo 2011.11.1
	 * @param $ArticleID
	 */
	public function getArrUidByArticleID($ArticleID){
		$sql="SELECT uid FROM event_history WHERE ArticleID = ".$ArticleID." AND duration=60";
        $query=$this->db->query($sql);
        $arr_uid=array();
        if($query->num_rows() > 0){
	        $arr_uid=array();
	        foreach ($query->result_array() as $row)
			{
				array_push($arr_uid, $row['uid']);
			}
        }
        return $arr_uid;
	}
	
	/**
	 * 根据用户id获取所有读过60s的文章，并按照读的数量多到少排序
	 * @author yeduo 2011.11.1
	 * @param $arr_uid
	 * @param $limit获取的数量 为空表示全部获取
	 */
	public function getArrArticleIDByArrUid($arr_uid,$limit_num){
		$arr_ArticleID = array();
		if(!empty($arr_uid))
		{
			$limit='';
			if($limit_num!=''){
				$limit="LIMIT 0,".$limit_num;
			}
			$str_uid = '';
			$str_uid=implode(',', $arr_uid);//用逗号分隔的字符串
			$sql="SELECT ArticleID FROM event_history 
			WHERE duration=60 AND uid IN(".$str_uid." )
			GROUP BY ArticleID 
			ORDER BY COUNT(ArticleID) DESC ".$limit;
	        $query=$this->db->query($sql);
	        
			$arr_ArticleID=array();
	        foreach ($query->result_array() as $row)
			{
				array_push($arr_ArticleID, $row['ArticleID']);
			}
		}
        return $arr_ArticleID;
	}
	
	/**
	 * 根据ArticleID获取用户读得最多的文章,除去本身
	 * @author yeduo 2011.11.1
	 * @param unknown_type $ArticleID
	 * @param unknown_type $limit_num
	 */
	public function getCommendArticleFromHistory($ArticleID,$limit_num){
		$arr_uid = $this->getArrUidByArticleID($ArticleID);
		$arr_ArticleID = $this->getArrArticleIDByArrUid($arr_uid, $limit_num);
		$arr_data=array();
		if(!empty($arr_ArticleID))
		{
			foreach ($arr_ArticleID as $AID){
				if($AID!=$ArticleID){
					$sql="SELECT ArticleID,Title FROM event_spider_articles WHERE ArticleID = ".$AID;
		        	$query=$this->db->query($sql);
					if($query->num_rows() > 0){
						foreach ($query->result_array() as $row){
							array_push($arr_data, $row);
						}
					}
				}
			}
		}
        return $arr_data;
	}
	
	/**
	 * 根据文章ID查询记录
	 * Enter description here ...
	 * @param unknown_type $ArticleID
	 */
	public function getAriticleInfo($ArticleID){
		$sql="SELECT Title,history_count,favorite_count FROM event_spider_articles WHERE ArticleID = ".$ArticleID;
        $query=$this->db->query($sql);
        $data=array();
		if($query->num_rows() > 0){
	        foreach ($query->result_array() as $row)
			{
				$data['Title']=$row['Title'];
				$data['history_count']=$row['history_count'];
				$data['favorite_count']=$row['favorite_count'];
			}
		}
		return $data;
	}
	
	/**
	 * 获得所有的浏览记录
	 */
	public function getHistories(){
		$sql = "SELECT Title,ArticleID,history_count FROM event_spider_articles 
			 WHERE history_count<>0";

		$query=$this->db->query($sql);
		$result=$query->result_array();
		return $result;
	}
	
	/**
	 * 获得所有收藏记录
	 */
	public function getFavorite(){
		
		$sql = "SELECT Title,ArticleID,favorite_count FROM event_spider_articles 
			 WHERE favorite_count<>0";

		$query=$this->db->query($sql);
		$result=$query->result_array();
		return $result;
	}
}