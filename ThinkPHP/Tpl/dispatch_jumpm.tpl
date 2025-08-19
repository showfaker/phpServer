<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
<title>页面提示</title>
<meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv='Refresh' content='{$waitSecond};URL={$jumpUrl}'>
<style>
html, body{margin:0; padding:0; border:0 none;font:14px Tahoma,Verdana;line-height:150%;background:white}
a{text-decoration:none; color:#174B73; border-bottom:1px dashed gray}
a:hover{color:#F60; border-bottom:1px dashed gray}
div.message{margin:10% auto 0px auto;clear:both;padding:5px;border:1px solid silver; text-align:center; width:45%}
span.wait{color:blue;font-weight:bold}
span.error{color:red;font-weight:bold}
span.success{color:blue;font-weight:bold}
div.msg{margin:20px 0px}
</style>
</head>
<body style="background:#f0f0f0">
<!--</br></br></br></br></br></br>-->
<script type="text/javascript">
     function refresh(){
            location.href = "{$jumpUrl}";
     }
     setTimeout("refresh()",1000);
</script>

<div class="message" style="position:absolute;left:27.5%" id="mess">
	<div class="msg">
	<present name="message" >
	<span class="success">{$msgTitle}{$message}</span>
	<else/>
	<span class="error">{$msgTitle}{$error}</span>
	</present>
	</div>
</div>
<script type="text/javascript">
	 document.getElementById("mess").style.top=parent.document.documentElement.scrollTop;
</script>
<!--
</br></br></br></br></br></br></br></br></br></br></br></br></br></br></br>--></br></br></br>
</br></br></br></br></br></br></br></br></br></br></br></br></br></br></br></br></br></br>
</br></br></br></br></br></br></br></br></br></br></br></br>
</br></br></br></br></br></br></br></br></br></br></br></br></br></br></br>
</body>
</html>