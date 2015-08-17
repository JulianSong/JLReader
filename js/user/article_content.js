// JavaScript Document
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

var ICON_URL = BASE_URL + "img/icon/";// 设置图标路径
var User_Selection = new String();// 用户选中的文本
var BUTTON_ADDED = false;// 添加笔记按钮是否已经被加入到页面
//var query=new Object();
//query.method="";
//query.condition=""
String.prototype.trim = function() {
	return this.replace(/(^\s*)|(\s*$)/g, "");
}
/**
 * 添加笔记
 */
function addNote(aid, sid) {
	$.post(BASE_URL + "note/add", {
		a : aid,
		s : sid,
		nc : User_Selection.toString(),
		t:1
	}, function(result) {
		if(result.error==0){
		   $("#add_note_button").hide();
		}else if(result.error==1){
		   alert("您还未登录不能添加笔记，请先登录！");
		}else if(result.error==2){
		   alert("文章id错误！");
		}else if(result.error==3){
		   alert("车系Id错误！");
		}else if(result.error==5){
		   alert("内容不能为空");
		}else if(result.error==6){
		   alert("未知系统错误！");
		}else if(result.error==8){
		   alert("您今天添加的笔记已经超出每日笔记最大数量，请您适当删除或者合并笔记！");
		}
	}, "json")
}
/**
 * 使笔记内容可编辑
 * @param nid 笔记id
 * @param opt 笔记操作
 * @return
 */
function showEditer(nid,opt){
	var textarea=document.createElement("textarea");
	textarea.id="nct_"+nid;
	var ncs=document.getElementById("ncs_"+nid);
	textarea.innerHTML=ncs.innerHTML;
	var ncp=document.getElementById("ncp_"+nid);
	var ncd=document.getElementById("ncd_"+nid);
	var opt_s=document.getElementById("opt_s_"+nid);
	textarea.style.height=ncs.offsetHeight+"px";
	$(textarea).bind('propertychange',
	  function(){$(this).style.height=$(this).scrollHeight + 'px';}
	);
	$(textarea).bind('input',
	  function(){this.style.height=this.scrollHeight + 'px';}
	);
	ncd.insertBefore(textarea,ncp);
	$(ncs).hide();
	$(opt).hide();
	$(opt_s).show();
}
/**
 * 编辑笔记
 * @param nid 笔记id
 * @param opt  操作
 * @return
 */
function editNote(nid,opt){
	var nct=document.getElementById("nct_"+nid);
 	$.post(BASE_URL + "note/edit", {
		n:nid,
		nc : nct.value
	}, function(result) {
		if(result.error==0){
			var opt_e=document.getElementById("opt_e_"+nid);
			var ncs=document.getElementById("ncs_"+nid);
			var ncd=document.getElementById("ncd_"+nid);
			ncs.innerHTML=nct.value;
			ncd.removeChild(nct);
			$(opt_e).show();
			$(ncs).show();
			$(opt).hide();
		}else if(result.error==1){
		   alert("您还未登录，请先登录！");
		}else if(result.error==4){
		   alert("笔记不存在！");
		}else if(result.error==5){
		   alert("内容不能为空");
		}else if(result.error==6){
		   alert("未知系统错误！");
		}
	}, "json")
}
/**
 * 是标签可编辑
 * @param nid 笔记id
 * @param opt 操作
 * @return
 */
function showTagEditer(nid,opt){
	var nt=document.getElementById("nt_"+nid);
	var opt_ts=document.getElementById("opt_ts_"+nid);
	nt.removeAttribute("readonly");
	$(nt).addClass("can_edit");
	nt.focus();
	$(opt).hide();
	$(opt_ts).show();
}
/**
 * 编辑笔记标签
 * @param nid  笔记id
 * @param opt 操作
 * @return
 */
function editNoteTag(nid,opt){
	var nt=document.getElementById("nt_"+nid);
	var opt_t=document.getElementById("opt_t_"+nid);
 	$.post(BASE_URL + "note/edittag", {
		n:nid,
		nt : nt.value
	}, function(result) {
		if(result.error==0){
           $(nt).removeClass("can_edit");
		   $(nt).attr("readOnly",true);
		   $(opt_t).show();
		   $(opt).hide();
		}else if(result.error==1){
		   alert("您还未登录，请先登录！");
		}else if(result.error==4){
		   alert("笔记不存在！");
		}else if(result.error==7){
		   if(window.confirm("放弃给笔记添加标签？")){
			   $(nt).removeClass("can_edit");
			   $(nt).attr("readOnly",true);
			   $(opt_t).show();
			   $(opt).hide();
		   }
		}else if(result.error==6){
		   alert("未知系统错误！");
		}
	}, "json")
}
/**
 * 获得用户选中的文本
 * 
 * @return
 */
