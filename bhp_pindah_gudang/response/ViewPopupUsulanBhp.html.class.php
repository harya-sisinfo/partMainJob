<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').'module/'.Dispatcher::Instance()->mModule.'/business/BhpPindahGudang.class.php';

class ViewPopupUsulanBhp extends HtmlResponse {
   
   function TemplateModule()
   {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application','docroot').
         'module/'.Dispatcher::Instance()->mModule.'/template');
      $this->SetTemplateFile('view_popup_usulan_bhp.html');
   }
	
	function ProcessRequest() {
		$obj = new BhpPindahGudang();
		
		if($_POST || isset($_GET['cari'])) {
			if($_POST) {
				$noUsulanBhp = $_POST['nousulanbhp'];
				$unitKerja = $_POST['unitid'];
				$namaBarang = $_POST['namabarang'];
			} elseif(isset($_GET['cari'])) {
				$noUsulanBhp = Dispatcher::Instance()->Decrypt($_GET['nousulanbhp']);
				$unitKerja = Dispatcher::Instance()->Decrypt($_GET['unitid']);
				$namaBarang = Dispatcher::Instance()->Decrypt($_GET['namabarang']);
			} else {
				$noUsulanBhp = '';$unitKerja = ''; $namaBarang = '';
			}
		}

	//view
		$itemViewed = 20;
		$currPage = 1;
		$startRec = 0 ;
		if(isset($_GET['page'])) {
			$currPage = (string)$_GET['page']->StripHtmlTags()->SqlString()->Raw();
			$startRec =($currPage-1) * $itemViewed;
		}
		
		$dataUsulan = $obj->GetDataUsulanBhp($startRec, $itemViewed, $unitKerja, $noUsulanBhp, $namaBarang);
		$totalData = $obj->GetCount();
		$url = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, Dispatcher::Instance()->mSubModule, Dispatcher::Instance()->mAction, Dispatcher::Instance()->mType . '&unitid=' . Dispatcher::Instance()->Encrypt($unitKerja) . '&nousulanbhp=' . Dispatcher::Instance()->Encrypt($noUsulanBhp) . '&namabarang=' . Dispatcher::Instance()->Encrypt($namaBarang) . '&cari=' . Dispatcher::Instance()->Encrypt(1));
		$dest = "popup-subcontent";
		Messenger::Instance()->SendToComponent('paging', 'Paging', 'view', 'html', 'paging_top', array($itemViewed,$totalData, $url, $currPage, $dest), Messenger::CurrentRequest);

		$return['data'] = $dataUsulan;
		$return['start'] = $startRec+1;

		$return['search']['nousulanbhp'] = $noUsulanBhp;
		$return['search']['namabarang'] = $namaBarang;
		$comboUnit = $obj->GetComboUnitKerja();
		Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'unitid',array('unitid', $comboUnit, $unitKerja, 'true', ''),Messenger::CurrentRequest);
		
		return $return;
	}
	
	function ParseTemplate($data = NULL)
   { 
		$search = $data['search'];
		$this->mrTemplate->AddVar('content', 'NOUSULANBHP', $search['nousulanbhp']);
		$this->mrTemplate->AddVar('content', 'NAMABARANG', $search['namabarang']);

		$this->mrTemplate->AddVar('content', 'URL_SEARCH', Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, Dispatcher::Instance()->mSubModule, Dispatcher::Instance()->mAction, Dispatcher::Instance()->mType));
		
		if (empty($data['data'])) {
			$this->mrTemplate->AddVar('data_list', 'DATA_EMPTY', 'YES');
		} else {
			$this->mrTemplate->AddVar('data_list', 'UNITKERJA_EMPTY', 'NO');
			$dataUsulan = $data['data'];

         	$x=0; $no=1;
         	$unitkerjaId='';($i % 2) ? '' : 'table-common-even';
         	$nomor_satuankerja=1;
			for ($i=0; $i<sizeof($dataUsulan); $i++) {
				$no = $i+$data['start'];
				$dataUsulan[$i]['number'] = $no;
				$dataUsulan[$i]['class_name'] = ($i % 2) ? '' : 'table-common-even';
				$this->mrTemplate->AddVars('data_item', $dataUsulan[$i], 'USULAN_');
				$this->mrTemplate->parseTemplate('data_item', 'a');	 
			}
		}
	}
}
?>
