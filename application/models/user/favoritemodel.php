<?php
/**
 * 
 * 收藏model
 * @author yeduo
 *
 */
class Favoritemodel extends CI_Model{
	
	/**
	 * 添加收藏
	 * @author yeduo 2011.10.18
	 * @param $uid
	 * @param $ArticleID
	 */
	public function addFavorite($uid,$ArticleID){
		$sql = "SELECT uid,is_del FROM event_favorite WHERE uid=".$uid." AND ArticleID=".$ArticleID;
        $query=$this->db->query($sql);
		$num=$query->num_rows();
		$now_time = $this->gmtime();
		//已经有收藏记录，查看是否删除
        if($num==1){
        	foreach ($query->result_array() as $row) {
				$is_del = $row['is_del'];
	     	}
	     	//已经有收藏，未删除
	     	if($is_del==0){
	     		return $num;
	     	//已有收藏，已删除
	     	}else if($is_del==1){
	     		$num_favorite = $this->getNumFavorite($uid);
	     		//修改收藏的删除状态为0未删除
        		$sql2="UPDATE event_favorite SET is_del = 0,add_time = ".$now_time." WHERE uid=".$uid." AND ArticleID=".$ArticleID;
				$this->db->query($sql2);
        		//记录收藏的次数
				$this->setFavoriteCount($ArticleID);
				return $num=0;//收藏成功
        		//if($num_favorite<50){//收藏文章限制50篇
        		if(true){//文章不限制收藏
        			
        			//修改收藏的删除状态为0未删除
        			$sql2="UPDATE event_favorite SET is_del = 0,add_time = ".$now_time." WHERE uid=".$uid." AND ArticleID=".$ArticleID;
					$this->db->query($sql2);
	        		//记录收藏的次数
					$this->setFavoriteCount($ArticleID);
					return $num=0;//收藏成功
	        	}else{
		        	return $num=2;//收藏已经超过限定	
	        	}
	     	}
        	
        //没有收藏
        }else if($num==0){
        	$num_favorite = $this->getNumFavorite($uid);
        	//if($num_favorite<50){//收藏文章限制50篇
        	if(true){//文章不限制收藏
	        	$data = array(
					'uid'			=> $uid,
					'ArticleID'		=> $ArticleID,
					'add_time'		=> $now_time
				);
				$this->db->insert('event_favorite',$data);
				//记录收藏的次数
				$this->setFavoriteCount($ArticleID);
				return $num;//收藏成功
        	}else{
	        	return $num=2;//收藏已经超过限定	
        	}
        }
	}
	
	/**
	 * 记录收藏的次数
	 * @author yeduo 2011.10.31
	 * @param unknown_type $ArticleID
	 */
	public function setFavoriteCount($ArticleID){
		//搜索文章表event_spider_articles，获取当前次数
		$sql = "SELECT favorite_count FROM event_spider_articles WHERE ArticleID=".$ArticleID;
		$query=$this->db->query($sql);
		$num=$query->num_rows();
        if($num==1){
        	//有记录,更新收藏记录
        	foreach ($query->result_array() as $row) {
				$favorite_count = $row['favorite_count'];
	     	}
	     	$new_favorite_count = $favorite_count + 1;//次数加一
	     	$sql2="UPDATE event_spider_articles SET favorite_count = ".$new_favorite_count." WHERE ArticleID=".$ArticleID;
			$this->db->query($sql2);
        }
	}
	
	/**
	 * 移除收藏
	 * @author yeduo 2011.10.18
	 * @param $uid
	 * @param $ArticleID
	 */
	public function delFavorite($uid,$ArticleID){
		$sql = "SELECT uid FROM event_favorite WHERE uid=".$uid." AND ArticleID=".$ArticleID;
        $query=$this->db->query($sql);
		$num=$query->num_rows();
        if($num==1){
        	$sql2="UPDATE event_favorite SET is_del = 1 WHERE uid=".$uid." AND ArticleID=".$ArticleID;
			$this->db->query($sql2);
        	return $num;//有收藏，移除成功
        }else if($num==0){
			return $num;//没有收藏，移除失败
        }
	}
	
