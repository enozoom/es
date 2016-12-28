<?php

namespace app\controllers\esadmin;

use app\controllers\esadmin\AbstractEsadmin;

final class Home extends AbstractEsadmin
{
    public function index(){
        //$this->view();
        $this->title = '欢迎使用';
        $this->welcome();
    }
    public function welcome(){
        $this->view();
    }
    
    public function setting($id=1){
        $this->load->model('Setting','S')->library('Html\\Form','F');
        $m = $s = $this->S->get();
        if( $this->isPost() ){
            $flag = FALSE;
            if(empty($m)){
                $flag = $this->S->insert($_POST);
            }else{
                $flag = $this->S->update($_POST);
            }
            die( json_encode(['err'=>!$flag]) );
        }
        
        $F = $this->F->init($this->S,$m);
        foreach($this->S->_attributes() as $k=>$v){
            !empty($m) && empty($m->$k) && $m->$k = '';
            $F->input($k);
        }
        $F->display();
    }
}

?>