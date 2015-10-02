ES框架  
@author Joe  
>= PHP5.5

-system 框架文件  
--core 核心文件  
--libraries 核心类库  
--helpers 核心辅助函数  
--database 数据库  

-application 开发文件  
--cache 缓存文件  
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

-logs 日志  
---def  
-----2015  
-------10  
---------2015-10-02.log  

-theme css,javascript文件  
--awesome  
--public 前端  
----css 
------home.index.css 控制器名.控制器方法名.css  
----js  
------home.index.js 控制器名.控制器方法名.js
--admin 后台  

-uploads 上传的文件|图片  
--build 开发时使用的图片  
--2015 按年份规整文件夹  
----10 按月份规整  
------5ef748a5195b12bf711d1ffded46e2e3.jpg

