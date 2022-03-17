<?php
/**
* @package : ViewInputBhpPindahGudang
* @copyright : Copyright (c) PT Gamatechno Indonesia
* @Analyzed By : Dyan Galih
* @author : Didi Zuliansyah
* @version : 01
* @startDate : 2013-01-01
* @lastUpdate : 2013-01-01
* @description : Class untuk input BHP Pindah Gudang
*/


class ViewInputBhpPindahGudang extends HtmlResponse {

	function TemplateModule() {
		$this->SetTemplateBasedir(GTFWConfiguration::GetValue( 'application', 'docroot').'module/'.Dispatcher::Instance()->mModule.'/template');
		$this->SetTemplateFile('view_input_bhp_pindah_gudang.html');
	}

	function ProcessRequest() {
		
		# init combo
		Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'gudangasal',array('gudangasal', @$gudangAsal, @$gudangAsalSelect, 'false', 'id="gudangasal"'),Messenger::CurrentRequest);
		Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'gudangtujuan',array('gudangtujuan', @$gudangTujuan, @$gudangTujuanSelect, 'false', 'id="gudangtujuan"'),Messenger::CurrentRequest);
		$tgl = date('Y-m-d');
		Messenger::Instance()->SendToComponent('tanggal', 'Tanggal', 'view', 'html', 'tglpindah', array($tgl), Messenger::CurrentRequest);
		
		# init url
		$return['url']['popup_unitasal'] = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'PopupUnitKerja', 'view', 'html').'&ukgroup=1';
		$return['url']['popup_unittujuan'] = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'PopupUnitKerja', 'view', 'html').'&ukgroup=2';
		//$return['url']['popup_usulan'] = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'PopupUsulanBhp', 'view', 'html');
		$return['url']['popup_barang'] = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'KodeBarang', 'popup', 'html');
		$return['url']['action'] = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'AddPindahGudang', 'do', 'json');
		$return['url']['back'] = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'BhpPindahGudang', 'view', 'html');
		
		return $return;
	}

	function ParseTemplate($data = NULL) {
		$this->mrTemplate->AddVars('content', $data['url'], 'URL_');
	}
}
?>