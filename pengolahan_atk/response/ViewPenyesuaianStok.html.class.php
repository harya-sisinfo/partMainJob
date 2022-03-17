<?php
/*
 @ClassName : View Input Mutasi Barang
 @Copyright : PT Gamatechno Indonesia
 @Analyzed By : Nanang Ruswianto <nanang@gamatechno.com>
 @Designed By : Rosyid <rosyid@gamatechno.com>
 @Author By : Dyan Galih <galih@gamatechno.com>
 @Version : 1.0
 @StartDate : Okt 20, 2008
 @LastUpdate : Okt 20, 2008
 @Description :
*/

require_once GTFWConfiguration::GetValue( 'application', 'docroot').
   'module/pengolahan_atk/business/AppPengolahanAtk.class.php';

class ViewPenyesuaianStok extends HtmlResponse
{	
   function TemplateModule()
   {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application','docroot').
         'module/pengolahan_atk/template');
      $this->SetTemplateFile('penyesuaian_stok.html');
   }
   
   function ProcessRequest()
   {
   	$obj = new AppPengolahanAtk();
      $GET = $_GET->AsArray();
   	
      // inisialisasi messaging
		$msg = Messenger::Instance()->Receive(__FILE__);
      $this->Data = $msg[0][0];
		$this->Pesan = $msg[0][1];
		$this->css = $msg[0][2];
      // ---------
      
		$barangId = $GET['barangId'];
		$ruangId = $GET['ruangId'];
      $return['srch'] = $GET['srch'];
		$return['barang'] = $obj->GetKodeBarangById($barangId, $ruangId);
   	if(!empty($this->Data)) $return['barang'] = $this->Data;
   	  	
   	Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'gudang_tujuan', array('gudang_tujuan', $gudang, $return['barang']['gudang_tujuan'], false, ''), Messenger::CurrentRequest);
   	
   	//$status = Array();
   	//$status['0']['id'] = 'Adjustment Stock';
   	//$status['0']['name'] = 'Adjustment Stock';
   	//Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'status_pemeliharaan', array('status_pemeliharaan', $status, $_POST['status_pemeliharaan'], false, 'onchange="setGuangUnit(this.form.status_pemeliharaan.value)"'), Messenger::CurrentRequest);
   	$return['barang']['status_pemeliharaan'] = 'Adjustment Stok';
      return $return;
   }
   
   
	function ParseTemplate($data = NULL)
   {
      // render message box
      if($this->Pesan)
      {
         $msg = '';
         if (count($this->Pesan) > 1) foreach ($this->Pesan as $value)
            $msg .= "\t$value<br/>\n";
         else $msg .= $this->Pesan[0];
         
			$this->mrTemplate->SetAttribute('warning_box', 'visibility', 'visible');
			$this->mrTemplate->AddVar('warning_box', 'ISI_PESAN', $msg);
			$this->mrTemplate->AddVar('warning_box', 'CLASS_PESAN', $this->css);
		}
      // ---------
      
   	$urlRefresh = Dispatcher::Instance()->GetUrl('pengolahan_atk', 'penyesuaianStok', 'view', 'html');
      $this->mrTemplate->addVar('content','URL_REFRESH',$urlRefresh.'&srch='.$data['srch']);
      
      $urlTambah = Dispatcher::Instance()->GetUrl('pengolahan_atk', 'updatePenambahanAtk', 'do', 'json');
      $this->mrTemplate->addVar('content','SUBMIT_URL',$urlTambah.'&srch='.$data['srch']);
      
		$data['barang']['sisa_barang'] = (int)$data['barang']['sisa_barang'];
		$data['barang']['sisa_barang_lama'] = (int)$data['barang']['sisa_barang'];
		$data['barang']['invAtkBiayaNominala'] = number_format($data['barang']['invAtkBiayaNominal'], 2, ',', '.');
		
      if(!empty($data['barang'])){
      	$this->mrTemplate->addVars('content',$data['barang'],'');
      	if($data['barang']['sisa_barang']!='')
      		$this->mrTemplate->addVar('content','JUMLAH_SISA','var sisa='.$data['barang']['sisa_barang'].';');
      	else
      		$this->mrTemplate->addVar('content','JUMLAH_SISA','var sisa=0;');
		}else
      		$this->mrTemplate->addVar('content','JUMLAH_SISA','var sisa=0;');
   }
}
?>
