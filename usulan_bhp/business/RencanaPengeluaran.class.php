<?php
class RencanaPengeluaran extends Database 
{
   protected $mSqlFile;

   function __construct($connectionNumber=1) 
   {
   		$this->mSqlFile = 'module/'.Dispatcher::Instance()->mModule.'/business/rencana_pengeluaran.sql.php';
   		parent::__construct($connectionNumber);
   }
      
   function GetData($offset, $limit, $data) 
   {
		$result = $this->Open($this->mSqlQueries['get_data'],
			array(
				'%'.$data['kode'].'%',
				'%'.$data['nama'].'%',
				$offset,
				$limit)
			);
		return $result;
   }

	function GetCount() 
   {
		$result = $this->Open($this->mSqlQueries['get_count_row'], array());
		
		if (!$result)
			return 0;
		else
			return $result[0]['total'];
   }
   
   function GetDataDetil($kegiatan_detil_id) {
		$result = $this->Open($this->mSqlQueries['get_data_detil'], array($kegiatan_detil_id));
		return $result;
	}
   
}
