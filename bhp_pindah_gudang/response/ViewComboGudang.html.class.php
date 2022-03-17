<?php

require_once GTFWConfiguration::GetValue( 'application', 'docroot').'module/'.Dispatcher::Instance()->mModule.'/business/BhpPindahGudang.class.php';

class ViewComboGudang extends HtmlResponse{

   	function TemplateModule(){
      	$this->SetTemplateBasedir(GTFWConfiguration::GetValue('application','docroot').'module/'.Dispatcher::Instance()->mModule.'/template');
      	$this->SetTemplateFile('combo_gudang.html');
   	}
   
	function ProcessRequest(){
		$obj = new BhpPindahGudang();
   	
	   	$idUnit = $_GET['idUnit'];
	   	$type = $_GET['cbtype'];
         $exclude = $_GET['exc'];
	   	$comboGudang = $obj->GetComboGudang($idUnit,$exclude);
	   	if($type == '2') Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'gudangtujuan', array('gudangtujuan', $comboGudang, @$gudangTujuan, 'false', 'id="gudangtujuan"'), Messenger::CurrentRequest);
   		else Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'gudangasal', array('gudangasal', $comboGudang, @$gudangAsal, 'false', 'id="gudangasal" onChange="removeAllRows();"'), Messenger::CurrentRequest);
      	$return['type'] = $type;
   		
   		return $return;
   	}
   
   
	function ParseTemplate($data = NULL){
		if($data['type'] == '2') $this->mrTemplate->AddVar('combo_jenis', 'COMBO_TUJUAN', 'YES');
		else $this->mrTemplate->AddVar('combo_jenis', 'COMBO_TUJUAN', 'NO');
   	}
}
?>
