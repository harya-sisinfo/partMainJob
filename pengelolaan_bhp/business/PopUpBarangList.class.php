<?php
class Barang extends Database
{
   protected $mSqlFile;
   
	function __construct($connectionNumber=0)
   {
      $this->mSqlFile  = 'module/pengelolaan_bhp/business/PopUpBarangList.sql.php';
		parent::__construct($connectionNumber);
		
	}
   
   function GetDataBarangCount($nama='', $barcode='', $setting='')
   {
      $result = $this->Open($this->mSqlQueries['get_data_barang_count'], array('%'.$nama.'%', '%'.$barcode.'%', $setting));
      if (is_array($result))
         return $result[0]['total'];
      else return 0;
   }
   
   function GetDataBarang($nama='', $barcode='', $setting='', $start, $limit)
   {
      $result = $this->Open($this->mSqlQueries['get_data_barang'], array('%'.$nama.'%', '%'.$barcode.'%', $setting, $start, $limit));
			
      return $result;
   }
}
?>
