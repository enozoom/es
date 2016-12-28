<?php

namespace app\controllers\esadmin;

use es\core\Toolkit\StrStatic;
use es\core\Toolkit\AuthTrait;
final class User extends AbstractEsadmin
{
    use TraitId, TraitList, AuthTrait;
    
    public function lists($per=10,$offset=0)
    {
        $rows = $this->_lists($this->U,['where'=>'','select'=>'user_id,user_name'],
                        'esadmin/user/lists',$per,$offset);
        echo $this->Datagrid->init($rows,$this->U)->display().
        '<p class="load-script" data-src="/min/esadmin.user.js"></p>';;
    }
    public function id($id=0){
        $m = $this->_id($id,$this->U);
        $pw = StrStatic::randomString();
        $this->Form->init($this->U,$m)
        ->input('user_name')
        ->input('user_password',['type'=>empty($m)?'text':'password',
                'help'=>empty($m)?'默认使用随机密码：'.$pw:'如果修改密码，可直接填入新密码，则不需要修改密码框内容。',
                'value'=>empty($m)?$pw:''])
        ->display();
    }
    
    public function del($id=0){
        $r = ['err'=>1,'msg'=>'禁止删除'];
        $id>1 && $this->U->_delete("user_id = {$id}") && $r = ['err'=>0];
        die( json_encode($r) );
    }
    
    protected function dataValidate(Array $data){
        if( empty($data['user_password']) || strlen($data['user_password'])>=60 ){// 密码未被修改
            unset($data['user_password']);
        }else{
            $data['user_password'] = $this->generateSign($data['user_password']);
        }
        return $data;
    }

}
