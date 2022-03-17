<?php

class AppPengolahanAtk extends Database {

   protected $mSqlFile= 'module/pengolahan_atk/business/pengolahan_atk.sql.php';
   
   function __construct($connectionNumber=0) {
      parent::__construct($connectionNumber);
      //$this->setDebugOn();
   }
   
   function CheckUploadedFile($name)
   {
      $fileName = strrev(basename($_FILES[$name]['name']));
      $fileExtPos = strpos($fileName, '.');
      $fileExt = strtolower(strrev(substr($fileName, 0, $fileExtPos)));
      $_FILES[$name]['name']= strrev(substr($fileName, $fileExtPos + 1));
      $_FILES[$name]['ext']= $fileExt;
      if
      (
         $_FILES[$name]['error'] !== 0 ||
         in_array($fileExt, array('pl','py','php','asp','cgi')) AND
         $fileName !== ''
      )
         return false;
      else return true;
   }
   
	function GetSatuan() {
      $result = $this->Open($this->mSqlQueries['get_cbx_satuan'], array());
      return $result;
   }
	
   function GetStockBarang(){
   	$stock['0']['id'] = '1';
   	$stock['0']['name'] = 'ada';
   	$stock['1']['id'] = '2';
   	$stock['1']['name'] = 'tidak ada';
   	$stock['2']['id'] = '3';
   	$stock['2']['name'] = 'ambang batas';
   	
   	return $stock;
	}
   
   function GetAtkList($gudang,$namaBarang,$statusStock,$offset,$limit){
      $kdSistem = $this->GetKodeSistem();
   	switch ($statusStock){
   		case 'all':	//$a = 0; $b = 999999;
                     $a = 'jumlah_stok >= 0';
   						break;
   		case '1'	 : //$a = '(stok_minimal*1.1)'; $b = 999999;
                     $a = 'jumlah_stok > 0';
   						break;
   		case '2'	 : //$a = 0; $b = 0;
                     $a = 'jumlah_stok = 0';
   						break;
   		case '3'	 : //$a = 1; $b = 'stok_minimal+5';
                     $a = 'jumlah_stok < stokMin';
   						break;
		}
		$gudang = (!empty($gudang))?$gudang:'all';
		//$NewSql = sprintf($this->mSqlQueries['atk_list_old'], '%s', '%s', '%s', '%s', '%s', $a, $b, '%d', '%d');
      $NewSql = str_replace('[JMLSTOK]','AND '.$a,$this->mSqlQueries['atk_list']);
      return $this->Open($NewSql,array($gudang, $gudang, $kdSistem, $kdSistem.'.%', '%'.$namaBarang.'%', '%'.$namaBarang.'%', $offset, $limit));
	}

   function GetCount($gudang,$namaBarang,$statusStock){
      /*$userKdSistem = $this->GetKodeSistem();
      switch ($statusStock){
         case 'all': $a = 0; $b = 999999;
                     break;
         case '1'  : $a = '(stok_minimal*1.1)'; $b = 999999;
                     break;
         case '2'  : $a = 0; $b = 0;
                     break;
         case '3'  : $a = 1; $b = 'stok_minimal+5';
                     break;
      }
      $gudang = (!empty($gudang))?$gudang:'all';
      $NewSql = sprintf($this->mSqlQueries['atk_list_count'], '%s', '%s', '%s', '%s', '%s', $a, $b);
      $result = $this->Open($NewSql,array($gudang,$gudang,$userKdSistem,$userKdSistem.'.%','%'.$namaBarang.'%'));*/
      $result = $this->Open($this->mSqlQueries['atk_list_count'],array());
      return $result[0]['total'];
   }
   
	function GetKodeBarang($gudang, $nama) {
      $result = $this->Open($this->mSqlQueries['get_kode_barang'], array('%'.$nama.'%', $gudang, $gudang));
		
		return $result;
	}
	
	function GetKodeBarangCount($gudang, $nama) {
		$result = $this->Open($this->mSqlQueries['get_kode_barang_count'], array($gudang,'%'.$nama.'%'));
		if (!$result) {
			return 0;
		} else {
			return $result[0]['total'];
		}
	}
	
	function GetKodeBarangById($barang, $ruang) {
      $result = $this->Open($this->mSqlQueries['get_kode_barang_by_id'], array($barang, $ruang));
		return $result[0];
	}
	
   function GetKodeSistem(){
   	  $userId = Security::Instance()->mAuthentication->GetCurrentUser()->GetUserId();
   	  $result = $this->Open($this->mSqlQueries['get_kode_sistem'], array($userId));
   	  return $result[0]['unitkerjaKodeSistem'];
   }
	
   # multiunit
   function GetComboGudang($all=false) {
      $kodeSistem = $this->GetKodeSistem();
      $sql = str_replace('[ALL]',($all)?'LEFT':'',$this->mSqlQueries['get_combo_gudang']);
      return $this->Open($sql, array($kodeSistem,$kodeSistem.'.%'));
   }

   function GetGudangById($ruangId){
      $result = $this->Open($this->mSqlQueries['get_gudang_by_id'], array($ruangId));
      return $result[0]['ruangNama'];
   }
   
   function GetLokasi() {
      return $this->Open($this->mSqlQueries['get_combo_lokasi'], array());
   }
   
   function GetUnitKerja() {
      return $this->Open($this->mSqlQueries['get_combo_unit'], array());
      
   }
   
