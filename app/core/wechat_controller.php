<?php namespace app\core;
/**
* 微信公众号开发
* @author Joe e@enozoom.com
* 2015年10月8日 下午1:42:54
*/
abstract class Wechat_controller extends \es\core\Controller{
  protected $appid;
  protected $appSecret;
  protected $encodingAesKey;
  
  public function __construct(){
    parent::__construct();
    $this->load->library('api_wechat\\mp_wechat','wechat');
    $this->wechat->_init($this->appid,$this->appSecret,$this->encodingAesKey);
  }
  
/**
 * 【被动方法】接受微信服务器交互的入口
 * 只需要在微信公众号的开发者中心的服务器配置配置
 * Wechat_controller的实现子类，如其实现子类为 Mywechat extends Wechat_controller
 * 则服务器配置URL填入：http://Yourhost/controller_dir/mywechat
 * 提醒服务器配置的Token(令牌)需要填写：enozoomstudio
 */
  public function index(){
    $this->wechat->_is_wechat() && $this->wechat->callback();
  }
  
/**
 * 【被动方法】api_wechat/mp_wechat 的callback()会自动调用
 * 根据微信请求来的事件进行响应
 * @param object $req 被解析为对象的微信事件请求
 * @return array
 * array(
 *       'Reply_msg::method_name',//Reply_msg的某一方法名
 *       array( $argname => $val //Reply_msg::method需要的出$from,$to,之外的参数)
 *       );
 * 如：
 * array('text'=>array('content','来自公众号的自动回复'))
 * 可以不用写键名，但是一定要安排参数顺写，以免参数位置不对而造成调用错误
 */
  public abstract function _events($req);
  
/**
 * 【被动方法】api_wechat/mp_wechat 的callback()会自动调用
 * 根据微信请求来的文本文字进行关键词匹配
 * @param string $key
 * @return array
 * array(
 *       'Reply_msg::method_name',//Reply_msg的某一方法名
 *       array( $argname => $val //Reply_msg::method需要的出$from,$to,之外的参数)
 *       );
 * 如：
 * array('text',array('content'=>'来自公众号的自动回复'))
 * 可以不用写键名，但是一定要安排参数顺写，以免参数位置不对而造成调用错误
 */
  public abstract function _keywords($key);
  
/**
 * 生成微信号的底部菜单菜单
 * 具体实现可参考:
 * public function menus(){
 *   $menus = array(
 *     array('name'=>'楼盘','type'=>'click','key'=>'ESTATE'),
 *     array('name'=>'客户','type'=>'click','key'=>'CUSTOMER'),
 *     array('name'=>'我','type'=>'click','key'=>'MY'),
 *   );
 *   echo $this->wechat->generate_menu($menus)?'success':'fail';
 * }
 */  
  public abstract function menus();
  
/**
 * 根据微信跳转而来的带有code参数的链接
 * 获取当前微信用户对应当前公众号的唯一openid
 * @param string $code
 * 直接输出openid
 */
  public function openid($code){
    die( $this->wechat->user_openid($code) );
  }
  
/**
 * 生成一个微信跳转的网址
 * 需要post接收一个url参数
 */
  public function wechat_link(){
   $url = empty($_POST['url']) ? '' : $_POST['url'];
   die( empty($url)? '': $this->wechat->wechat_openid_link($url));
  }
}