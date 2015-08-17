/**
 * 修改
 */
function edit(){
	document.getElementById("ok_edit").style.display="none";
	document.getElementById("do_edit").style.display="";
	document.getElementById("email").style.display="none";
	document.getElementById("edit_email").style.display="";
}
/**
 * 取消修改
 */
function cancel(){
	document.getElementById("ok_edit").style.display="";
	document.getElementById("do_edit").style.display="none";
	document.getElementById("email").style.display="";
	document.getElementById("edit_email").style.display="none";
	document.getElementById("email_alert").style.display="none";
}

function save(){
	var email = document.getElementById("edit_email").value;
	var mobile_phone = document.getElementById("mobile_phone").innerHTML;
	if(email==""||email==null){
		alert("请填写邮箱");
	}else if(check_email(email)){
		$.post(BASE_URL+"user/updateuserinfo",
		        {
					mobile_phone:mobile_phone,
					email:email
		        },
		        function(result){
			    	if(result.error==1){
//			    		location.reload();	
			    		alert("修改成功");
			    		document.location=BASE_URL+"user.html"
					}else if(result.error==2){
						alert('邮箱格式错误，修改失败');
					}
		        },
		        'json'
		 );
	}else{
		alert("邮箱格式错误,应为: xxx@xxx.xxx");
	}
}

//判断Email是否正确
function check_email(email)
{
	var result = true;
	if(email!="")
	{
		//var re =/^([.a-zA-Z0-9_ -])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
		var re =/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
		if (!re.test(email))
		{
			document.getElementById("email_alert").style.display="";
			document.getElementById("email_alert").innerHTML="邮箱格式错误,应为: example@example.com";
			result = false;
		}else{
			document.getElementById("email_alert").style.display="";
			document.getElementById("email_alert").innerHTML="邮箱正确";
			result = true;
		}
	}else{
		document.getElementById("email_alert").style.display="";
		document.getElementById("email_alert").innerHTML="请填写邮箱";
		result = false;
	}
	return result;
}