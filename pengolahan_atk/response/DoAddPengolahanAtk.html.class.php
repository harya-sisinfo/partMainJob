<?php
/*
 @ClassName : Do Mutasi Barang html
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
   'module/pengolahan_atk/response/PengolahanAtk.proc.php';

class DoAddPengolahanAtk extends HtmlResponse
{

   function TemplateModule()
   {
      
   }
   
   function ProcessRequest()
   {
   	$obj = new ProcPengolahanAtk();
   	
   	$urlRedirect = $obj->addPengolahanAtk();
   	$this->RedirectTo($urlRedirect) ; 
      return NULL;
   }
   
   
	function ParseTemplate($data = NULL)
   {
      
   }
}
?>
