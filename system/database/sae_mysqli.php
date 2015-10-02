<?php defined('SYSPATH') OR exit('POWERED BY Enozoomstudio');
/**
 * 为SAE优化
 * @author Joe e@enozoom.com
 * 2015年9月19日上午8:36:57
 *
 */
class Sae_Mysqli extends ES_Mysqli{
	public function __construct($configs){
		$configs->host = SAE_MYSQL_HOST_M;
		$configs->user = SAE_MYSQL_USER;
		$configs->password = SAE_MYSQL_PASS;
		$configs->dbname = SAE_MYSQL_DB;
		$configs->port = SAE_MYSQL_PORT;
		parent::__construct($configs);
	}
	/**
	 * 获取一个单例
	 *
	 * @return ES_Mysqli
	 */
	public static function get_instance($configs){
		self::$instance instanceof self || self::$instance = new self($configs);
		return self::$instance;
	}	
}