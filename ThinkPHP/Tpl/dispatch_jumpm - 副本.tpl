<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
<title>页面提示</title>
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.css">
<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
<script src="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv='Refresh' content='{$waitSecond};URL={$jumpUrl}'>
</head>
<body>
<script type="text/javascript">
     function refresh(){
            location.href = "{$jumpUrl}";
     }
     setTimeout("refresh()",3000);
</script>
<div data-role="page">
  <div data-role="content">
  <h2><present name="message" >
	<span class="success">{$msgTitle}{$message}</span>
	<else/>
	<span class="error">{$msgTitle}{$error}</span>
	</present></h2>
    <present name="closeWin" >
		页面将在 <span class="wait">{$waitSecond}</span> 秒后自动关闭，如果不想等待请点击 <a href="{$jumpUrl}">这里</a> 关闭
	<else/>
		页面将在 <span class="wait">{$waitSecond}</span> 秒后自动跳转，如果不想等待请点击 <a href="{$jumpUrl}">这里</a> 跳转
	</present>
  </div>
</div>
</body>
</html>