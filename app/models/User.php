<?php
namespace app\models;

use es\core\Model\ModelAbstract;
use es\core\Toolkit\AuthTrait;
class User extends ModelAbstract
{
    use AuthTrait;
    public function _attributes($attr = '')
    {
        $attrs = [
                    'user_id'=>'#',
                    'user_name'=>'用户',
                    'user_password'=>'密码',
                 ];
        return isset($attrs[$attr])?$attrs[$attr]:$attrs;
    }
    
    /**
     * 登录,成功返回User对象,否则返回FALSE
     * @param string $user_name
     * @param string $user_pword
     * @return boolean|\es\core\Model\[obj,..]/[[],..]
     */
    public function login($user_name,$user_pword)
    {
        // 为保证驱动一致
        $sql = sprintf( 'SELECT * FROM %s WHERE %s LIMIT 1',
                        $this->db->tablename($this->tableName),
                        sprintf("user_name = '%s'",addslashes($user_name )) );
        $u = $this->db->query($sql);
        return !empty($u) && $this->validateSign($user_pword, $u[0]->user_password)?$u[0]:FALSE;
    }
    
    /**
     * _insert的别名
     * @param string $user_name
     * @param string $user_pword
     * @param number $user_status
     * @param number $user_level
     * @return int
     */
    public function register($user_name,$user_pword)
    {
        return $this->_insert([
            'user_name'=>$user_name,
            'user_password'=>$this->generateSign($user_pword),
        ]);
    }
    
    public function init($user_name,$user_pword){
        if( !$this->_getTotalnum() ){
            if(!empty($uid = $this->register($user_name, $user_pword))){
                return $this->_getByPKID($uid);
            }
        }
        return FALSE;
    }
}
