<?php 
header("content-type:text/html;charset=utf-8"); 
  $options = array(  
    'http' => array(  
      'method' => 'POST',  
      'header' => "Content-Type:application/x-www-form-urlencoded; charset=UTF-8\n\r",  
      'content' => 'cont={"send":"kefu","receive":"guke002","content":"zengmohuishi..."}',  
      'timeout' => 15 * 60 
    )  
  );  
  $context = stream_context_create($options);  
  $result = file_get_contents('http://localhost/service/service.php',false,$context);  
  echo $result;

?>
网页聊天室之js和jQuery实现ajax长轮询
众所周知，HTTP协议是无状态的，所以一次的请求都是一个单独的事件，和前后都没有联系。所以我们在解决网页实时聊天时就遇到一个问题，如何保证与服务器的长时间联系，从而源源不段地获取信息。
一直以来的方式无非有这么几种：
1、长连接，即服务器端不断开联系，PHP服务器端用ob系列函数来不停的读取输出；
2、Flash socket，flash的as3语言，创建一个socket服务器用来处理信息。
3、轮询，顾名思义就是不停地发送查询消息，一有新消息立刻更新。
4、长轮询，是轮询的升级版，需要服务器端的配合。
5、websocket，HTML5的通信功能，建立一个与服务器端的专用接口ws协议来进行通讯，改天研究一下这个。
这篇博文总结一下用JS和JQ两种方式（其实不同就是js和jq的实现），实现AJAX长轮询。

长轮询的思想：
如图：用AJAX发送询问信息，服务器在没有信息要返回的时候进入无限等待。由于AJAX异步的特性，PHP在服务器端执行等待不会影响到页面的正常处理。一旦服务器查询到返回信息，服务器返回信息，AJAX用回调函数处理这条信息，同时迅速再次发送一个请求等待服务器处理。
与传统轮询相比，长轮询在服务器没的返回信息的时候进入等待，减少了普通轮询服务器无数次的空回复。可以这样认为，长轮询使服务器每次的返回更有目的性，而不是盲目返回。

长轮询的服务器端实现：
聊天信息存储：
数据表包括
create table msg{
msgid char(16) not null primary key auto_increment,
sender char(16) not null,
receiver char(16) not null,
content text,     //信息内容用text类型，存储量可达到65535字符
senderRead tinyint enum(0,1) default 0,
receiverRead tinyint enum(0,1) default 0    //设置一个是否已读的flag标记
}
PHP方面：
  set_time_limit(0);//设置脚本超时时间为无限，不然在过了超时时间后脚本会自动关闭，轮询失败。
  $link=new mysqli("host","user","password","database");
  $search="select sender,receiver,content from msg where receiverRead=0 limit 1";//限制每次读出一条数据，便于修改其已读flag
  $change="update chat set receiverRead=1 where receiverRead=0 limit 1";
  while (true) {    //进入无限循环
  $res=$link->query($sql);  //查询结果
    if($res->num_rows!=0){  //当有未读信息时读取信息
      $link->query($change);//将信息的已读flag设为1
      $msg=$res->fetch_assoc();
      $jsonstr=json_encode($msg);//取到信息，将信息用转码为json格式，返回给JS
      echo $jsonstr;
      break;//输出信息后退出while循环，结束当前脚本
    }
    usleep(1000);//如果没有信息不会进入if块，但会执行一下等待1秒，防止PHP因循环假死。
  }

客户端实现：
JS：
function link(){
    var xhr=null;//先设置xhr为空，为了轮询时再次调用函数对xhr重用，引发错误
    xhr=new XMLHttpRequest();
    xhr.open('GET','serviceback.php',true);//第三个参数一定要设置为true，异步不阻塞，不会影响到后面JS的执行。
    xhr.send();
    xhr.onreadystatechange=function(){
      if (xhr.readyState==4) { 严密也可加使用（xhr.readyState==4 && xhr.status ==200）限定服务器响应码为200时才进行处理。
        if(xhr.responseText!=''){
          process...  //服务器端返回信息，且返回信息不为空，则开始处理返回信息。
        }
        setTimeout("link()",300);//递归再次调用link()函数，用setTimeOut()设置延时是因为服务器端进行sql操作时会耗时，当有新信息时，在服务器将要置已读flag为1还未成功时，AJAX可能已经又发出多条查询信息了，会导致一条信息多次返回。
      }
    };
  }
jQuery：
var link={  //jQuery的AJAX执行的配置对象
      type:"GET",//设置请求方式，默认为GET，
      async:true,//设置是否异步，默认为异步
      url:"customback.php",
      dataType:"json",//设置期望的返回格式，因服务器返回json格式，这里将数据作为json格式对待
      success:function (msg){
          process...
          setTimeout("link()",300);
      }//成功时的回调函数，处理返回数据，并且延时建立新的请求连接
    }
$.ajax(link);//执行ajax请求。

当然，我们还可以对这个程序进行扩充：
添加发送聊天窗口：
新建一个函数用来处理ajax的POST请求，用ajax将发信人，每次发送的信息，收信人发送到服务器端，并设置一个单独的PHP脚本处理信息，将信息插入数据库。
需要注意的是，用JS原生实现POST请求发送信息时，要设置ajax对象的HTTP头，模拟表单提交的操作：
xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");

改为聊天室形式：
为了防止每次都查询到全部信息，我们对数据库的查询操作更改一下，设置idflag=0,每次查询后，设置idflag为查询到的数据的id，查询时我们查询比idflag大的ID，即，新添加进去的信息。
这样，一个简单的聊天室程序就做好了。