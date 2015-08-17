/**
 * 重设密码按钮事件
 */
function edit_psw_click(){
	var new_psw = document.getElementById("new_psw").value;
	var re_psw = document.getElementById("re_psw").value;
	if(check_psw()){
		var uid = document.getElementById("uid").value;
		var key = document.getElementById("key").value;
		setPassword(uid,key,new_psw);
	}
}
/**
 * 向服务器发送设置密码请求
 * @param uid
 * @param key
 * @param new_psw
 */
function setPassword(uid,key,new_psw){
	$.post(BASE_URL+"user/resetpassword",
	        {
				uid:uid,
				key:key,
				new_psw:new_psw
	        },
	        function(json){
	        	var result=json.result;
		        if(result==0){
					alert("您的新密码已经修改成功！请重新登录");
					//跳转到登录页面
		            window.location.href=BASE_URL+"user/login";
			    }else if(result==1){
					alert("该链接过期，请重新申请找回密码");
				}else if(result==2){
					document.getElementById("new_psw_error").innerHTML="密码位数为6到15位";
				}else if(result==3){
					alert("该链接过期，请重新申请找回密码");
				}
	        },
	        'json'
	  );
}
/**
 * 验证密码
 * @returns {Boolean}
 */
function check_psw(){
	var new_psw = document.getElementById("new_psw").value;
	var re_psw = document.getElementById("re_psw").value;
	var new_psw_alert = document.getElementById("new_psw_error");
	var re_psw_alert = document.getElementById("re_psw_error");
	new_psw_alert.innerHTML="";
	re_psw_alert.innerHTML="";
	if(new_psw.length == 0){
		new_psw_alert.innerHTML="请输入新密码";
		return false;
	}
	if(re_psw.length == 0){
		re_psw_alert.innerHTML="请输入确认密码";
		return false;
	}
	if(new_psw.length<6||new_psw.length>15){
		new_psw_alert.innerHTML="密码长度为6-15位";
		return false;
	}
	if(new_psw!=re_psw){
		re_psw_alert.innerHTML="确认密码不正确";
		return false;
	}
	return true;
}
/**
 * 根据密码的长度和内容，返回密码强度数值
 * @param word
 * @returns
 */
function EvaluatePassword(word) 
{ 
	if (word == "") 
	{ 
		return 0; 
	} 
	else if (word.length < 6) 
	{ 
		return 1; 
	} 
	else 
	{ 
		return word.match(/[a-z](?![^a-z]*[a-z])|[A-Z](?![^A-Z]*[A-Z])|\d(?![^\d]*\d)|[^a-zA-Z\d](?![a-zA-Z\d]*[^a-zA-Z\d])/g).length; 
	} 
} 
/**
 * 根据密码强度数值给出页面提示
 * @param password
 */
function CheckPassword(password) 
{ 
	document.getElementById("new_psw_error").innerHTML = function (pwd) 
	{ 
		switch (EvaluatePassword(pwd)) 
		{ 
			case 0: 
			return "未输入"; 
			case 1: 
			return "弱"; 
			case 2: 
			return "还可以"; 
			case 3: 
			return "比较强"; 
			case 4: 
			return "非常强"; 
		} 
	}(password) 
} 
/**
 * 输入密码时，删除确认密码输入框的内容
 */
function edit_password(password){
	if(document.getElementById("re_psw").value != ""){
		document.getElementById("re_psw").value = "";
		document.getElementById("re_psw_error").innerHTML="";
	}
}

/**
 * 输入确认密码时，和密码进行比较并提示
 */
function edit_repassword(){
	var password = document.getElementById("new_psw").value;
	var re_password = document.getElementById("re_psw").value;
	if(password==""){
		document.getElementById("new_psw_error").innerHTML="请输入新密码";
	}
	if(re_password!=""&&re_password.length>5){
		if(password != re_password){
			document.getElementById("re_psw_error").innerHTML="确认密码不正确";
		}else {
			document.getElementById("re_psw_error").innerHTML="密码正确";
		}
	}
}

/*
 * 处理键盘事件
 */
if (navigator.userAgent.indexOf("MSIE") > 0) {
// IE
	document.onkeydown = function() {
		if (event.keyCode == 13) {
			document.getElementById("edit_psw").click();
		} 
	}
} else {
	// 非IE
	window.onkeydown = function(event) {
		if (event.keyCode == 13) {
			document.getElementById("edit_psw").click();
		}
	}
}

/**
 * 新密码获得焦点时提示信息
 */
function new_psw_help(){
	var password = document.getElementById("new_psw").value;
	if(password==""){
		document.getElementById("new_psw_error").innerHTML="密码由6-16位半角字符（字母、数字、符号）组成，区分大小写";
	}
}