   function GetLokasiBarang($idUnit){
		return $this->Open($this->mSqlQueries['get_lokasi_barang'],array($idUnit));
	}
	
	function GetStockByGudang($ruangId){
		$arrResult = $this->Open($this->mSqlQueries['get_stock_by_gudang'], array($ruangId));
		return $arrResult['0'];
	}
	
	function GetAtkById($id){
		$arrResult = $this->Open($this->mSqlQueries['atk_by_id'],array($id));
		return $arrResult['0'];
	}
	
	function GetAtkByBarcode($barcode, $gudangId){
		$arrResult = $this->Open($this->mSqlQueries['get_barang_by_barcode'],array($barcode,$gudangId));
		return $arrResult['0'];
	}
	
   function GetSettingStokAtkByIdJenis($jenis_id,$ruangId){
		$arrResult = $this->Open($this->mSqlQueries['get_setting_stock_atk_by_id_jenis'],array($jenis_id,$ruangId));
		return $arrResult['0'];
	}
   
	function AddSettingStokAtk($barang_id,$ruangId,$stok_minimal, $keterangan){
      $data = $this->GetSettingStokAtkByIdJenis($barang_id,$ruangId);
      if(!isset($data['idJenis'])){
         return $this->Execute($this->mSqlQueries['set_add_setting_stock_atk'],array($barang_id,$ruangId,$stok_minimal, $keterangan));
      } else {
         return true;
      }
	}
	
	function UpdateSettingAtk($atk_id, $stok_minimal, $keterangan){
		return $this->Execute($this->mSqlQueries['update_atk'],array($stok_minimal, $keterangan,$atk_id));
	}
   
   function AddPemeliharaanAtk ($data)
   {
      if (!$this->CheckUploadedFile('logAtkFile')) return false;
      
      extract($data);
      $this->StartTrans();
      if ($_FILES['logAtkFile']['name'] !== '')
      {
         $tmp = dechex(mt_rand(65536, 1048576));
         $logAtkFile = "logAtk-".$_FILES['fileUsulan']['name']."-$tmp.".$_FILES['logAtkFile']['ext'];
      }
      
      $tanggal = "$tanggal_mutasi_year-$tanggal_mutasi_mon-$tanggal_mutasi_day 00:00:00";
      if (strtotime($tanggal) == 0) $tanggal = date('Y-m-d H:i:s');
      if ($status_pemeliharaan != 'Mutasi Ke Gudang Lain') $gudang_tujuan = null;
      if ($status_pemeliharaan != 'Mutasi Ke Unit Lain') $unit = null;
      
      // mencari atk_gudang_asal
      $arg = array
      (
         $barcode,
         $gudang,
         ($jumlah_barang > 0) ? 0 : 1
      );
      
      $atk_gudang = $this->Open($this->mSqlQueries['get_inv_atk_gudang_ada_barang'], $arg);
      if (empty($atk_gudang))
      {
         $this->EndTrans(false);
         return false;
      }
      
      $atk_gudang_asal = array();
      foreach (array_keys($atk_gudang) as $key)
      {
         if ($atk_gudang[$key]['invAtkGudangJumlah'] > $jumlah_barang)
            $atk_gudang[$key]['invAtkGudangJumlah'] = $jumlah_barang;
         
         $atk_gudang_asal[] = $atk_gudang[$key];
         $jumlah_barang -= $atk_gudang[$key]['invAtkGudangJumlah'];
         if ($jumlah_barang == 0) break;
      }
      // ---------
      
      foreach (array_keys($atk_gudang_asal) as $key)
      {
         $jumlah_barang += $atk_gudang_asal[$key]['invAtkGudangJumlah'];
         
         // update inv_atk_gudang asal
         $arg = array
         (
            -$atk_gudang_asal[$key]['invAtkGudangJumlah'],
            $atk_gudang_asal[$key]['invAtkGudangId']
         );
         
         if (!$this->Execute($this->mSqlQueries['update_inv_atk_gudang'], $arg))
         {
            $this->EndTrans(false);
            return false;
         }
         // ---------
         
         // update inv_atk_gudang tujuan
         if ($gudang_tujuan)
         {
            $arg = array
            (
               $atk_gudang_asal[$key]['invAtkGudangJumlah'],
               $atk_gudang_asal[$key]['invBrgAtkId'],
               $gudang_tujuan
            );
            
            // coba dulu update inv_atk_gudang
            $this->Execute($this->mSqlQueries['update_inv_atk_gudang_tujuan'], $arg);
            // kalo ngga ada row yang affected, baru add
            if (!$this->Affected_Rows() AND !$this->Execute($this->mSqlQueries['add_inv_atk_gudang'], $arg))
            {
               $this->EndTrans(false);
               return false;
            }
         }
         // ---------
         
         // tambah data log_atk
         $arg = array
         (
            $gudang,
            $atk_gudang_asal[$key]['invBrgAtkId'],
            $atk_gudang_asal[$key]['invAtkGudangJumlah'],
            $status_pemeliharaan,
            $unit,
            $gudang_tujuan,
            $tanggal,
            $keterangan,
            $logAtkFile
         );
         
         if (!$this->Execute($this->mSqlQueries['add_log_atk'], $arg))
         {
            $this->EndTrans(false);
            return false;
         }
         // ---------
      }
      
      // memaksa commit, sebab diatas muncul warning, tapi itu sudah direncanakan
      $this->Execute('COMMIT', array());
      $this->EndTrans(true);
      return true;
   }
}
?>
