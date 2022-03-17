<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').
   'module/pengelolaan_bhp/business/Pengelolaan.class.php';

//set Label for module
require_once GTFWConfiguration::GetValue( 'application', 'docroot') .
'module/label/response/Label.proc.class.php';

class ViewInputPengelolaan extends HtmlResponse
{
	function TemplateLabeling(){
      $ObjLabel = new GetModuleLabel();
      $arrLabel = $ObjLabel->GetLabel();
      
      //patTemplate name yang memiliki label2 dinamic
      $arrContent=array("content");
      
      for($i=0;$i<count($arrContent);$i++){
         for($j=0;$j<count($arrLabel);$j++){
            $this->mrTemplate->AddVar($arrContent[$i],$arrLabel[$j]['labelCode'],$arrLabel[$j]['labelName']);
         }
      }
   }
	
   function TemplateModule()
   {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application','docroot').
         'module/pengelolaan_bhp/template');
      $this->SetTemplateFile('view_input_pengelolaan.html');
   }
   
   function ProcessRequest()
   {
   	$Obj = new Pengelolaan();
   	
      // inisialisasi messaging
		$msg = Messenger::Instance()->Receive(__FILE__);
		$this->Data = $msg[0][0];
		$this->Pesan = $msg[0][1];
		$this->css = $msg[0][2];
      // ---------
      
      // inisialisasi default value
      $data = array();
      if (is_array($this->Data))
      {
			$datas = $this->Data;
			$defaultDate= $datas['tanggalLelang_year']."-".$datas['tanggalLelang_mon']."-".$datas['tanggalLelang_day'];
			$data['trans_id'] = $datas['idtrans'];
			$data['trans_keterangan'] = $datas['catatan'];
			$data['trans_nama'] = $datas['penerima'];
			$data['trans'] = $datas['barangLelang'];
			$data['unitkerjaNama'] = $datas['unitkerjaNama'];
			$data['unitkerjaId'] = $datas['unitkerjaId'];
      }
      elseif (isset($_GET['id']))
      {
         $result = $Obj->GetDataPengelolaanById ($_GET['id']->Integer()->Raw());
			
         if ($result === false) 
				unset($_GET['id']);
         else 
				$data = $result[0];
				$data['trans_id']=$_GET['id']->Integer()->Raw();
				$data['trans']=$result;
				$defaultDate=$data['trans_tanggal'];
      }	
			else 
		{
			$defaultDate=date('Y-m-d');
		}
      
      $return['data'] = $data;
      $return['json']['barang_lelang'] = json_encode($data['trans']);
      unset ($return['data']['trans']);
      // --------
	Messenger::Instance()->SendToComponent('tanggal', 'Tanggal', 'view', 'html', 'tanggalLelang',
    array($defaultDate, $tglawal, $tglakhir), Messenger::CurrentRequest);
      
		// inisialisasi url
      $return['url']['action'] = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'addPengelolaan', 'do', 'json');

		if($_GET['id'] !="") $idtrans = $_GET['id'];
		else $idtrans = $data['trans_id'];
		
      if (isset($_GET['id']) || $data['trans_id'] !="") $return['url']['action'] .= "&id=".$idtrans;
      $return['url']['pop_up_barang'] = Dispatcher::Instance()->GetUrl('pengelolaan_bhp', 'kodeBarang', 'popup', 'html');
      // ---------
      
      return $return;
   }
   
	function ParseTemplate($data = NULL)
   {
      $this->TemplateLabeling();
      
      // render message box
     if ($this->Pesan) {
			$this->mrTemplate->SetAttribute('warning_box', 'visibility', 'visible');
			$this->mrTemplate->AddVar('warning_box', 'ISI_PESAN', $this->Pesan);
		}
      // ---------
		$this->mrTemplate->AddVar('content', 'URL_POP_UP_UNIT_PJ', Dispatcher::Instance()->GetUrl('pengelolaan_bhp', 'PopUpUnitPj' , 'view', 'html'));
      
		// Render JSON data
      $this->mrTemplate->AddVars('content', $data['json'], 'JSON_');
      // ---------
      
      // Render URL
      $this->mrTemplate->AddVars('content', $data['url'], 'URL_');
      // ---------
      
      // render default form value
		if ($_REQUEST['id']!="" || $data['data']['trans_id']!="") {
			$data['data']['mode'] = "Ubah";
		}
			else 
		{
			$data['data']['mode'] = "Tambah";			
		}

		$data['data']['total'] += $data['data']['total'];
		$data['data']['total'] = number_format($data['data']['total'], 2, ',', '.');
		$this->mrTemplate->AddVars('content', $data['data'], '');
      // ---------
		$this->mrTemplate->AddVar('content', 'URL_KEMBALI', Dispatcher::Instance()->GetUrl('pengelolaan_bhp', 'pengelolaan', 'view', 'html'));   
   }
}
?>
