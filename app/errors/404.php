<!DOCTYPE html>
<html>
<head>
<title>您访问的页面不存在</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-type">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta name="robots" content="noindex,nofollow,noarchive" />
<style>
p{
 margin-top: 100px;text-align: center;font-weight: bold;font:22px 黑体;
}
#footer{
  width:60px;text-align: center;position: absolute;bottom:20px;left:50%;margin-left:-30px;
  background-color:#0088cc;height:30px;line-height:30px;border-radius: 4px;
}
#footer a{color:#01476a;text-decoration: none;font: 14px arial;text-shadow:0 1px 1px #a4dffd }
#footer span{
  position: absolute;left:-14px;top:8px;
  border-style: solid;border-width: 7px;
  border-color: transparent #0088cc transparent transparent;
}
</style>
</head>
<body>
<?php if(!empty($title) && !empty($msg)):?>
<p>【<?php echo $title?>】<?php echo $msg?></p>
<?php else:?>
<p>您访问的页面不存在或已经被删除<br>e@enozoom.com</p>
<?php endif?>
<div></div>
<div id="footer"><a href="http://www.enozoom.com">&copy;ES</a><span></span></div>
</body>
</html>