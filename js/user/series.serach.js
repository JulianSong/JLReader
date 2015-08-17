/**
 * 显示或关闭搜索工具
 * @param icon 按钮图标
 * @return
 */
var is_show=0;//搜索工具是否显示
var is_load=0;//搜索工具是否加载
String.prototype.trim = function() {
	return this.replace(/(^\s*)|(\s*$)/g, "");
}
function showSerachBox(){
	if(!is_show){
		$("#highlight_box").show();
		if(is_load){
			$("#highlight_box").show();
		}else{
			$.get(BASE_URL+"series/showSerachBox",function(html){
			   $("#add_concerned_series").html(html);
			});
			is_load=1;
		}
		is_show=1;
    }else{
	    $("#highlight_box").hide();
		is_show=0;
	}
}
/**
 * 搜索
 * @return
 */
function serach() {
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
		hideLogo();
		SERACH_RESULET_CONTEBT.innerHTML = result;
		var l=pagination();
		var p_w=$(SERACH_RESULET_CONTEBT).innerWidth(true);
		$("#page_conter").html(l);
		$("#sliper").height(434);
		$(SERACH_RESULET_CONTEBT).height(434);
		$("#serach_series").slip(p_w,l,true,null,null);
		$("#serach_series").counter(1);
		$("#serach_series").clength(l);
	}, 'text');
}
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
/**
 * 按enter键进行搜索
 * @return
 */
function serachByEnterKey(){
	if (navigator.userAgent.indexOf("MSIE") > 0) {// IE
		document.onkeydown = function() {
			if (event.keyCode == 13) {
				serach();
			}
		}
	} else {
		window.onkeydown = function(event) {
			if (event.keyCode == 13) {
				serach();
			}
		}
	}
}
serachByEnterKey();
/**
 * 输入框输入文本时候实时显示搜索结果
 * @return
 */
function serachLive() {
	serach();
}
function hideLogo(){
	if(typeof(INDEX_PAGE)!="undefind"){
		$("#logo").hide();
		$("#logo_befor_serach").show();
	}
}
$("#serach_button").bind("click",function (){serach();});