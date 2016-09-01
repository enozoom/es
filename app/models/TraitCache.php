<?php

namespace app\models;
use es\core\Cache\Querycache;
trait TraitCache {
    protected $Cache;
    protected $cacheExpires = 604800;// 缓存时长，更新此值可以强制更新
    protected function read(Array $cmdq=[]){
        $this->initCache($cmdq);
        $this->Cache->expires = $this->cacheExpires;
        return $this->Cache->read();
    }
    
    protected function save($obj){
        $this->Cache->save( $obj );
    }
    
    /**
     * 
     * @param array $cmdq
     */
    protected function cmdq(Array $cmdq){
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,4)[3] ;
        $cmdq = $cmdq+['c'=>str_replace(__NAMESPACE__.'\\', '', $trace['class']),'d'=>'model','m'=>$trace['function']];
        return $cmdq;
    }
    
    protected function clear(Array $cmdq= []){
        $this->initCache($cmdq);
        $this->Cache->clean();
    }
    
    protected function renameDir(Array $cmdq= []){
        $this->initCache($cmdq);
        $this->Cache->renameCacheDir();
    }
    
    protected function initCache(Array $cmdq= []){
        $this->Cache = new Querycache( (object)$this->cmdq($cmdq) );
    }
}