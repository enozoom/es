# 公共辅助类控制器
**Min.php**  对css,js进行汇总输出，对css进行压缩。
### 控制器在引用css,js
> 设置`HtmlController`子类的public属性$css，或在其view()中传入`['css'=>$css]`，在view()中传参的css是对类属性$css的追加。  
如：`public $css = 'public.base.min'`;  
或：`$this->view(['css'=>'public.dom.min'])`;  
**js**同理。

* **前台** 使用css,js时，路径受`configs/config.eno`中的`theme_path`影响，如前台的某页面为`esweb/home/index`,则其对应的css为`theme/"theme_path"/css/esweb.home.index.css`
* **后台** 使用css,js时，需要加前缀_"esadmin."_,如后台的某页面为`esadmin/home/index`,则其对应的css为`app/data/esadmin/css/esadmin.home.index.css`。

新增：  

支持`public $css = '/min/2017/public.base.min';`，以`min/4位年份/*.(cs|j)s`时，会忽视`configs/config.eno`中的`theme_path`中的年份设置，其实际css路径为`theme/esweb/2017/css/public.base.min.css`  
