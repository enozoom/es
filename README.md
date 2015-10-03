<h1>ES框架 </h1> 
<p>@author Joe</p>  
<h2>前提</h2>
<p>≽ PHP5.5  
如果不会用到事务，则PHP5以上的环境即可运行。  </p>
<p>服务器必须开启rewrite支持</p>
<p>保证./uploads、./logs、./application/cache、./application/data具有读写权限。</p>


<h2>快速上手</h2>
<h3>示例数据库创建及配置</h3>
<p>创建一个名为"e_demo"数据库，导入./application/data/db.sql</p>
<p>在./configs/database.eno配置数据库</p>  

<h3>路由配置</h3>
<p>路由通过./configs/routes.eno,以正则表达式的方式进行设置，需要注意"\"需要转义，如匹配数字"\d"，需要写成"\\d"</p>
<p>路由使用的前提是服务器支持rewrite功能，.htaccess对应Apache，web.config对应IIS，config.yaml对应SAE。</p>
<p>默认访问的控制器及方法：./application/controllers/public/home.php中的index()方法。当然默认访问可以通过routes.eno进行设置。</p>

<h3>执行demo示例</h3>
<p>默认首页 http://Yourhost</p>
<p>默认model操作 http://Yourhost/public/home/demo,当然这个URL也可以通过routes.eno进行简化设置。</p>

<h2>一个页面执行流程</h2>
<p>./index.php → ./system/core/enozoomstudio.php → ./system/core/route.php → ./system/core/controller.php → ./application/controllers/your_controller_class.php → 
./application/views/your_view_file.php</p>
<p>仅需要关注./application中你的控制器文件和视图文件。</p>


<h2>文件结构</h2>  
<h3>-system 框架文件</h3>  
<p>
--core 核心文件  
--libraries 核心类库  
--helpers 核心辅助函数  
--database 数据库  
</p>

<h3>-application 开发文件</h3>  
<p>
--cache 缓存文件,需要具有读写的权限    
--controllers 控制器  
----public 前端控制器  
------home 默认前端控制
--core 核心基类    
--errors 异常页面  
----404.php  
----500.php  
--helpers 辅助函数库  
--libraries 类库  
--models 数据模型  
--views 视图文件  
----html   
------public  
--------layout 公共头尾文件夹  
----------header.php 公共头部  
----------footer.php 公共尾部  
--------home 与控制器名一致的视图文件夹  
----------index.php 与控制器方法名一致的文件  
----haml  
</p>

<h3>-logs 日志 需要具有读写权限</h3>  
<p>
---def  
-----2015  
-------10  
---------2015-10-02.log  
</p>

<h3>-theme css,javascript文件</h3>  
<p>
--awesome  
--public 前端  
----css 
------home.index.css 控制器名.控制器方法名.css  
----js  
------home.index.js 控制器名.控制器方法名.js
--admin 后台  
</p>

<h3>-uploads 上传的文件|图片</h3>  
<p>
--build 开发时使用的图片  
--2015 按年份规整文件夹  
----10 按月份规整  
------5ef748a5195b12bf711d1ffded46e2e3.jpg
</p>
