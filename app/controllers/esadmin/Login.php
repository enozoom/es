<?php

namespace app\controllers\esadmin;

use es\core\Toolkit\AuthTrait;
use es\core\Toolkit\AryTrait;
final class Login extends AbstractEsadmin
{
    use AuthTrait,AryTrait;
    public $css = 'public.base,esadmin.form,esadmin.login.index.min';
    public $js = '';
    public function index(){
        if( $this->isPost() && $this->isRequired(['user_name','user_password','sign']) ){
            extract($_POST);
            if( $this->validateFormSign($sign) && 
                // 如果没有管理员则第一次登录就默认注册为管理员
                ( !empty($U=$this->U->init($user_name,$user_password)) ||
                  !empty($U=$this->U->login($user_name,$user_password)) ) 
              ){
                  $_SESSION[$this->SESSIONUSER] = $U;
                  $this->redirect($this->home);
              }
        }
        $this->load->library('Html\\Form','F');
        
        $this->title = '登录';
        $data = ['F'=>$this->F->init($this->U),
                 'sign'=>$this->formSign() ];
        
        $this->view($data,0);
    }

}