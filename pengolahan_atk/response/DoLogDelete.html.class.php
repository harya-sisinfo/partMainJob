<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').
   'module/'.Dispatcher::Instance()->mModule.'/response/ProcessLogInput.proc.class.php';

class DoLogDelete extends HtmlResponse {

   function TemplateModule()
   {
   }
   
   function ProcessRequest()
   {
      $Obj = new ProcessLogInput;
      $urlRedirect = $Obj->Delete();
      
      $this->RedirectTo($urlRedirect) ;
      return NULL;
   }

   function ParseTemplate($data = NULL)
   {
   }
}
?>