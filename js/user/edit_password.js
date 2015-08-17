/**
 * 确认修改按钮事件，获取原密码、新密码和确认密码，然后进行校验，通过后发送修改请求
 */
function edit_psw_click(){
	var old_psw = document.getElementById("old_psw").value;
	var new_psw = document.getElementById("new_psw").value;
	var re_psw = document.getElementById("re_psw").value;
	if(check_psw()){
		editPassword(old_psw,new_psw);
	}
}

/**
 * 校验密码
 * @returns {Boolean}
 */
function check_psw(){
	var old_psw = document.getElementById("old_psw").value;
	var new_psw = document.getElementById("new_psw").value;
	var re_psw = document.getElementById("re_psw").value;
	var old_psw_alert = document.getElementById("old_psw_error");
	var new_psw_alert = document.getElementById("new_psw_error");
	var re_psw_alert = document.getElementById("re_psw_error");
	old_psw_alert.innerHTML="";
	re_psw_alert.innerHTML="";
	if(old_psw.length == 0){
		old_psw_alert.innerHTML="请输入原密码";
		return false;
	}
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
 * 修改密码
 */
function editPassword(old_psw,new_psw){
	$.post(BASE_URL+"user/editpassword",
	        {
				old_psw:old_psw,
				new_psw:new_psw
	        },
	        function(json){
		        var result=json.result;
		        if(result==0){
					alert("修改成功");
					document.location=BASE_URL+"user.html"
			    }else if(result==1){
					document.getElementById("old_psw_error").innerHTML="密码位数为6到15位";
				}else if(result==2){
					document.getElementById("new_psw_error").innerHTML="密码位数为6到15位";
				}else if(result==3){
					alert("页面过期，需重新登录");
				}else if(result==4){
					document.getElementById("old_psw_error").innerHTML="原密码不正确";
				}
	        },
	        'json'
	  );
}

/*
 * 处理键盘回车事件
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
 * 新密码获得焦点时提示信息
 */
function new_psw_help(){
	var password = document.getElementById("new_psw").value;
	if(password==""){
		document.getElementById("new_psw_error").innerHTML="密码由6-16位半角字符（字母、数字、符号）组成，区分大小写";
	}
}