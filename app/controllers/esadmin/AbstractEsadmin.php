<?php

namespace app\controllers\esadmin;

use es\core\Controller\HtmlController;
use es\core\Http\HeaderTrait;
/**
 *
 * @author Joe
 *        
 */
abstract class AbstractEsadmin extends HtmlController
{
    use HeaderTrait,TraitMenu{
        TraitMenu::menus as _menus;
    }
    public $title = '网站管理后台 ';
    public $css = '//cdn.bootcss.com/ionicons/2.0.1/css/ionicons.min,public.base,esadmin.dom,esadmin.datagrid,esadmin.form';
    public $js = '//cdn.bootcss.com/jquery/3.1.0/jquery.min,/theme/ueditor1_4_3_2/ueditor.config,/theme/ueditor1_4_3_2/ueditor.all.min,esadmin.dom,esadmin.confirmdialog';
    
    protected $home = '/esadmin/home';
    protected $login = '/esadmin/login';
    protected $User;
    protected $SESSIONUSER = 'sessionuser';
    
    public function __construct()
    {
        parent::__construct();
        $this->title .= ES_POWER;
        $this->load->model('User','U');
        $this->load->model('Category','C');
        $this->isLogin();
    }
    
    protected function isPost()
    {
        return $this->reqestMethod('post');
    }
    
    /**
     * 判断是否登录
     */
    private function isLogin()
    {
        $cmdq = $this->getConfigs('cmdq');
        empty($_SESSION[$this->SESSIONUSER]) || $this->User = &$_SESSION[$this->SESSIONUSER];
        if( $cmdq->c != 'login' && empty($this->User))
        {
            $this->redirect($this->login);
        }
    }
    
    /**
     * 这里需要分权
     * 
     */
    private function menus(){
        $menus = $this->_menus();
        
        return $this->generateMenus($menus);
    }
    
    protected function __data__(&$data){
        parent::__data__($data);
        $data['menus'] = $this->menus();
    }
    
    /**
     * 搜索提交而来
     * 如果带有查询条件则按照查询条件查询
     * @param string $where
     * @param string $sql
     */
    protected function _search_where($where,$sql){
        isset($_GET['search-key']) && $where = str_replace('%s', urldecode($_GET['search-key']), $sql);
        return $where;
    }
    
    protected function _search_pagination($url){
        return $url.( isset($_GET['search-key'])?('?search-key='.$_GET['search-key']):'' );
    }
}

?>