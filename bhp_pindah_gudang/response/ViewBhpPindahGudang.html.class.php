<?php
/**
* @package : ViewBhpPindahGudang
* @copyright : Copyright (c) PT Gamatechno Indonesia
* @Analyzed By : Dyan Galih
* @author : Didi Zuliansyah
* @version : 01
* @startDate : 2013-01-01
* @lastUpdate : 2013-01-01
* @description : View List BHP Pindah Gudang
*/

require_once GTFWConfiguration::GetValue( 'application', 'docroot') . 'module/'.Dispatcher::Instance()->mModule.'/business/BhpPindahGudang.class.php';

class ViewBhpPindahGudang extends HtmlResponse {

	function TemplateModule() {
		$this->SetTemplateBasedir(GTFWConfiguration::GetValue( 'application', 'docroot').'module/'.Dispatcher::Instance()->mModule.'/template');
		$this->SetTemplateFile('view_bhp_pindah_gudang.html');
	}

	function ProcessRequest() {
		$obj = new BHPPindahGudang();
		$_POST = $_POST->AsArray();
		$_GET = $_GET->AsArray();
		
		# inisialisasi messaging
		$msg = Messenger::Instance()->Receive(__FILE__);
		$this->Data = $msg[0][0];
		$this->Pesan = $msg[0][1];
		$this->css = $msg[0][2];
		
		if($_POST || isset($_GET['cari'])){
			if($_POST){
				$noTransaksi = $_POST['notrans'];
				$unitAsal = $_POST['unitasal'];
				$unitTujuan = $_POST['unittujuan'];
				$namaBarang = $_POST['namabarang'];
			}elseif(isset($_GET['cari'])){
				$noTransaksi = $_GET['notrans'];
				$unitAsal = $_GET['unitasal'];
				$unitTujuan = $_GET['unittujuan'];
				$namaBarang = $_GET['namabarang'];
			}else{
				$noTransaksi = '';$unitAsal = '';$unitTujuan = '';$namaBarang = '';
			}
		}
		
		$itemViewed = 20;
		$currPage = 1;
		$startRec = 0 ;
		if(isset($_GET['page'])) {
			$currPage = (string)$_GET['page'];
			$startRec =($currPage-1) * $itemViewed;
		}
		$url = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule,
				Dispatcher::Instance()->mSubModule,
				Dispatcher::Instance()->mAction,
				Dispatcher::Instance()->mType
		).'&notrans='.$noTransaksi.'&unitasal='.$unitAsal.'&unittujuan='.$unitTujuan.'&namabarang='.$namaBarang.'&cari=1';
		 
		$return['data'] = $obj->GetDataBHPPindahGudang($noTransaksi, $unitAsal, $unitTujuan, $namaBarang,$startRec,$itemViewed);
		$totalData = $obj->GetCount();
		 
		Messenger::Instance()->SendToComponent('paging', 'Paging', 'view', 'html', 'paging_top',array($itemViewed,$totalData, $url, $currPage),	Messenger::CurrentRequest);
		
		# search vars
		$return['search'][1] = $noTransaksi;
		$return['search'][2] = $unitAsal;
		$return['search'][3] = $unitTujuan;
		$return['search'][4] = $namaBarang;
		
		$return['start'] = $startRec+1;
		
		return $return;
	}

	function ParseTemplate($data = NULL) {
		$this->mrTemplate->AddVar('content', 'URL_SEARCH', Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, Dispatcher::Instance()->mSubModule, 'view', 'html') );
		
		# render message box
		if($this->Pesan)
		{
			$this->mrTemplate->SetAttribute('warning_box', 'visibility', 'visible');
			$this->mrTemplate->AddVar('warning_box', 'ISI_PESAN', $this->Pesan);
			$this->mrTemplate->AddVar('warning_box', 'NOTEBOX', $this->css);
		}
		
		# search var
		$this->mrTemplate->AddVar('content', 'NOTRANS', $data['search'][1]);
		$this->mrTemplate->AddVar('content', 'UNITASAL', $data['search'][2]);
		$this->mrTemplate->AddVar('content', 'UNITTUJUAN', $data['search'][3]);
		$this->mrTemplate->AddVar('content', 'NAMABARANG', $data['search'][4]);
		
		# url add
		$urlAdd = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule,'InputBhpPindahGudang',Dispatcher::Instance()->mAction,Dispatcher::Instance()->mType);
		$this->mrTemplate->AddVar('content', 'URL_TAMBAH', $urlAdd);
		
		if (empty($data['data'])) {
			$this->mrTemplate->AddVar('data', 'DATA_EMPTY', 'YES');
		} else {
			$this->mrTemplate->AddVar('data', 'DATA_EMPTY', 'NO');
			$dataGrid = $data['data'];
			@$no = $i+$data['start'];
			for($i=0;$i < count($dataGrid);$i++) {
				$dataGrid[$i]['nomor'] = $no;
				if((int)$dataGrid[$i]['penerima']) $dataGrid[$i]['css'] = 'background-color: #F9D54B;';
				else{
					$dataGrid[$i]['css'] = '';
					$dataGrid[$i]['class_name'] = ($no % 2 != 0)?'table-common-even':'';
				}
				$idEnc = Dispatcher::Instance()->Encrypt($dataGrid[$i]['bhpMutasiMstId']);			
				$dataGrid[$i]['url_detil'] = Dispatcher::Instance()->GetUrl(
					Dispatcher::Instance()->mModule, 'PopupDetil', 'view', 'html'
				).'&id='.$idEnc;
				$no++;
				 
				$this->mrTemplate->AddVars('data_item', $dataGrid[$i], 'BHP_');
				$this->mrTemplate->parseTemplate('data_item', 'a');
			}
		}
	}
}
?>