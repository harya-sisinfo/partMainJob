<?php

class popupUnitPj extends Database {

	protected $mSqlFile;
	
	function __construct($connectionNumber=0) {
		$this->mSqlFile = 'module/'.Dispatcher::Instance()->mModule.'/business/popupUnitPj.sql.php';
		parent::__construct($connectionNumber);
		#$this->setDebugOn();
	}
	
	function GetDataUserByUsername ($name)
   {
      if (isset($this->mUserByUserName[$name])) return $this->mUserByUserName[$name];
      $result = $this->Open($this->mSqlQueries['get_data_user_by_user_name'], array($name));  
      $this->mUserByUserName[$name] = $result[0];
      
		return $result[0];
   }
	
    # multiunit
	function GetDataUnitkerja ($offset, $limit, $unitkerja='', $kode='') {
		$User = $this->GetDataUserByUsername($_SESSION['username']);
		$kodeSistem = $User['unitkerjaKodeSistem']; 
		return $this->Open($this->mSqlQueries['get_data_unitkerja'], array('%'.$kode.'%', '%'.$kode.'%', '%'.$unitkerja.'%', '%'.$unitkerja.'%', $kodeSistem,$kodeSistem.'.%', $offset, $limit));
	}
	
	function GetCount(){
		$result = $this->Open($this->mSqlQueries['get_count'],array());
		return $result[0]['total'];
	}

	function GetCountDataUnitkerja ($unitkerja='', $kode='') {
		$User = $this->GetDataUserByUsername($_SESSION['username']);
		$user1 = $User['unitkerjaId']; 
		$user2 = $User['unitkerjaId']; 
		$user3 = $User['role_id'];
		$result = $this->Open($this->mSqlQueries['get_count_data_unitkerja'], array('%'.$kode.'%', '%'.$kode.'%', '%'.$unitkerja.'%', '%'.$unitkerja.'%', $user1, $user2, $user3));

		if (!$result) {
			return 0;
		} else {
			return $result[0]['total'];
		}
	}
	 
	function GetDataUnitkerjaById($unitkerjaId) {
		$result = $this->Open($this->mSqlQueries['get_data_unitkerja_by_id'], array($unitkerjaId));
	  //$debug = sprintf($this->mSqlQueries['get_data_unitkerja_by_id'], $unitkerjaId);
	  //echo $debug;
		return $result;
	}

	function GetDataUnitkerjaByArrayId($arrUnitkerjaId) {
		$unitkerjaId = implode("', '", $arrUnitkerjaId);
		$result = $this->Open($this->mSqlQueries['get_data_unitkerja_by_array_id'], array($unitkerjaId));
	  //$debug = sprintf($this->mSqlQueries['get_data_unitkerja_by_id'], $unitkerjaId);
	  //echo $debug;
		return $result;
	}

	//untuk combo box
	function GetDataSatker($unitkerjaId = NULL) {
		$result = $this->Open($this->mSqlQueries['get_data_satker'], array());
		return $result;
	}

	function GetStatusUnitKerja(){
		return $this->Open($this->mSqlQueries['get_status_unit_kerja'],array());
	}

//===DO==
	
	function DoAddUnitkerja($pimpinan, $unitkerjaKode, $unitkerjaNama, $satker, $statusunit) {
      if($satker == "-")
      {
         $satker = "0";
         $unitkerjaKode = floor($unitkerjaKode / 100);
      }
      else $unitkerjaKode = $unitkerjaKode % 100;
      
		$result = $this->Execute($this->mSqlQueries['do_add_unitkerja'], array($unitkerjaKode, $unitkerjaNama, $satker, $statusunit, $pimpinan));
		return $result;
	}
	
	function DoUpdateUnitkerja($pimpinan, $unitkerjaKode, $unitkerjaNama, $satker, $statusunit, $unitkerjaId) {
      if($satker == "-")
      {
         $satker = "0";
         $unitkerjaKode = floor($unitkerjaKode / 100);
      }
      else $unitkerjaKode = $unitkerjaKode % 100;
      
		$result = $this->Execute($this->mSqlQueries['do_update_unitkerja'], array($unitkerjaKode, $unitkerjaNama, $satker, $statusunit, $pimpinan, $unitkerjaId));
		return $result;
	}
	
	function DoDeleteUnitkerjaById($unitkerjaId) {
		$result=$this->Execute($this->mSqlQueries['do_delete_unitkerja_by_id'], array($unitkerjaId,$unitkerjaId));
		return $result;
	}
   
	function DoDeleteUnitkerjaByArrayId($arrUnitkerjaId) {
		$unitkerjaId = implode("', '", $arrUnitkerjaId);
		$result=$this->Execute($this->mSqlQueries['do_delete_unitkerja_by_array_id'], array($unitkerjaId,$unitkerjaId));
		return $result;
	}

   function GetComboUnitKerja(){
      return $this->Open($this->mSqlQueries['get_combo_unit_kerja'],array());
   }
}
?>
