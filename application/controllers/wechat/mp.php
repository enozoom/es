<?php defined('APPPATH') OR exit('POWERED BY Enozoomstudio');
/**
* 微信开发
* @author Joe e@enozoom.com
* 2015年10月8日 下午1:40:03
* 常量：
* APP_ID
* APP_SECRET
* ENCODING_AES_KEY
* 均定义在configs/constants.eno
* 在设置公众号响应地址时（URL(服务器地址)）：http://xxx.com/wechat/mp即可
* 默认Token(令牌)：enozoomstudio
*/
class Mp extends Wechat_controller{
  protected $appid = APP_ID;
  protected $appSecret = APP_SECRET;
  protected $encodingAesKey = ENCODING_AES_KEY;
  
  public function _events($req){
    $data = array();
    switch ( strtolower($req->Event) ){
      case 'click':// 底部菜单点击事件
        $article = '';
        switch ( strtolower($req->EventKey) ){
          case 'estate':
            $article =
            array(
              'Title'=>'标题，分享给朋友时显示',
              'Description'=>'描述，分享到朋友圈显示',
              'PicUrl'=>'200X200px的分享链接中的小图片,必须是外部链接http://',
              'Url'=>'点击链接跳想的地址，必须是外部链接http://'
              );
          break;
        }
        empty($article) || $data = array('article',array('articles'=>$article));
      break;
    }
    return $data;
  }
  
  public function _keywords($key){
    return array('text',array('content'=>"所有的输入，公众号均响应本内容，可使用switch"));
  }
  
/**
 * 生成微信底部菜单
 */  
  public function menus(){
    exit();
    $menus = array(
      array('name'=>'菜单1','type'=>'click','key'=>'Menu1'),
      array('name'=>'菜单2','type'=>'click','key'=>'Menu2'),
      array('name'=>'菜单3','type'=>'click','key'=>'Menu3'),
    );
    var_dump($this->wechat->generate_menu($menus));
  }
}