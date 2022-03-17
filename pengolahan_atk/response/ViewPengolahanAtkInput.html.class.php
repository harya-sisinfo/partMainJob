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

require_once GTFWConfiguration::GetValue( 'application', 'docroot') . 'module/setting/business/Setting.class.php';
   
//set Label for module
require_once GTFWConfiguration::GetValue( 'application', 'docroot') .
'module/label/response/Label.proc.class.php';

class ViewPengolahanAtkInput extends HtmlResponse
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
      $this->SetTemplateFile('input_pengolahan_atk.html');
   }
   
   function ProcessRequest()
   {
   	$obj = new AppPengolahanAtk();
      $Obj2 = new Setting();
      //set combo kib
      $arrSetting = $Obj2->GetSetting();
		
      if (count($_POST) == 0)
      {
         $_POST = array();
         $_POST['gudang'] = $arrSetting['1']['settingValue'];
      }
      
      
   	//set combo gudang
   	$gudang = $obj->GetComboGudang(true);
   	Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'gudang', array('gudang', $gudang, $_POST['gudang'], 'false', ''), Messenger::CurrentRequest);
   	
   	
		
      return $return;
   }
   
   
	function ParseTemplate($data = NULL)
   {
   	$urlRefresh = Dispatcher::Instance()->GetUrl('pengolahan_atk', 'PengolahanAtkInput', 'view', 'html');
      $this->mrTemplate->addVar('content','URL_REFRESH',$urlRefresh);
      
      $urlTambah = Dispatcher::Instance()->GetUrl('pengolahan_atk', 'AddPengolahanAtk', 'do', 'html');
      $this->mrTemplate->addVar('content','URL_ACTION',$urlTambah);
      
      $urlPopUp = Dispatcher::Instance()->GetUrl('pengolahan_atk', 'kodeBarang', 'popup', 'html').'&cari=all||||||||all';
      $this->mrTemplate->addVar('content','URL_POP_UP_KD_BARANG',$urlPopUp);
      
      $this->mrTemplate->addVar('content','JUDUL','Tambah');
      
      $this->TemplateLabeling();
   }
}
?>
