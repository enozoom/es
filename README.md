# ES3.0框架 PHP微框架
@author Joe

## 前提

 1. 版本适用于PHP5.5.x-PHP5.6.x。
 2. 服务器必须开启rewrite支持。
 3. 保证`./uploads`、`./logs`、`./app/cache`、`./app/data`具有读写权限。

## 示例快速上手
### 第一步、示例数据库创建及配置
1. 创建一个名为"e_demo"数据库，导入`./app/data/db.sql`。
2. 在`./configs/database.eno`配置数据库。


### 第二步、访问demo示例
  默认首页示例 `http://Yourhost`  
  默认model操作示例 `http://Yourhost/home/demo/`

### 第三步、查看控制器和视图
  示例控制器的位置：`./app/controllers/esweb/home.php`  
  示例视图的位置：`./app/views/esweb/home/index.php`

## 文件结构
###app *开发文件*

>cache *缓存文件,需要具有读写的权限*  
  controllers *控制器*  

>>esweb *默认前端控制器*  
>>>home.php *默认前端控制*  
>>common *公共控制器(必须存在)*  
>>>install.php *框架初始化*  
  min.php *合并css,js*  
	
>core *核心基类*  
  errors *异常页面*  
>>404.php  
  500.php  

>helpers *辅助函数库*  
  libraries *类库*  
  models *数据模型*  
  views *视图文件*  
>>html
>>>esweb *默认前端视图文件夹*
>>>>layout *公共头尾文件夹*
>>>>>header.php *公共头部*  
  footer.php *公共尾部*

>>>>home *与控制器名一致的视图文件夹* 
>>>>>index.php *与控制器方法名一致的文件*

>>haml *moreaboutviewruby*

### configs *配置文件*
>config.eno *基本设置*  
  constants.eno *全局常量*  
  database.eno *数据库设置*  
  routes.eno *路由设置*  

### es *框架文件*
>core *核心文件*  
  libraries *核心类库*  
  helpers *核心辅助函数*  
  database *数据库*  

###logs *日志 需要具有读写权限*
>def *默认日志文件夹*
>>2016  
>>>02
>>>>2016-02-19.log *所有调用`\es\core\log_msg()`均打印到相应日期日志文件下*

### theme *css,javascript文件*
>esweb *前端*
>>2015 *受`./configs/config.eno`设置的`theme_path`影响*
>>>css 
>>>>home.index.css *控制器名.控制器方法名.css*

>>>js
>>>>home.index.js *控制器名.控制器方法名.js*

### uploads *上传的文件|图片*

## 关于路由配置  
  路由通过`./configs/routes.eno`,以正则表达式的方式进行设置，需要注意`\`需要转义，如匹配数字`\d`，需要写成`\\d`(两条反斜杠)。

  路由使用的前提是服务器支持rewrite功能，.htaccess对应Apache，web.config对应IIS，config.yaml对应SAE。

## 关于一个页面执行流程  
    如:http://Yourhost/home/demo/
    → ./es/core/enozoomstudio.php 
    → ./es/core/route.php 
    → ./es/core/controller.php 
    → ./app/controllers/esweb/home.php 
    → ./app/views/html/esweb/home/demo.php  

## 关于css,js的命名及引用
  css,js的默认存放文件夹为`./configs/config.eno`中规定的`theme_path`  
  css,js的命名规则为控制器名.控制器方法名.css|js,如，当前控制器名为home和当前控制器方法名为index,则css|js的命名为home.index.css|js    

  css,js的引用有两种方式，均在控制器内设置  
  1. 控制器类属性`public $css = 'base,dom';`或者`public $js = 'base,jquery.min';`
  2. 控制器在调用`protected function view($data=array(),...){}`中传入的`$data`中设置`$data['css']`
  css,js默认通过控制器`min`(`./app/controllers/common/min.php`)调用。  
  3. 设置时，只需要填入css|js的在默认存放文件夹中的名字，如`./theme/default_theme_path/home.index.css`,只需要填入`public $css = 'home.index';`或者`$data['css'] = 'home.index'`,多个引用，用半角逗号隔开，如`public $css = 'base,home.index'`，当然默认控制器的`view()`方法能够自动引用与控制器名和控制器方法名相同的css|js    

  如果引用的css,js是一个外部链接，如`http://cdn.com/style.css`,则应该这样填写,`public $css = '//cdn.com/style,base,home.index'`,系统会自动获取，但不会产生文件的合并，即最终将生成以下HTML：  

    `<link rel="stylesheet" href="//cdn.com/style.css">`  
    `<link rel="stylesheet" href="base,home.index.css">`  

## 关于图片的引用
  使用相对路径进行引用，如`./uploads/build/whoami.png`,如果引用则`<img src="/uploads/build/whoami.png" alt="" />`

## 自动生成`\es\core\Model`子类
    系统会自动生成基本的`\es\core\Model`子类，操作也非常简单，  
    虽然能够生成基本的`\es\core\Model`，但更多的数据库交互，仍建议手动进行完善，  
    以避免完善后的model不被覆盖（如果model已经存在则不会再次生成），请一定进行第2条操作。  
  1. 访问`http://Yourhost/install/model/`，系统会自动在`./app/models/`下生成`\es\core\Model`的子类。
  2. 生成结束后，建议删除`./app/controllers/common/install.php`文件。


## 关于缓存的使用
  ES使用了文件缓存，将页面完全静态化后存放在`./app/cache/html`文件夹下，文件缓存包括了三部分，"数据库查询结果缓存","css|js合并压缩文件","完全静态化文件"。
  建议IIS服务器开启wincache，文件缓存对服务器io有依赖。