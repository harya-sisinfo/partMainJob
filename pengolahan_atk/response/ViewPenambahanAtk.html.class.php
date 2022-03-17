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

require_once GTFWConfiguration::GetValue( 'application', 'docroot') .   'module/pengolahan_atk/business/AppPengolahanAtk.class.php';

require_once GTFWConfiguration::GetValue( 'application', 'docroot') . 'module/setting/business/Setting.class.php';

//set Label for module
require_once GTFWConfiguration::GetValue( 'application', 'docroot') .
'module/label/response/Label.proc.class.php';

class ViewPenambahanAtk extends HtmlResponse {

   function TemplateModule()
   {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application','docroot').'module/pengolahan_atk/template'
		);
      $this->SetTemplateFile('penambahan_atk.html');
   }
		
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

   function ProcessRequest()
   {
   	$obj = new AppPengolahanAtk();
      $Obj2 = new Setting();
      $GET = $_GET->AsArray();
      $return['srch'] = $GET['srch'];
      //set combo kib
      $arrSetting = $Obj2->GetSetting();
		
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
      } elseif (!empty($this->Data)) {
         $return['barang'] = $this->Data;
      } else {
         $return['barang']['gudang'] = $arrSetting['1']['settingValue'];
      }

   	//$gudang = $obj->GetComboGudang(true);
   	//Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'gudang', array('gudang', $gudang, $return['barang']['gudang'], '', 'id="gudang"'), Messenger::CurrentRequest);
		
		$satuanBarang = $obj->GetSatuan();
      Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'satuan_barang', array('satuan_barang', $satuanBarang, $return['satuan_barang'], false, ''), Messenger::CurrentRequest);

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
      $this->mrTemplate->addVar('content','URL_REFRESH',$urlRefresh.'&srch='.$data['srch']);

      $urlTambah = Dispatcher::Instance()->GetUrl('pengolahan_atk', 'addPenambahanAtk', 'do', 'json');
      $this->mrTemplate->addVar('content','SUBMIT_URL',$urlTambah.'&srch='.$data['srch']);

      $urlPopUp = Dispatcher::Instance()->GetUrl('pengolahan_atk', 'kodeBarang', 'popup', 'html').'&cari=all||||||||all';
      $this->mrTemplate->addVar('content','URL_POPUP_KODE_BARANG', $urlPopUp);
      $urlPopUp2 = Dispatcher::Instance()->GetUrl('pengolahan_atk', 'PopupUsulanBhp', 'view', 'html');
      $this->mrTemplate->addVar('content','URL_POPUP_USULAN_BHP', $urlPopUp2);
      $urlPopup3 = Dispatcher::Instance()->GetUrl('popup', 'Ruang', 'popup', 'html').'&rtype=-1&bhp=2';
      $this->mrTemplate->addVar('content','URL_POPUP_RUANG',$urlPopup3);

      $this->mrTemplate->addVar('content','JUDUL','Penambahan BHP Kantor');

      if(!empty($data['barang'])){
      	$this->mrTemplate->addVars('content',$data['barang'],'');
      	if($data['barang']['sisa_barang']!='')
      		$this->mrTemplate->addVar('content','JUMLAH_SISA','var sisa='.$data['barang']['sisa_barang'].';');
      	else
      		$this->mrTemplate->addVar('content','JUMLAH_SISA','var sisa=0;');
		}else
      		$this->mrTemplate->addVar('content','JUMLAH_SISA','var sisa=0;');

      $this->mrTemplate->AddVar('content', 'JUMLAH_BARANG', '1');
      $this->TemplateLabeling();
   }
}
?>
