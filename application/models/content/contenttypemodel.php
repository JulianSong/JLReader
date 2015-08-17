<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *文章内容类型model
 * @author julian
 */
class ContentTypeModel  extends CI_Model{
	/**
	 * 获得所有文章内容类型
	 */
	public function getAllContentType(){
		$sql="SELECT id ,name FROM event_spider_articles_contenttype ";
		$query=$this->db->query($sql);
		return $query->result_array();
	}
	/**
	 * 获得摸个类型信息
	 * @param $ctid
	 */
	public function getContentType($ctid){
		$sql="SELECT id ,name FROM event_spider_articles_contenttype WHERE id=".$ctid;
		$query=$this->db->query($sql);
		return $query->first_row("array");
	}

	public function prossPic($dir,$s){
		$sql="update  event_spider_contents set LocalImageFileName =? , ImageWidth= ? ,ImageHeight= ?   WHERE  ContentID = ? ";
		$selql="select ContentID from event_spider_contents WHERE Type = 2 ORDER BY ContentID";
		$query=$this->db->query($selql);
		$article=$query->result_array();
		if (is_dir($dir)) {
			
			if ($dh = opendir($dir)) {
				$i=0;
				
				while (($file = readdir($dh)) !== false) {
					
					if(!is_dir($file)){

						if(!isset($article[$i])){
							die ($i);
						}

						$bind=array();
						$size=getimagesize($dir.$file);
						array_push($bind,$s."/".$file,$size[0],$size[1],$article[$i]['ContentID']);
						echo $i,":", $article[$i]['ContentID'] ," file:" ,$file,"</br>";
						print_r($bind);
						echo "</br>";
						$this->db->query($sql,$bind);
						
						$i++;

					}
						
				}
				closedir($dh);
			}
		}
	}
}