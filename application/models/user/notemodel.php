<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 用户笔记
 * @author julian
 */
class NoteModel extends CI_Model{
    /**
     * 添加用户笔记
     * @param  $uid 用户id
     * @param  $aid 文章id
     * @param  $sid 车系id
     * @param  $noteContent 笔记内容
     * @param  $time 时间
     */
    public function addNote($uid,$aid,$sid,$noteContent,$type=1,$time="auto_get"){
    	//先检测sid是否是aid的车系，是则添加，不是则把sid换成对的sid
    	$sql_sid="SELECT SeriesID FROM event_article_series WHERE ArticleID=".$aid;
    	$query_sid=$this->db->query($sql_sid);
    	$reslut=$query_sid->result_array();
    	$sid_arr=array();
    	foreach ($reslut as $row){
    		array_push($sid_arr,$row['SeriesID']);
    	}
    	if(in_array($sid, $sid_arr)){
    		//sid是aid的车系
    		
    	}else{
    		//sid是aid的车系
    		$sid=$sid_arr[0];
    	}
        $sql="INSERT INTO event_user_note (`uid`, `ArticleID`, `SeriesID`, `content`,`time`,`note_type`) VALUES (?,?,?,?,?,?)";
        if($time==="auto_get"){
            $time=time();
        }
        $noteContent=trim($noteContent);
        return $this->db->query($sql,array($uid,$aid,$sid,$noteContent,$time,$type));
    }
    /**
     *
     * @param $uid
     * @param $nid
     * @param $noteContent
     */
    public function editNoteContent($uid,$nid,$noteContent){
        $sql="UPDATE   event_user_note SET content = ? WHERE uid = ? AND note_id= ?";
        return $this->db->query($sql,array($noteContent,$uid,$nid));
    }
    /**
     *
     * @param $uid
     * @param $nid
     * @param $noteTag
     */
    public function editNoteTag($uid,$nid,$noteTag){
        $sql="UPDATE   event_user_note SET note_tag = ? WHERE uid = ? AND note_id= ?";
        return $this->db->query($sql,array($noteTag,$uid,$nid));
    }
    /**
     * 删除用户的一条笔记
     * @param  $uid 用户ID
     * @param  $nid 笔记ID
     */
    public function delUserNote($uid,$nid){
        $sql="DELETE FROM event_user_note WHERE uid = ? AND note_id= ?";
        return $this->db->query($sql,array($uid,$nid));
    }
    /**
     * 获得笔记
     * @param  $uid 用户id
     * @param  $paramArray 参数数组
     */
    public function getNotes($paramArray=array()){
        $sql="SELECT eun.note_id, eun.uid, eun.ArticleID, eun.SeriesID,ec.cat_name,eun.content , eun.note_tag ,eun.time,eun.note_type
            FROM event_user_note AS eun LEFT JOIN event_category AS ec
            ON ec.cat_id=eun.SeriesID
            WHERE ";
        if(isset($paramArray['uid'])){
            $sql.="  uid = ".$paramArray['uid'];
        }
        if(isset($paramArray['start'])){
            $sql.=" AND time > ".$paramArray['start'];
        }
        if(isset($paramArray['end'])){
            $sql.=" AND time < ".$paramArray['end'];
        }
        if(isset($paramArray['tag'])){
            $sql.=" AND note_tag = '".$paramArray['tag']."'";
        }
        if(isset($paramArray['ids'])){
            $sql.=" AND note_id in ( ".$paramArray['ids']." )";
        }
        $sql.=" ORDER BY eun.time DESC " ;
        if(isset($paramArray['limit'])){
            $sql.=" LIMIT  ".$paramArray['limit'];
        }
        $query=$this->db->query($sql);
        $resultArray=array();
        $resultArray['length']=$query->num_rows();
        $resultArray['data']=array();
        $date=array();
        $date['date_time']=strtotime("today");
        $date['notes']=array();
        $date_notes_numb=0;
        $num_rows=$query->num_rows();
        foreach($query->result_array() as $row){
        	/**
             * 获取相关车系
             * by yeduo 2011.11.30
             */
        	$relate=array();
        	if($row['ArticleID']>0){
				$relatesql="SELECT 
	                       a.SeriesID,c.cat_name 
	                       FROM event_article_series AS a LEFT JOIN event_category AS c ON a.SeriesID = c.cat_id
	                       WHERE a.ArticleID =".$row['ArticleID'];
	            $relatequery=$this->db->query($relatesql);
                $row['relate']=$relatequery->result_array();
        	}else{
        		$row['relate']=array();
        	}
            
            $num_rows--;
            $row['ftime']=date("H:i:s",$row['time']);
            $row['fdate']=date("Y-m-d",$row['time']);
            if($row['note_type']==2){
                $asql="SELECT 
                       a.attr_name,g.attr_value 
                       FROM goods_attr AS g LEFT JOIN attribute AS a ON a.attr_id = g.attr_id
                       WHERE g.goods_attr_id IN(".$row['content'].")";
                $aquery=$this->db->query($asql);
                $row['content']=$aquery->result_array();
            }
            //echo $date['date_time'].",".$date['date_time']."<br>";
            if($date['date_time']==strtotime($row['fdate'])){
                array_push($date['notes'],$row);
                $date_notes_numb++;
            }else{
                $date['date_time']=date("Y年m月d日",$date['date_time']);
                if($date_notes_numb!=0){
                    array_push($resultArray['data'],$date);
                }
                $date_notes_numb=0;
                $date['date_time']=strtotime($row['fdate']);
                $date['notes']=array();
                array_push($date['notes'],$row);
                $date_notes_numb++;
            }
            //
            if($num_rows==0){
                $date['date_time']=date("Y年m月d日",$date['date_time']);
                array_push($resultArray['data'],$date);
            }
            
        }
        return $resultArray;
    }
    /**
     * 获得用户的笔记的所有标签
     * @param  $uid 用户id
     */
    public function getNoteTags($uid){
        $sql="SELECT note_tag FROM event_user_note
              WHERE uid = ? ORDER BY note_tag";
        $query=$this->db->query($sql,array($uid));
        $result=array();
        foreach($query->result_array() as $row){
            if(!in_array($row['note_tag'],$result))
            array_push($result,$row['note_tag']);
        }
        return $result;
    }
    /**
     * 获得用户的一条笔记
     * @param  $uid 用户ID
     * @param  $nid 笔记ID
     */
    public function getUserNote($uid,$nid){
        $sql="SELECT eun.note_id, eun.uid, eun.ArticleID, eun.SeriesID,ec.cat_name,eun.content , eun.note_tag ,eun.time
            FROM event_user_note AS eun LEFT JOIN event_category AS ec
            ON ec.cat_id=eun.SeriesID
            WHERE eun.uid = ? AND eun.note_id= ?";
        $query=$this->db->query($sql,array($uid,$nid));
        if(!$query->num_rows()){
            return false;
        }
        $resultArray=$query->first_row("array");
        $resultArray['ftime']=date("Y/m/d H:i:s",$resultArray['time']);
        return $resultArray;
    }
    /**
     * 获得用户某天的笔记数量
     * @param  $uid
     * @param  $date
     */
    public function getUserDailyNotesNumb($uid,$month=0,$date=0,$year=0){
        $sql="SELECT note_id FROM event_user_note
              WHERE uid = ? ";
        if($date==0){
            $sql.=" AND time > ".strtotime("today");
        }else{
            $time=mktime(0,0,0,$month,$date,$year);
            $sql.=" AND time >= ".$time;
            $sql.=" AND time < ".($time+24*60*60);
        }
        $query=$this->db->query($sql,array($uid));
        return $query->num_rows();
    }
    /**
     * 获得用户笔记的月分布情况
     * @param  $uid
     */
    public function getNoteProfileOfMonth($uid,$year,$month){
        $calMonth=array();
        $daysInMonth=cal_days_in_month(CAL_GREGORIAN,$month,$year);
        $inday=1;
        for($ws=0;$ws<6;$ws++){
            $calWeek=array();
            for($date=0;$date<7;$date++){
                $jd=unixtojd(mktime(0,0,0,$month,$inday,$year));
                $calDay=cal_from_jd($jd,CAL_GREGORIAN);
                if($calDay['dow']==$date&$inday<=$daysInMonth){
                    $daily=array();
                    $daily['date']=$inday;
                    $daily['note_num']=$this->getUserDailyNotesNumb($uid,$month,$inday,$year);
                    array_push($calWeek,$daily);
                    $inday++;
                }else{
                    array_push($calWeek,0);
                }
            }
            array_push($calMonth,$calWeek);
        }
        return $calMonth;
    }
    /**
     *  合并笔记
     * @param  $nids 笔记id
     */
    public function mergeNotes($uid,$nids){
        $nidArray=explode(",",$nids);
        arsort($nidArray);
        $content='';
        $tag='';
        foreach($nidArray as $key=>$row){
            $note=$this->getUserNote($uid,$row);
            if(!$note){
                return false;
            }
            if($key!=0){
                if(!$this->delUserNote($uid,$row)){
                    return false;
                }
            }
            $content.=$note['content'];
            $tag.=$note['note_tag'];
        }
        if(!$this->editNoteContent($uid,$nidArray[0],$content)){
            return false;
        }
        if(!$this->editNoteTag($uid,$nidArray[0],$tag)){
            return false;
        }
        $result=array();
        $result['note_id']=$nidArray[0];
        $result['content']=$content;
        $result['tag']=$tag;
        array_shift($nidArray);
        $result['del_notes']=$nidArray;
        return $result;
    }
    
    /**
     * 设置笔记的相关车系
     * Enter description here ...
     * @param $uid
     * @param $notes_id
     * @param $series_id
     */
    public function editSeries($uid,$notes_id,$series_id){
    	$sql="UPDATE event_user_note SET SeriesID = ".$series_id." WHERE note_id=".$notes_id." AND uid=".$uid;
		return $this->db->query($sql);
    }
}
