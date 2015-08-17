<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 车款model
 * @author julian
 */
class CarModel extends CI_Model{
    /**
     * 获得所有属性分组
     */
    public function getAllAttributes(){
        $sql="SELECT  attr_group FROM goods_type WHERE cat_id=8";
        $query=$this->db->query($sql);
        $result=$query->first_row('array');
        $result = explode("\n", strtr($result['attr_group'], "\r", ''));
        return $result;
    }
    /**
     * 获得车款的所有属性
     * @param  $cid车款id
     */
    public function getCarsAttribute($cid){
       	$sql = "SELECT a.attr_id, a.attr_name, a.attr_group, a.attr_type, ".
                "g.goods_attr_id, g.attr_value " .
            'FROM  goods_attr  AS g ' .
            'LEFT JOIN attribute  AS a ON a.attr_id = g.attr_id ' .
            "WHERE g.goods_id = ? AND a.attr_id  NOT IN(13,14,15,119,120,138,141) " .
            'ORDER BY a.attr_group';
       	$query=$this->db->query($sql,array($cid));
       	$attributeGroup=$this->getAllAttributes();
       	$attributes=array();
       	$attributes['car_price']=false;
       	foreach($attributeGroup as $key=>$grow){
       	    $attriG=explode("-", $grow);
       	    $group=array();
       	    $group['group_name']=$attriG[0];
       	    $group['attribute']=array();
       	    $attriGIds=explode(",", $attriG[1]);
       	    $arr=$query->result_array();
       	    foreach($arr as $row){
       	        if(in_array($row['attr_group'],$attriGIds)&&(!empty($row['attr_value']))){
       	            array_push($group['attribute'],$row);
       	            if($row['attr_id']==113){
       	                 $attributes['car_price']=$row;
       	            }
       	        }
       	    }
       	   if(count($group['attribute'])!=0)
       	    array_push($attributes,$group);
       	}
       	return $attributes;  
    }

}