function getUserSelection() {
	var userSelection;
	var text;
	if (window.getSelection) { // 现代浏览器
		userSelection = window.getSelection();
	} else if (document.selection) { // IE浏览器 考虑到Opera，应该放在后面
		userSelection = document.selection.createRange();
	}
	if (!(text = userSelection.text)) {
		text = userSelection;
	}
	return String(text).trim();
}
/**
 * 显示按钮
 * 
 * @param event
 * @return
 */
function showButton(event) {
//	AID=getNowArticleID();
	if (BUTTON_ADDED) {
		var button = document.getElementById("add_note_button");
		button.style.left = event.clientX + "px";
		button.style.top = event.clientY + "px";
		$("#add_note_button").show();
	} else {
		var button = document.createElement("span");
		button.id = "add_note_button";
		button.style.left = event.clientX + "px";
		button.style.top = event.clientY + "px";
		$(button).bind("click", function() {
			addNote(AID, SID);
		});
		var icon = document.createElement("img");
		icon.src = ICON_URL + "pen_alt_fill_16x16.png";
		var text = document.createElement("strong");
		text.innerHTML = "加为笔记";
		button.appendChild(icon);
		button.appendChild(text);
		document.body.appendChild(button);
		BUTTON_ADDED = true;
	}
}
function hideButton() {
	$("#add_note_button").hide();
}
/**
 * 鼠标选中文本时候显示按钮
 */
var onmouse_down = false;
document.onmousedown = function() {
	onmouse_down = true;
}
var onmouse_move = false;
document.onmousemove = function() {
	if (onmouse_down)
		onmouse_move = true;
}
if(CAN_ADD_NOTES){
$(document).bind("mouseup", function(event) {
	if (onmouse_down && onmouse_move) {
		User_Selection = getUserSelection();
		if (User_Selection.length != 0) {
			showButton(event);
		}
	} else {
		$("#add_note_button").hide();
	}
	onmouse_down = false;
	onmouse_move = false;

});
}
/**
 * 显示日历
 */
var is_cal_show=0;// 搜索工具是否显示
var is_cal_load=0;// 搜索工具是否加载
function showCalendar(){
	if(!is_cal_show){
		$("#calendar").show();
		if(is_cal_load){
			$("#calendar").show();
		}else{
			$.get(BASE_URL+"note/calendar",function(html){
			   $("#calendar").html(html);
			});
			is_cal_load=1;
		}
		is_cal_show=1;
    }else{
		$("#calendar").hide();
	    $("#calendar").hide();
		is_cal_show=0;
	}
}
/**
 * 隐藏日历
 */
function hideCalendar(){
		$("#calendar").hide();
	    $("#calendar").hide();
		is_cal_show=0;
}
/**
 * 显示某天的笔记
 * @param date 日期
 * @return
 */
function showDailyNots(date){
	$.post(BASE_URL + "note/dailynotes", {
		y:YEAR,
		m: MONTH,
		d: date
	}, function(result) {
	     if(result==1){
		   alert("您还未登录，请先登录！");
		}else if(result==9){
		}else{
			 $("#daily_notes").html(result);
		}
	}, "text")
}
/**
 * 显示某月的日历及这个月的笔记添加情况
 * @param nextMonth 月份
 * @return
 */
function toMonth(nextMonth){
	if(nextMonth){
		if((MONTH+1)>12){
			MONTH=1;
			YEAR++;
		}else{
			MONTH++;
		}
	}else{
		if((MONTH-1)<1){
			MONTH=12;
			YEAR--;
		}else{
			MONTH--;
		}
	}
   $.post(BASE_URL + "note/calendar", {
		y:YEAR,
		m: MONTH,
	   }, function(result) {
	    if(result==1){
		   alert("您还未登录，请先登录！");
		}else{
			 $("#calendar").html(result);
		}
	}, "text")
}
/**
 * 显示标签列表
 */
var is_tags_show=0;// 搜索工具是否显示
var is_tags_load=0;// 搜索工具是否加载
function showTagList(){
	if(!is_tags_show){
		$("#tag_list").show();
		if(is_tags_load){
			$("#tag_list").show();
		}else{
			$.get(BASE_URL+"note/tags",function(html){
			   $("#tag_list").html(html);
			});
			is_tags_load=1;
		}
		is_tags_show=1;
    }else{
		$("#tag_list").hide();
		is_tags_show=0;
	}
}
/**
 * 通过标签搜素笔记
 * @param tag 标签
 * @return
 */
function showTagNots(tag){
	$.post(BASE_URL + "note/tagnotes", {
		t: tag
	}, function(result) {
	     if(result==1){
		   alert("您还未登录，请先登录！");
		}else if(result==10){
		}else{
			 $("#daily_notes").html(result);
		}
	}, "text");
}
/**
 * 显示笔记前的checkbox
 * @param isShow 是否显示
 * @return
 */
