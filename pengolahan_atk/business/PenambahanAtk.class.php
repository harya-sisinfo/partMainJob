<?php

/*
@ClassName : PenambahanAtk
@Copyright : PT Gamatechno Indonesia
@Analyzed By : Nanang Ruswianto <nanang@gamatechno.com>
@Author By : Dyan Galih <galih@gamatechno.com>
@Version : 0.1
@StartDate : 2010-12-08
@LastUpdate : 2010-12-08
@Description : Penambahan Atk
*/

class PenambahanAtk extends Database
{

   protected $mSqlFile;
   function __construct($connectionNumber = 0)
   {
      $this->mSqlFile = 'module/pengolahan_atk/business/penambahan_atk.sql.php';
      parent::__construct($connectionNumber);
      //$this->setDebugOn();
   }

   function checkDate($date){
      $result = $this->Open($this->mSqlQueries['check_date'],array($date));
      return $result[0];
   }
   
   function GetKodeSistem(){
   		$userId = Security::Instance()->mAuthentication->GetCurrentUser()->GetUserId();
   		$result = $this->Open($this->mSqlQueries['get_kode_sistem'],array($userId));
   		return $result[0]['unitkerjaKodeSistem'];
   }
   
   # multiunit
   function GetDataUsulanBhp($startRec, $itemViewed, $unitKerja, $noUsulanBhp, $namaBarang){
   	    $kodeSistem = $this->GetKodeSistem();
   	    $sql = $this->mSqlQueries['get_data_usulan_bhp'];
   	    $sql = ($unitKerja <> '' && $unitKerja <> 'all') ? str_replace('[SEARCH_UNIT]', ' AND usulanBrgUnitId = '.$unitKerja, $sql) : str_replace('[SEARCH_UNIT]', '', $sql);
   	    $result = $this->Open($sql,array('%'.$noUsulanBhp.'%','%'.$namaBarang.'%',$kodeSistem,$kodeSistem.'.%',$startRec, $itemViewed));
   	    return $result;
   }
   
   # multiunit
   function GetUnitKerja(){
   		$kodeSistem = $this->GetKodeSistem();
   		$result = $this->Open($this->mSqlQueries['get_unit_kerja'],array($kodeSistem,$kodeSistem.'.%'));
   		return $result;
   }
   
   function GetCount(){
   	$result = $this->Open($this->mSqlQueries['get_count'],array());
   	return $result[0]['total'];
   }
   
   private function GetAtkMstByBrgId($barangId)
   {
      $result = $this->Open($this->mSqlQueries['get_atk_mst_id'], array(
         $barangId
      ));

      return $result['0']['invAtkMstId'];
   }

   private function AddAtkMst($data)
   {
      $result = $this->Execute($this->mSqlQueries['penambahan_atk_mst'], $data);

      return $this->AffectedRows();
   }

   private function GetAtkDetId($data)
   {
      $result = $this->Open($this->mSqlQueries['get_atk_det_id'], $data);

      return $result['0']['invAtkDetId'];
   }

   private function UpdateAtkDetil($data)
   {
      $result = $this->Execute($this->mSqlQueries['update_atk_detil'], $data);

      return $this->AffectedRows();
   }

   private function AddAtkDetil($data)
   {
      $result = $this->Execute($this->mSqlQueries['penambahan_atk_detil'], $data);

      return $this->AffectedRows();
   }

   private function AddAtkBiaya($data)
   {
      $result = $this->Execute($this->mSqlQueries['penambahan_atk_biaya'], $data);

      return $this->AffectedRows();
   }

   private function GetAtkBiayaByMstId($id)
   {
      $result = $this->Open($this->mSqlQueries['get_atk_biaya_by_mst_id'], array(
         $id
      ));

      return $result['0']['invAtkBiayaId'];
   }

	private function GetAtkBrgByMstId($id)
   {
      $result = $this->Open($this->mSqlQueries['get_atk_brg_by_mst_id'], array($id));
      return $result;
   }
	
   private function UpdateAtkBiaya($data)
   {
      $result = $this->Execute($this->mSqlQueries['update_atk_biaya'], $data);

      return $this->AffectedRows();
   }

   private function AddAtkBrg($data)
   {
      $result = $this->Execute($this->mSqlQueries['penambahan_atk_brg'], $data);

      return $this->AffectedRows();
   }
	
	private function UpdateAtkBrg($data)
   {
      $result = $this->Execute($this->mSqlQueries['perubahan_atk_brg'], $data);
      return $this->AffectedRows();
   }

   private function AddAtkGudang($data)
   {
      $result = $this->Execute($this->mSqlQueries['penambahan_atk_gudang'], $data);
      return $this->AffectedRows();
   } 
	
	private function UpdateAtkGudangMax($data)
   {
      $result = $this->Execute($this->mSqlQueries['update_atk_gudang_max'], $data);
      return $this->AffectedRows();
   }
	