	/**
	 * 获得所有收藏的信息
	 * @author yeduo 2011.10.18
	 * @param $uid
	 * @param $limit
	 */
	public function getAllFavorite($uid,$limit,$order){
		if($limit==''){//获取所有收藏
			$sql = "SELECT art.Title,fav.add_time,fav.uid,fav.ArticleID FROM event_favorite AS fav LEFT JOIN event_spider_articles AS art
				 ON fav.ArticleID=art.ArticleID WHERE fav.uid=".$uid." AND fav.is_del = 0 ORDER BY fav.add_time ".$order;
		}else{
			$sql = "SELECT art.Title,fav.add_time,fav.uid,fav.ArticleID FROM event_favorite AS fav LEFT JOIN event_spider_articles AS art
				 ON fav.ArticleID=art.ArticleID WHERE fav.uid=".$uid." AND fav.is_del = 0 ORDER BY fav.add_time ".$order." LIMIT ".$limit;
		}
		$query=$this->db->query($sql);
		$result=$query->result_array();
		$i=0;
		foreach ($result as $row) {
			$result[$i]['add_time']=date('Y-m-d H:i',$row['add_time']);
			$i++;
        }
		return $result;
	}
	
	/**
	 * 获得所有收藏的ArticleID（逗号隔开）
	 * @author yeduo 2011.11.8
	 * @param $uid
	 */
	public function getAllFavoriteArticleID($uid){
		if(empty($uid)){
			return $this->getAllFavoriteArticleIDCookie();
		}
		$sql = "SELECT ArticleID FROM event_favorite WHERE uid=".$uid." AND is_del = 0";
		$query=$this->db->query($sql);
		$result = array();
		$str_ArticleID="";
		if($query->num_rows() > 0){
	        foreach ($query->result_array() as $row)
			{
				array_push($result, $row['ArticleID']);
			}
			$str_ArticleID=implode(',', $result);//用逗号分隔的字符串
        }
		return $str_ArticleID;
	}
	
	/**
	 * 获得收藏的数量
	 * @author yeduo 2011.10.21
	 * @param $uid
	 */
	public function getNumFavorite($uid){
		$sql = "SELECT COUNT(ArticleID) AS num FROM event_favorite WHERE uid=".$uid." AND is_del = 0";
        $query=$this->db->query($sql);
        foreach ($query->result_array() as $row)
		{
		   $num = $row['num'];
		}
		return $num;
	}
	
	/**
	 * 获得当前格林威治时间的时间戳
	 *
	 * @return  integer
	 */
	function gmtime()
	{
	    return (time() - date('Z'));
	}

	/**
	 * 未登录用户收藏**************************************************************
	 */
	

	/**
	 * 从cookie中获得所有收藏的信息
	 * @author yeduo 2011.10.18
	 * @param $uid
	 * @param $limit
	 */
	public function getAllFavoriteCookie(){
//		$favorite_aid = $this->getAllFavoriteArticleIDCookie();
//		if($favorite_aid==""){
//			return;//没有数据
//		}
//		$sql = "SELECT Title,ArticleID FROM event_spider_articles WHERE ArticleID IN (".$favorite_aid.") ORDER BY ArticleID";
//		$query=$this->db->query($sql);
//		$result=$query->result_array();
//		return $result;
		
		$this->load->helper('cookie');
		$favorite = $this->input->cookie("myfavorite",TRUE);
		if($favorite==""){
			return;//没有数据
		}else{
			$favorite_arr = unserialize($favorite);
			
			$result=array();
			foreach ($favorite_arr as $row)
			{
				$sql = "SELECT Title,ArticleID FROM event_spider_articles WHERE ArticleID = ".$row['ArticleID'];
				$query=$this->db->query($sql);
				$one=$query->result_array();
				array_push($result, $one[0]);
			}
			return $result;
		}
	}
	
	/**
	 * 从cookie获得所有收藏的ArticleID（逗号隔开）
	 * Enter description here ...
	 */
	public function getAllFavoriteArticleIDCookie(){
		$this->load->helper('cookie');
		$favorite = $this->input->cookie("myfavorite",TRUE);
		$str_ArticleID="";
		if($favorite==""){
			$str_ArticleID="";
		}else{
			$favorite_arr = unserialize($favorite);
			
			$result=array();
			foreach ($favorite_arr as $row)
			{
				array_push($result, $row['ArticleID']);
			}
			$str_ArticleID=implode(',', $result);//用逗号分隔的字符串
		}
		return $str_ArticleID;
	}
	
