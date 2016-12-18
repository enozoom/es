<?php
namespace app\models;

use es\core\Model\ModelAbstract;

class User extends ModelAbstract
{
    public function _attributes($attr = '')
    {
        $attrs = [
                    'user_id'=>'#',
                    'user_name'=>'用户名',
                    'user_password'=>'密码',
                 ];
        return isset($attrs[$attr])?$attrs[$attr]:$attrs;
    }
}
