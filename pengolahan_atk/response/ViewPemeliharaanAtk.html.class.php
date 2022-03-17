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

//set Label for module
require_once GTFWConfiguration::GetValue( 'application', 'docroot') .
'module/label/response/Label.proc.class.php';

class ViewPemeliharaanAtk extends HtmlResponse
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
         'module/pengolahan_atk/template');
      $this->SetTemplateFile('pemeliharaan_atk.html');
   }
   
   function ProcessRequest()
   {
   	$obj = new AppPengolahanAtk();
   	
      // inisialisasi messaging
		$msg = Messenger::Instance()->Receive(__FILE__);
      $this->Data = $msg[0][0];
		$this->Pesan = $msg[0][1];
		$this->css = $msg[0][2];
      // ---------
      
   	if(isset($_GET['barcode']))
      {
   		$return['barang'] = $obj->GetAtkByBarcode($_GET['barcode'],$_GET['gudangId']);
         if ($return['barang'] == null)
         {
            $this->Pesan[] = 'Barang tidak ditemukan!';
            $this->css = 'notebox-alert';
         }
         $return['barang']['gudang'] = (string) $_GET['gudangId'];
         $return['barang']['barcode'] = (string) $_GET['barcode'];
      }
      elseif (!empty($this->Data)) $return['barang'] = $this->Data;
   	
   	$unitKerja = $obj->GetUnitKerja();
   	Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'unit', array('unit', $unitKerja, $return['barang']['unit'], false, ''), Messenger::CurrentRequest);

   	$gudang = $obj->GetComboGudang();
   	Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'gudang', array('gudang', $gudang, $return['barang']['gudang'], 'false', 'id="gudang"'), Messenger::CurrentRequest);
   	
   	Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'gudang_tujuan', array('gudang_tujuan', $gudang, $return['barang']['gudang_tujuan'], false, ''), Messenger::CurrentRequest);
   	
   	$status = Array();
   	$status['0']['id'] = 'Rusak';
   	$status['0']['name'] = 'Rusak';
   	$status['1']['id'] = 'Hilang';
   	$status['1']['name'] = 'Hilang';
   	$status['2']['id'] = 'Mutasi Ke Unit Lain';
   	$status['2']['name'] = 'Mutasi ke Unit Lain';
   	$status['3']['id'] = 'Mutasi Ke Gudang Lain';
   	$status['3']['name'] = 'Mutasi ke Gudang Lain';
   	$status['4']['id'] = 'Adjustment Stock';
   	$status['4']['name'] = 'Adjustment Stock';
   	Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'status_pemeliharaan', array('status_pemeliharaan', $status, $_POST['status_pemeliharaan'], false, 'onchange="setGuangUnit(this.form.status_pemeliharaan.value)"'), Messenger::CurrentRequest);
   	
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
      
   	$urlRefresh = Dispatcher::Instance()->GetUrl('pengolahan_atk', 'pemeliharaanAtk', 'view', 'html');
      $this->mrTemplate->addVar('content','URL_REFRESH',$urlRefresh);
      
      $urlTambah = Dispatcher::Instance()->GetUrl('pengolahan_atk', 'addPemeliharaanAtk', 'do', 'html');
      $this->mrTemplate->addVar('content','SUBMIT_URL',$urlTambah);
      
      $urlPopUp = Dispatcher::Instance()->GetUrl('pengolahan_atk', 'kodeBarangAtk', 'popup', 'html');
      $this->mrTemplate->addVar('content','URL_POPUP_KODE_BARANG', $urlPopUp);
      
      $this->mrTemplate->addVar('content','JUDUL','Penambahan BHP Kantor');
      
      if(!empty($data['barang'])){
      	$this->mrTemplate->addVars('content',$data['barang'],'');
      	if($data['barang']['sisa_barang']!='')
      		$this->mrTemplate->addVar('content','JUMLAH_SISA','var sisa='.$data['barang']['sisa_barang'].';');
      	else
      		$this->mrTemplate->addVar('content','JUMLAH_SISA','var sisa=0;');
		}else
      		$this->mrTemplate->addVar('content','JUMLAH_SISA','var sisa=0;');
      $this->TemplateLabeling();
   }
}
?>
