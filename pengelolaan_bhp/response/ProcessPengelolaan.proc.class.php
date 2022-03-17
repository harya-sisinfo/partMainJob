<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot') . 'module/pengelolaan_bhp/business/Pengelolaan.class.php';
class ProcessPengelolaan {

	var $_POST;
	var $Obj;
	var $pageView;
	var $pageInput;
	//css hanya dipake di view
	var $cssDone = "notebox-done";
	var $cssFail = "notebox-warning";

	var $return;
	var $decId;
	var $encId;
	var $msg;
	var $lelangDate;

	function __construct() 
	{	
		$this->Obj = new Pengelolaan();
		$this->_POST = $_POST->AsArray();
		$this->decId = Dispatcher::Instance()->Decrypt($_REQUEST['dataId']);
		$this->encId = Dispatcher::Instance()->Encrypt($this->decId);
		$this->pageView = Dispatcher::Instance()->GetUrl('pengelolaan_bhp', 'pengelolaan', 'view', 'html').'&grp='.Dispatcher::Instance()->Encrypt($this->_POST['idtrans']);
		$this->pageViewDel = Dispatcher::Instance()->GetUrl('pengelolaan_bhp', 'pengelolaan', 'view', 'html').'&grp='.Dispatcher::Instance()->Decrypt($_GET['grp']);
		$this->pageInput = Dispatcher::Instance()->GetUrl('pengelolaan_bhp', 'inputPengelolaan', 'view', 'html');
		$this->lelangDate = $this->_POST['tglRth'];
		$this->idUser = Security::Instance()->mAuthentication->getcurrentuser()->GetUserId();	
	}

	function createReturn($status,$value,$css){
		return array('status'=>$status,'value'=>$value,'css'=>$css);
	}

   function sendMsg($sub,$msg,$css){
      Messenger::Instance()->Send('pengelolaan_bhp', $sub, 'view', 'html', array('',$msg,$css),Messenger::NextRequest); 
   }

	function Check(){
		if (isset($_POST['btnsimpan'])) {
			$cekDate = $this->Obj->checkDate($this->_POST['tglRth']);
			if(
				trim($this->_POST['penerima']) == "" ||
				empty($this->_POST['unitkerjaId']) ||
				count($this->_POST['barangLelang']) == 0
			) $this->msg[] = 'Field Bertanda * Wajib Diisi';
			elseif($cekDate['accept'] == 0)
				$this->msg[] = 'Tanggal Pembukuan Harus Lebih Besar Dari '.$cekDate['tgl'];
			else{
				$detil = $this->_POST['barangLelang'];
				$over = 0;
				foreach ($detil as $value) {
					if($value['jumlah'] > $value['stok']){
						$over++;
						break;
					}
				}
				if($over) $this->msg[] = 'Jumlah Diminta Melebihi Stok Yang Tersedia';
			}

			if(count($this->msg)>0) $this->msg = implode('\n', $this->msg);
		}
	}
	
	function GenerateNumber()
	{
		$gnumber = $this->Obj->GetAtkMstNomor();
		$explode = explode(".", $gnumber[0]['transAtkMstNomor']);
		$lenght 	= strlen(intval($explode[1]+1));
		$year		= date('Y');
		switch($lenght){
			case 1: $generate = $year.'.'.'00000'.($explode[1]+1); break;
			case 2: $generate = $year.'.'.'0000'.($explode[1]+1); break;
			case 3: $generate = $year.'.'.'000'.($explode[1]+1); break;
			case 4: $generate = $year.'.'.'00'.($explode[1]+1); break;
			case 5: $generate = $year.'.'.'0'.($explode[1]+1); break;
			case 6: $generate = $year.'.'.($explode[1]+1); break;
			default: $generate = $year.'.'.($explode[1]+1); break;
		}
		return $generate;
	}

