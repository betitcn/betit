$(document).ready(function(){
 

   alert(localStorage.getItem('wxkey'));
 $.ajax({
    url:"../../source/wx_mine.php",
    data:"wxkey="+wxkey+"",
    type:"POST",
     success: function( data ) {
      /* Get the movies array from the data */
    
      }
    
    })
        })
function loginout1(auth){
      $.ajax({
    dataType: "jsonp",
    url: "http://www.betit.cn/capi/cp.php?ac=common&op=logout&m_auth="+ encodeURIComponent(auth),
     
    success: function( data ) {
      /* Get the movies array from the data */
      if(data.code==0){
      
      alert(data.msg);
      window.location.href="http://www.betit.cn/wx/wx.php";
      }else{
        alert(data.msg);
      }
    }
    })
        }