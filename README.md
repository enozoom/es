# ES框架 
@author Joe

## 前提
1.≽ PHP5.5 如果不会用到事务，则PHP5以上的环境即可运行。
2.服务器必须开启rewrite支持。</li>
3.保证`./uploads`、`./logs`、`./application/cache`、`./application/data`具有读写权限。

## 示例快速上手
### 第一步、示例数据库创建及配置
1.创建一个名为"e_demo"数据库，导入`./application/data/db.sql`。
2.在`./configs/database.eno`配置数据库。
</ol>  

### 第二步、访问demo示例
默认首页示例 http://Yourhost
默认model操作示例 http://Yourhost/public/home/demo

### 第三步、查看控制器和视图
示例控制器的位置：`./application/controllers/public/home.php`
示例视图的位置：`./application/views/public/home/index.php`

## 文件结构
### system *框架文件*
>core *核心文件* 
libraries *核心类库*
helpers *核心辅助函数*
database *数据库*


###application *开发文件*

>cache *缓存文件,需要具有读写的权限*
>controllers *控制器*
	>>public *前端控制器*
	>>>home *默认前端控制*
	
>core *核心基类*
>errors *异常页面*  
    >>404.php  
    500.php

>helpers *辅助函数库*
libraries *类库*
models *数据模型*
views *视图文件*
>>html   
>>>layout *公共头尾文件夹*  
>>>>header.php *公共头部*  
        footer.php *公共尾部*

>>>home *与控制器名一致的视图文件夹* 
>>>> index.php *与控制器方法名一致的文件*

>>haml

###logs *日志 需要具有读写权限*
>def *默认日志文件夹*
>>2015  
>>>10
>>>>2015-10-02.log

### theme *css,javascript文件*
>awesome
>public *前端*
>>css 
>>>home.index.css *控制器名.控制器方法名.css*

>>js
>>>home.index.js *控制器名.控制器方法名.js*

>admin *后台*

### uploads *上传的文件|图片*
>build *开发时使用的图片 *
>>2015 *按年份规整文件夹*
>>>10 *按月份规整*
>>>>whoami.png

##关于路由配置
路由通过./configs/routes.eno,以正则表达式的方式进行设置，需要注意"\"需要转义，如匹配数字"\d"，需要写成"\\d"。

路由使用的前提是服务器支持rewrite功能，.htaccess对应Apache，web.config对应IIS，config.yaml对应SAE。

##关于一个页面执行流程
    ./index.php 
    → ./system/core/enozoomstudio.php 
    → ./system/core/route.php 
    → ./system/core/controller.php 
    → ./application/controllers/your_controller_class.php 
    → ./application/views/your_view_file.php