	function Add() 
	{
		$this->Check();
		if(!empty($this->msg)) 
			return $this->CreateReturn(false, $this->msg, 'notebox-alert');
		else{
			$gnumber = $this->GenerateNumber();
			$this->Obj->StartTrans();
			$detil = $this->_POST['barangLelang'];
			$addMst = $this->Obj->DoAddPengelolaan($this->_POST['tglRth'], $gnumber, $this->_POST['catatan'], $this->_POST['penerima'], $this->_POST['unitkerjaId'], $this->idUser);
			$mstId = $this->Obj->LastInsertId();

			if(!empty($detil)){
				foreach ($detil as $value){
					$addDet = $this->Obj->DoAddPengelolaanDet(
						$mstId,$value['barangId'], $value['barangDiskripsi'], $value['jumlah'], $value['lelangBiaya']
					);
					if(!$addDet) break;
				}			
			}
			
			$updateStok = $this->Obj->UpdateStokGedung($detil,$gnumber,$this->_POST['tglRth'],$this->_POST['unitkerjaId']);

			$result = $addMst && $addDet && $updateStok['stat'];
			$this->Obj->EndTrans($result);

			if(!$updateStok['stat']) return $this->createReturn(false,$updateStok['msg'],'');

			if ($result) $this->sendMsg('pengelolaan','Penambahan Data Berhasil Dilakukan', $this->cssDone);
			else $this->sendMsg('pengelolaan','Penambahan Data Gagal'.@$addNotif, $this->cssFail);
			
			return $this->createReturn(true,$this->pageView,'');
		}
	}

	function Update()
	{
		$cek = $this->Check();
		if($cek === true ) 
		{
			$this->Obj->StartTrans();	
			$detil=$this->_POST['barangLelang'];
		
			/* if(!empty($detil))
			{
				foreach ($detil as $value)
				{
					$totalBiaya += $value['lelangBiaya'];
				}			
			}
				else
			{
				$totalBiaya=0;
			} */
			
			$updatePengelolaan = $this->Obj->DoUpdatePengelolaan($this->lelangDate, $this->_POST['catatan'], $this->_POST['penerima'], $this->_POST['unitkerjaId'], $this->idUser, $this->_POST['idtrans']);
			$delete = $this->Obj->DoDeletePengelolaanDetil($this->_POST['idtrans']);
				
			if(!empty($detil))
			{
				foreach($detil as $value)
				{
					$updatePengelolaanDet = $this->Obj->DoUpdatePengelolaanDet($this->_POST['idtrans'], $value['barangId'], $value['barangDiskripsi'], $value['jumlah'], $value['lelangBiaya']);
				}			
			}
				
			$this->Obj->EndTrans($updatePengelolaanDet);
			
			if($updatePengelolaanDet === true) 
			{
				Messenger::Instance()->Send('pengelolaan_bhp', 'pengelolaan', 'view', 'html', array($this->_POST,'Perubahan Data Berhasil Dilakukan', $this->cssDone),Messenger::NextRequest);
			} 
				else 
			{
				Messenger::Instance()->Send('pengelolaan_bhp', 'pengelolaan', 'view', 'html', array($this->_POST,'Gagal Mengubah Data', $this->cssFail),Messenger::NextRequest);
			}
		}
			elseif($cek == "empty") 
		{
			Messenger::Instance()->Send('pengelolaan_bhp', 'inputPengelolaan', 'view', 'html', array($this->_POST,'Lengkapi Isian Data, Tanda * menunjukkan field tersebut harus diisi atau dipilih'),Messenger::NextRequest);
			return $this->pageInput . "&dataId=" . $this->encId .'&grp='. Dispatcher::Instance()->Encrypt($this->_POST['idtrans']);;
		} 
		return $this->pageView;	
	}
	
	function Delete()
	{
		$arrId = $this->_POST['idDelete'];
		$deleteDetil = $this->Obj->DoDeletePengelolaanDetil($arrId);
		$deleteArrData = $this->Obj->DoDeletePengelolaanByArrayId($arrId);
		if($deleteArrData === true) 
		{
			Messenger::Instance()->Send('pengelolaan_bhp', 'pengelolaan', 'view', 'html', array($this->_POST,'Penghapusan Data Berhasil Dilakukan', $this->cssDone),Messenger::NextRequest);
		} 
			else 
		{
			//jika masuk disini, berarti PASTI ada salah satu atau lebih data yang gagal dihapus
			for($i=0;$i<sizeof($arrId);$i++)
			{
				$deleteData = false;
				$deleteData = $this->Obj->DoDeleteBgDepartemenById($arrId[$i]);
				if($deleteData === true)
				{
					$sukses += 1;
				}
					else 
				{
					$gagal += 1;
				}
			}
			Messenger::Instance()->Send('pengelolaan_bhp', 'pengelolaan', 'view', 'html', array($this->_POST, $gagal . ' Data Tidak Dapat Dihapus.', $this->cssFail),Messenger::NextRequest);
		}
		return $this->pageView;
	}
}
?>
