<?php 
header("content-type:text/html;charset=utf-8");
if (!isset($_COOKIE['name'])) {
	setcookie("name","user".rand(20000,90000));
}
echo "<div id='cookiename' style='color:red;display:none;'>".$_COOKIE['name']."</div>";
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
		#to_user{display: inline-block;width: 10em;height:20px;text-align: center;}
	</style>
	<script type="text/javascript" src="./jquery-1.11.3.min.js"></script>
	<script type="text/javascript">
	function link(){
		console.log("start")
		$.ajax({
			type:"POST",
			url:"customback.php",
			dataType:"json",
			success:function (con){
				/*console.log(con);
				var raw=con.send+"对<span>"+con.receive+"</span>说：<br />"+con.content;
				console.log(raw);
				$('"<div>"+raw+"</div>"').appendTo($("#messages"));*/
				var newp=document.createElement('p');
				var raw=con.send+"对<span>"+con.receive+"</span>说：<br />"+con.content;
				newp.innerHTML=raw;
				document.getElementById('messages').appendChild(newp);
				var cc=document.getElementsByTagName('span').length;
				document.getElementsByTagName('span')[cc-1].addEventListener("click",function(){
					document.getElementById('to_user').innerHTML=this.innerHTML;
				})
				setTimeout("link()",300);
			}
		})
	}
	function send(){
		cont={
			"send":$('#cookiename').html(),
			"receive":"kefu",
			"content":$('#messages_content').val()
		}
		$.post("custom_process.php",{"cont":cont});
		$("#messages_content").val("");
	}
	$(function(){
		link();
	})
	</script>
</head>
<body>
<div id="messages"></div><br>
对<span id="to_user">kefu</span>说：<br />
<textarea id="messages_content" name="messages_content" rows="5" cols="50">
</textarea>
<br />
<input type="submit" value="发送" onclick="send()">
</body>
</html>