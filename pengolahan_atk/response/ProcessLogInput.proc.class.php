<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').
   'module/'.Dispatcher::Instance()->mModule.'/business/LogSirkulasi.class.php';

class ProcessLogInput
{
	var $_POST;
	var $Obj;
	var $pageView;
	var $pageInput;
	//css hanya dipake di view
	var $cssDone = "notebox-done";
	var $cssFail = "notebox-warning";

	var $return;

	function __construct() {
      // inisialisasi messaging
		$msg = Messenger::Instance()->Receive(__FILE__);
      $this->Data = $msg[0][0];
      // ---------
      
		$this->Obj = new LogSirkulasi;
		$this->_POST = $_POST->AsArray();
      if (is_array($this->Data)) $this->_POST += $this->Data;
      
		$this->pageView = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'logList', 'view', 'html');
		$this->pageInput = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'logInput', 'view', 'html');
	}
   
   function Check ()
   {
      if (!isset($this->_POST['id']) OR isset($this->_POST['btnBalik'])) return $this->pageView;
      $this->_POST['logAtkJmlBrg'] = round($this->_POST['logAtkJmlBrg']);
      $maxJmlBrg = $this->_POST['invAtkGudangJumlah'] + $this->_POST['logAtkJmlBrgOld'];
      $minJmlBrg = $this->_POST['logAtkJmlBrgOld'] - $this->_POST['tujuanJml'];
      
      if ($this->_POST['logAtkJmlBrg'] < 1) $msg[] = "Jumlah barang harus lebih dari nol";
      if ($this->_POST['logAtkJmlBrg'] > $maxJmlBrg) $msg[] = "Jumlah barang tidak boleh lebih dari $maxJmlBrg!";
      if ($this->_POST['logAtkJmlBrg'] < $minJmlBrg) $msg[] = "Jumlah barang tidak boleh kurang dari $minJmlBrg!";
      if ($this->_POST['logAtkJmlBrg'] == $this->_POST['logAtkJmlBrgOld']) $msg[] = "Tekan tombol batal jika tidak jadi mengubah data!";
      
      if (count($msg) > 0)
      {
         $msg = array($this->_POST, $msg, 'notebox-alert');
         Messenger::Instance()->Send(Dispatcher::Instance()->mModule, 'logInput', 'view', 'html', $msg, Messenger::NextRequest);
         
         return $this->pageInput;
      }
      
      return true;
   }
   
   function Update()
   {
      $check = $this->Check();
      if ($check !== true) return $check;
      
      $result = $this->Obj->Update($this->_POST);
      if ($result)
         $msg = array(1=>'Pengubahan log sirkulasi berhasil dilakukan.', $this->cssDone);
      else $msg = array(1=>"Proses pengubahan data gagal!", $this->cssFail);
      Messenger::Instance()->Send(Dispatcher::Instance()->mModule, 'logList', 'view', 'html', $msg, Messenger::NextRequest);
      
      return $this->pageView;
   }
   
   function Delete()
   {
      $result = $this->Obj->Delete($this->_POST['idDelete']);
      if ($result === 0)
         $msg = array(1=>'Penghapusan Data Berhasil Dilakukan.', $this->cssDone);
      else $msg = array(1=>"Ada $result data yang tidak bisa dihapus.", $this->cssFail);
      Messenger::Instance()->Send(Dispatcher::Instance()->mModule, 'logList', 'view', 'html', $msg, Messenger::NextRequest);
      
      return $this->pageView;
   }
}
?>