	private function UpdateAtkGudang($data)
   {
      $result = $this->Execute($this->mSqlQueries['perubahan_atk_gudang'], $data);

      return $this->AffectedRows();
   }

   private function GetMaxAtkBrg()
   {
      $result = $this->Open($this->mSqlQueries['get_atk_max_brg'], array());

      return $result[0]['invBrgAtkId'];
   }
	
	private function GetMaxAtkGdgByDetId($detId, $ruangId)
   {
      $result = $this->Open($this->mSqlQueries['get_atk_max_gdg_by_det_id'], array($detId, $ruangId));
		return $result[0]['jumlah'];
   }

   private function AddLogAtk($data){
      $result = $this->Execute($this->mSqlQueries['add_log_atk'], $data);
      return $this->AffectedRows();
   }
	
	private function AddLogAtkUpdate($data){
      $result = $this->Execute($this->mSqlQueries['add_log_atk_update'], $data);
      return $this->AffectedRows();
   }

   // NOTE: fungsi ini digunakan juga oleh modul distribusi_pengadaan, hati2 bila ingin diupdate
   public function AddAtk($data)
   {
      $mstStat = $detStat = $biayaStat = $atkBrgStat = $gudangStat = $usulanBhpStat = $logStat = false;
      $this->StartTrans();
      $userId = Security::Instance()->mAuthentication->GetCurrentUser()->GetUserId();
      $mstId = $this->GetAtkMstByBrgId($data['barangId']);

      if (empty($mstId))
      {
         $dataMst = array(
            $data['barangId'],
				$data['satuan_barang'],
            $data['keterangan'],
            $userId
         );

         if ($this->AddAtkMst($dataMst)){
            $mstStat = true;
            $mstId = $this->GetAtkMstByBrgId($data['barangId']);
         }else $mstStat = false;
      }else $mstStat = true;

      $dataDetAtk = array(
         $data['barcode'],
         $data['barcode'],
         $mstId
      );
      $detId = $this->GetAtkDetId($dataDetAtk);

      if (empty($detId))
      {
         $dataDet = array(
            $mstId,
            //$data['barcode'],
				!empty($data['barcode'])?$data['barcode']:NULL,
            $data['label_barang'],
            $data['merk_barang'],
            $data['spesifikasi'],
            $userId
         );
         if($this->AddAtkDetil($dataDet)){
            $detStat = true;
            $detId = $this->GetAtkDetId($dataDetAtk);
         }else $detStat = false;
      }
      else
      {
         $dataDet = array(
            $mstId,
            $data['label_barang'],
            $data['merk_barang'],
            $data['spesifikasi'],
            $userId,
            $detId
         );
         $detStat = ($this->UpdateAtkDetil($dataDet)>=0)?true:false;
      }

      $biayaId = $this->GetAtkBiayaByMstId($mstId);

      if (empty($biayaId))
      {
         $dataDet = array(
            $mstId,
            $data['invAtkBiayaNominal']
         );
         $biayaStat = ($this->AddAtkBiaya($dataDet))?true:false;
      }
      else
      {
         $dataDet = array(
            $mstId,
            $data['invAtkBiayaNominal'],
            $biayaId
         );
         $biayaStat = ($this->UpdateAtkBiaya($dataDet)>=0)?true:false;
      }

      $dataDet = array(
         $detId,
         (isset($data['tgl_beli'])?$data['tgl_beli']:date('Y-m-d')),
         $data['invAtkBiayaNominal'],
         $data['jumlah_barang'],
         (isset($data['barcodekeu'])?$data['barcodekeu']:'')
      );
      $atkBrgStat = ($this->AddAtkBrg($dataDet))?true:false;

      $atkBrgId = $this->GetMaxAtkBrg();
      $dataDet = array(
         $data['gudang'],
         $atkBrgId,
         $data['jumlah_barang'],
         $data['invAtkBiayaNominal'] / $data['jumlah_barang'],
         $data['jumlah_barang']
      );
      $gudangStat = ($this->AddAtkGudang($dataDet))?true:false;
      
      $statusLog = (!empty($data['statusLog']))?$data['statusLog']:'Penambahan Stock Tanpa Pengadaan'; #default
      
      # update usulan if exist
      if($data['usulanbhpid'] <> ''){
      	$usulanBhpStat = ($this->UpdateUsulanBhpDetail($data['jumlah_barang'],$data['usulanbhpid'])>=0)?true:false;
      	$statusLog = 'Penambahan Stock Dengan Usulan';
      	$data['keterangan'] = $data['nousulanbhp'];
      }else $usulanBhpStat = true;

      $dataDet = array(
         $data['gudang'],$atkBrgId,$data['jumlah_barang'],
         $statusLog,(isset($data['tgl_beli'])?$data['tgl_beli']:date('Y-m-d')),
         ($data['invAtkBiayaNominal']/$data['jumlah_barang']),
         $data['keterangan'],$userId
      );

      $logStat = ($this->AddLogAtk($dataDet))?true:false;

      #echo $this->GetLastError();
      $arrStatus = explode(" ", $this->GetLastError());
      $addAtk = $mstStat && $detStat && $biayaStat && $atkBrgStat && $gudangStat && $usulanBhpStat && $logStat;

      //echo $mstStat.'|'.$detStat.'|'.$biayaStat.'|'.$atkBrgStat.'|'.$gudangStat.'|'.$usulanBhpStat.'|'.$logStat;
      $this->EndTrans($addAtk);
      return ($addAtk)?true:false;

      #$result = $this->Execute($this->mSqlQueries['add_atk'], array($data));

   }
	
