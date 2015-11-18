<?php 
	$cont=$_POST['cont'];
	$jsob=json_decode($cont,true);
		if(($jsob['send']!='')&&($jsob['receive']!='')&&($jsob['content']!='')){
			$link=new mysqli("localhost","root","root","mine");
			$sql="insert into chat (send,receive,content) values('".$jsob['send']."','".$jsob['receive']."','".$jsob['content']."')";
			$link->query($sql);
		}
?>