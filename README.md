<h1>ES<sup><small>EnozoomStudio</small></sup>框架</h1> 
<p>@author Joe</p>
<h2>前提</h2>
<ol>
<li>≽ PHP5.5 如果不会用到事务，则PHP5以上的环境即可运行。  </li>
<li>服务器必须开启rewrite支持。</li>
<li>保证./uploads、./logs、./application/cache、./application/data具有读写权限。</li>
</ol>

<h2>示例快速上手</h2>
<h3>第一步、示例数据库创建及配置</h3>
<ol>
<li>创建一个名为"e_demo"数据库，导入./application/data/db.sql</li>
<li>在./configs/database.eno配置数据库</li>
</ol>  

<h3>第二步、访问demo示例</h3>
<p>默认首页示例 http://Yourhost</p>
<p>默认model操作示例 http://Yourhost/public/home/demo</p>

<h3>第三步、查看控制器和视图</h3>
<p>示例控制器的位置：./application/controllers/public/home.php</p>
<p>示例视图的位置：./application/views/public/home/index.php</p>

<h2>文件结构</h2>  
<h3>system 框架文件</h3>  
<blockquote>
<p>core 核心文件</p>  
<p>libraries 核心类库</p>  
<p>helpers 核心辅助函数</p>
<p>database 数据库
</blockquote>

<h3>application 开发文件</h3>  
<blockquote>
  <p>cache 缓存文件,需要具有读写的权限</p>
  <p>controllers 控制器
  <blockquote>
    <p>public 前端控制器
    <blockquote>home 默认前端控制</blockquote>
    </p>
  </blockquote></p>
<p>core 核心基类</p>
<p>errors 异常页面  
  <blockquote>
    <p>404.php</p>  
    <p>500.php</p>
  </blockquote> 
</p>
<p>helpers 辅助函数库</p>
<p>libraries 类库</p>
<p>models 数据模型</p>
<p>views 视图文件
  <blockquote>
    <p>html   
      <blockquote> 
      <p>layout 公共头尾文件夹  
        <blockquote>
        <p>header.php 公共头部</p>  
        <p>footer.php 公共尾部</p>
        </blockquote>
      </p>
      <p>home 与控制器名一致的视图文件夹  
        <blockquote>index.php 与控制器方法名一致的文件</blockquote>
      </p>
      </blockquote>
    </p>
    <p>haml</p>
  </blockquote>
</p>
</blockquote>

<h3>logs 日志 需要具有读写权限</h3>
<blockquote>
<p>def
  <blockquote>
  <p>2015  
    <blockquote>
      <p>10
        <blockquote>2015-10-02.log</blockquote>
      </p>
    </blockquote>
  </p>
  </blockquote>
</p>
</blockquote>

<h3>theme css,javascript文件</h3>
<blockquote>
  <p>awesome</p>

  <p>public 前端
  <blockquote>
    <p>css 
      <blockquote>home.index.css 控制器名.控制器方法名.css</blockquote>
    </p>
    <p>js
      <blockquote>home.index.js 控制器名.控制器方法名.js</blockquote>
    </p>
  </blockquote>
</p>
  <p>admin 后台</p> 

</blockquote>

<h3>uploads 上传的文件|图片</h3>
<blockquote>
<p>build 开发时使用的图片  
  <blockquote>
    <p>2015 按年份规整文件夹
      <blockquote>
        <p>10 按月份规整
          <blockquote>whoami.png</blockquote>
        </p>
      </blockquote>
    </p>
  </blockquote>
</p>
</blockquote>

<h2>关于路由配置</h2>
<p>路由通过./configs/routes.eno,以正则表达式的方式进行设置，需要注意"\"需要转义，如匹配数字"\d"，需要写成"\\d"</p>
<p>路由使用的前提是服务器支持rewrite功能，.htaccess对应Apache，web.config对应IIS，config.yaml对应SAE。</p>
<p>默认访问的控制器及方法：./application/controllers/public/home.php中的index()方法。当然默认访问可以通过routes.eno进行设置。</p>

<h2>关于一个页面执行流程</h2>
<p>./index.php <br>→ ./system/core/enozoomstudio.php <br>→ ./system/core/route.php <br>→ ./system/core/controller.php 
  <br>→ ./application/controllers/your_controller_class.php <br>→ ./application/views/your_view_file.php</p>