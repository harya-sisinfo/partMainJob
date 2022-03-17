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

class DoUpdatePengolahanAtk extends JsonResponse
{

   function TemplateModule()
   {
      
   }
   
   function ProcessRequest()
   {
      $GET = $_GET->AsArray();
   	$obj = new ProcPengolahanAtk();
   	if ($_POST['id_atk']!="") $urlRedirect = $obj->editPengolahanAtk();
		else $urlRedirect = $obj->addPengolahanAtk();
		
      return array( 'exec' => 'GtfwAjax.replaceContentWithUrl("subcontent-element","'.$urlRedirect.'&srch='.$GET['srch'].'&ascomponent=1")');
   }
   
	function ParseTemplate($data = NULL)
   {
      
   }
}
?>
