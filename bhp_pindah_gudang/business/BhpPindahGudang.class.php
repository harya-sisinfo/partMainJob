<?php
/*
	@ClassName : BhpPindahGudang
	@Copyright : PT Gamatechno Indonesia
	@Analyzed By : Dyan Galih
	@Author By : Didi Zuliansyah
	@Version : 01
	@StartDate : 2013-01-01
	@LastUpdate : 2013-01-01
	@Description : Class BhpPindahGudang
*/

class BhpPindahGudang extends Database
{
	protected $mSqlFile;
	var $msg;

	function __construct ($connectionNumber=0)
	{
		$this->mSqlFile = 'module/'.Dispatcher::Instance()->mModule.'/business/bhp_pindah_gudang.sql.php';
		parent::__construct($connectionNumber);
		//$this->setDebugOn();
	}
	
	# =========== GET FUNCTION ============== #
	
	function checkDate($date){
		$result = $this->Open($this->mSqlQueries['check_date'],array($date));
		return $result[0];
	}

	function GetKodeSistem(){
		$userId = Security::Instance()->mAuthentication->GetCurrentUser()->GetUserId();
		$result = $this->Open($this->mSqlQueries['get_kode_sistem'], array($userId));
		return $result[0]['unitkerjaKodeSistem'];
	}
	
	function GetDataBHPPindahGudang($noTransaksi,$unitAsal,$unitTujuan,$namaBarang,$startRec,$itemViewed){
		$kodeSistem = $this->GetKodeSistem();
		$result =  $this->Open($this->mSqlQueries['get_data_bhp_pindah_ruang'],array(
			$kodeSistem, '%'.$noTransaksi.'%','%'.$namaBarang.'%','%'.$unitAsal.'%','%'.$unitAsal.'%',
			'%'.$unitTujuan.'%', '%'.$unitTujuan.'%', $kodeSistem, $kodeSistem.'.%', $kodeSistem, 
			$startRec,$itemViewed
		));
		return $result;
	}

	function GetDetilById($id){
		$result = $this->Open($this->mSqlQueries['get_detil_by_id'],array($id));
		return $result;
	}
	
	# multiunit
	function GetDataUsulanBhp($startRec, $itemViewed, $unitKerja, $noUsulanBhp, $namaBarang){
		$kodeSistem = $this->GetKodeSistem();
		$sql = $this->mSqlQueries['get_data_usulan_bhp'];
		$sql = ($unitKerja <> '' && $unitKerja <> 'all') ? str_replace('[SEARCH_UNIT]', ' AND usulanBrgUnitId = '.$unitKerja, $sql) : str_replace('[SEARCH_UNIT]', '', $sql);
		$result = $this->Open($sql,array('%'.$noUsulanBhp.'%','%'.$namaBarang.'%',$kodeSistem,$kodeSistem.'.%',$startRec, $itemViewed));
		return $result;
	}
	
	function GetDataUsulanBhpById($usulanId){
		$result = $this->Open($this->mSqlQueries['get_data_usulan_bhp_by_id'],array($usulanId));
		return $result[0];
	}
	
	# multiunit
	function GetDataUnitkerja ($offset, $limit, $unitkerja='', $kode='',$group=1) {
		# show unit group default
		$kodeSistem = ($group == 1) ? $this->GetKodeSistem() : '';
		$sql = $this->mSqlQueries['get_data_unitkerja'];
		$sql = ($kodeSistem <> '') ? str_replace('[KODE_SISTEM]', "AND (a.unitkerjaKodeSistem = '".$kodeSistem."' OR a.unitkerjaKodeSistem LIKE '".$kodeSistem.".%%')", $sql) : str_replace('[KODE_SISTEM]', "", $sql);
		$result = $this->Open($sql, array('%'.$kode.'%', '%'.$kode.'%', '%'.$unitkerja.'%', '%'.$unitkerja.'%', $offset, $limit));
		return $result;
	}
	
	# multiunit
	function GetComboUnitKerja(){
		$kodeSistem = $this->GetKodeSistem();
		$result = $this->Open($this->mSqlQueries['get_combo_unit_kerja'],array($kodeSistem,$kodeSistem.'.%'));
		return $result;
	}
	
