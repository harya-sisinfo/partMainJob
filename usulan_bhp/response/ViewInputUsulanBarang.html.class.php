<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').'module/'.Dispatcher::Instance()->mModule.'/business/Usulan.class.php';

class ViewInputUsulanBarang extends HtmlResponse
{
   function TemplateModule()
   {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application','docroot').'module/'.Dispatcher::Instance()->mModule.'/template');
      $this->SetTemplateFile('view_input_usulan_barang.html');
   }
   
   function ProcessRequest()
   {
   
   	$Obj = new Usulan;
      
		$msg = Messenger::Instance()->Receive(__FILE__);
      	@$this->Data = $msg[0][0];
		@$this->Pesan = $msg[0][1];
		@$this->css = $msg[0][2];
   	  	
   	$data = array();
      if (is_array($this->Data)){
         $data = $this->Data;
      } 	
   	 
		if (!isset($data['usulanBrgNoUsulan']))
      {
         $date = date('Ymd.His');
         $data['usulanBrgNoUsulan'] = 'usul.'.$date;
      }
		
		if (!isset($data['tglUsulan1Display']) || !isset($data['usulanBrgTglUsulan']))
      {
			$datenow = date('Y-m-d+1');
			$data['usulanBrgTglUsulan'] = strtotime($datenow);
		}
		
		$data['json_detail_barang'] = json_encode((!empty($data['detail_barang'])) ? $data['detail_barang'] : new stdClass);
      unset($data['detail_barang']);
      $return['data'] = $data;
		
   	// buat combo disini
//    	$comboPeriode = $Obj->GetDataPeriode();
// 		Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'periode', array('periode', $comboPeriode, $data['periode'], 'false', ''), Messenger::CurrentRequest);
		
//    	$comboJenisPengadaan = $Obj->GetDataJenisPengadaan();
//    	$return['json_jenis_pengadaan'] = json_encode($comboJenisPengadaan);

//       $comboJenisBarang = $Obj->GetDataJenisBarang();
//       $return['json_jenis_barang'] = json_encode($comboJenisBarang);
   	    	
   	//-- definisikan url
   	$return['url'] = array(
   		'url_rencana_pengadaan' => Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'rencanaPengadaan', 'popup', 'html'),
   		'url_action' => Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'usulanInput', 'do', 'html'),
   		'url_popup_unit' => Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'popUpUnitPj', 'view', 'html'),
   		'url_popup_dhs_barang_jasa' => Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'PopUpKodefikasi', 'view', 'html'),
   		'url_back' => Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'listUsulanBarang', 'view', 'html')
   	);
   	
      return $return;
   }
   
	function ParseTemplate($data = NULL)
	{
		if($this->Pesan){
         $msg = '';
         if (count($this->Pesan) > 1) foreach ($this->Pesan as $value)
            $msg .= "\t$value<br/>\n";
         else $msg .= $this->Pesan[0];
         
			$this->mrTemplate->SetAttribute('warning_box', 'visibility', 'visible');
			$this->mrTemplate->AddVar('warning_box', 'ISI_PESAN', $msg);
			$this->mrTemplate->AddVar('warning_box', 'CLASS_PESAN', $this->css);
		}
	   
	   $this->mrTemplate->AddVars('content', $data['data'], '');

		$this->mrTemplate->AddVars('content', $data['url'], '');
		@$this->mrTemplate->AddVar('content', 'JSON_JENIS_PENGADAAN', $data['json_jenis_pengadaan']);
      @$this->mrTemplate->AddVar('content', 'JSON_JENIS_BARANG', $data['json_jenis_barang']);
	}
}
?>