	public function ab($nilai,$data,$i,$akhir,$habis){
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
			//if(count($_SESSION['hasil']) > $akhir)unset($_SESSION['hasil']);
			
		}
	}

   function GetGudangByMstId($mstId,$ruangId){
      $result = $this->Open($this->mSqlQueries['get_gudang_by_mst_id'], array($mstId,$ruangId));
      return $result;
   }

   function GetKodeSistemByRuang($ruangId){
      $result = $this->Open($this->mSqlQueries['get_kode_sistem_by_ruang'], array($ruangId));
      return $result[0]['unitkerjaKodeSistem'];
   }

   function GetHrgSatTerakhir($mstId,$kodeSistem){
      $result = $this->Open($this->mSqlQueries['get_hrg_sat_akhir'], array($mstId,$kodeSistem,$kodeSistem.'%'));
      return $result[0]['hrgSat'];
   }

   function UpdateGudang($jmlBrg,$gudangId){
      $result = $this->Execute($this->mSqlQueries['update_gudang'], array($jmlBrg,$gudangId));
      return $this->AffectedRows();
   }
	
   // adjustment stok (stok opname)
	public function UpdateAtk($data)
   {		
      $this->StartTrans();
      $userId = Security::Instance()->mAuthentication->GetCurrentUser()->GetUserId();
		$ruangId = $data['gudang'];
      $mstId = $this->GetAtkMstByBrgId($data['barang_id']);
		$stokBaru = (int)$data['sisa_barang'];
		$stokLama = (int)$data['sisa_barang_lama'];

      if(trim($stokBaru) == ''||$stokBaru < 0) return false;

      if($stokBaru == $stokLama) $stat = 'SM';
      elseif($stokBaru < $stokLama) $stat = 'LK';
      elseif($stokBaru > $stokLama) $stat = 'LB';

		switch ($stat) {
			case 'SM': //-- sama
				return true;
				break;
			case 'LK': //-- kurang (lakukan RTH secara FIFO)
            $sisaRth = $stokLama - $stokBaru;
            $arrGudang = $this->GetGudangByMstId($mstId,$ruangId);
            foreach($arrGudang as $val){
               if($sisaRth == 0) break;
               $jmlRth = ($val['jml']>=$sisaRth)?$sisaRth:$val['jml'];
               $sisaStokGdg = $val['jml']-$jmlRth;
               $sisaRth -= $jmlRth;
               $update = $this->UpdateGudang($sisaStokGdg,$val['invGdgId']);
               $log = $this->AddLogAtkUpdate(array(
                  $val['ruangId'],$val['invBrgAtkId'],(-1*$jmlRth),
                  $val['hrgSat'],$val['jml'],$data['keterangan'],$userId
               ));
               $adjustment = $update && $log;
               if(!$adjustment) break;
            }
				break;
			case 'LB': //-- tambah (lakukan penambahan stok dengan harga satuan terakhir)
            $selisih = $stokBaru - $stokLama;
            $kodeSistem = $this->GetKodeSistemByRuang($ruangId);
            $hrgSatAkhir = $this->GetHrgSatTerakhir($mstId,$kodeSistem);
            $dataDetAtk = array('','',$mstId);
            $detId = $this->GetAtkDetId($dataDetAtk);
            $dataDet = array($detId,date('Y-m-d'),$hrgSatAkhir*$selisih,$selisih,'');
            $addAtkBrg = $this->AddAtkBrg($dataDet);
            $atkBrgId = $this->LastInsertId();
            $dataDet = array($ruangId,$atkBrgId,$selisih,$hrgSatAkhir,$selisih);
            $addGudang = $this->AddAtkGudang($dataDet);
            $dataDet = array($ruangId,$atkBrgId,$selisih,$hrgSatAkhir,$stokLama,$data['keterangan'],$userId);
            $addLog = $this->AddLogAtkUpdate($dataDet);
            $adjustment = $addAtkBrg && $addGudang && $addLog;
				break;
		}

      $this->EndTrans($adjustment);
      return (!$adjustment)?false:true;
	}
	
	private function UpdateUsulanBhpDetail($jmlBrg, $usulanDetId){
		$result = $this->Execute($this->mSqlQueries['update_usulan_bhp_detail'], array($jmlBrg,$jmlBrg,$usulanDetId));
		return $this->AffectedRows();
	}
}
?>
