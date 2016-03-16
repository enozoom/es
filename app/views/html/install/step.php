<?php echo \es\helpers\generate_html5_head('ES数据初始化 - '.ES_POWER,'install-install','','',FALSE,TRUE)?>


<div id="step1" class="step d_h">
  <form class="iform">
    <p class="t_center">①配置数据库</p>
    <input name='dbhost' placeholder='数据库地址' value='localhost'>
    <input name='dbport' placeholder='数据库端口' value='3306'>
    <input name='dbuser' placeholder='数据库用户' value='root'>
    <input name='dbpwrd' placeholder='数据库密码'>
    <input name='dbname' placeholder='数据库名称'>
    <input name='dbpfix' placeholder='数据库表前缀' value='es_'>
    <button type="button" class="next">下一步</button>
  </form>
</div>

<div id="step2" class="step d_h">
  <form class="iform">
    <p class="t_center">②设定管理员</p>
    <input name='usrname' placeholder='账号 [\w@#$_-]{6,16}'>
    <input name='usrpwrd' placeholder='密码 \w{6}' type="password">
    <button type="button" class="prev">上一步</button>
    <button type="button" class="next">下一步</button>
  </form>
</div>

<div id="step3" class="step d_h">
  <form class="iform">
    <p class="t_center">③设置网站基本信息</p>
    <input name='webtit' placeholder='网站标题'>
    <input name='webkey' placeholder='关键词'>
    <input name='webdes' placeholder='描述'>
    <button type="button" class="next">完成</button>
  </form>
</div>

<div id="step4" class="step d_h">
  <p class="t_center">④配置完成!</p>
  <p class="t_center"><small><?php echo ES_VERSION?></small></p>
  <p class="t_center"><small><?php echo ES_POWER?></small></p>
</div>

<div id="mask" class="d_h">
  <div id="dialog"><p>KO,错误给了程序致命一击。</p><small>关闭</small></div>
</div>

<?php echo \es\helpers\generate_html5_foot('//cdn.fcmayi.com/jquery/jquery-2.x-latest.min,install-install')?>