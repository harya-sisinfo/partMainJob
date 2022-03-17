<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot') . 'module/pengelolaan_bhp/response/ProcessPengelolaan.proc.class.php';

class DoAddPengelolaan extends JsonResponse {

	function TemplateModule() {
	}
	
	function ProcessRequest() {
		$Obj = new ProcessPengelolaan();
		
		if ($_GET['id']!="") $return = $Obj->Update();
		else $return = $Obj->Add();
		
		if(!$return['status']) return array( 'exec' => 'alert("'.$return['value'].'");');		
		else return array( 'exec' => 'GtfwAjax.replaceContentWithUrl("subcontent-element","'.$return['value'].'&ascomponent=1")');
	 }

	function ParseTemplate($data = NULL) {
	}
}
?>
