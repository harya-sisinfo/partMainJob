<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot') . 'module/pengelolaan_bhp/response/ProcessPengelolaan.proc.class.php';

class DoAddPengelolaan extends HtmlResponse {

	function TemplateModule() {
	}
	
	function ProcessRequest() {
		$Obj = new ProcessPengelolaan();
		
		if ($_GET['id']!="")
         $urlRedirect = $Obj->Update();
		 else $urlRedirect = $Obj->Add();
		 
		$this->RedirectTo($urlRedirect);
		return NULL;
	 }
	function ParseTemplate($data = NULL) {	
	}
}
?>
