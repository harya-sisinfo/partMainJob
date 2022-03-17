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

class ViewPengolahanAtkEdit extends HtmlResponse
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
      $this->SetTemplateFile('edit_pengolahan_atk.html');
   }
   
   function ProcessRequest()
   {
   	$obj = new AppPengolahanAtk();
      $GET = $_GET->AsArray();
   	$editId = $GET['idEdit'];
		$barang = $GET['barangId'];
		$ruang = $GET['ruangId'];
      $return['srch'] = $GET['srch'];
   	$return['data'] = $obj->GetAtkById($editId);
   	$return['data']['id'] = $_GET['idEdit']->StripHtmlTags()->SqlString()->Raw(); ;
   	//set combo gudang
		if(empty($return['data']['id'])){
			$return['data2'] = $obj->GetKodeBarangById($barang, $ruang);
			$return['data2']['stok_minimal'] = 0;
      }
		
		return $return;
   }
   
   
	function ParseTemplate($data = NULL)
   {   
		if(isset($data['data2'])){
			$this->mrTemplate->AddVars('content',$data['data2'],'UPDATE_');
		} else {
			$this->mrTemplate->AddVars('content',$data['data'],'UPDATE_');
		}

		//$urlPopUp = Dispatcher::Instance()->GetUrl('popup', 'kodeBarang', 'popup', 'html').'&cari=5||||||||2';
      //$this->mrTemplate->addVar('content','URL_POP_UP_KD_BARANG',$urlPopUp);
      $urlTambah = Dispatcher::Instance()->GetUrl('pengolahan_atk', 'updatePengolahanAtk', 'do', 'html');
      $this->mrTemplate->addVar('content','URL_ACTION', $urlTambah.'&srch='.$data['srch']);
       
      $this->mrTemplate->addVar('content','JUDUL','Ubah');
      
      $this->TemplateLabeling();
   }
}
?>
