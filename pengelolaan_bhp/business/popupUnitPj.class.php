<?php

class popupUnitPj extends Database {

	protected $mSqlFile= 'module/pengelolaan_bhp/business/popupUnitPj.sql.php';
	
	function __construct($connectionNumber=0) {
		parent::__construct($connectionNumber);
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
		$result = $this->Open($this->mSqlQueries['get_data_unitkerja'], array('%'.$kode.'%', '%'.$kode.'%', '%'.$unitkerja.'%', '%'.$unitkerja.'%', $kodeSistem,$kodeSistem.'.%', $offset, $limit));
		
		return $result;
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
		return $result;
	}

	function GetDataUnitkerjaByArrayId($arrUnitkerjaId) {
		$unitkerjaId = implode("', '", $arrUnitkerjaId);
		$result = $this->Open($this->mSqlQueries['get_data_unitkerja_by_array_id'], array($unitkerjaId));
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
}
?>
