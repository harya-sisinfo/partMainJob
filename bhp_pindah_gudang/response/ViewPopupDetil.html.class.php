<?php
/**
* @package : ViewDetil
* @copyright : Copyright (c) PT Gamatechno Indonesia
* @Analyzed By : Dyan Galih
* @author : Didi Zuliansyah
* @version : 01
* @startDate : 2013-01-01
* @lastUpdate : 2013-01-01
* @description : Class untuk input BHP Pindah Gudang
*/

require_once GTFWConfiguration::GetValue( 'application', 'docroot').'module/'.Dispatcher::Instance()->mModule.'/business/BhpPindahGudang.class.php';

class ViewPopupDetil extends HtmlResponse {

	function TemplateModule() {
		$this->SetTemplateBasedir(GTFWConfiguration::GetValue( 'application', 'docroot').'module/'.Dispatcher::Instance()->mModule.'/template');
		$this->SetTemplateFile('view_popup_detil.html');
	}

	function ProcessRequest() {
		$obj = new BhpPindahGudang();
		$GET = $_GET->AsArray();
		if(!empty($GET['id']))
			$return['data'] = $obj->GetDetilById($GET['id']);

		//$return['data']['url_back'] = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'BhpPindahGudang', 'view', 'html');
		
		return $return;
	}

	function ParseTemplate($data = NULL) {
		if(!empty($data['data'])){
			$this->mrTemplate->AddVar('data', 'DATA_EMPTY', 'NO');
			$this->mrTemplate->AddVars('content', $data['data'][0], '');
			foreach($data['data'] as $i=>$val){
            $val['no'] = $i+1; 
            $this->mrTemplate->AddVars('data_item', $val, '');
            $this->mrTemplate->parseTemplate("data_item", "a");
         }
		}else $this->mrTemplate->AddVar('data', 'DATA_EMPTY', 'YES');
	}
}
?>