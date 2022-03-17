<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot') . 'module/pengelolaan_bhp/response/ProcessPengelolaan.proc.class.php';

class DoDeletePengelolaan extends JsonResponse {

	function TemplateModule() {}

	function ProcessRequest() {
		$Obj = new ProcessPengelolaan();
		$urlRedirect = $Obj->Delete();
		return array( 'exec' => 'GtfwAjax.replaceContentWithUrl("subcontent-element","'.$urlRedirect.'&ascomponent=1")');
	}

	function ParseTemplate($data = NULL) {}
}
?>