	function GetComboGudang($idUnit,$exclude=''){
		$sql = str_replace(
			'[EXCLUDE]',
			($exclude)?sprintf("AND ruangId != '%s'",$exclude):'',
			$this->mSqlQueries['get_combo_gudang']
		);
		$result = $this->Open($sql,array($idUnit));
		return $result;
	}
	
	function GetCount(){
		$result = $this->Open($this->mSqlQueries['get_count'],array());
		return $result[0]['total'];
	}
	
	# FOR POPUP KODE BARANG
	function GetDataJenis($data,  $unitId, $gudangId, $tglMts, $usulId, $start = 0, $limit = 1000)
	{
		extract ($data);
		if (is_numeric($CARI_GOLONGAN))
			$newQuery = str_replace('%golongan%', 'AND', $this->mSqlQueries['get_data_jenis']);
		else $newQuery = str_replace('%golongan%', 'OR', $this->mSqlQueries['get_data_jenis']);
	
		if (is_numeric($CARI_BIDANG_ID))
			$newQuery = str_replace('%bidang%', 'AND', $newQuery);
		else $newQuery = str_replace('%bidang%', 'OR', $newQuery);
	
		if (is_numeric($CARI_KELOMPOK_ID))
			$newQuery = str_replace('%kelompok%', 'AND', $newQuery);
		else $newQuery = str_replace('%kelompok%', 'OR', $newQuery);
	
		if (is_numeric($CARI_SUB_KELOMPOK_ID))
			$newQuery = str_replace('%subKelompok%', 'AND', $newQuery);
		else $newQuery = str_replace('%subKelompok%', 'OR', $newQuery);
	
		if (is_numeric($CARI_JENIS_BARANG))
			$newQuery = str_replace('%jenisBarang%', 'AND', $newQuery);
		else $newQuery = str_replace('%jenisBarang%', 'OR', $newQuery);
		
		if ($usulId <> '' && $usulId <> NULL){
			$arrUsul = $this->GetDataUsulanBhpById($usulId);
			$newQuery = str_replace('[VAR_JML_USULAN]', ',"'.$arrUsul['stok_pindah'].'" AS stokPindah', $newQuery);
			$newQuery = str_replace('[SEARCH_BRG_USULAN]', ' AND invAtkJenisRefId = '.$arrUsul['barangId'], $newQuery);
		}else{
			$newQuery = str_replace('[VAR_JML_USULAN]', '', $newQuery);
			$newQuery = str_replace('[SEARCH_BRG_USULAN]', '', $newQuery);
		}
	
		$arg = array
		(
				$unitId,
				$gudangId,
				$tglMts,
				$CARI_GOLONGAN,
				$CARI_BIDANG_ID,
				$CARI_KELOMPOK_ID,
				$CARI_SUB_KELOMPOK_ID,
				"%$CARI_NAMA_BARANG%",
				"%$CARI_NAMA_BARANG%",
				$CARI_JENIS_BARANG,
				$start,
				$limit
		);
		$result = $this->Open($newQuery, $arg);
		if (count($result) == 0) return array();
		foreach ($result as $value)
			$return[$value['idKelompok']][$value['idSubKelompok']][$value['idBarang']][$value['id']] = $value;
		return $return;
	}
	
	function GetDataBarang($data, $start = 0, $limit = 1000)
	{
		extract ($data);
		if (is_numeric($CARI_GOLONGAN))
			$newQuery = str_replace('%golongan%', 'AND', $this->mSqlQueries['get_data_barang']);
		else $newQuery = str_replace('%golongan%', 'OR', $this->mSqlQueries['get_data_barang']);
	
		if (is_numeric($CARI_BIDANG_ID))
			$newQuery = str_replace('%bidang%', 'AND', $newQuery);
		else $newQuery = str_replace('%bidang%', 'OR', $newQuery);
	
		if (is_numeric($CARI_KELOMPOK_ID))
			$newQuery = str_replace('%kelompok%', 'AND', $newQuery);
		else $newQuery = str_replace('%kelompok%', 'OR', $newQuery);
	
		if (is_numeric($CARI_SUB_KELOMPOK_ID))
			$newQuery = str_replace('%subKelompok%', 'AND', $newQuery);
		else $newQuery = str_replace('%subKelompok%', 'OR', $newQuery);
	
		if (is_numeric($CARI_JENIS_BARANG))
			$newQuery = str_replace('%jenisBarang%', 'AND', $newQuery);
		else $newQuery = str_replace('%jenisBarang%', 'OR', $newQuery);
	
		$arg = array
		(
				$CARI_GOLONGAN,
				$CARI_BIDANG_ID,
				$CARI_KELOMPOK_ID,
				$CARI_SUB_KELOMPOK_ID,
				"%$CARI_NAMA_BARANG%",
				"%$CARI_NAMA_BARANG%",
				$CARI_JENIS_BARANG,
				$start,
				$limit
		);
		$result = $this->Open($newQuery, $arg);
		if (count($result) == 0) return array();
		foreach ($result as $value)
			$return[$value['idKelompok']][$value['idSubKelompok']][$value['id']] = $value;
		return $return;
	}
	