	/**
	 * 未登录用户记录收藏到cookie
	 * 最多收藏10个
	 * Enter description here ...
	 * @param unknown_type $ArticleID
	 */
	public function addFavoriteCookie($ArticleID){
		$this->load->helper('cookie');
        $favorite = $this->input->cookie("myfavorite",TRUE);
		if($favorite==""){
			$favorite_arr=array();
			$now_time=$this->gmtime();
			$favorite_new=array("ArticleID"=>$ArticleID,"addtime"=>$now_time);
			array_push($favorite_arr, $favorite_new);
			$favorite = serialize($favorite_arr);
			$cookie = array(
                   'name'   => 'myfavorite',
                   'value'  => $favorite,
                   'expire' => '2592000',
                   'domain' => '',
                   'path'   => '/',
                   'prefix' => '',
        	);
        	$this->input->set_cookie($cookie);
        	return 3;//成功
		}else{
			$favorite_arr = unserialize($favorite);
			if(count($favorite_arr)<10){
				$favorite_new=array("ArticleID"=>$ArticleID,"addtime"=>$this->gmtime());
				array_push($favorite_arr, $favorite_new);
				$favorite = serialize($favorite_arr);
				$cookie = array(
	                   'name'   => 'myfavorite',
	                   'value'  => $favorite,
	                   'expire' => '2592000',
	                   'domain' => '',
	                   'path'   => '/',
	                   'prefix' => '',
	        	);
	        	delete_cookie("myfavorite");//清除Cookie
				$this->input->set_cookie($cookie);
				return 3;//成功
			}else{
				return 4;//超过10个了
			}
		}
	}
	
	/**
	 * 未登录用户从cookie删除收藏
	 * Enter description here ...
	 * @param unknown_type $ArticleID
	 */
	public function delFavoriteCookie($ArticleID){
		$this->load->helper('cookie');
		$favorite = $this->input->cookie("myfavorite",TRUE);
		$favorite_arr = unserialize($favorite);
		$favorite_new=array();
		$j=0;
		foreach ($favorite_arr as $row)
		{
			if($ArticleID!=$row['ArticleID']){
				$favorite_new[$j]=$row;
				$j++;
			}
		}
		$favorite = serialize($favorite_new);
		$cookie = array(
                   'name'   => 'myfavorite',
                   'value'  => $favorite,
                   'expire' => '2592000',
                   'domain' => '',
                   'path'   => '/',
                   'prefix' => '',
        );
        delete_cookie("myfavorite");//清除Cookie
		$this->input->set_cookie($cookie);
	}
	
	/**
	 * 检测Cookie中的收藏比原来新增了多少
	 * Enter description here ...
	 */
	public function checkFavoriteCookie($uid){
		$this->load->helper('cookie');
		$favorite = $this->input->cookie("myfavorite",TRUE);
		if($favorite==""){
			return $num=0;
		}
		$favorite_cookie=unserialize($favorite);
		$favorite_cookie_num=count($favorite_cookie);
		$result=array();
		foreach ($favorite_cookie as $row)
		{
			array_push($result, $row['ArticleID']);
		}
		$str_ArticleID=implode(',', $result);//用逗号分隔的字符串
		$sql = "SELECT COUNT(ArticleID) AS num FROM event_favorite WHERE ArticleID IN(".$str_ArticleID.") AND uid=".$uid." AND is_del = 0";
        $query=$this->db->query($sql);
        foreach ($query->result_array() as $row)
		{
		   $num = $row['num'];
		}
		return $favorite_cookie_num-$num;
	}
	
	/**
	 * 合并Cookie中的收藏到我的收藏中
	 * Enter description here ...
	 */
	public function combineFavoriteCookie($uid){
		$this->load->helper('cookie');
		$favorite = $this->input->cookie("myfavorite",TRUE);
		if($favorite==""){
			return $OK_num=0;
		}
		$favorite_cookie=unserialize($favorite);
		$OK_num=0;
		foreach ($favorite_cookie as $row)
		{
			$result = $this->addFavorite($uid, $row['ArticleID']);
			if($result==0){
				//收藏成功数目
				$OK_num++;
			}
		}
		delete_cookie("myfavorite");//清除Cookie
		return $OK_num;
	}
}