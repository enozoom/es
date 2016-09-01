<?php
namespace es\libraries\Excel;
use es\core\Toolkit\File;
require_once 'PHPExcel-1.8.1/Classes/PHPExcel.php';
class Excel{
    use File;
    public $sheet = ['sheet1','sheet2']; // 多个sheet
    private $dir = './uploads/excel';    // excel保存路径
    
/**
* 生成一个excel
* @param array $ths    [ ['excel第一张表格中的标题1','标题2],['第二张表格标题',..],.. ]
* @param array $tds    [ [['excel第一张表格第一行数据'],['第二行数据']], [['excel第二张表格第一行数据']],.. ]
* @param string $file  excel文件名
* 
* @return string 保存文件的路径
*/
    public function init($ths = [],$tds = [],$file = 'excel',$download=FALSE){
        if( empty($tds) || !is_array($tds) ){
            die("data must be a array");
        }
        if( empty($file) || empty($this->sheet) ){
            die('excel|sheet name not define');
        }
        $file_path = $file.'.xlsx';
        $objPHPExcel = $this->readExcel($file_path);
        if(empty($objPHPExcel)){
            $objPHPExcel = new \PHPExcel();
            $this->properties($objPHPExcel);
        }
        $this->set_data($ths,$tds,$objPHPExcel);
        $this->save($objPHPExcel,$this->dir().$file_path,$download);
        return str_replace('./','/',$this->dir().$file_path);
    }
 
/**
* 设置部分属性
* @param  $objPHPExcel
* 
* @return
*/
    private function  properties(\PHPExcel &$objPHPExcel,$title=''){
        $objPHPExcel->getProperties()->setCreator("Joe Enozoomstudio");
//        $objPHPExcel->getProperties()->setLastModifiedBy("Maarten Balliauw");
        $objPHPExcel->getProperties()->setTitle($title);
//        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
//        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");
//        $objPHPExcel->getProperties()->setKeywords("office 2007 openxml php");
//        $objPHPExcel->getProperties()->setCategory("Test result file");
    }

/**
* 生成文件路径
* 
* @return string
*/
     private function dir(){
        return $this->mkdir($this->dir,1);
    }    
/**
* 
* @param string $filePath
* @return
*/    
    private function readExcel($filePath){
        
        $filePath = $this->dir().$filePath;
        if(!is_file($filePath)){
            return FALSE;
        }
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        if($PHPReader->canRead($filePath)){
            return $PHPReader->load($filePath);
        }
        return FALSE;
    }
    
/**
* 为sheet载入数据
* @param array $title       [['BID','BNAME'],..]
* @param array $data        [[1,'JOE'],...]
* 
* @return
*/
    private function set_data($title,$data,&$objPHPExcel){
        foreach($this->sheet as $sheet_index=>$sheet_name){
            if($objPHPExcel->getSheetCount()-1<$sheet_index){// 工作表少于索引表量
                $if = $sheet_index+1-$objPHPExcel->getSheetCount();
                for($i=0;$i<$if;$i++){
                    $objPHPExcel->createSheet();
                }
            }
            
            $objPHPExcel->setActiveSheetIndex($sheet_index);
            $objPHPExcel->getActiveSheet()->setTitle($sheet_name);
            $k1 = $k2 = ord("A");
            
            if(isset( $title[$sheet_index] )){
                foreach($title[$sheet_index] as $tit){
                    $objPHPExcel->getActiveSheet()->setCellValue(chr($k1++).'1',$tit);
                }                
            }
            
            if(isset( $data[$sheet_index] )){
                foreach($data[$sheet_index] as $row=>$_data){
                    foreach( array_values($_data) as $col=>$__dat ){
                        $objPHPExcel->getActiveSheet()->setCellValue( chr($k2+$col).''.($row+2),$__dat);
                    }
                }
            }
            
        }

    }
    
/**
* 读取excel2007+
* @param string $filePath xls文件路径，不含xls
* @param int    $sheet    xls文件中的第几个sheet，从0索引
* @return
*/      
    public function get_data($sheet=0,$filename='excel'){
        $file_path = $filename.date('.Y.m.d').'.xlsx';
        $PHPExcel = $this->readExcel($file_path);
        $currentSheet = $PHPExcel->getSheet($sheet);
        $allColumn = $currentSheet->getHighestColumn();
        $allRow = $currentSheet->getHighestRow();

        $_data = array();

        for($currentRow = 1;$currentRow <= $allRow;$currentRow++){
          $data = array();
          for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
            $val = $currentSheet
                   ->getCellByColumnAndRow(ord($currentColumn)-65,$currentRow)
                   ->getValue();
            $data[] = $val;
          }
          $_data[] = $data;
        }
        return $_data;
    }
/**
 * 保存或在浏览器下载
 * @param \PHPExcel $objPHPExcel
 * @param string $path
 * @param unknown $download
 */
    protected function save(\PHPExcel $objPHPExcel,$path='',$download=FALSE){
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        if($download){
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
            header("Content-Type:application/force-download");
            header("Content-Type:application/vnd.ms-execl");
            header("Content-Type:application/octet-stream");
            header("Content-Type:application/download");;
            header('Content-Disposition:attachment;filename="bill.xlsx"');
            header("Content-Transfer-Encoding:binary");
            $objWriter->save('php://output');
        }else{
            $objWriter->save($path);
        }
    }
}
?>