	function GetKelompokList()
	{
		$result = $this->Open($this->mSqlQueries['get_kelompok_list'], array());
		if (count($result) == 0) return array();
		foreach ($result as $value)
			$return[$value['id']] = $value;
		return $return;
	}
	
	function GetSubKelompokList()
	{
		$result = $this->Open($this->mSqlQueries['get_sub_kelompok_list'], array());
		foreach ($result as $value)
			$return[$value['idKelompok']][$value['id']] = $value;
		return $return;
	}
	
	function GetLastInsertId(){
		$result = $this->Open($this->mSqlQueries['get_last_insert_id'], array());
		return $result[0]['id'];
	}
	
	# ====== UPDATE STOK GUDANG ======= #
	
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
	
	function AddGudangTujuan($gudangId,$atkBrgId,$jumlah){
		$result = $this->Execute($this->mSqlQueries['add_gudang_tujuan'], array($gudangId,$atkBrgId,$jumlah));
		return $this->AffectedRows();
	}
	
	function AddInvAtkBrg($atkJenisRefId,$jumlah){
		$result = $this->Execute($this->mSqlQueries['add_inv_atk_brg'], array($atkJenisRefId,$jumlah));
		return $this->AffectedRows();
	}

	function GetLastStock($mstId,$tglRth,$gudangId){
		$result = $this->Open($this->mSqlQueries['get_last_stock'], array($mstId,$tglRth,$gudangId));
		return (float)$result[0]['stok'];
	}

	function GetGudangReady($mstId,$tglRth,$gudangId){
		$result = $this->Open($this->mSqlQueries['get_gudang_ready'], array($mstId,$tglRth,$gudangId));
		return $result[0];
	}

	function GetAtkDetId($data){
      $result = $this->Open($this->mSqlQueries['get_atk_det_id'], $data);
      return $result['0']['invAtkDetId'];
   }

   function UpdateGudangMax($data) {
		$result = $this->Execute($this->mSqlQueries['update_gudang_max'], $data);
		return $this->AffectedRows();
	}

   function AddAtkBrg($data){
      $result = $this->Execute($this->mSqlQueries['penambahan_atk_brg'], $data);
      return $this->AffectedRows();
   }

   function AddAtkGudang($data){
      $result = $this->Execute($this->mSqlQueries['penambahan_atk_gudang'], $data);
      return $this->AffectedRows();
   }

   function AddLogUpdate($data){
		$result = $this->Execute($this->mSqlQueries['add_log_update'], $data);
		return $this->AffectedRows();
	}

