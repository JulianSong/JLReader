<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *文章内容类型model
 * @author julian
 */
class ArticleSitesModel  extends CI_Model{
    /**
     * 获得所有文章内容类型
     */
    public function getAllArticleSites(){
        $sql="SELECT siteID ,siteName FROM event_spider_articles_sites ";
        $query=$this->db->query($sql);
        return $query->result_array();
    }
}