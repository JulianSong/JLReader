<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 模型信息model
 * @author julian
 */
class TemplateModel extends CI_Model{
    public function addTemplate($blockAmount){
        $data = array(
               'BlockAmount' =>$blockAmount
         );
         $this->db->insert('event_template',$data);
         return $this->db->insert_id(); 
    }
    /**
     * 获得所有模板
     */
    public function getAllTemplate(){
        $sql = "SELECT TemplateID as ti, BlockAmount as ba,PlateInfo as pinfo FROM event_template WHERE CanUse = 1 ORDER BY BlockAmount DESC";
        $queryTemplates=$this->db->query($sql);
        $templateArray = array();
        $arr=$queryTemplates->result_array();
        foreach ($arr as $row) {
            $row['used_weight']=0;
            array_push($templateArray, $row);
        }
        return $templateArray;
    }
    /**
     *解析模板
     * 模板字符串以逗号分隔存储信息。如1,1,1,1,0,0,1,1
     * 第一个数字为模板ID，
     * 第二个为模板中区块的数量，
     * 第三第四个数字为模板宽高比的最小公约数。
     * 之后每四个数字为一组表示模板中每个区块矩阵的起始位置和结束位置
     * @param $tepInfoStr 模板字符串
     * @param $width  整个模板宽度
     * @param $hight  整个模板高度
     */
    public function parsingTemplate($tepInfoStr, $width, $height) {
        $templateDate = explode(",", $tepInfoStr);
        //获得每列的宽度
        $clumWidth = round($width / $templateDate['2']);
        $rowHeight = round($height / $templateDate['3']);
        //解析出模板每块的位置和大小
        $length = count($templateDate) - 4;
        $template = array();
        $template['x_unit']=$templateDate['2'];
        $template['y_unit']=$templateDate['3'];
        $template['blocks'] = array();
        for($i = 4; $i <= $length; $i += 4) {
            //获得每个区块的矩阵坐标
            $block = array();
            $block['x1'] = $templateDate[$i];
            $block['y1'] = $templateDate[$i + 1];
            $block['x2'] = $templateDate[$i + 2];
            $block['y2'] = $templateDate[$i + 3];
            //获得区块在父容器中相对位置和大小
            $block['left'] = $block['x1'] * $clumWidth;
            $block['top']  = $block['y1'] * $rowHeight;
            $block['width']  = ($block['x2'] - $block['x1']) * $clumWidth;
            $block['height'] = ($block['y2'] - $block['y1']) * $rowHeight;
            //获得区块的倒向（横竖）
            $block['follow']=($block['width']*0.7 > $block['height'])? 1:0;
            //获得区块的权重
            $block['weight']=round($block['width']*$block['height']/280*0.1);
            array_push($template['blocks'],$block);
        }
       
        return ($template);
    }
    /**
     * 获得所有模板并解析
     * @param unknown_type $width 模板宽度
     * @param unknown_type $height 模板高度
     */
    public function getAllParsedTemplate($width, $height){
        $sql = "SELECT TemplateID as ti, BlockAmount as ba,PlateInfo as pinfo ,CanUse  FROM event_template WHERE  1  ORDER BY TemplateID DESC LIMIT 50";
        $queryTemplates=$this->db->query($sql);
        $templateArray = array();
        $arr=$queryTemplates->result_array();
        $templateArray=array();
        foreach ($arr as $row) {
            $row['blocks'] = $this->parsingTemplate($row['pinfo'], $width, $height);
            array_push($templateArray, $row);
        }
        return $templateArray;
    }
    public function editPlate($pate=array()){
        $this->db->where('TemplateID', $pate['TemplateID']);
        return  $this->db->update('event_template', $pate); 
    }
    public  function find($id){
    	$sql="SELECT * from event_template WHERE TemplateID= ".$id;
    	$query=$this->db->query($sql);
    	if(!$query->num_rows()){
            return false;
        }
        $resultArray=$query->first_row("array");
        return $resultArray;
    }
    function sortBlock(){
    	
    }
}