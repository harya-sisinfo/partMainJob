<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').'module/'.Dispatcher::Instance()->mModule.'/response/ProcessUsulanBarang.proc.class.php';

class DoUsulanUpdate extends HtmlResponse {

   function TemplateModule()
   {
   }
   
   function ProcessRequest()
   {
      $Obj = new ProcessUsulanBarang;

		$urlRedirect = $Obj->Update();
      
      $this->RedirectTo($urlRedirect) ;
      return NULL;
   }

   function ParseTemplate($data = NULL)
   {
   }
}
?>
