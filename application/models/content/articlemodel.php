<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 文章model
 * @author julian
 */
class ArticleModel extends CI_Model{
    /**
     *
     * @param  $batch 数据批次
     * @param  $seriesID 车系id
     * @param  $contentType 内容类型
     * @param  $size 每次获得的数据数量
     */
    public function getArticles($seriesID=0,$contentType=0,$batch=1,$size=30) {
        $sql = "SELECT esa.ArticleID,esa.Title,esa.Author,esa.SpiderWebSiteName,esa.ArticleDate
                FROM event_spider_articles AS esa
                JOIN event_article_series AS eas
                ON esa.ArticleID = eas.ArticleID";
        $where=" ";
        if($seriesID!=0){
            $where.=" WHERE  eas.SeriesID=".$seriesID;
        }
        if($contentType!=0){
            $where.=" AND esa.contentType IN ( ".$contentType ." ) ";
        }
        $order=" ORDER BY esa.ArticleDate DESC";
        $limit=" LIMIT ".($batch-1)*$size.",".$size;
        $sql.=$where.$order.$limit;
        $queryArticles=$this->db->query($sql);
        return $this->processArticles($queryArticles);
    }
    /**
     * 获得文章综合权重
     * 根据文字图片的比例确定文章的权重
     * 以文字数量除以图片数量得到每个图片所“跟随”的文字数量
     * @param $articles 文章信息
     */
    public function countArticleWeight($articles){
        $articles['textWeight']=0;
        $articles['imgWeight']=0;
        //获得文字数量
        $sql='SELECT  SUM(ContentLength) AS TextLength  FROM  event_spider_contents WHERE  ArticleID = '.$articles['ArticleID'];
        $querySumCl=$this->db->query($sql);
        $textLength=$querySumCl->first_row();
        $articles['textLength']=$textLength->TextLength;
        //获得图片数量
        $sql2='SELECT  COUNT(LocalImageFileName) AS ImgCount  FROM  event_spider_contents WHERE Type=2 AND  ArticleID = '.$articles['ArticleID'];
        $querySumImg=$this->db->query($sql2);
        $imgCount=$querySumImg->first_row();
        $articles['imgCount']=$imgCount->ImgCount;
        /*
         *计算文章易读性
         *文字数量与图片数量的比越趋近250（此数值暂定）文章可读性越高
         *文章的权重值设定在17-170之间（这两个边界值分别代表了最小区块到最大区块的权重）
         *文章权重确定有以下三种情况
         *1，如果文章没有图片则设置该文章权重为最小值17
         *2，如果文章没有文字则以文章封面图片的面积确定权重
         *3，如果文章既有文字又有图片则根据文章易读性确定文章的权重,如果文章
         *   的文字图片比大于500则视文章文字过多图片过少易读性较低
         */
        if($articles['imgCount']==0){
            $articles['textWeight']=40;
            $articles['imgWeight']=0;
        }elseif($articles['textLength']==0&&$articles['img']){
            $articles['textWeight']=0;
            $articles['imgWeight']=round($articles['img']['ImageWidth']*$articles['img']['ImageHeight']/280*0.1);
        }else{
            $tl2ic=round($articles['textLength']/$articles['imgCount']);
            if($tl2ic>600){
                $articles['textWeight']=20;
            }elseif($tl2ic>300){
                $articles['textWeight']=600-$tl2ic;
            }else{
                $articles['textWeight']=$tl2ic;
            }
        }
        //文章权重=标题权重+文字权重+图片权重
        $articles['weight']=$articles['textWeight']+$articles['imgWeight'];
        return $articles;
    }
    /**
     * 创建一棵文章数据并设置为树状结构
     * @param  $articleId 文章id
     */
    public function getArticleContent($articleId = ''){
        if (empty($articleId)){
            return "";
        }
        $article=array();
        $article = $this->getArticleByAid($articleId);
        $article['contents'] =$this->getContentByAid($articleId);
        return $article;
    }
    /**
     * 通过文章id取文章数据
     * @param  $articleId 文章id
     */
    public function getArticleByAid($articleId = '0'){
        $sql = "select * from `event_spider_articles` where ArticleID='$articleId'";
        $query=$this->db->query($sql);
        return $query->row_array();
    }
    /**
     * 获得文章的段落
     * @param  $aid 文章id
     * @param  $numb 段落数量默认为all
     */
    public function getContentByAid($aid,$numb="all"){
        $sql = "select ContentID,ArticleID,Type,Content,ContentLength,URL,LocalImageFileName,ImageWidth,ImageHeight,OrderID,ImageWeight,isStrong
                from `event_spider_contents`  where ArticleID='$aid' order by ContentID" ;
        if($numb!="all"){
            $sql.=" LIMIT ".$numb;
        }
        $query=$this->db->query($sql);
        return   $query->result_array();
    }
    /**
     * 获得某车系下，某个网站，某个类别的文章数量 Scs(Series,ContentType,Sites)
     * @param  $sid 		车系id
     * @param  $contentType 文章类型
     * @param  $sites 		网站
     */
    public function getArticlesNumByScs($sid,$contentType=0,$sites=0){
        $sql = "SELECT COUNT(esa.ArticleID) as num
                FROM event_spider_articles AS esa
                JOIN event_article_series AS eas
                ON esa.ArticleID = eas.ArticleID  WHERE  eas.SeriesID= ?";
        if($contentType!=0){
            $sql.=" AND esa.contentType IN ( ".$contentType ." ) ";
        }
        if($sites!=0){
            $sql.=" AND esa.siteID = ".$sites;
        }
        $query=$this->db->query($sql,array($sid));
        $result=$query->first_row("array");
        return $result['num'];
    }
    /**
     * 处理获得文章数据
     * @param  $queryArticles 查询结果
     */
    public function processArticles($queryArticles){
        $articles = array();
        $list = array();
        $length=0;
        /*遍历获取的文章数据*/
        $arr1=$queryArticles->result_array();
        foreach ($arr1 as $row) {
            $row['Summary']='';
            $sql2 = "SELECT Content,isStrong FROM event_spider_contents WHERE Type = 1 AND ContentLength > 0 AND ArticleID = ?  ORDER BY ContentID LIMIT 10";
            $querySummaries=$this->db->query($sql2,array($row['ArticleID']));
            $arr2=$querySummaries->result_array();
            foreach ($arr2 as $row2) {
                if($row2['isStrong']){
                   // $row['Summary'] .= "<b>".$row2['Content']."</b><br/>";
                }else{
                    //$row['Summary'] .= "<span>".$row2['Content']."</span><br/>";
                }
                $row['Summary'] .= $row2['Content'];
            }
            /*
             * 获得封面图片
             */
            $sql3 = "SELECT LocalImageFileName,ImageWidth,ImageHeight
                     FROM event_spider_contents 
                     WHERE Type = 2  AND LocalImageFileName <>'' AND ArticleID = ? LIMIT 1 ";
            $queryImg=$this->db->query($sql3,array($row['ArticleID']));
            $img=array(
                 'LocalImageFileName'=>'',
                 'ImageWidth'=>0,
                 'ImageHeight'=>0
            );
            if($queryImg->num_rows()){
                $row['img']=$queryImg->first_row('array');
            }else{
                $row['img']=$img;
            }
            /**
             * 获得文章关联的车系
             */
            $sql4="SELECT ec.cat_name FROM event_article_series AS eas LEFT JOIN event_category AS ec  ON eas.SeriesID=ec.cat_id WHERE eas.ArticleID= ? " ;
            $queryCompeteSeries=$this->db->query($sql4,array($row['ArticleID']));
            $row['csLength']=$queryCompeteSeries->num_rows();
            $row['competeSeries']=$queryCompeteSeries->result_array();
            /*计算权重*/
            $row = $this->countArticleWeight($row);
            if($row['weight']!=0){
                array_push($list,$row);
                $length++;
            }
        }
        $articles['list']=$list;
        $articles['length']=$length;
        return $articles;
    }
    /**
     * 搜索包含竞争车系为文章
     * @param  $sid  			车系id
     * @param  $contentType		文章类型
     * @param  $csids			竞争车系id
     */
    public function serachArticle($sid,$contentType,$csids,$batch=1,$size=30){
        $sql="SELECT esa.ArticleID,esa.Title,esa.Author,esa.SpiderWebSiteName,esa.ArticleDate
              FROM  event_article_series AS a
              JOIN  event_article_series AS b
              ON a.ArticleID=b.ArticleID
              LEFT JOIN event_spider_articles AS esa
              ON esa.ArticleID=a.ArticleID
              WHERE b.SeriesID=$sid AND esa.contentType = $contentType  AND a.SeriesID IN ($csids) 
              GROUP BY a.ArticleID  ";
        $limit=" LIMIT ".($batch-1)*$size.",".$size;
        $sql.= $limit;
        $query=$this->db->query($sql);
        return $this->processArticles($query); 
    }
    /**
     * 获得搜索出包含竞争车系的文章数量
     * @param  $sid  			车系id
     * @param  $contentType		文章类型
     * @param  $csids			竞争车系id
     */
    public function getSerachArticleNumb($sid,$contentType,$csids){
        $sql="SELECT COUNT(esa.ArticleID) as num
              FROM  event_article_series AS a
              JOIN  event_article_series AS b
              ON a.ArticleID=b.ArticleID
              LEFT JOIN event_spider_articles AS esa
              ON esa.ArticleID=a.ArticleID
              WHERE b.SeriesID=$sid AND esa.contentType = $contentType  AND a.SeriesID IN ($csids) 
              GROUP BY a.ArticleID  ";
        $query=$this->db->query($sql);
        return $query->num_rows(); 
    }
}