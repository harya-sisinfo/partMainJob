<?php

class Pengelolaan extends Database {

	protected $mSqlFile= 'module/pengelolaan_bhp/business/pengelolaan.sql.php';
	var $userId;
	
	function __construct($connectionNumber=0) {
		parent::__construct($connectionNumber);
		$this->userId = Security::Instance()->mAuthentication->GetCurrentUser()->GetUserId();
		//$this->setDebugOn();
	}
	
	function GetDataUserByUsername ($name)
	{
		if (isset($this->mUserByUserName[$name])) return $this->mUserByUserName[$name];
		$result = $this->Open($this->mSqlQueries['get_data_user_by_user_name'], array($name));  
		$this->mUserByUserName[$name] = $result[0];
		
		return $result[0];
	}

	function checkDate($date){
		$result = $this->Open($this->mSqlQueries['check_date'],array($date));
		return $result[0];
	}
	
	# multiunit
	function GetDataPengelolaan ($offset, $limit, $notrans, $nama, $tglAwal, $tglAkhir, $setting='') {
		$User = $this->GetDataUserByUsername($_SESSION['username']);
		$kodeSistem = $User['unitkerjaKodeSistem']; 
		if(empty($setting) || $setting == '') $setting = '';
		$result = $this->Open($this->mSqlQueries['get_data_lelang_barang'], array(
			'%'.$notrans.'%', '%'.$nama.'%', $setting, 
			$kodeSistem, $kodeSistem.'.%', $tglAwal, $tglAkhir,
			$offset, $limit
		));		
		return $result;
	}
	
	function GetCount(){
		$result = $this->Open($this->mSqlQueries['get_count'],array());
		return $result[0]['total'];
	}
	
	function GetCountDataPengelolaan($notrans, $nama, $setting='') {
		$User = $this->GetDataUserByUsername($_SESSION['username']);
		$user1 = $User['unitkerjaId']; 
		$user2 = $User['unitkerjaId']; 
		$user3 = $User['role_id'];
		$result = $this->Open($this->mSqlQueries['get_count_data_lelang_barang'], array('%'.$notrans.'%', '%'.$nama.'%', $setting, $setting, $user1, $user2, $user3));
		
		if (!$result) 
		{
			return 0;
		} 
			else 
		{
			return $result[0]['total'];
		}
	}
	
	function GetDataPengelolaanById ($id) {
		$result = $this->Open($this->mSqlQueries['get_data_pengelolaan_by_id'], array($id));
		return $result;
	}
	
	function GetAtkMstNomor() {
		$result = $this->Open($this->mSqlQueries['get_atk_mst_nomor'], array());
		return $result;
	}
	
	//---DO--
	function DoAddPengelolaan($tanggal, $gnumber, $deskripsi, $nama, $unitId, $userId)
	{
		$result = $this->Execute($this->mSqlQueries['do_add_lelang_barang'], array($tanggal, $gnumber, $deskripsi, $nama, $userId, $unitId));
		return $result;
	}
	
	function DoAddPengelolaanDet($mstId,$barangId, $diskripsi, $jumlah, $amount) {
		$result = $this->Execute($this->mSqlQueries['do_add_pengelolaan_det'], array($mstId,$barangId, $diskripsi, $jumlah, $amount));
		return $result;
	}
	
	function DoUpdatePengelolaan($tanggal, $catatan, $penerima, $unitId, $iduser, $idtrans)
	{
		$result = $this->Execute($this->mSqlQueries['do_update_lelang_barang'], array($tanggal, $catatan, $penerima, $unitId, $iduser, $idtrans));
		return $result;
	}
	
	function DoUpdatePengelolaanDet($idtrans, $barangId, $diskripsi, $jumlah, $amount) {
		$result = $this->Execute($this->mSqlQueries['do_update_pengelolaan_det'], array($idtrans, $barangId, $diskripsi, $jumlah, $amount));
		return $result;
	}
	
	function DoDeletePengelolaanDetil($idTrans){
		$result = $this->Execute($this->mSqlQueries['do_delete_pengelolaan_det'], array($idTrans));
	}
	
	function DoDeletePengelolaanByArrayId($arrTransid) {
		$transId = implode("', '", $arrTransid);
		$result=$this->Execute($this->mSqlQueries['do_delete_pengelolaan_by_array_id'], array($transId));
		return $result;
	}
	
	//-- update stok di gedung
	function ab($nilai,$data,$i,$akhir,$habis){
		if ($i < $akhir){
			if ($habis==1){
				$sisa = $data[$i];
			}	else	{
				$sisa = $nilai-$data[$i];	
				$sisa2 = $sisa;
				if($sisa<0){
					$sisa = $sisa*(-1);
					$habis = 1;
				} elseif($sisa){
					$sisa=0;
				}
			}
			
			$x = $i+1;
			
			$this->ab($sisa2,$data,$x,$akhir,$habis);
			$_SESSION['hasil'][] = $sisa;
		}
	}
	
