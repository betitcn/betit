function loginout(auth){
      $.ajax({
    dataType: "jsonp",
    url: "http://www.betit.cn/capi/cp.php?ac=common&op=logout&m_auth="+ encodeURIComponent(auth),
     
    success: function( data ) {
      /* Get the movies array from the data */
      if(data.code==0){
      
      alert(data.msg);
      location.href("http://www.betit.cn/wx/wx.php");
      }else{
        alert(data.msg);
      }
    }
    })
        }