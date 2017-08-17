<?php

namespace es\core\Cache;
/*
用于存储查询缓存，如:
<?php
namespace app\libraries\api;
class Cache{
  private $QCache;
  
  public function __construct($cmdq){
      $this->QCache = new \es\core\Cache\Querycache((object)$cmdq);
  }
  
  public function save($data){
    $this->QCache->save($data);
  }
  
  public function read(){
    return $this->QCache->read();
  }
}
 */

class Querycache extends Cache
{
    protected $cache_path = 'query';
    protected $cache_file_suffix = '.json';
    public $expires = 604800;
    
    public function __construct($cmdq){
        parent::__construct();
        $this->cmdq = $cmdq;
    }
    
    public function read($print=FALSE){
        $json = parent::read($print);
        return json_decode( preg_replace('/<[^>]+>/', '', $json) );
    }
    public function save($mix,$len=0){
        parent::save( json_encode($mix,JSON_UNESCAPED_UNICODE) ,$len);
    }
    
    protected function is_allow_cache(){
        return $this->expires>0;
    }
}

?>