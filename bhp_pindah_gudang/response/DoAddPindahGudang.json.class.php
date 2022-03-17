<?php
/**
* @package DoAddPindahGudang
* @copyright Copyright (c) PT Gamatechno Indonesia
* @author Didi Zuliansyah <didi@gamatechno.com>
* @version 0.1
* @startDate 2013-01-01
* @lastUpdate 2013-01-01
* @description Class DoAddPindahGudang
*/

require_once Configuration::Instance()->GetValue( 'application', 'docroot') . 'module/'.Dispatcher::Instance()->mModule.'/response/ProcBhpPindahGudang.proc.class.php';

class DoAddPindahGudang extends JsonResponse {

	function TemplateModule() {
	}

	function ProcessRequest() {
		$obj = new ProcBhpPindahGudang();
		$return = $obj->AddBhpPindahGudang();
		$showMsg = 'alert("'.$return['msg'].'")';
		if ($return['status'] == false){
			return array( 'exec' => $showMsg);
		}else{
			return array( 'exec' => 'GtfwAjax.replaceContentWithUrl("subcontent-element","'.$return['url'].'&ascomponent=1");');
		}
	}

	function ParseTemplate($data = NULL) {
	}
}
?>