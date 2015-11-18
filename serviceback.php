<?php
	$link=new mysqli("localhost","root","root","mine");
	$sql="select send,receive,content from chat where seread=0 limit 1";
	$change="update chat set seread=1 where seread=0 limit 1";
	while (true) {
	$res=$link->query($sql);
		if($res->num_rows!=0){
			$link->query($change);
			$row=$res->fetch_assoc();
			$jsonstr=json_encode($row);
			echo $jsonstr;
			break;
		}
		sleep(1);
	}	
?>