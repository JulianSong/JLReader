var t;
var time=10000;//设置发送时间 单位 毫秒
var arr = new Array();//记录浏览过的ArticleID和浏览时间
var old_aid;//打开的Article

/**
 * 打开页面时事件
 */
window.onload=function(){
	int_state=1;//页面有焦点
	set_starttime();
}

///**
// * 关闭页面时事件
// */
//window.onbeforeunload=function(){
//	set_endtime(AID);
//}

/**
 * 打开页面时，记录打开时间点
 * 记录页面信息
 * @param uid
 * @param ArticleID
 */
function set_starttime(){
	var ArticleID = AID;
	$.post(BASE_URL+"history/setstart",
	        {
				ArticleID:ArticleID
	        },
	        function(json){
	        	var result=json.result;
	        	{
	        		if(result==1){
	        			//用户已经登录
	        			read_time=json.duration;
	        			arr[ArticleID]=read_time;
	        			old_aid=ArticleID;
        				clearTimeout(t);//计时停止
    	        		t=setTimeout("send_time()",time);//time秒触发一次检测
	        		}else if(result==0){
		        		//没登录
	        			arr[ArticleID]=0;
	        			old_aid=ArticleID;
		        	}
	        	}
	        },
	        'json'
	  );
}

///**
// * 关闭页面时，根据打开时间点算出持续时间，记录持续时间
// * @param uid
// * @param ArticleID
// */
//function set_endtime(ArticleID){
//	$.post(BASE_URL+"history/setend",
//	        {
//				ArticleID:ArticleID
//	        },
//	        function(json){
//				//alert("end_ok");
//	        },
//	        'json'
//	  );
//}

/**
 * 添加收藏按钮事件
 */
function addfavorite_click(){
	AID=getNowArticleID();
	addFavorite(AID);
}

/**
 * 添加收藏
 */
function addFavorite(ArticleID){
	$.post(BASE_URL+"favorite/addfavorite",
	        {
				ArticleID:ArticleID
	        },
	        function(json){
		        var result=json.result;
		        if(result==0){
					alert("收藏成功");
			    }else if(result==1){
					alert("已经收藏，不能重复收藏");
				}else if(result==2){
					alert("收藏失败！最多能收藏50篇文章");
				}else if(result==3){
					alert("您登录后才可以收藏该文章！");
				}
	        },
	        'json'
	  );
}
var a=1;
/**
 * 页面获得焦点
 */
function page_int(){
	AID=getNowArticleID();
	old_aid=AID;
	clearTimeout(t);//计时停止
	t=setTimeout("send_time()",time);//time秒发一次请求
}

/**
 * 页面失去焦点
 */
function page_out(){
	clearTimeout(t);//计时停止
}

/**
 * 发送时间请求
 * @param ArticleID
 */
function send_time(){
	//检测url有没有变化
	AID=getNowArticleID();
	var now_aid=AID;
	if(now_aid!=old_aid){
		//url有变化
		old_aid=now_aid;
		if(!arr[now_aid]){
			//aid没有记录过，重新记录
			AID=getNowArticleID();
			set_starttime();
		}else{
			//aid记录过
			clearTimeout(t);//计时停止
    		t=setTimeout("send_time()",time);//time秒触发一次检测
		}
	}else{
		//url没有变化
		if(arr[now_aid]<60){
			//浏览时间不够60s
			$.post(BASE_URL+"history/actiontime",
			        {
						ArticleID:now_aid,
						count_time:time
			        },
			        function(json){
			        	if(json.result==1){
			        		read_time=json.duration;
			        		arr[now_aid]=read_time;
	        				clearTimeout(t);//计时停止
	    	        		t=setTimeout("send_time()",time);//time秒触发一次检测
			        	}else if(json.result==0){
			        		//没登录
			        	}
			        },
			        'JSON'
			 );
		}else{
			//浏览时间够60s
			clearTimeout(t);//计时停止
    		t=setTimeout("send_time()",time);//time秒触发一次检测
		}
	}
}


/**
 * 获取当前文章的ArticleID
 */
function getNowArticleID(){
	//var ifms=document.getElementById('win').contentWindow;
	//var iobj=ifms.document.getElementById('chromeContainer');
	var iobj=document.getElementById('chromeContainer');
	var nowiobj=iobj.getElementsByTagName('div');
	for(var i=0;i<nowiobj.length;i++){
		if(nowiobj[i].className=="article template current"){
			var aobj = nowiobj[i].getElementsByTagName('a');
			var url = aobj[0].href;
			var start=url.lastIndexOf("/")+1;
			var stop=url.lastIndexOf(".");
			var ArticleID=url.substring(start,stop);
			return ArticleID;
		}
	}
}
