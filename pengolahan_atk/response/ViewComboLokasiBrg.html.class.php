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
   'module/mutasi_barang/business/AppMutasi.class.php';

class ViewComboLokasiBrg extends HtmlResponse
{

   function TemplateModule()
   {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application','docroot').
         'module/mutasi_barang/template');
      $this->SetTemplateFile('combo_lokasi_barang.html');
   }
   
   function ProcessRequest()
   {
   	$obj = new AppMutasiBarang();
   	
   	//get id from get
   	$idUnit = $_GET['idUnit'];
   	//set combo unit kerja
   	$lokasiBarang = $obj->getLokasiBarang($idUnit);
   	Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'lokasi_barang', array('lokasi_barang', $lokasiBarang, $_POST['lokasi_barang'], 'false', ''), Messenger::CurrentRequest);
   	
      return $return;
   }
   
   
	function ParseTemplate($data = NULL)
   {
      
   }
}
?>
