<?php
/*
 @ClassName : View Combo Lokasi Barang
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

class ViewStokMinimalGudang extends HtmlResponse
{

   function TemplateModule()
   {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application','docroot').
         'module/pengolahan_atk/template');
      $this->SetTemplateFile('stok_minimal_gudang.html');
   }
   
   function ProcessRequest()
   {
   	$obj = new AppPengolahanAtk();
   	
   	//get id from get
   	$gudangId = $_GET['gudangId'];
   	//set combo unit kerja
   	$return['stok'] = $obj->GetStockByGudang($gudangId);
   	
      return $return;
   }
   
   
	function ParseTemplate($data = NULL)
   {
      $this->mrTemplate->addVar('content','STOK_MINIMAL',$data['stok']['stok_minimal']);
      $this->mrTemplate->addVar('content','KETERANGAN',$data['stok']['keterangan']);
   }
}
?>
