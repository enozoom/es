<!DOCTYPE html>
<html>
<head>
  <title><?php echo $tit?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
  <meta name="robots" content="noindex,nofollow,noarchive" />
  <style>
    body{font-family:"Microsoft YaHei";}
    #footer{
      width:70px;text-align: center;position: absolute;bottom:20px;left:50%;margin-left:-35px;
      border:1px solid #0088cc;height:30px;line-height:30px;
    }
    #footer a{color:#0088cc;text-decoration: none;font: 14px arial;}
    #footer span,#footer span:after{
      position: absolute;left:-14px;top:8px;
      border-style: solid;border-width: 7px;
      border-color: transparent #0088cc transparent transparent;
    }
    #footer span:after{
      border-right-color:#fff;
      content:" ";
      left:-6px;
      top:-7px;
    }
  </style>
</head>
<body>
<h1><?php echo $tit?></h1>
<p><?php echo $msg?></p>
<div id="footer"><a href="<?php echo ES_LINK?>">&copy;&nbsp;ES<?php echo number_format(ES_VERSION,1)?></a><span></span></div>
</body>
</html>