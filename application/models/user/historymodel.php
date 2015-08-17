<?php
/**
 * 
 * 历史记录model
 * @author yeduo
 *
 */
class Historymodel extends CI_Model{
	
	/**
	 * 记录打开文章内页的时间
	 * 规则：先查看有没有记录，没有则新插入，有则更新打开时间
	 * 查询该文章有没有阅读够60秒，没有则把duration置零
	 * @author yeduo 2011.10.19
	 * @param unknown_type $uid
	 * @param unknown_type $ArticleID
	 */
	public function setStartHistory($uid,$ArticleID){
		$sql = "SELECT uid,duration FROM event_history WHERE uid=".$uid." AND ArticleID=".$ArticleID;
        $query=$this->db->query($sql);
		$num=$query->num_rows();
        if($num==1){
        	//已经有记录,返回阅读时间
        	foreach ($query->result_array() as $row) {
				$duration = $row['duration'];
				if($duration<60){
					$duration = 0;
				}
	        }
        	//已经有记录,更新打开时间,设置阅读时间
        	$now_time = $_SERVER['REQUEST_TIME'];
	        $sql2="UPDATE event_history SET time = ".$now_time.",duration = ".$duration." WHERE uid=".$uid." AND ArticleID=".$ArticleID;
			$this->db->query($sql2);
        	return $duration;
        }else if($num==0){
        	//没有记录，添加一条记录
        	$time = $_SERVER['REQUEST_TIME'];
        	$data = array(
				'uid'			=> $uid,
				'ArticleID'		=> $ArticleID,
				'time'			=> $time,
        		'duration'		=> 0
			);
			$this->db->insert('event_history',$data);
			return $duration=0;
        }
	}
//	
//	/**
//	 * 记录关闭文章内页的持续时间
//	 * 规则：先查看有没有记录，没有则新插入，有则更新持续时间
//	 * @author yeduo 2011.10.19
//	 * @param unknown_type $uid
//	 * @param unknown_type $ArticleID
//	 */
//	public function setEndHistory($uid,$ArticleID){
//		$sql = "SELECT time,duration FROM event_history WHERE uid=".$uid." AND ArticleID=".$ArticleID;
//        $query=$this->db->query($sql);
//        $num=$query->num_rows();
//        if($num==1){//有记录
//			foreach ($query->result_array() as $row) {
//				$time = $row['time'];
//				$duration = $row['duration'];
//	        }
//			$now_time = $_SERVER['REQUEST_TIME'];
//			$new_duration = $now_time - $time + $duration;
//	        $sql2="UPDATE event_history SET duration = ".$new_duration." WHERE uid=".$uid." AND ArticleID=".$ArticleID;
//			$this->db->query($sql2);
//        }else{//无记录,则记录一条新记录
//        	$this->setStartHistory($uid, $ArticleID);
//        }
//		return $result=0;
//	}
	
	/**
	 * 更新文章内页阅读的时间
	 * 规则：先查看有没有记录，没有则新插入，有则更新持续时间
	 * @author yeduo 2011.10.31
	 * @param unknown_type $uid
	 * @param unknown_type $ArticleID
	 * @param unknown_type $count_time 计时的时间 毫秒
	 */
	public function setDuration($uid,$ArticleID,$count_time){
		$sql = "SELECT time,duration FROM event_history WHERE uid=".$uid." AND ArticleID=".$ArticleID;
        $query=$this->db->query($sql);
        $num=$query->num_rows();
        if($num==1){//有记录
			foreach ($query->result_array() as $row) {
				$duration = $row['duration'];
	        }
	        if($duration<60){
				$new_duration = $count_time/1000 + $duration;
				//累加时间
				$sql2="UPDATE event_history SET duration = ".$new_duration." WHERE uid=".$uid." AND ArticleID=".$ArticleID;
				$this->db->query($sql2);
				//当阅读时间够60s则往文章列表event_spider_articles记录一条浏览历史记录数量
				if($new_duration>59){
					$this->setHistoryCount($ArticleID);
				}
	        }else{
	        	$new_duration = $duration;
	        }
			return $new_duration;
        }else{//无记录,则记录一条新记录
        	$this->setStartHistory($uid, $ArticleID);
        	return $duration=0;
        }
	}
	
	/**
	 * 记录浏览的次数
	 * @author yeduo 2011.11.1
	 * @param unknown_type $ArticleID
	 */
	public function setHistoryCount($ArticleID){
		//搜索文章表event_spider_articles，获取当前次数
		$sql = "SELECT history_count FROM event_spider_articles WHERE ArticleID=".$ArticleID;
		$query=$this->db->query($sql);
		$num=$query->num_rows();
        if($num==1){
        	//有记录,更新浏览记录
        	foreach ($query->result_array() as $row) {
				$history_count = $row['history_count'];
	     	}
	     	$new_history_count = $history_count + 1;//次数加一
	     	$sql2="UPDATE event_spider_articles SET history_count = ".$new_history_count." WHERE ArticleID=".$ArticleID;
			$this->db->query($sql2);
        }
	}
	
	/**
	 * 获得当天的历史记录
	 * @author yeduo 2011.10.19
	 * @param $uid
	 * @param $limit
	 */
	public function getHistories($uid,$limit){
		// 当天的零点
		$today = strtotime(date('Y-m-d', time()));
		// 当天的24
		$end = $today + 24 * 60 * 60;
		
		if($limit==''){//获取所有历史记录
			$sql = "SELECT art.Title,his.time,his.uid,his.ArticleID FROM event_history AS his LEFT JOIN event_spider_articles AS art
				 ON his.ArticleID=art.ArticleID WHERE his.uid=".$uid." AND time> ".$today." AND time< ".$end.
				 " ORDER BY his.time DESC;";
		}else{
			$sql = "SELECT art.Title,his.time,his.uid,his.ArticleID FROM event_history AS his LEFT JOIN event_spider_articles AS art
				 ON his.ArticleID=art.ArticleID WHERE his.uid=".$uid." AND time> ".$today." AND time< ".$end.
				 " ORDER BY his.time DESC LIMIT ".$limit;
		}
		$query=$this->db->query($sql);
		$result=$query->result_array();
		$i=0;
		foreach ($result as $row) {
			$result[$i]['time']=date('Y-m-d H:i',$row['time']);
			$i++;
        }
		return $result;
	}
	
	/**
	 * 获得当天浏览文章的数量
	 * @author yeduo 2011.10.21
	 * @param $uid
	 */
	public function getNumHistory($uid){
		// 当天的零点
		$today = strtotime(date('Y-m-d', time()));
		// 当天的24
		$end = $today + 24 * 60 * 60;
		$sql = "SELECT COUNT(ArticleID) AS num FROM event_history WHERE uid=".$uid." AND time> ".$today." AND time< ".$end;
        $query=$this->db->query($sql);
        foreach ($query->result_array() as $row)
		{
		   $num = $row['num'];
		}
		return $num;
	}
}