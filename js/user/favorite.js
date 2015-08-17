favorite_distance = $(".favorite_page").outerWidth(true);// 把每次移动的距离设置为显示容器的宽度
favorite_prevPage = 0;// 当前显示页面的前个页面页码默认为0
favorite_currentPage = 1;// 当前呈现在显示容器中的页码默认为1
favorite_nextPage = 2;// 当前显示页面的后个页面页码默认为2
favorite_duration = 300;// 动画执行的速度单位毫秒
favorite_is_stop = 0;// 是否停止动画
$("#favorite_pages").css("width",favorite_distance*favorite_pageLength);
/**
 * 上一页事件
 */
function favorite_up_page()
{
	if(favorite_pageLength>0){
		favorite_showPage(1);
	}
}

/**
 * 下一页事件
 */
function favorite_next_page()
{
	if(favorite_pageLength>0){
		favorite_showPage(0);
	}
}

/*
 * 滚动显示某一页 left 是否向左滚动
 */
function favorite_showPage(left) {
	if (favorite_is_stop == 1)
		return false;
	if (left) {
		if (favorite_currentPage == 1) {
			return;
		}
		$("#favorite_pages").animate( {
			left : "+=" + favorite_distance
		}, favorite_duration);
		favorite_currentPage -= 1;
		$("#favorite_pagenumber").html(favorite_currentPage);
	} else {
		if (favorite_currentPage == favorite_pageLength) {
			return;
		}
		$("#favorite_pages").animate( {
			left : "-=" + favorite_distance
		}, favorite_duration);
		favorite_currentPage += 1;
		$("#favorite_pagenumber").html(favorite_currentPage);
	}
}
function toPage(pageNumber) {
	if (pageNumber < favorite_currentPage) {
		var interval = favorite_currentPage - pageNumber;
		$("#favorite_pages").animate( {
			left : "+=" + (favorite_distance * interval)
		}, favorite_duration);
		favorite_currentPage = pageNumber;
		$("#favorite_pagenumber").html(favorite_currentPage);
	} else {
		var interval = pageNumber - favorite_currentPage;
		$("#favorite_pages").animate( {
			left : "-=" + (favorite_distance * interval)
		}, favorite_duration);
		favorite_currentPage = pageNumber;
		$("#favorite_pagenumber").html(favorite_currentPage);
	}
}
/**
 * 用户中心移除收藏
 */
function delFavorite(ArticleID){
	var r=confirm("确定要移除该收藏吗？");
	if(r==true){
		$.post(BASE_URL+"favorite/delfavorite",
		        {
					ArticleID:ArticleID
		        },
		        function(result){
		        	if(result==0){
		        		//用户没登录
		        		var r=confirm("您的登录已过期！现在重新登录吗？");
						if(r==true){
							//跳转到登录页面
				            window.location.href=BASE_URL+"user/login";
						}
		        	}else{
			        	favorite_distance = $(".favorite_page").outerWidth(true);// 把每次移动的距离设置为显示容器的宽度
			        	FAVORITE_RESULET.innerHTML = result;
			        	var favorite_new_pl=parseInt($("#favorite_new_pl").html());
			        	favorite_pageLength=favorite_new_pl;
			        	$("#favorite_pages").css("width",favorite_distance*favorite_new_pl);       	
			        	favorite_now_page=favorite_currentPage;
			        	if(favorite_currentPage>favorite_new_pl){
			        		favorite_currentPage=favorite_new_pl;
			        		favorite_now_page=favorite_new_pl;
			        	}
			        	favorite_currentPage = 1;
			        	favorite_duration = 1;// 动画执行的速度单位毫秒
			        	$("#favorite_pagenumber").html(favorite_currentPage);
			        	$("#favorite_pagecount").html(favorite_new_pl);
			        	toPage(favorite_now_page);
			        	favorite_duration = 300;// 动画执行的速度单位毫秒
		        	}
		        },
		        'text'
		  );
	}
}

/**
 * 用户中心移除页面的内容
 */
function html_delFavorite(uid,ArticleID){
	var favorite_page = document.getElementById("favorite_page");
	var favorite_line = document.getElementById("favorite_line"+ArticleID);
	favorite_page.removeChild(favorite_line);
}


