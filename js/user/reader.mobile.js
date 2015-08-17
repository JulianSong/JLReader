/*
*edit by julian
*/
distance = $(".page").outerWidth(true);// 把每次移动的距离设置为显示容器的宽度
prevPage = 0;// 当前显示页面的前个页面页码默认为0
currentPage = 1;// 当前呈现在显示容器中的页码默认为1
nextPage = 2;// 当前显示页面的后个页面页码默认为2
duration = 300;// 动画执行的速度单位毫秒
is_stop = 0;// 是否停止动画
time_counting = 1;// 是否开始记录用户在某页面的停留时间
page_open_time = 0; // 转到某页的开始时间
$("#pages").slip({distance:distance
                 ,length:pageLength
				},null,
                function(c){
				   getMoreArticles();
				}
          );
function toPage(pageNumber) {
	$("#pages").slipTo(pageNumber,duration).printCount("#pagenumber");
	currentPage=$("#pages").counter();
	loadImg(currentPage);
}
/*
 * 滚动显示某一页 left 是否向左滚动
 */
function showPage(left,d) {
	if(typeof(d)==="undefined"){
			d=distance;
    }
	if (is_stop == 1)
		return false;
	if (left) {
		$("#pages").slipLeft(duration,d).printCount("#pagenumber");
	} else {
        $("#pages").slipRight(duration,d).printCount("#pagenumber");
	}
	currentPage=$("#pages").counter();
	loadImg($("#pages").counter());
}
function loadImg(pageNumber){
	 var page=document.getElementById("page_"+pageNumber);
	 var imgs=page.getElementsByTagName("img");
	 for(i=0;i<imgs.length;i++){
		 if(!(imgs[i].getAttribute("width")*imgs[i].getAttribute("height")==0||imgs[i].getAttribute("width")==""||imgs[i].getAttribute("height")=="")){
		 	 imgs[i].setAttribute("src",imgs[i].getAttribute("data-src"));
		 }
	 }
}
/**
 * 获得更多文章
 */
var batch=1;
function getMoreArticles() {
	$(".pages").animate( {
		left : "-=" + distance
	}, 0);
	is_stop = 1;
    $.post(BASE_URL+"reader/morepages",{
		b:batch+1,//第几批数据
		sid:SID  ,//车系id
		ct:CT//内容类型
	}, function(data) {
		if(data>=1){
			$("#pages").animate({left : "+=" + distance}, duration);
		}else{
			$("#data").append(data);
			var reader=new AutoReader(tempateJson,windowSize,"data-bind-"+(batch+1),"data-info-"+(batch+1),pageLength);
			pageLength=reader.saw();
			batch++;
			currentPage += 1;
			$("#pages").clength(pageLength);
			$("#pages").counter(currentPage).printCount("#pagenumber");
			$("#loaded-num").html($("#load-data-num-"+batch).val());
			bind_show_big_img();
			loadImg($("#pages").counter());
			showUserFavorite();
		}
         is_stop = 0;
	}, "text");
}
// 显示评论内容或添加新的评论
function bind_show_big_img(){
$(".img").bind(
		'click',
		function() {
			show_big_img($(this).attr('src'), $(this).offset().left, $(this)
					.offset().top);
});
}
bind_show_big_img();
// 显示图片大图
function show_big_img(src, left, top) {
	is_stop = 1;
	$("#big_img").attr("src", src);
	$("#big_img_content").css( {
		left : left,
		top : top,
		width : $("#big_img").offset().width,
		height : $("#big_img").offset().height
	});
	$("#big_img_content").show();
	var windowWidth = document.body.clientWidth;
	var windowHeight = document.body.clientHeight;
	var popupHeight = $("#big_img_content").height();
	var popupWidth = $("#big_img_content").width();
	$("#big_img_content").animate( {
		top : windowHeight / 2 - popupHeight / 2,
		left : windowWidth / 2 - popupWidth / 2
	},160);
	$("#ih_overlay2").show();
}
$("#ih_overlay2").bind('click', function() {
	is_stop = 0;
	$("#ih_overlay2").hide();
	$("#big_img_content").hide();
});
$("#show_add_c").click(function() {
	$("#add_c").show("fast");
});
$("#concel_comments").click(function() {
	$("#add_c").hide("fast");
});

// 获得时间
function getStandingTime() {
	var date = new Date();
	standing_time = date.getTime() - page_open_time;
}
$("#prev").click(function() {
	showPage(true);
});
$("#next").click(function() {
	showPage(false);
});
/**
 * 显示关闭文章列表
 */
// 判断事件源对象
function isSrcElement(e, handler) {
	e = e || window.event;
	target = e.target || e.srcElement;
	return (target == handler);

}
allist_is_show = false;
function showLeftSidebar() {
	if (allist_is_show) {
		$("#left_sidebar").hide();
		$("#list").show();
		$("#content").css('margin-left', 32);
		$("#list-close").css('left', 0);
		$("#list-close").hide();
		allist_is_show = false;
	} else {
		$("#left_sidebar").show();
		$("#list").hide();
		$("#content").css('margin-left', 352);
		$("#list-close").css('left', 320);
		$("#list-close").show();
		allist_is_show = true;
	}
}

$("#list").click(function() {
	showLeftSidebar();
});
$("#list-close").click(function() {
	showLeftSidebar();
});
$("#button_list").click(function() {
	$("#article_list").show();
	$("#favorite_list").hide();
	$("#button_list").addClass("on_sel");
	$("#button_favorite").removeClass("on_sel");
});
$("#button_favorite").click(function() {
	$("#favorite_list").show();
	$("#article_list").hide();
	$("#button_favorite").addClass("on_sel");
	$("#button_list").removeClass("on_sel");
});
loadImg(1);





//处理屏幕touch事件
$("#list").bind("touchend",function() {
	showAlist();
});
$("#list-close").bind("touchend",function() {
	showAlist(this);
});
var touchDistance, startX, startY;
var left=0
function touchStart(event) {
        //event.preventDefault();
         //if (! event.touches.length) return;
         var touch = event.touches[0];
         startX = touch.pageX;
         startY = touch.pageY;
         if($("#pages").css("left")!='auto'){
	     	left=parseInt($("#pages").css("left"));
		 }
}
$("#maincontent").bind('touchstart',function(){
	touchStart(event);
	
});
var toucheslength=0;
$("#maincontent").bind('touchmove',function(){
	 event.preventDefault();//阻止提交
	 if (!event.touches.length) return;
	 toucheslength=event.touches.length;
     var touch = event.touches[0];
     touchDistance = touch.pageX-startX;
	 if(touchDistance>0){
	   $("#pages").css("left",parseInt(left+Math.abs(touch.pageX-startX))) ;
	 }else{ 
       $("#pages").css("left",parseInt(left-Math.abs(touch.pageX-startX)));	
	 }
});
$("#maincontent").bind('touchend',function(){//alert(left);
	 if (event.touches.length<0) return;
	 if(toucheslength==1)
	 if(Math.abs(touchDistance)>50){
	   d=distance-Math.abs(touchDistance);
	   showPage(touchDistance>0,d);
	 }else{

	 }
	 touchDistance=0;
});

