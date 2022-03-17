<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').'module/'.Dispatcher::Instance()->mModule.'/business/InventarisasiBhp.class.php';

class ProcessInventarisasiBhp
{
	var $_POST;
	var $_GET;
	var $Obj;
	var $pageView;
	var $pageInput;
	var $pesanErr;
	
	//css hanya dipake di view
	var $cssDone = "notebox-done";
	var $cssFail = "notebox-warning";

	var $return;

	function __construct() {
		$this->Obj = new InventarisasiBhp();
		$this->_POST = $_POST->AsArray();
		$this->_GET = $_GET->AsArray();
      
		$this->pageView = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'inventarisasiBhp', 'view', 'html');
		$this->pageInput = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'inputInventarisasiBhp', 'view', 'html');
	}
	
	function validasi(){
      $this->pesanErr = '';
      $validasi = true;
      
      if($this->_POST['barang_id'] == '' || $this->_POST['detil_barang'] == ''){
         $this->pesanErr .= 'Fields yang bertanda * harus diisi.<br />';
         $validasi = false;
      }
		
      return $validasi;
   }
   
	function GenerateNumber()
	{
		$gnumber = $this->Obj->GenerateNumber($this->_POST['kode_barang']);
		$explode = explode(".", $gnumber[0]['kode']);
		$lenght 	= strlen(intval($explode[5]+1));
		switch($lenght){
			case 1: $generate = '00000'.($explode[5]+1); break;
			case 2: $generate = '0000'.($explode[5]+1); break;
			case 3: $generate = '000'.($explode[5]+1); break;
			case 4: $generate = '00'.($explode[5]+1); break;
			case 5: $generate = '0'.($explode[5]+1); break;
			case 6: $generate = ($explode[5]+1); break;
		}
		return $generate;
	}
	
	function Add()
   {
		if($this->validasi()!=true){
			Messenger::Instance()->Send(Dispatcher::Instance()->mModule, 'inputInventarisasiBhp', 'view', 'html', array($this->_POST,$this->pesanErr,$this->cssFail),Messenger::NextRequest);
			return $this->pageInput;
		} else {
		
			$generate = $this->GenerateNumber();
			$userId = trim(Security::Instance()->mAuthentication->GetCurrentUser()->GetUserId());
			$dataUpdate = array(
				$this->_POST['barang_id'],
				$generate,
				$this->_POST['detil_barang'],
				(empty($this->_POST['stat'])?'N':$this->_POST['stat']),
				$userId
			);
			
			$result = $this->Obj->DoInsertDetilBHP($dataUpdate);
			if($result == true){
				Messenger::Instance()->Send(Dispatcher::Instance()->mModule, 'inventarisasiBhp', 'view', 'html', array($this->_POST,'Penambahan Data Berhasil Dilakukan', $this->cssDone),Messenger::NextRequest);
			} 
				else 
			{
				Messenger::Instance()->Send(Dispatcher::Instance()->mModule, 'inputInventarisasiBhp', 'view', 'html', array($this->_POST,'Gagal Menambah Data', $this->cssFail),Messenger::NextRequest);
			}
			return $this->pageView;
		}
   }
	
   function Update()
   {
      if($this->validasi()!=true){
			Messenger::Instance()->Send(Dispatcher::Instance()->mModule, 'inputInventarisasiBhp', 'view', 'html', array($this->_POST,$this->pesanErr,$this->cssFail),Messenger::NextRequest);
			return $this->pageInput;
		} else {
		
			$generate = $this->GenerateNumber();
			$userId = trim(Security::Instance()->mAuthentication->GetCurrentUser()->GetUserId());
			$dataUpdate = array(
				$this->_POST['detil_barang'],
				(empty($this->_POST['stat'])?'N':$this->_POST['stat']),
				$userId,
				$this->_GET['id']
			);
			
			$result = $this->Obj->DoUpdateDetilBHP($dataUpdate);
			if($result == true){
				Messenger::Instance()->Send(Dispatcher::Instance()->mModule, 'inventarisasiBhp', 'view', 'html', array($this->_POST,'Perubahan Data Berhasil Dilakukan', $this->cssDone),Messenger::NextRequest);
			} 
				else 
			{
				Messenger::Instance()->Send(Dispatcher::Instance()->mModule, 'inputInventarisasiBhp', 'view', 'html', array($this->_POST,'Gagal Mengubah Data', $this->cssFail),Messenger::NextRequest);
			}
			return $this->pageView;
		}
   }
}
?>