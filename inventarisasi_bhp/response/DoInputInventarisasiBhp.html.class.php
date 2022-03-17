<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').'module/'.Dispatcher::Instance()->mModule.'/response/ProcessInventarisasiBhp.proc.class.php';

class DoInputInventarisasiBhp extends HtmlResponse {

   function TemplateModule()
   {
   }
   
   function ProcessRequest()
   {
      $Obj = new ProcessInventarisasiBhp();
      if ($_GET['id']!="")
         $urlRedirect = $Obj->Update();
		 else $urlRedirect = $Obj->Add();
		 
      $this->RedirectTo($urlRedirect);
      return NULL;
   }

   function ParseTemplate($data = NULL)
   {
   }
}
?>