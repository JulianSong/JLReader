/**
 * 删除某个车系的浏览历史
 * 
 * @param sid
 * @param obj
 * @return
 */
function delHistory(sid, obj) {
	var history = document.getElementById("history");
	history.removeChild(obj.parentNode);
	var sid_string = getCookie(cname);
	sid_array = sid_string.split(",");
	del_array = Array();
	while (row = sid_array.shift()) {
		if (row != sid) {
			del_array.push(row);
		}
	}
	setCookie(cname, del_array.join(), 30);
}
/**
 * 清除所有浏览历史
 * 
 * @return
 */
function delAllHistory() {
	var history = document.getElementById("history");
	history.innerHTML = "";
	setCookie(cname, "", 30);
}
/**
 * 获得cookie的值
 * 
 * @param c_name
 *            cookie名称
 * @return
 */
function getCookie(c_name) {
	if (document.cookie.length > 0) {
		c_start = document.cookie.indexOf(c_name + "=")
		if (c_start != -1) {
			c_start = c_start + c_name.length + 1
			c_end = document.cookie.indexOf(";", c_start)
			if (c_end == -1)
				c_end = document.cookie.length
			return unescape(document.cookie.substring(c_start, c_end))
		}
	}
	return ""
}
/**
 * 给cookie设置值
 * 
 * @param c_name
 *            cookie名称
 * @param value
 *            cookie值
 * @param expiredays
 *            cookie时间
 * @return
 */
function setCookie(c_name, value, expiredays) {
	var exdate = new Date()
	exdate.setDate(exdate.getDate() + expiredays)
	document.cookie = c_name + "=" + escape(value)
			+ ((expiredays == null) ? "" : ";expires=" + exdate.toGMTString())
}
