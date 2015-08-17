
/**
 * 登录
 * by yeduo 2011.10.11
 */

/**
 * 用户登录请求
 * type 1登录页登录，2弹出层登录
 */
function user_login(type){
	var mobile_phone = document.getElementById("mobile_phone").value;
	var password = document.getElementById("password").value;
	var mobile_phone_error=document.getElementById("mobile_phone_error");
	var password_error =document.getElementById("password_error");
	mobile_phone_error.innerHTML="";
	if(mobile_phone==""){
		mobile_phone_error.innerHTML="请填写手机号码！";
		return false;
	}
	mobile_phone_error.innerHTML="";
	if(password==""){
		password_error.innerHTML="请输入密码！";
		return false;
	}
	password_error.innerHTML="";
	$.post(BASE_URL+"user/userlogin",
		        {
					mobile_phone:mobile_phone,
					password:password
		        },
		        function(json){
			        if(json.error==0){ //登录成功
			        	combine_favorite(type);
//			        	if(type==1){//登录页登录成功
//			        		document.location=BASE_URL+"user.html";
//			        	}else if(type==2){//弹出层登录成功
//			        		window.parent.$.colorbox.close();//关闭页面
//			        		location.reload();
//			        	}
				    }else{
                       show_error(json.error);
			        }		
		        },
		        'json'
		  );
}
function show_error(error){
	var mobile_phone_error=document.getElementById("mobile_phone_error");
	var password_error =document.getElementById("password_error");
    switch (error){
      case '1':
    	  mobile_phone_error.innerHTML="请正确填写手机号!";   
    	  break;
      case '2':
    	  password_error.innerHTML="密码格式不正确，为6-12位";  
    	  break;
      case "3":
    	  mobile_phone_error.innerHTML="手机号不存在!";  
    	  break;
      case "4":
    	  password_error.innerHTML="密码不正确，请重新登录!";  
    	  break;
    }
}

/**
 * 合并收藏
 */
function combine_favorite(type){
	$.post(BASE_URL+"favorite/checkfavoritecookie",
	        function(text){
		        if(text==0){
		        	//无数据
		        	if(type==1){//登录页登录成功
		        		document.location=BASE_URL+"user.html";
		        	}else if(type==2){//弹出层登录成功
		        		window.parent.$.colorbox.close();//关闭页面
		        		location.reload();
		        	}
		        }else{
		        	//有数据
		        	var message="我们检测到您新增了"+text+"条收藏，需要导入到您的收藏中吗?";
		        	var r=confirm(message);
					if(r==true){
						$.post(BASE_URL+"favorite/combinefavorite",
						        function(num){
									var msg="成功为您导入了"+num+"条收藏";
									alert(msg);
									if(type==1){//登录页登录成功
						        		document.location=BASE_URL+"user.html";
						        	}else if(type==2){//弹出层登录成功
						        		window.parent.$.colorbox.close();//关闭页面
						        		location.reload();
						        	}
						        },
						        'text'
						  );
					}else{
			        	if(type==1){//登录页登录成功
			        		document.location=BASE_URL+"user.html";
			        	}else if(type==2){//弹出层登录成功
			        		window.parent.$.colorbox.close();//关闭页面
			        		location.reload();
			        	}
					}
		        }
	        },
	        'text'
	  );
}

/*
 * 处理键盘回车事件
 */
if (navigator.userAgent.indexOf("MSIE") > 0) {
// IE
	document.onkeydown = function() {
		if (event.keyCode == 13) {
			document.getElementById("login").click();
		} 
	}
} else {
	// 非IE
	window.onkeydown = function(event) {
		if (event.keyCode == 13) {
			document.getElementById("login").click();
		}
	}
}