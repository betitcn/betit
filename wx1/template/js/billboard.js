
		$(document).ready(function(){
		$.ajax({
			dataType: "jsonp",
			url: "http://www.betit.cn/capi/space.php?do=top&view=experience",
		   
			success: function( data ) {
			  /* Get the movies array from the data */
				
			  if(data.code==0){
					data=data.data;
					//data.message = html_entity_decode(data.message);
					//data.dateline = date('Y-m-d H:i',data.dateline);
					//data.user = getUser(data.uid, auth);
					//data.idtype = "photoid";
					//data.piclistlen = data.piclist.length;
					$("#detailTemplate").tmpl(data ).appendTo('#detail-panel');
					
					

			  }else{
				alert("123");
			  }
			}
		  })
})

