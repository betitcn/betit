function iOSversion() {
  if (/iP(hone|od|ad)/.test(navigator.platform)) {
	// supports iOS 2.0 and later: <http://bit.ly/TJjs1V>
	var v = (navigator.appVersion).match(/OS (\d+)_(\d+)_?(\d+)?/);
	return [parseInt(v[1], 10), parseInt(v[2], 10), parseInt(v[3] || 0, 10)];
  }
}




function pubilchPhoto(picid, message, tags, auth){
		$.ajax({
		dataType: "jsonp",
		url: "http://www.familyday.com.cn/dapi/cp.php?ac=photo&m_auth=" + encodeURIComponent(auth) + "&title["+picid+"]=&message=" + encodeURIComponent(message) + "&friend=0&come=wx&makefeed=1&photosubmit=1&tags=" + encodeURIComponent(tags),
	   
		success: function( data ) {
		  /* Get the movies array from the data */

		  if(data.error==0){
			data = data.data;
			alert("提交成功，获得了"+data.credit+"点积分");
			window.location.href = "http://www.familyday.com.cn/wx/wx.php?do=feed&wxkey="+$('#wxkey').val();
		  }else{
			alert(data.msg);
		  }
		}
	  });
}

function publicBlog(subject, message, tags, auth){
	$.ajax({
		dataType: "jsonp",
		url: "http://www.familyday.com.cn/dapi/cp.php?ac=blog&m_auth=" + encodeURIComponent(auth) + "&message=" + encodeURIComponent(message) + "&friend=0&come=wx&makefeed=1&blogsubmit=1&tags=" + encodeURIComponent(tags) + "&subject=" + encodeURIComponent(subject),
	   
		success: function( data ) {
		  /* Get the movies array from the data */

		  if(data.error==0){
			data = data.data;
			alert("提交成功，获得了"+data.credit+"点积分");
			window.location.href = "http://www.familyday.com.cn/wx/wx.php?do=feed&wxkey="+$('#wxkey').val();
		  }else{
			alert(data.msg);
		  }
		}
	  });
}

function submitBlog(){
	var pattern = /^[\s]{0,}$/g;
	if (!pattern.test($('#message').val())){
		publicBlog($('#subject').val(), $('#message').val(), $('#tags').val(), $('#auth').val());
	}else{
		alert("至少写一点东西！");
	}
}
	
function submitPhoto(){
	var pattern = /^[\s]{0,}$/g;
	if (!pattern.test($('#message').val())){
		pubilchPhoto($('#picid').val(), $('#message').val(), $('#tags').val(), $('#auth').val());
	}else{
		alert("至少写一点东西！");
	}
}

$(function(){
	$('#auth').val(localStorage.getItem('auth'));
	$('.msg-image').click(function(){
		 ver = iOSversion();
		if (typeof(ver) != 'undefined' && ver != null){
			if (ver[0] < 6) {
			  alert('ios系统当前仅6.0以上的系统支持上传图片,请更新你的系统');
			}else{
				$('input[name=Filedata]').trigger('click');
			}
		}else{
			$('input[name=Filedata]').trigger('click');
		}
	});

	$('input[name=Filedata]').change(function(e){
		 var file = e.target.files[0];
		 $('#pic').remove();
		 $.canvasResize(file, {
					width   : 640,
					height  : 0,
					crop    : false,
					quality : 60,
					callback: function(data, width, height, poolAndriod){
						// SHOW AS AN IMAGE
						// =================================================
						
						$('<img>').load(function(){ 
							
							$(this).appendTo('.msg-image');
							
							
						}).attr('src', data).attr('id','pic');

						// /SHOW AS AN IMAGE
						// =================================================
						
						// IMAGE UPLOADING
						// =================================================
							
						// Create a new formdata
						var fd = new FormData();
						// Add file data
						var f = $.canvasResize('dataURLtoBlob',data);
						f.name = file.name;
						if (poolAndriod)
							fd.append($('input').attr('name'), file, $('input[type=file]')[0].files[0].name);
						else
							fd.append($('input').attr('name'), f, $('input[type=file]')[0].files[0].name);
						fd.append("op", "uploadphoto");
						fd.append("topicid", "0");
						fd.append("pic_title", "from wx");
						fd.append("m_auth", $('#auth').val());

						 $.ajax({
							url        : 'http://www.familyday.com.cn/dapi/cp.php?ac=upload',
							type       : 'POST',
							data       : fd,
							dataType   : 'json',
							contentType: false,
							processData: false,
							beforeSend : function (xhr) {
								xhr.setRequestHeader("pragma", "no-cache");
							},
							xhr        : function(){
								var xhr = new window.XMLHttpRequest();
								//Upload progress
								xhr.upload.addEventListener("progress", function(e){
									if (e.lengthComputable) {
										var loaded = Math.ceil((e.loaded / e.total) * 100);
										$('#pbar').css({
											'width':loaded + "%"
										}).html(loaded + "%");
									}
								}, false);
								return xhr;
							} 
						}).done(function (response) {
						   
						   
							if(response.error==0){
								// Complete
								$("#picid").val(response.data.picid);
								console.log(response.data.picid);
								$('#pbar').html('完成');
							}else{
								 $('#pbar').html('失败');
							}
							
						});
					   
							
						// /IMAGE UPLOADING
						// =================================================
						}
				});  
	});


	$('#select-choice-1').change(function(){
		$('#tags').val($('#select-choice-1').val());
	});

   $('#select-choice-2').change(function(){
		window.location.href = $('#select-choice-2').val();
	});

 

});