	function GetMstByBrgId($barangId)
	{
		$result = $this->Open($this->mSqlQueries['get_mst_id'], array($barangId));
		return $result['0']['invAtkMstId'];
	}
	
	function GetBrgByMstId($id)
	{
		$result = $this->Open($this->mSqlQueries['get_brg_by_mst_id'], array($id));
		return $result;
	}
	
	function GetMaxGdgByDetId($detId, $ruangId)
	{
		$result = $this->Open($this->mSqlQueries['get_max_gdg_by_det_id'], array($detId, $ruangId));
		return $result[0]['jumlah'];
	}
	
	function UpdateGudangMax($data)
	{
		$result = $this->Execute($this->mSqlQueries['update_gudang_max'], $data);
		return $this->AffectedRows();
	}
	
	function AddLogUpdate($data){
		$result = $this->Execute($this->mSqlQueries['add_log_update'], $data);
		return $this->AffectedRows();
	}
	
	function GetGdgByMstId($mstId,$tglRth,$unitId){
		$User = $this->GetDataUserByUsername($_SESSION['username']);
		$kdSistem = $User['unitkerjaKodeSistem'];
		$result = $this->Open($this->mSqlQueries['get_gdg_by_mst_id'], array($mstId,$tglRth,$unitId,$kdSistem,$kdSistem.'.%'));
		return $result;
	}

	function GetGudangReady($mstId,$tglRth,$unitId){
		$result = $this->Open($this->mSqlQueries['get_gudang_ready'], array($mstId,$tglRth,$unitId));
		return $result[0];
	}

	function GetLastStock($mstId,$tglRth,$unitId){
		$result = $this->Open($this->mSqlQueries['get_last_stock'], array($mstId,$tglRth,$unitId));
		return (float)$result[0]['stok'];
	}
	
	function UpdateStokGedung($data,$rthNumber,$tglRth,$unitId)
	{		
		$userId = Security::Instance()->mAuthentication->GetCurrentUser()->GetUserId();
		foreach($data as $val){
			$kodeBrg = $val['barangKode'].' ('.addslashes($val['barangNama']).')';
			$lastStock = $this->GetLastStock($val['mstId'],$tglRth,$unitId);
			if((float)$val['jumlah'] <= (float)$lastStock){
				
				// New Process : Always Check Last Stock Each Time Stock Updated

				$sisaRth = (float)$val['jumlah'];
				while($sisaRth > 0 && $sisaRth <= $lastStock){
					$gdgReady = $this->GetGudangReady($val['mstId'],$tglRth,$unitId);
					if(!empty($gdgReady)){
						$jmlRth = (float)(($gdgReady['jmlBrg'] >= $sisaRth)?$sisaRth:$gdgReady['jmlBrg']);
						$sisaStokGdg = (float)$gdgReady['jmlBrg'] - $jmlRth;
						$sisaRth -= $jmlRth;
						$update = $this->UpdateGudangMax(array(
							$sisaStokGdg, $gdgReady['ruangId'], $gdgReady['invBrgAtkId'], $jmlRth
						));
						$log = $this->AddLogUpdate(array(
							$gdgReady['ruangId'], $gdgReady['invBrgAtkId'],	$jmlRth,	$rthNumber,
							$gdgReady['hrgSatuan'],	$rthNumber,	$val['barangDiskripsi'], $userId
						));
						$result = $update && $log;
						if(!$result) break;
						else $lastStock = $this->GetLastStock($val['mstId'],$tglRth,$unitId);
					}else break;
				}

				// ======= OLD PROCESS =========

				/*
				$arrGudang = $this->GetGdgByMstId($val['mstId'],$tglRth,$unitId);
				$sisaRth = (float)$val['jumlah'];

				foreach($arrGudang as $value){
					if($sisaRth == 0) break;
					$jmlRth = (float)(($value['jmlBrg'] >= $sisaRth)?$sisaRth:$value['jmlBrg']);
					$sisaStokGdg = (float)$value['jmlBrg'] - $jmlRth;
					$sisaRth -= $jmlRth;
					$update = $this->UpdateGudangMax(array(
						$sisaStokGdg, 
						$value['ruangId'], 
						$value['invBrgAtkId'], 
						$jmlRth
					));
					$log = $this->AddLogUpdate(array(
						$value['ruangId'],
						$value['invBrgAtkId'],
						$jmlRth,
						$rthNumber,
						$value['hrgSatuan'],
						$rthNumber,
						$val['barangDiskripsi'],
						$userId
					));
			 		$result = $update && $log;
			 		if(!$result) break;
				}
				*/

				// =======
			}
		}
		
		$return['stat'] = $result;
		$return['msg'] = (!$result)?'Stok Sediaan Berikut Tidak Mencukupi :\n- '.$kodeBrg.
			'\n\nMohon Hapus Dan Entry Ulang Sediaan Tersebut Untuk Melihat Kondisi Stok Terupdate':'';
		return $return;
	}
	
}
?>
