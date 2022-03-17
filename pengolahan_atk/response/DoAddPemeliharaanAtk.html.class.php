<?php
/*
 @ClassName : Do Mutasi Barang html
 @Copyright : PT Gamatechno Indonesia
 @Analyzed By : Nanang Ruswianto <nanang@gamatechno.com>
 @Designed By : Rosyid <rosyid@gamatechno.com>
 @Author By : Dyan Galih <galih@gamatechno.com>
 @Version : 1.0
 @StartDate : Nov 3, 2008
 @LastUpdate : Nov 6, 2008
 @Description :
*/


require_once GTFWConfiguration::GetValue( 'application', 'docroot').
   'module/pengolahan_atk/response/PemeliharaanAtk.proc.php';

class DoAddPemeliharaanAtk extends HtmlResponse
{

   function TemplateModule()
   {
      
   }
   
   function ProcessRequest()
   {
   	$obj = new ProcPemeliharaanAtk();
   	
   	$urlRedirect = $obj->addPemeliharaanAtk();
   	$this->RedirectTo($urlRedirect) ; 
      return NULL;
   }
   
   
	function ParseTemplate($data = NULL)
   {
      
   }
}
?>
