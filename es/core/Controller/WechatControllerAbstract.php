<?php
namespace es\core\Controller;
/**
 * 微信端的页面使用ionic,所以无需再继承HtmlController
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 * 2016年10月23日上午11:11:35
 */
use es\libraries\Wechat\MpWechat;
use es\core\Log\LogTrait;
abstract class WechatControllerAbstract extends DataController
{
    use LogTrait;
    protected $appid;
    protected $appSecret;
    protected $encodingAesKey;
    protected $Wechat;
    
    public function __construct(){
        parent::__construct();
        // 将常量作为默认值放入参数
        foreach(['appid'=>'APP_ID','appSecret'=>'APP_SECRET','encodingAesKey'=>'ENCODING_AES_KEY']
                as $k=>$v){
            empty($this->$k) && $this->$k = constant( $v );
        }
        $this->Wechat = new MpWechat($this->appid,$this->appSecret,$this->encodingAesKey);
    }
    
    /**
     * 【被动方法】接受微信服务器交互的入口
     * 只需要在微信公众号的开发者中心的服务器配置配置
     * WechatControllerAbstract的实现子类，如其实现子类为 Mywechat extends WechatControllerAbstract
     * 则服务器配置URL填入：http://Yourhost/controller_dir/mywechat
     * 提醒服务器配置的Token(令牌)需要填写：enozoomstudio
     */
    final public function index(){
        $this->Wechat->_is_wechat() && $this->Wechat->callback();
    }
    
    /**
     * 【被动方法】WechatAbstract子类的callback()会自动调用
     * 根据微信请求来的事件进行响应
     * @param object $req 被解析为对象的微信事件请求
     * @return []
     * [
     *     'Reply_msg::method_name',//ReplyStatic的某一方法名
     *     [$argname => $val,..] //ReplyStatic::method需要的出$from,$to,之外的参数
     * ];
     * 如：
     * ['text'=>['content','来自公众号的自动回复']]
     * 可以不用写键名，但是一定要安照方法参数顺写，以免参数位置不对而造成调用错误
     */
    public abstract function _events($req);
    
    /**
     * 【被动方法】WechatAbstract子类的callback()会自动调用
     * 根据微信请求来的文本文字进行关键词匹配
     * @param string $key
     * @return []
     *  [
     *      'Reply_msg::method_name',//ReplyStatic的某一方法名
     *      [ $argname => $val] //ReplyStatic::method需要的出$from,$to,之外的参数
     *  ];
     * 如：
     * ['text',['content'=>'来自公众号的自动回复']]
     * 可以不用写键名，但是一定要安照方法参数顺写，以免参数位置不对而造成调用错误
     */
    public abstract function _keywords($key);
    
    /**
     * 【手动方法】生成微信号的底部菜单菜单
     * 具体实现可参考:
     * public function menus()
     * {
     *   $menus = 
     *     [
     *       ['name'=>'事件', 'type'=>'click', 'key'=>'CLICKEVENET001'],
     *       ['name'=>'网页', 'type'=>'view', 'url'=>'http://www.enozoom.com'],
     *       ['name'=>'一级', 'sub_button'=>
     *           [
     *             ['name'=>'二级', 'type'=>'view', 'url'=>'http://enozoom.com']
     *           ] 
     *       ],
     *     ];
     *   echo $this->wechat->generate_menu($menus)?'success':'fail';
     * }
     */
    public abstract function menus();
    
}