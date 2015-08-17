/**
 * 通过checkbox启用或禁用注册按钮
 * by yeduo 2011.10.10
 */
function cbclick(obj){
	if(obj.checked){
		document.getElementById("sign").disabled="";
	}
	else{
		document.getElementById("sign").disabled="false";
	}
}

/**
 * 注册新用户
 * by yeduo 2011.10.10
 */
function user_sign(){
	if(check_userinfo())
	{
		$.post(BASE_URL+"user/checkuserinfo",
		        {
		          phone:document.getElementById("mobile_phone").value
		        },
		        function(json){
		        	document.getElementById("phone_alert").style.display="";
					document.getElementById("phone_alert").innerHTML=json.msg;
					if(json.error==1){
						$.post(BASE_URL+"user/adduser",
						        {
									mobile_phone:document.getElementById("mobile_phone").value,
									email:document.getElementById("email").value,
									password:document.getElementById("password").value,
									input_captcha:document.getElementById("input_captcha").value.toLowerCase()
						        },
						        function(result){
							        if(result==0){
							            alert("恭喜你注册成功,可以用新号码登录了！");
							            //跳转到登录页面
							            window.location.href=BASE_URL+"user/login";
							            //update_captcha();
							        }else{
							        	show_error(result);
									}
						        },
						        'text'
						  );
					}else if(json.error==2){
						alert("该手机号已被注册");
					}	
		        },
		        'json'
		  );
		
	}
}

/**
 * 校验数据填写是否符合，提交时最后校验
 */
