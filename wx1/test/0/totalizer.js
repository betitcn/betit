function Write2Text(newNum,filepath)
{
var fso = new ActiveXObject("Scripting.FileSystemObject");
var f = fso.CreateTextFile(filepath,true);
f.write(newNum);
f.Close();
}

function ReadText(filepath) {
var ForReading=1;
var fso=new ActiveXObject("Scripting.FileSystemObject");
var f=fso.OpenTextFile(filepath,ForReading,true);
return(f.ReadLine());
}

function Totalizer(filepath){
try
{
var txt=ReadText(filepath);
var num=parseInt(txt)+1;
Write2Text(num,filepath);
return(num);
}
catch(err)
{
Write2Text(1,filepath);
return(1);
}
}