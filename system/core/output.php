<?php defined('SYSPATH') OR exit('POWERED BY Enozoomstudio');
class ES_output{
	protected $final_output;
/**
 * 追加字符串
 * @param string $output
 */	
	public function append_output($output){
		$this->final_output = empty($this->final_output)?$output:$this->final_output.$output;
	}
	/**
	 * 显示最终结果
	 */
	public function display(){
		echo $this->final_output;
	}
	
	public function length(){
		return strlen($this->final_output);
	}
}
?>