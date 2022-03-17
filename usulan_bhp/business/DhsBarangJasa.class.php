<?php
class DhsBarangJasa extends Database
{
   protected $mSqlFile;
   
	function __construct($connectionNumber=0)
   {
      $this->mSqlFile  = 'module/'.Dispatcher::Instance()->mModule.'/business/dhs_barang_jasa.sql.php';
		parent::__construct($connectionNumber);
	}
	
	function GetDataDhs($data)
   {
   	$result = $this->Open($this->mSqlQueries['get_data_dhs_barang_jasa'], $data);
      return $result;
   }
   
	function GetDataDhsCount()
   {
   	$result = $this->Open($this->mSqlQueries['get_data_count_dhs_barang_jasa'], array());
   	if(empty($result)){
   		$result = 0;
   	} else {
   		$result = $result[0]['total'];
   	}
      return $result;
   }
}
?>