	function UpdateStokGedung($data,$rthNumber,$noTrans,$tglRth,$gudangAsal,$gudangTujuan)
	{		
		$userId = Security::Instance()->mAuthentication->GetCurrentUser()->GetUserId();
		foreach($data as $val){
			$kodeBrg = $val['barangKode'].' ('.addslashes($val['barangNama']).')';
			$lastStock = $this->GetLastStock($val['mstId'],$tglRth,$gudangAsal);
			if((float)$val['jumlah'] <= (float)$lastStock){			
				$sisaRth = (float)$val['jumlah'];
				while($sisaRth > 0 && $sisaRth <= $lastStock){
					$gdgReady = $this->GetGudangReady($val['mstId'],$tglRth,$gudangAsal);
					if(!empty($gdgReady)){
						// add "RTH Like" for sender
						$jmlRth = (float)(($gdgReady['jmlBrg'] >= $sisaRth)?$sisaRth:$gdgReady['jmlBrg']);
						$sisaStokGdg = (float)$gdgReady['jmlBrg'] - $jmlRth;
						$sisaRth -= $jmlRth;
						$update = $this->UpdateGudangMax(array(
							$sisaStokGdg, $gdgReady['ruangId'], $gdgReady['invBrgAtkId'], $jmlRth
						));
						$logRth = $this->AddLogUpdate(array(
							$gdgReady['ruangId'], $gdgReady['invBrgAtkId'],	(-1*$jmlRth), 'Mutasi Ke Gudang Lain',
							$tglRth, $gdgReady['hrgSatuan'],	$rthNumber,	$noTrans, $val['barangDiskripsi'], 
							(float)$gdgReady['jmlBrg'], $gdgReady['ruangId'], $gudangTujuan, $userId
						));
						$result = $update && $logRth;

						if(!$result) break;
						else{ // add "Penambahan Stock" for receiver
			            $dataDetAtk = array('','',$val['mstId']);
			            $detId = $this->GetAtkDetId($dataDetAtk);
			            $dataDet = array($detId,$tglRth,$gdgReady['hrgSatuan']*$jmlRth,$jmlRth,'');
			            $addAtkBrg = $this->AddAtkBrg($dataDet);
			            $atkBrgId = $this->LastInsertId();
			            $dataDet = array($gudangTujuan,$atkBrgId,$jmlRth,$gdgReady['hrgSatuan']);
			            $addGudang = $this->AddAtkGudang($dataDet);
			            $dataDet = array(
			            	$gudangTujuan, $atkBrgId, $jmlRth, 'Penambahan Stock Dengan Mutasi',
								$tglRth, $gdgReady['hrgSatuan'],	$rthNumber,	$noTrans, $val['barangDiskripsi'], 
								NULL, NULL, NULL, $userId
			            );
			            $addLog = $this->AddLogUpdate($dataDet);
			            $result = $result && $addAtkBrg && $addGudang && $addLog;

			            if(!$result) break;
			            else $lastStock = $this->GetLastStock($val['mstId'],$tglRth,$gudangAsal);
						}
					}else break;
				}
			}
		}
		
		$this->msg = (!$result)?'Stok Sediaan Berikut Tidak Mencukupi :\n- '.$kodeBrg.
			'\n\nMohon Hapus Dan Entry Ulang Sediaan Tersebut Untuk Melihat Kondisi Stok Terupdate'.$gudangAsal:'';
		return $result;
	}

	// ======== INSERT BHP PINDAH GUDANG =========
	
	function AddBhpMutasiMst($arrData){
		return $this->Execute($this->mSqlQueries['add_bhp_mutasi_mst'],$arrData);
	}
	
	function AddBhpMutasiDet($arrData){
		return $this->Execute($this->mSqlQueries['add_bhp_mutasi_det'],$arrData);
	}
	
	function InsertBhpPindahGudang($data){
		$this->StartTrans();
		
		// add bhp_mutasi_mst
		$userId = Security::Instance()->mAuthentication->GetCurrentUser()->GetUserId();
		$data['usulanbhpid'] = ($data['usulanbhpid'] == '') ? NULL : $data['usulanbhpid'];
		$uniqId = 'MTS.'.uniqid();
		$arrDataMst = array(
			$data['unitasalid'],
			$data['gudangasal'],
			$data['unittujuanid'],
			$data['gudangtujuan'],
			$data['usulanbhpid'],
			$data['tglpindah'],
			$data['pic'],
			$data['notrans'], // no BA
			$uniqId, // no trans
			$data['keterangan'],
			$userId
		);
		$addMst = $this->AddBhpMutasiMst($arrDataMst);
		$mstId = $this->LastInsertId();
		
		// add bhp_mutasi_det
		$detil = $data['barangList'];
		foreach($detil as $value){
			$arrDataDet = array($mstId, $value['mstId'],	$value['jumlah']);
			$addDet = $this->AddBhpMutasiDet($arrDataDet);
			if(!$addDet) break;
		}
		
		// do "RTH like" for gudang unit asal and "Penamabahan Stock" for gudang tujuan
		$updateStokGudang = $this->UpdateStokGedung(
			$detil, $data['notrans'], $uniqId,
			$data['tglpindah'], $data['gudangasal'], $data['gudangtujuan']
		);

		$add = $addMst && $addDet && $updateStokGudang;
		$this->EndTrans($add);
		return ($add)?true:false;		
	}
}
?>