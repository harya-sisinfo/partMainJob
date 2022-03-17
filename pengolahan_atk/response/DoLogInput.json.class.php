<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').
   'module/'.Dispatcher::Instance()->mModule.'/response/ProcessLogInput.proc.class.php';

class DoLogInput extends JsonResponse {

   function TemplateModule()
   {
   }
   
   function ProcessRequest()
   {
      $Obj = new ProcessLogInput;
      $urlRedirect = $Obj->Update();
      
      return array( 'exec' => 'GtfwAjax.replaceContentWithUrl("subcontent-element","'.$urlRedirect.'&ascomponent=1")');  
   }

   function ParseTemplate($data = NULL)
   {
   }
}
?>