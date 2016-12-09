<?php
namespace es\core\Controller;
use es\libraries\Wechat\QyWechat;
/**
 * 企业微信
 * @author Joe
 * 2016年12月8日13:56:50
 */
abstract class QyWechatControllerAbstract extends DataController
{
    protected $EncodingAESKey;
    protected $CorpID;
    protected $Token;
    protected $Secret;
    
    protected $Wechat;
    public function __construct(){
        parent::__construct();
        $this->Wechat = new QyWechat($this->EncodingAESKey,$this->CorpID,$this->Secret,$this->Token);
        $this->Wechat->isWechat();
    }
}