function showNotesId(isShow){
    if(isShow){
	 	$(".note_id").hide();
	}else{
	  $(".note_id").show();
   }
}
/**
 * 显示子菜单
 * sub_button_show 子菜单是否已经显示
 */
var sub_button_show=false;
function showSubButtons(){
	showNotesId(sub_button_show);
	if(sub_button_show){
	  $("#sub_button").hide();
	  $("#option_notice").hide();
	  sub_button_show=false;
	}else{
	  $("#sub_button").show();
	   $("#option_notice").show();
	  sub_button_show=true;
	}
	
}
/**
 * 隐藏子菜单
 */
function hideSubButtons(){
	showNotesId(true);
	$("#sub_button").hide();
	$("#option_notice").hide();
	sub_button_show=false;
}
/**
 * 获得选中的checkbox的值
 * @param content checkbox的父容器
 * @return
 */
function getCheckboxsValue(content){
	var content=document.getElementById(content);
	var snArray = new Array();
	var eles = content.getElementsByTagName("input");
	for (var i=0; i<eles.length; i++) {
		if (eles[i].tagName == 'INPUT' && eles[i].type == 'checkbox' && eles[i].checked && eles[i].value != 0) {
			snArray.push(eles[i].value);
		}
	}
	if (snArray.length == 0) {
		return false;
	} else {
		return snArray.toString(); ;
	}
}
/**
 * 删除选中的笔记
 * @param nid 某个笔记id
 * @return
 */
function delNotes(nid){
  if(!window.confirm("您确定要删除所选笔记？")){
	        return false;
  }
  if(nid==0){
	  var ids=getCheckboxsValue("daily_notes");
  }else{
	  var ids=nid;
  }
   if(!ids){
     alert("请选择笔记后在进行操作！");
     return false;
   }
    $.post(BASE_URL + "note/del", {
		nids : ids
	}, function(result) {
		if(result.error==0){
			var User_Notes=document.getElementById("daily_notes");
			for(var one in result.del_notes){
		      User_Notes.removeChild(document.getElementById("note_"+result.del_notes[one]));
		    }
		}else if(result.error==1){
		   alert("您还未登录不能添加笔记，请先登录！");
		}else if(result.error==4){
		   alert("笔记不存在或已经删除！");
		}else if(result.error==6){
		   alert("未知系统错误！");
		}
	}, "json")
}
/**
 * 合并选中的笔记
 * @return
 */
function mergeNotes(){
 var ids=getCheckboxsValue("daily_notes");
 if((!ids)||ids.split(",").length<=1){
   alert("至少要选中两个笔记才能合并！");
   return false;
 }
 $.post(BASE_URL + "note/merge", {
		nids : ids,
	}, function(result) {
		if(result.error==0){
            $("#ncs_"+result.note_id).html(result.content);
			$("#nt_"+result.note_id).val(result.tag);
			var User_Notes=document.getElementById("daily_notes");
			for(var one in result.del_notes){
		      User_Notes.removeChild(document.getElementById("note_"+result.del_notes[one]));
		    }
		}else if(result.error==1){
		   alert("您还未登录不能执行此操作，请先登录！");
		}
	}, "json")
}
/**
 * 打印选中的笔记
 * @param formId 表单id属性
 * @return 
 */
function printNotes(formId){
   var ids=getCheckboxsValue("daily_notes");
   if(!ids){
     alert("请选择笔记后在进行操作！");
     return false;
   }
   form=document.getElementById(formId);
   form.setAttribute("action",BASE_URL+"note/printnotes");
   form.submit();
   form.setAttribute("action","");
}

/**
 * 点击相关车系
 * @param noteid
 */
function editSeries(noteid){
	document.getElementById("series_"+noteid).style.display="";
	document.getElementById("select_"+noteid).focus();
	document.getElementById("s_"+noteid).style.display="none";
}

/**
 * 下拉菜单失去焦点,下拉菜单消失
 * @param noteid
 */
function showSeries(noteid){
	document.getElementById("series_"+noteid).style.display="none";
	document.getElementById("s_"+noteid).style.display="";
}

/**
 * 设置关联车系
 * @param noteid
 */
function setSerise(noteid){
	var seriesid = document.getElementById("select_"+noteid).value;
	var selectedIndex = document.getElementById("select_"+noteid).selectedIndex;
	var cat_name = document.getElementById("select_"+noteid).options[selectedIndex].text;

	$.post(BASE_URL + "note/setseries", {
		nid : noteid,
		seriesid : seriesid
	}, function(result) {
		if(result==0){
			//登录过期
			alert("修改失败！您的登录已过期，请重新登录！");
		}else if(result==1){
			//修改成功
			document.getElementById("s_"+noteid).innerHTML=cat_name;
			showSeries(noteid);
		}else{
			//失败
			alert("修改失败！请重试");
		}
	}, "text")
}
