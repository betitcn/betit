$(function(){
		$("#name").change(function(){
			var msg = $("#name").val()+"，我在familyday.com.cn帮你注册了，账号是你的手机号码，密码是******，我们在这儿团聚吧!($space[name])";
			$('.tips2').html(msg);
		});
	});