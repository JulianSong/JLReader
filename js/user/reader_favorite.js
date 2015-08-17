/**
 * 显示收藏标志
 */
function showUserFavorite(){
	$.get(BASE_URL+"favorite/getfavoriteaids",
		function(text){
			var ufaidArray=text.split(",");
			for(i=0;i<ufaidArray.length;i++){
				$("#ti_"+ufaidArray[i]).attr("src",BASE_URL + "img/favorite_orange.png").attr("isadd",1).attr("title","点击星标移除收藏！");
			}
	   },"text"
    );
} 
showUserFavorite();

/**
 * 获取收藏信息
 * by yeduo 2011.11.9
 */
function getFavorite(){
	$.get(BASE_URL+"favorite/getallfavorite",
			function(json){
				var favoriteList = document.getElementById("favorite_list");
				favoriteList.innerHTML=json;
		   },"text"
	    );
}
getFavorite();

/**
 * 文章列表页中设置收藏状态
 */
function setFavorite(ArticleID,favorite_img){
	var favorite_isadd=favorite_img.getAttribute("isadd");
	if(favorite_isadd==0){
		addFavoriteFromArticle(ArticleID,favorite_img);
	}else{
		delFavoriteFromArticle(ArticleID,favorite_img);
	}
	return true;
}
/**
 * 文章列表页中添加收藏
 */
function addFavoriteFromArticle(ArticleID,img){
	$.post(BASE_URL+"favorite/addfavorite",
	        {
				ArticleID:ArticleID
	        },
	        function(json){
		        var result=json.result;
		        if(result==0){
					img.src = BASE_URL + "img/favorite_orange.png";
					img.title = "点击星标移除收藏！";
					img.setAttribute("isadd",1);
					getFavorite();
			    }else if(result==1){
					//已经收藏
					img.src = BASE_URL + "img/favorite_orange.png";
					img.title = "点击星标移除收藏！";
					img.setAttribute("isadd",1);
					getFavorite();
				}else if(result==2){
					alert("只能为您收藏50篇文章");
				}else if(result==3){
					//未登录,收藏成功
					img.src = BASE_URL + "img/favorite_orange.png";
					img.title = "点击星标移除收藏！";
					img.setAttribute("isadd",1);
					getFavorite();
					
					/*未登录
					var r=confirm("您登录后才可以收藏该文章！现在登录吗？");
					if(r==true){
						colorboxShow();
						//跳转到登录页面
			            //window.location.href=BASE_URL+"user/login";
					}*/
				}else if(result==4){
					//未登录，收藏超过10个
					var r=confirm("你已经收藏10篇文章，想收藏更多文章，请登录吧！");
					if(r==true){
						//跳转到登录页面
						colorboxShow();
						//跳转到登录页面
			            //window.location.href=BASE_URL+"user/login";
					}
				}
	        },
	        'json'
	  );
}

function colorboxShow(){
	$.colorbox({href:BASE_URL+"user/showLoginBox"});
}
/**
 * 文章列表页中移除收藏
 */
function delFavoriteFromArticle(ArticleID,img){
	$.post(BASE_URL+"favorite/delfavoritefromarticle",
	        {
				ArticleID:ArticleID
	        },
	        function(result){
	        	if(result==2){
	        		//用户没登录，删除Cookie
	        		img.src = BASE_URL + "img/favorite_gray.png";
					img.title = "点击星标收藏,过会儿再看！";
					img.setAttribute("isadd",0);
					getFavorite();
	        		/*
	        		var r=confirm("您的登录已过期！现在重新登录吗？");
					if(r==true){
						//跳转到登录页面
			            window.location.href=BASE_URL+"user/login";
					}*/
	        	}else if(result==1){
		        	//移除收藏成功
	        		img.src = BASE_URL + "img/favorite_gray.png";
					img.title = "点击星标收藏,过会儿再看！";
					img.setAttribute("isadd",0);
					getFavorite();
	        	}else if(result==0){
					//没有收藏，移除失败
	        		img.src = BASE_URL + "img/favorite_gray.png";
					img.title = "点击星标收藏,过会儿再看！";
					img.setAttribute("isadd",0);
					getFavorite();
		        }
	        },
	        'text'
	  );
}

