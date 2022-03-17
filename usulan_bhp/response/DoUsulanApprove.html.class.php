<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').'module/'.Dispatcher::Instance()->mModule.'/response/ProcessUsulanBarang.proc.class.php';

class DoUsulanApprove extends HtmlResponse {

   function TemplateModule()
   {
   }
   
   function ProcessRequest()
   {
      $Obj = new ProcessUsulanBarang;

		$urlRedirect = $Obj->Approve();
      
      $this->RedirectTo($urlRedirect) ;
      return NULL;
   }

   function ParseTemplate($data = NULL)
   {
   }
}
?>