function check_userinfo(){
	var mobile_phone = document.getElementById("mobile_phone").value;
	var email = document.getElementById("email").value;
	var password = document.getElementById("password").value;
	var repeat_password = document.getElementById("repeat_password").value;
	var input_captcha = document.getElementById("input_captcha").value.toLowerCase();
	document.getElementById("phone_alert").innerHTML="";
	if(mobile_phone==""){
		document.getElementById("phone_alert").innerHTML="请输入你的手机号码，它将成为你未来的登录帐号";
		return false;
	}else if(!isMobile(mobile_phone)){
		document.getElementById("phone_alert").innerHTML="手机号码不正确";
		return false;
	}
	check_mobile_phone(mobile_phone);
	if(!check_email(email)){
		document.getElementById("email_alert").innerHTML="请输入正确的邮箱地址。";
		return false;
	}else if(password==""||password.length<6){
		document.getElementById("password1_alert").innerHTML="密码位数为6到15位";
		return false;
	}else if(repeat_password==""){
		document.getElementById("password_alert").style.display="";
		document.getElementById("password_alert").innerHTML="请输入重复密码";
		return false;
	}else if(password!=repeat_password){
		document.getElementById("password_alert").style.display="";
		document.getElementById("password_alert").innerHTML="两次密码不一致";
		return false;
	}else if(input_captcha==""){
		document.getElementById("captcha_alert").innerHTML="请输入验证码";
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
	document.getElementById("password1_alert").innerHTML = function (pwd) 
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
    if(password.length<6)
	{
		document.getElementById("password1_alert").style.display="";
		document.getElementById("password1_alert").innerHTML="密码位数为6到15位";
	}else{
		document.getElementById("password1_alert").style.display="";
		document.getElementById("password1_alert").innerHTML="密码正确";
	}
	document.getElementById("password_alert").style.display="none";
	if(document.getElementById("repeat_password").value != ""){
		document.getElementById("repeat_password").value = "";
	}
}

/**
 * 输入确认密码时，和密码进行比较并提示
 */
function edit_repassword(){
	var password = document.getElementById("password").value;
	var re_password = document.getElementById("repeat_password").value;
	if(re_password!=""&&re_password.length>5){
		if(password != re_password){
			document.getElementById("password_alert").style.display="";
			document.getElementById("password_alert").innerHTML="两次密码不一致";
		}else {
			document.getElementById("password_alert").style.display="";
			document.getElementById("password_alert").innerHTML="密码正确";
		}
	}
}

/**
 * 焦点离开手机号，先检测是否有效，再检查是否可用
 */
function change_mobile_phone(mobile_phone){
	//检查手机号是否有效
	if(mobile_phone!="")
	{
		if(isMobile(document.getElementById("mobile_phone").value)){
			check_mobile_phone(mobile_phone);
		}else{
			document.getElementById("phone_alert").style.display="";
			document.getElementById("phone_alert").innerHTML="手机号码不正确";
			return false;
		}
	}else{
		document.getElementById("phone_alert").style.display="";
		document.getElementById("phone_alert").innerHTML="请输入你的手机号码，它将成为你未来的登录帐号";
		return false;
	}
	
}

/**
 * 检测手机号是否被注册过
 */
function check_mobile_phone(mobile_phone){ 
	$.post(BASE_URL+"user/checkuserinfo",
	        {
	          phone:mobile_phone
	        },
	        function(json){
	        	document.getElementById("phone_alert").style.display="";
				document.getElementById("phone_alert").innerHTML=json.msg;	
	        },
	        'json'
	  );
}

//将全角转换为半角
function StoBCcase(str)
{
    var result = '';
    for (var i=0 ; i<str.length; i++)
    {
        code = str.charCodeAt(i);//获取当前字符的unicode编码
        if (code == 12288)//空格
        {
            result += String.fromCharCode(str.charCodeAt(i) - 12288 + 32);
        }
        else if (code == 12290)//句号
        {
            result += ".";
        }
        else if (code >= 65281)
        {
            result += String.fromCharCode(str.charCodeAt(i) - 65248);//把全角字符的unicode编码转换为对应半角字符的unicode码
        }
        else
        {
            result += str.charAt(i);
        }
    }
    return result;
}

//判断是否为数字（1234567890）(value1为字符串)
function isNumeric(value1)
{
	var reIntValue="1234567890";
	value1 = StoBCcase(value1);//将值中的全角全部转换为半角
	if (value1!="")
	{
	  for(var t=0;t<value1.length;t++)
		{
		    if (reIntValue.indexOf(value1.charAt(t))==-1)
				return false;
		}
	}
	else
		return false;
	return true;
}

//判断手机号码前三位是否有误
function isValidCMCCMobile(mobile)
{
	if(mobile!="")
	{
		if (mobile.length<11)
			return false;
		//判断是否为数字，并将全角转为半角
		if (!isNumeric(mobile))
			return false;
		var len = mobile.length;
		if (mobile.length>11)
		{
			prix = mobile.substr(mobile.length-11,3);
		}
		else
		{
			prix = mobile.substr(0,3);
		}
		
		var aryPrix = new Array("130","131","132","133","134","135","136","137","138","139","147","150","151","152","153","155","156","157","158","159","182","186","188","189","187","180");
		for(alength=0;alength<aryPrix.length;alength++)
		{
			if (prix==aryPrix[alength])
				return true;
		}
		return false;
	}
	return false;
}
/**
 * 判断手机号码是否正确
 */
function isMobile(strMobile)
{
	if(strMobile!="")//传进的手机号不为空
	{
		var invaildMobile = "";
		var iList,re;
		
		re=/[^\(（,，]*[\(|（](\d{11})[\)|）](,|$)/g;  //初始化正则表达式设置
		strMobile = strMobile.replace(re,"$1$2");
		re=/(\d{11})，/g;
		strMobile = strMobile.replace(re,"$1,");
		
		if(!isValidCMCCMobile(strMobile)) //判断手机号码是否有误
			return false;
	}else{
		return false;
	}
	return true;
}

//判断Email是否正确
function check_email(email)
{
	var result = true;
	if(email!="")
	{
		var re =/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
		if (!re.test(email))
		{
			document.getElementById("email_alert").style.display="";
			document.getElementById("email_alert").innerHTML="请输入正确的邮箱地址，如：example@example.com";
			result = false;
		}else{
			document.getElementById("email_alert").style.display="";
			document.getElementById("email_alert").innerHTML="邮箱正确";
			result = true;
		}
	}else{
		document.getElementById("email_alert").style.display="";
		document.getElementById("email_alert").innerHTML="请输入你的常用邮箱，如：example@example.com，它可以为你找回密码";
		result = false;
	}
	return result;
}

/**
 * 刷新验证码
 */
function update_captcha(){
	$.post(BASE_URL+"user/updatecaptcha",
	        {

	        },
	        function(result){
		    	document.getElementById("captcha").innerHTML=result.image;
	        },
	        'json'
	  );
}

/**
 * 根据错误码显示错误提示
 * @param error
 */
function show_error(error){
	var mobile_phone_error=document.getElementById("phone_alert");
	var email_error=document.getElementById("email_alert");
	var password1_error =document.getElementById("password1_alert");
	var password_error =document.getElementById("password_alert");
	var captcha_error =document.getElementById("captcha_alert");
    switch (error){
      case "1":
    	  mobile_phone_error.innerHTML="请正确填写手机号!";   
    	  break;
      case "2":
    	  email_error.innerHTML="请输入正确的邮箱地址。";  
    	  break;
      case "3":
    	  password1_error.innerHTML="密码格式不正确!";  
    	  break;
      case "4":
    	  captcha_error.innerHTML="验证码过期，请刷新页面后再注册";  
    	  break;
      case "5":
    	  captcha_error.innerHTML="验证码错误，请重新注册";  
    	  break;
    }
}

/*
 * 处理键盘的回车事件
 */
if (navigator.userAgent.indexOf("MSIE") > 0) {
// IE
	document.onkeydown = function() {
		if (event.keyCode == 13) {
			document.getElementById("sign").click();
		} 
	}
} else {
	// 非IE
	window.onkeydown = function(event) {
		if (event.keyCode == 13) {
			document.getElementById("sign").click();
		}
	}
}

/**
 * 手机获得焦点时提示信息
 */
function mobile_phone_help(){
	var mobile_phone = document.getElementById("mobile_phone").value;
	if(mobile_phone==""){
		document.getElementById("phone_alert").innerHTML="请输入你的手机号码，它将成为你未来的登录帐号";
	}
}

/**
 * email获得焦点时提示信息
 */
function email_help(){
	var email = document.getElementById("email").value;
	if(email==""){
		document.getElementById("email_alert").innerHTML="请输入你的常用邮箱，如：example@example.com，它可以为你找回密码";
	}
}

/**
 * 创建密码获得焦点时提示信息
 */
function password_help(){
	var password = document.getElementById("password").value;
	if(password==""){
		document.getElementById("password1_alert").innerHTML="密码由6-16位半角字符（字母、数字、符号）组成，区分大小写";
	}
}