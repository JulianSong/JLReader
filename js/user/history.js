history_distance = $(".history_page").outerWidth(true);// 把每次移动的距离设置为显示容器的宽度
history_prevPage = 0;// 当前显示页面的前个页面页码默认为0
history_currentPage = 1;// 当前呈现在显示容器中的页码默认为1
history_nextPage = 2;// 当前显示页面的后个页面页码默认为2
history_duration = 300;// 动画执行的速度单位毫秒
history_is_stop = 0;// 是否停止动画
$("#history_pages").css("width",history_distance*history_pageLength);
/**
 * 上一页事件
 */
function history_up_page()
{
	if(history_pageLength>0){
		history_showPage(1);
	}
}

/**
 * 下一页事件
 */
function history_next_page()
{
	if(history_pageLength>0){
		history_showPage(0);
	}
}

/*
 * 滚动显示某一页 left 是否向左滚动
 */
function history_showPage(left) {
	if (history_is_stop == 1)
		return false;
	if (left) {
		if (history_currentPage == 1) {
			return;
		}
		$("#history_pages").animate( {
			left : "+=" + history_distance
		}, history_duration);
		history_currentPage -= 1;
		$("#history_pagenumber").html(history_currentPage);
	} else {
		if (history_currentPage == history_pageLength) {
			return;
		}
		$("#history_pages").animate( {
			left : "-=" + history_distance
		}, history_duration);
		history_currentPage += 1;
		$("#history_pagenumber").html(history_currentPage);
	}
}
