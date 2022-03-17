<?php
/*
 @ClassName : Do Mutasi Barang Json
 @Copyright : PT Gamatechno Indonesia
 @Analyzed By : Nanang Ruswianto <nanang@gamatechno.com>
 @Designed By : Rosyid <rosyid@gamatechno.com>
 @Author By : Dyan Galih <galih@gamatechno.com>
 @Version : 1.0
 @StartDate : Okt 16, 2008
 @LastUpdate : Okt 17, 2008
 @Description :
*/


require_once GTFWConfiguration::GetValue( 'application', 'docroot').
   'module/pengolahan_atk/response/PengolahanAtk.proc.php';

class DoAddPengolahanAtk extends JsonResponse
{

   function TemplateModule()
   {
      
   }
   
   function ProcessRequest()
   {
   	$obj = new ProcPengolahanAtk();
   	
   	$urlRedirect = $obj->addPengolahanAtk('json');
   	
      return array( 'exec' => 'GtfwAjax.replaceContentWithUrl("subcontent-element","'.$urlRedirect.'&ascomponent=1")');
   }
   
   
	function ParseTemplate($data = NULL)
   {
      
   }
}
?>
