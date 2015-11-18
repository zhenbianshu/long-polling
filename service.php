<?php 
header("content-type:text/html;charset=utf-8");
set_time_limit(0);
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		*{margin: 0;padding: 0;}
		#messages{height: 300px;width: 500px;overflow: auto;border: 1px solid #333;}
		#messages_content{resize:none;}
		#to_user{display: inline-block;width: 6em;height:20px;text-align: center;}
		#back{}
	</style>
	<script type="text/javascript">
	function link(){
		var xhr=null;
		xhr=new XMLHttpRequest();
		xhr.open('GET','serviceback.php',true);
		xhr.send();
		xhr.onreadystatechange=function(){
			if (xhr.readyState==4) {
				if(xhr.responseText!=''){
					var newp=document.createElement('p');
					var con=JSON.parse(xhr.responseText);
					var raw=con.send+"对<span>"+con.receive+"</span>说：<br />"+con.content;
					newp.innerHTML=raw;
					document.getElementById('messages').appendChild(newp);
					var cc=document.getElementsByTagName('span').length;
					document.getElementsByTagName('span')[cc-1].addEventListener("click",function(){
						document.getElementById('to_user').innerHTML=this.innerHTML;
					},false)
				}
				setTimeout("link()",300);
			};
		}
	}
		function send(){
			var content=document.getElementById('messages_content').value;
			var send="kefu";
			var receive=document.getElementById('to_user').innerHTML;
			document.getElementById('messages_content').value='';
			cont={
				"send":send,
				"receive":receive,
				"content":content
			}
			cont=JSON.stringify(cont);
			var hhh=null;
			hhh=new XMLHttpRequest();
			hhh.open('POST','service_process.php',true);
			hhh.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			hhh.send("cont="+cont);
		}
	link();
	</script>
</head>
<body>
<div id="messages"></div><br>
对<div id="to_user">guke002</div>说：<br />
<textarea id="messages_content" name="messages_content" rows="5" cols="50">
</textarea>
<br />
<input type="submit" value="发送" onclick="send()">
</body>
</html>