<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot') . 'module/pengelolaan_bhp/response/ProcessPengelolaan.proc.class.php';

class DoDeletePengelolaan extends HtmlResponse {

	function TemplateModule() {}
	
	function ProcessRequest() {
		$Obj = new ProcessPengelolaan();
		$urlRedirect = $Obj->Delete();
		$this->RedirectTo($urlRedirect);
		return NULL;
	}

	function ParseTemplate($data = NULL) {}
}
?>
