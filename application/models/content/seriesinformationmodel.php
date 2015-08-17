<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 用户笔记搜索内容到车型页面显示
 * @author yeduo
 */
class SeriesInformationModel extends CI_Model{
	/**
	 * 获取车系下所有车款的属性
	 * Enter description here ...
	 * @param $SeriesID
	 */
	public function getSeriesInformation($SeriesID){
		//获得车系下的所有车款
		$cars_arr=$this->getCarsBySeriesID($SeriesID);
		$result=array();
		foreach ($cars_arr as $key_year=>$year){
			foreach($year['cars'] as $key_car=>$car){
				$cid=$car['goods_id'];
				//获得车款的参数
				$carinformation_arr=$this->getCarsAttribute($cid);
				$car['information']=$carinformation_arr;
				/**
				 * 只取前两年的车款
				 * date("Y")-2：当前年份的前2年
				 */
				if(@$car['information'][6]>date("Y")-2){
					array_push($result,$car);
				}
			}
		}
		return $result;
	}
	
	/**
     * 获得车系下的所有车款
     * @param  $sid
     */
    public function getCarsBySeriesID($sid){
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
     * 获得车款的所有属性
     * @param  $cid车款id
     */
    public function getCarsAttribute($cid){
       	$sql = "SELECT a.attr_id,g.attr_value " .
            'FROM  goods_attr  AS g ' .
            'LEFT JOIN attribute  AS a ON a.attr_id = g.attr_id ' .
            "WHERE g.goods_id = ?  " .
            'ORDER BY a.attr_id';
       	$query=$this->db->query($sql,array($cid));
       	$attributes=$query->result_array();
       	$result=array();
       	foreach ($attributes as $key=>$row){
       		$attr_id=$row['attr_id'];
       		$attr_value=$row['attr_value'];
       		$result[$attr_id]=$attr_value;
       	}
       	return $result; 
    }
    
	/**
     * 获得所有属性分组
     */
    public function getAllAttributesGroup(){
        $sql="SELECT  attr_group FROM goods_type WHERE cat_id=2";
        $query=$this->db->query($sql);
        $result=$query->first_row('array');
        $result = explode("\n", strtr($result['attr_group'], "\r", ''));
        return $result;
    }
    
	/**
     * 获得所有属性
     */
    public function getAllAttributes(){
        $sql="SELECT  attr_id,cat_id,attr_name,attr_group FROM attribute WHERE attr_group NOT IN(0) ORDER BY attr_id";
        $query=$this->db->query($sql);
        $result=$query->result_array();
        
    	$attributeGroup=$this->getAllAttributesGroup();
       	$attributes=array();
       	foreach($attributeGroup as $key=>$grow){
       	    $group=array();
       	    $group['group_name']=$grow;
       	    $group['attribute']=array();
       	    foreach($result as $row){
       	        if($row['attr_group']==$key){
       	            array_push($group['attribute'],$row);
       	        }
       	    }
       	   if(count($group['attribute'])!=0)
       	    array_push($attributes,$group);
       	}
        return $attributes;
    }
}
