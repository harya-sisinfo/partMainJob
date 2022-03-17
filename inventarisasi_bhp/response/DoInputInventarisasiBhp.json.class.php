<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').'module/'.Dispatcher::Instance()->mModule.'/response/ProcessInventarisasiBhp.proc.class.php';

class DoInputInventarisasiBhp extends JsonResponse {

   function TemplateModule()
   {
   }
   
   function ProcessRequest()
   {
      $Obj = new ProcessInventarisasiBhp();
		if ($_GET['id']!="")
         $urlRedirect = $Obj->Update();
		 else $urlRedirect = $Obj->Add();
		 
      return array('exec' => 'GtfwAjax.replaceContentWithUrl("subcontent-element","'.$urlRedirect.'&ascomponent=1")');  
   }

   function ParseTemplate($data = NULL)
   {
   }
}
?>