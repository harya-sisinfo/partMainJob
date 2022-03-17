<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').'module/'.Dispatcher::Instance()->mModule.'/business/popupUnitPj.class.php';

class ViewPopupUnitPj extends HtmlResponse {

	function TemplateBase()
   {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application', 'docroot') . 'main/template/');
      $this->SetTemplateFile('document-common-popup.html');
      $this->SetTemplateFile('layout-common-popup.html');
   }
   
   function TemplateModule()
   {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application','docroot').'module/'.Dispatcher::Instance()->mModule.'/template');
      $this->SetTemplateFile('pop_up_unitpj.html');
   }
	
	function ProcessRequest() {
		$unitkerjaObj = new popupUnitPj();
		
		if($_POST || isset($_GET['cari'])) {
					
			if(isset($_POST['kode'])) {
				$kode = $_POST['kode'];
			} elseif(isset($_GET['kode'])) {
				$kode = Dispatcher::Instance()->Decrypt($_GET['kode']);
			} else {
				$kode = '';
			}
		  
			if(isset($_POST['nama'])) {
				$unitkerja = $_POST['nama'];
			} elseif(isset($_GET['nama'])) {
				$unitkerja = Dispatcher::Instance()->Decrypt($_GET['nama']);
			} else {
				$unitkerja = '';
			}

		}
		
	//view
		//$totalData = $unitkerjaObj->GetCountDataUnitkerja($unitkerja, $kode);
		$itemViewed = 20;
		$currPage = 1;
		$startRec = 0 ;
		if(isset($_GET['page'])) {
			$currPage = (string)$_GET['page']->StripHtmlTags()->SqlString()->Raw();
			$startRec =($currPage-1) * $itemViewed;
		}
		
		$dataUnitkerja = $unitkerjaObj->getDataUnitkerja($startRec, $itemViewed, $unitkerja, $kode);
		$totalData = $unitkerjaObj->GetCount();
		
		$url = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, Dispatcher::Instance()->mSubModule, Dispatcher::Instance()->mAction, Dispatcher::Instance()->mType . '&kode=' . Dispatcher::Instance()->Encrypt($kode) . '&nama=' . Dispatcher::Instance()->Encrypt($unitkerja) . '&cari=' . Dispatcher::Instance()->Encrypt(1));
		$dest = "popup-subcontent";
		Messenger::Instance()->SendToComponent('paging', 'Paging', 'view', 'html', 'paging_top', array($itemViewed,$totalData, $url, $currPage, $dest), Messenger::CurrentRequest);

		$return['dataUnitkerja'] = $dataUnitkerja;
		$return['start'] = $startRec+1;

		$return['search']['satker'] = @$satker;
		$return['search']['kode'] = $kode;
		$return['search']['unitkerja'] = $unitkerja;
		$return['search']['tipeunit'] = @$tipeunit;
		return $return;
	}
	   
	function filter($string){
		return addslashes($string);
	}
	
	function ParseTemplate($data = NULL)
   {
		$search = $data['search'];
		$this->mrTemplate->AddVar('content', 'SATKER', $search['satker']);
		$this->mrTemplate->AddVar('content', 'KODE', $search['kode']);
		$this->mrTemplate->AddVar('content', 'NAMA', $search['unitkerja']);

		$this->mrTemplate->AddVar('content', 'URL_SEARCH', Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'popUpUnitPj', 'view', 'html'));
		
		if (empty($data['dataUnitkerja'])) {
			$this->mrTemplate->AddVar('data_unitkerja', 'UNITKERJA_EMPTY', 'YES');
		} else {
			$decPage = Dispatcher::Instance()->Decrypt($_REQUEST['page']);
			$encPage = Dispatcher::Instance()->Encrypt($decPage);
			$this->mrTemplate->AddVar('data_unitkerja', 'UNITKERJA_EMPTY', 'NO');
			$dataUnitkerja = $data['dataUnitkerja'];

         $x=0; $no=1;
         $unitkerjaId='';
         $nomor_satuankerja=1;
			for ($i=0; $i<sizeof($dataUnitkerja); $i++) {
				$no = $i+$data['start'];
				$dataUnitkerja[$i]['number'] = $no;
				if((int)$dataUnitkerja[$i]['isParent'] > 0)$dataUnitkerja[$i]['class_name'] = 'table-common-even';
   			else $dataUnitkerja[$i]['class_name'] = '';
				
				$dataUnitkerja[$i]['unit'] = $this->filter($dataUnitkerja[$i]['unit']);
				
				$this->mrTemplate->AddVars('data_unitkerja_item', $dataUnitkerja[$i], 'UNITKERJA_');
				$this->mrTemplate->parseTemplate('data_unitkerja_item', 'a');	 
			}
		}
	}
}
?>
