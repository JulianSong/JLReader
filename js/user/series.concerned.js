var ICON_URL = BASE_URL + "img/icon/";// 设置图标路径
/**
 * 显示可用操作
 * 
 * @param icon
 *            图标
 * @return
 */
function showOpt(icon) {
	icon.src = ICON_URL + icon.getAttribute("opt") + "_p.png";
}
/**
 * 显示车系为文章状态
 * 
 * @param icon
 *            图标
 * @return
 */
function showStat(icon) {
	icon.src = ICON_URL + icon.getAttribute("st") + "_s.png";
}
/**
 * 操作
 * 
 * @param series_id
 *            车系id
 * @param series_name
 *            车系名称
 * @param icon
 *            图标
 * @return
 */
function operate(series_id, series_name, icon) {
	if (icon.getAttribute("opt") == 1) {
		delConcerned(series_id, series_name, icon);
	} else if (icon.getAttribute("opt") == 0) {
		addConcerned(series_id, series_name, icon);
	} else {
		return true ;
	}
}
/**
 * 关注车系
 * 
 * @param series_id
 *            车系id
 * @param series_name
 *            车系名称
 * @param icon
 *            图标
 * @return
 */
function addConcerned(series_id, series_name, icon) {
	$.post(BASE_URL + "concerned/add", {
		sid : series_id
	}, function(result) {
		if (result.error == 0) {
			icon.setAttribute("opt", 1);
			if(typeof(CHANG_ICON) != "undefined"){
			  icon.src = ICON_URL + "1_p.png";
			}
			if(typeof(ADD_SERIES_BOX) != "undefined"){
				cloneConcernedDom(icon,ADD_SERIES_BOX);
				icon.setAttribute("opt", 2);
			}
		} else if (result.error == 2) {
			alert("您还未登录，不能进行此操作");
		} else if (result.error == 3) {
			alert("车系'" + series_name + "'已添加");
		} else if (result.error == 4) {
			alert("未能成功添加车系：" + series_name);
		}
	}, 'json');
}
/**
 * 取消关注车系
 * 
 * @param series_id
 *            车系id
 * @param series_name
 *            车系名称
 * @param icon
 *            图标
 * @return
 */
function delConcerned(series_id, series_name, icon) {
	$.post(BASE_URL + "concerned/del", {
		sid : series_id
	}, function(result) {
		if (result.error == 0) {
			icon.setAttribute("opt", 0);
			if(typeof(CHANG_ICON) != "undefined"){
			  icon.src = ICON_URL + "0_p.png";
			}
			if(typeof(ADD_SERIES_BOX) != "undefined"){
				document.getElementById(ADD_SERIES_BOX).removeChild(icon.parentNode);
			}
		} else if (result.error == 2) {
			alert("您还未登录，不能进行此操作");
		} else if (result.error == 1) {
			alert("未能成功删除车系：" + series_name);
		}
	}, 'json');
}
function cloneConcernedDom(icon) {
	var addSeriesBox=document.getElementById(ADD_SERIES_BOX);
	var cd = icon.parentNode.cloneNode(true);
	var img = cd.getElementsByTagName("img");
	img[0].onmouseout = null;
	img[0].onmouseover = null;
	img[0].setAttribute("opt", 1);
	img[0].src = ICON_URL + "1_p.png";
	addSeriesBox.insertBefore(cd, addSeriesBox.firstChild);
}

 var car_on_sel=0;
 var HTML="";
 function carAttributes(cid){
	$.post(BASE_URL + "car/attributes", {
		c : cid
	}, function(html) {
		$("#car_attr").html(html);
		$("#series_conent").hide();
		$("#car_"+car_on_sel).removeClass("car_sel");
		$("#car_"+cid).addClass("car_sel");
		car_on_sel=cid;
		$("#car_attr").show();
	}, 'html');
}
function closeCA(){
	$("#car_attr").hide();
	$("#series_conent").show();
}