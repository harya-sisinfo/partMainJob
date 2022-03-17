<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').'module/'.Dispatcher::Instance()->mModule.'/response/ProcessUsulanBarang.proc.class.php';

class DoUsulanInput extends JsonResponse {

   function TemplateModule()
   {
   }
   
   function ProcessRequest()
   {
      $Obj = new ProcessUsulanBarang;

		$urlRedirect = $Obj->Add();
      
		return array( 'exec' => 'GtfwAjax.replaceContentWithUrl("subcontent-element","'.$urlRedirect.'&ascomponent=1")');  
   }

   function ParseTemplate($data = NULL)
   {
   }
}
?>
