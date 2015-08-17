function get_password_click(){
	var mobile_phone = document.getElementById("mobile_phone").value;
	var email = document.getElementById("email").value;
	if(check_mobile_phone(mobile_phone)&&check_email(email)){
		document.getElementById("get_password_alert").innerHTML="正在提交申请，请稍后......";
		get_password(mobile_phone,email);
	}
}

function get_password(mobile_phone,email){
	var message = document.getElementById("get_password_alert");
	$.post(BASE_URL+"user/getpasswordbyemail",
	        {
				mobile_phone:mobile_phone,
				email:email
	        },
	        function(json){
	        	message.innerHTML="";
	        	if(json.result==0){
	        		message.innerHTML="申请成功，请查看你的邮件。"
					alert("您成功申请邮件找回密码，请到您的邮箱查看，并且在有效期7日内进行密码重置操作！");
					//跳转到登录页面
		            window.location.href=BASE_URL+"user/login";
		        }else if(json.result==1){
					alert("请正确填写手机号");
			    }else if(json.result==2){
					alert("请正确填写邮箱");
			    }else if(json.result==3){
					alert("手机号和电子邮件地址不匹配，请重新输入！");
			    }else if(json.result==4){
					alert("您已经申请过通过邮件找回密码，请到您的邮箱查看，并且在有效期7日内进行密码重置操作！");
			    }
	        },
	        'json'
	  );
}

//判断Email是否正确
function check_email(email)
{
	document.getElementById("email_alert").innerHTML="";
	var result = true;
	if(email!="")
	{
		var re =/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
		if (!re.test(email))
		{
			document.getElementById("email_alert").innerHTML="请输入正确的邮箱地址，如：example@example.com";
			result = false;
		}else{
			document.getElementById("email_alert").innerHTML="正确";
			result = true;
		}
	}else{
		document.getElementById("email_alert").innerHTML="请填写邮箱";
		result = false;
	}
	return result;
}

/**
 * 焦点离开手机号，先检测是否有效，再检查是否可用
 */
function check_mobile_phone(mobile_phone){
	document.getElementById("phone_alert").innerHTML="";
	//检查手机号是否有效
	if(mobile_phone!="")
	{
		if(isMobile(mobile_phone)){
			document.getElementById("phone_alert").innerHTML="正确";
			return true;
		}else{
			document.getElementById("phone_alert").innerHTML="手机号码不正确";
			return false;
		}
	}else{
		document.getElementById("phone_alert").innerHTML="请填写手机号";
		return false;
	}
	return true;
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

/*
 * 处理键盘事件
 */
if (navigator.userAgent.indexOf("MSIE") > 0) {
// IE
	document.onkeydown = function() {
		if (event.keyCode == 13) {
			document.getElementById("get_password").click();
		} 
	}
} else {
	// 非IE
	window.onkeydown = function(event) {
		if (event.keyCode == 13) {
			document.getElementById("get_password").click();
		}
	}
}