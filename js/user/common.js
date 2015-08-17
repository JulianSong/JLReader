/**
*模板解析显示脚本
*edit by julian 
*/
/*************导航条***************************************************************************/
/*
*初始化弹出层
*/
 $("#topbar_login").colorbox();//登录
 $("#article_filter").colorbox();//文章筛选
String.prototype.trim = function() {
	return this.replace(/(^\s*)|(\s*$)/g, "");
}
function showSerachBox(event){
	$.colorbox({innerWidth:"560px",innerHeight:"530px;",href:BASE_URL+"series/showSerachBox"},
	          function(){
				  $("#key").val($("#top_key").val());
				  serach();
				  $("#key").bind("keydown",function(event){if(event.keyCode == 13){serach();}});
			  }
	
	);//文章筛选
}
function showSerachBoxUseKey(event){
  if (event.keyCode == 13) {
		    showSerachBox();
  }
}
/**
 * 搜索
 * @return
 */
function serach(){
	var keyString=new String();
	keyString=$("#key").val();
	keyString.trim();
	if(keyString.length==0){
		alert("请输入搜索条件！");
		SERACH_RESULET_CONTEBT.innerHTML="请输入搜索条件";
		return;
	}
	$.post(BASE_URL + "series/serach", {
		key :keyString
	}, function(result) {
		SERACH_RESULET_CONTEBT.innerHTML = result;
		var l=pagination();
		var p_w=$(SERACH_RESULET_CONTEBT).innerWidth(true);
		$("#page_conter").html(l);
		$("#sliper").height(434);
		$(SERACH_RESULET_CONTEBT).height(434);
		$("#serach_series").slip({distance:p_w,length:l});
		//$("#serach_series").counter(1);
		//$("#serach_series").clength(l);
	}, 'text');
}
$("#serach_button").bind("click",function (){serach();});
function pagination(){
	var serach_series=document.getElementById("serach_series");
	var dl=$("#data_num").val();
	var pageWidth=$(SERACH_RESULET_CONTEBT).innerWidth(true);
	var pageDateNum=3*Math.floor(pageWidth/132);
	var pl=Math.ceil(dl/pageDateNum);
	for(p=0;p<pl;p++){
		var page=document.createElement("div");
		page.className="spage";
		page.style.width=pageWidth+"px";
		page.id="sp_"+p
		i=p*pageDateNum;
		for(;i<dl;i++){
            page.appendChild(document.getElementById("sb_"+i));	
			if(((i+1)%pageDateNum==0)||i>dl){
              break;
			}	
		}
		var clear=document.createElement("div");
		clear.className="clear";
		page.appendChild(clear);
		serach_series.appendChild(page);
	}
	var clear=document.createElement("div");
	clear.className="clear";
	serach_series.appendChild(clear);
	return pl;
}
