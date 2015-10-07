# ES框架 
@author Joe

## 前提

 1. ≽ PHP5.5 如果不会用到事务，则PHP5以上的环境即可运行。
 2. 服务器必须开启rewrite支持。
 3. 保证`./uploads`、`./logs`、`./application/cache`、`./application/data`具有读写权限。

## 示例快速上手
### 第一步、示例数据库创建及配置
1. 创建一个名为"e_demo"数据库，导入`./application/data/db.sql`。
2. 在`./configs/database.eno`配置数据库。


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
>build *开发时使用的图片*
>>2015 *按年份规整文件夹*
>>>10 *按月份规整*
>>>>whoami.png

## 关于路由配置  
  路由通过./configs/routes.eno,以正则表达式的方式进行设置，需要注意"\"需要转义，如匹配数字"\d"，需要写成"\\d"。

  路由使用的前提是服务器支持rewrite功能，.htaccess对应Apache，web.config对应IIS，config.yaml对应SAE。

## 关于一个页面执行流程  
    ./index.php 
    → ./system/core/enozoomstudio.php 
    → ./system/core/route.php 
    → ./system/core/controller.php 
    → ./application/controllers/your_controller_class.php 
    → ./application/views/your_view_file.php
## 关于css,js的命名及引用
  css,js的默认存放文件夹为`./configs/config.eno`中规定的`theme_path`  
  css,js的命名规则为控制器名.控制器方法名.css|js,如，当前控制器名为home和当前控制器方法名为index,则css|js的命名为home.index.css|js    

  css,js的引用有两种方式，均在控制器内设置  
  1. 控制器类属性`public $css = 'base,dom';`或者`public $js = 'base.jquery.min';`
  2. 控制器在调用`protected function view($data=array(),...){}`中传入的`$data`中设置`$data['css']`
  css,js默认通过控制器`min`(`./application/controllers/common/min.php`)调用。  
  3. 设置时，只需要填入css|js的在默认存放文件夹中的名字，如`./theme/default_theme_path/home.index.css`,只需要填入`public $css = 'home.index';`或者`$data['css'] = 'home.index'`,多个引用，用半角逗号隔开，如`public $css = 'base,home.index'`，当然默认控制器的`view()`方法能够自动引用与控制器名和控制器方法名相同的css|js  

## 关于图片的引用
  使用相对路径进行引用，如`./uploads/build/whoami.png`,如果引用则`<img src="/uploads/build/whoami.png" alt="" />`

## 自动生成ES_model
  系统会自动生成基本的ES_model子类，操作也非常简单  
  1. 访问`http://Yourhost/common/model_install`，生成变开始了
  2. 生成结束后，请删除`./application/controllers/common/model_install.php`文件。
