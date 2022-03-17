<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').'module/'.Dispatcher::Instance()->mModule.'/business/Usulan.class.php';

class ProcessUsulanBarang {

	var $_POST;
	var $Obj;
	var $pageView;
	var $pageInput;
	
	var $cssDone = "notebox-done";
	var $cssFail = "notebox-warning";

	var $return;

	function __construct() {
		$this->Obj = new Usulan;
		$this->_POST = $_POST->AsArray();
      $this->pageView = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'listUsulanBarang', 'view', 'html');
		$this->pageInput = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'inputUsulanBarang', 'view', 'html');
      $this->pageUpdate = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'updateUsulanBarang', 'view', 'html');
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
   
   function Check ()
   {
      if (isset($this->_POST['btnbalik'])) return $this->pageView;
      unset($this->_POST['btnsimpan']);
      unset($this->_POST['formula']);
      unset($this->_POST['nominal']);
      unset($this->_POST['jumlah']);
      
      $msg = array();
      
      if (!empty($this->_POST['detail_barang'])) foreach ($this->_POST['detail_barang'] as $key=>$value)
      {
         if (intval($value['usulanBrgDetJml']) != $value['usulanBrgDetJml'] OR $value['usulanBrgDetJml'] == 0)
         {
            $msg[] = "Jumlah Usulan harus numeris dan nilainya lebih dari 0.";
            break;
         }
         
         if (intval($value['brgHps']) != $value['brgHps'] OR $value['brgHps'] == 0)
         {
         	$msg[] = "Harga Satuan harus numeris dan nilainya lebih dari 0.";
         	break;
         }
         
         /*if (trim($value['spesifikasi']) == "")
         {
            $msg[] = "Spesifikasi barang harus diisi";
            break;
         }
         
         if (trim($value['usulanBrgDetBrgKelompokId']) == "")
         {
            $msg[] = "Jenis barang harus diisi";
            break;
         }
         if (trim($value['usulanBrgDetJenisPengadaanId']) == "")
         {
            $msg[] = "Jenis pengadaan harus diisi";
            break;
         }
         */
         if (trim($value['usulanBrgDetBrgId']) == "")
         {
            $msg[] = "Barang harus diisi";
            break;
         }
         /*
         if (trim($value['usulanBrgDetBrgSatuan']) == "")
         {
            $msg[] = "Satuan barang harus diisi";
            break;
         }
         */        
         $totalHarga += ($value['brgHps']*$value['usulanBrgDetJml']);
         $this->_POST['detail_barang'][$key]['usulanBrgDetJml'] = intval($value['usulanBrgDetJml']);
      }else{
      	$msg[] = 'Detail Barang harus diisi';
      }
		
// 		$usulanBrgMAKNominal = (int)$this->_POST['usulanBrgMAKNominal'];
// 		if($usulanBrgMAKNominal != 0){
// 			if($totalHarga > $usulanBrgMAKNominal){
// 				$totalMaksimal = number_format($usulanBrgMAKNominal,0,'','.');
// 				$msg[] = "Maksimal total harga berdasarkan RAB tidak boleh melebihi <strong>Rp. {$totalMaksimal}</strong>";
// 			}
// 		}
		
      if ($this->_POST['usulanBrgUnitId'] == '' /*|| $this->_POST['usulanBrgMAKId'] == '' || $this->_POST['periode'] == ''  */|| $this->_POST['usulanBrgNoUsulan'] == '')
         $msg[] = "Form bertanda <strong>*</strong> wajib diisi.";

      if (count($msg) > 0)
      {
         $msg = array($this->_POST, $msg, 'notebox-alert');
         if (isset($_GET['id'])){
            Messenger::Instance()->Send(Dispatcher::Instance()->mModule, 'updateUsulanBarang', 'view', 'html', $msg, Messenger::NextRequest);
               $return = $this->pageUpdate;
         } else {
            Messenger::Instance()->Send(Dispatcher::Instance()->mModule, 'inputUsulanBarang', 'view', 'html', $msg, Messenger::NextRequest);
               $return = $this->pageInput;
         }
         

         if (isset($_GET['id'])) $return .= "&id=".$_GET['id'];
         return $return;
      }
      
      return true;
   }
   
   function Add()
   {
      $check = $this->Check();
      if ($check !== true) return $check;
      $result = $this->Obj->Add($this->_POST);
      if ($result === 0)
         $msg = array(1=>'Penambahan Data Berhasil Dilakukan.', $this->cssDone);
      else $msg = array(1=>"Proses Penambahan Data Gagal!", $this->cssFail);
      Messenger::Instance()->Send('mr_usulan_barang', 'listUsulanBarang', 'view', 'html', $msg, Messenger::NextRequest);
      
      return $this->pageView;
   }

   function Update()
   {
      $check = $this->Check();
      if ($check !== true) return $check;
      $result = $this->Obj->Update($this->_POST, $_GET['id']);
      if ($result === 0)
         $msg = array(1=>'Penggubahan Data Berhasil Dilakukan.', $this->cssDone);
      else $msg = array(1=>"Proses Penggubahan Data Gagal!", $this->cssFail);
      Messenger::Instance()->Send('mr_usulan_barang', 'listUsulanBarang', 'view', 'html', $msg, Messenger::NextRequest);
      
      return $this->pageView;
   }
   
   function Approve()
   {
      $result = $this->Obj->Approval($this->_POST);
      if ($result === 0)
         $msg = array(1=>'Approval Data Berhasil Dilakukan.', $this->cssDone);
      else $msg = array(1=>"Proses Approval Data Gagal!", $this->cssFail);
      Messenger::Instance()->Send('mr_usulan_barang', 'listUsulanBarang', 'view', 'html', $msg, Messenger::NextRequest);
      
      return $this->pageView;
   }

}
?>
