
var i=1,j 
function shoppingcat(){ 
if(i<=12){ 
document.getElementById('div'+i).style.display='block' 
i++ 
setTimeout('shoppingcat()',1000) 
} 
else if(i=13) 
{document.getElementById('div'+j).style.border='3px #666 double' 
document.getElementById('div'+j).style.background='#ccc'} 
} 