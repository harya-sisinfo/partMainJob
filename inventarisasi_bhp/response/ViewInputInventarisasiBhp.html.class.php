<?php

require_once GTFWConfiguration::GetValue( 'application', 'docroot').'module/inventarisasi_bhp/business/InventarisasiBhp.class.php';
   
class ViewInputInventarisasiBhp extends HtmlResponse
{
   function TemplateModule()
   {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application','docroot').'module/inventarisasi_bhp/template');
      $this->SetTemplateFile('view_input_inventarisasi_bhp.html');
   }
   
   function ProcessRequest()
   {
   	$Obj = new InventarisasiBhp();
      
		$msg = Messenger::Instance()->Receive(__FILE__);
      $this->Data = $msg[0][0];
		$this->Pesan = $msg[0][1];
		$this->css = $msg[0][2];
      
      // inisialisasi default value
      $data = array();
      if (is_array($this->Data)){
         $data = $this->Data;
      } elseif(isset($_GET['id'])){
         $result = $Obj->GetInventarisasiBhpById($_GET['id']->Integer()->Raw());
			if ($result == false) unset($_GET['id']);
         else $data = $result;
      } #else {$data['pengBrgTgl'] = time();}
      
      $return['data'] = $data;
      return $return;
   }
   
   
	function ParseTemplate($data = NULL)
   {
      if ($this->Pesan) {
			$this->mrTemplate->SetAttribute('warning_box', 'visibility', 'visible');
			$this->mrTemplate->AddVar('warning_box', 'ISI_PESAN', $this->Pesan);
		}
		
		$urlAction = Dispatcher::Instance()->GetUrl('inventarisasi_bhp', 'inputInventarisasiBhp', 'do', 'html');
      $urlBack = Dispatcher::Instance()->GetUrl('inventarisasi_bhp', 'inventarisasiBhp', 'view', 'html');
		$urlPopupBarang = Dispatcher::Instance()->GetUrl('inventarisasi_bhp', 'kodeBarang', 'popup', 'html');
      
		
		$this->mrTemplate->addVar('content','URL_BACK',$urlBack);
      
		if(isset($_GET['id'])){
			$this->mrTemplate->addVar('content','TITLE','Ubah');
			$urlAction = $urlAction.'&id='.$_GET['id'];
		} else {
			$this->mrTemplate->addVar('content','URL_POP_UP_KD_BARANG',$urlPopupBarang);
			$this->mrTemplate->addVar('content','TITLE','Tambah');
		}
		
		$this->mrTemplate->addVar('content','URL_ACTION',$urlAction);
		
		if($data['data']){
         if($data['data'][0]['stat'] == 'Y') $data['data'][0]['checked'] = 'checked="checked"';
			$this->mrTemplate->addVars('content',$data['data'][0],'');
		}
		
   }
}
?>
