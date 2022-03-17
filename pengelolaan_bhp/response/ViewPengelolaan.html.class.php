<?php
require_once GTFWConfiguration::GetValue('application', 'docroot').'module/'.Dispatcher::Instance()->mModule.'/business/Pengelolaan.class.php';
require_once GTFWConfiguration::GetValue('application', 'docroot') . 'main/function/date.php';

require_once GTFWConfiguration::GetValue( 'application', 'docroot') . 'module/setting/business/Setting.class.php';

class ViewPengelolaan extends HtmlResponse
{
	function TemplateModule ()
	{
		$this->SetTemplateBasedir(GTFWConfiguration::GetValue('application','docroot').'module/'.Dispatcher::Instance()->mModule.'/template');
		$this->SetTemplateFile('view_pengelolaan.html');
	}
	
	function ProcessRequest ()
   {
		$Obj = new Pengelolaan();
		$Obj2 = new Setting();
		
		$arrSetting = $Obj2->GetSetting();
		// inisialisasi messaging
		$msg = Messenger::Instance()->Receive(__FILE__);
		$this->Data = $msg[0][0];
		$this->Pesan = $msg[0][1];
		$this->css = $msg[0][2];

		$_POST= $_POST->AsArray();
		$GET = $_GET->AsArray();

		$tglAwal = date('Y-01-01');
		$tglAkhir = date('Y-m-d');
	
		if($_POST || isset($_GET['cari'])){
			if(isset($_POST['notransaksi'])){
				$notrans = $_POST['notransaksi'];
				$nama = $_POST['nama'];
				$tglAwal = $_POST['ta_year'].'-'.$_POST['ta_mon'].'-'.$_POST['ta_day'];
         	$tglAkhir = $_POST['takh_year'].'-'.$_POST['takh_mon'].'-'.$_POST['takh_day'];
			}elseif(isset($_GET['notransaksi'])){
				$notrans = Dispatcher::Instance()->Decrypt($_GET['notransaksi']);
				$nama = Dispatcher::Instance()->Decrypt($_GET['nama']);	
				$tglAwal = $GET['ta'];
				$tglAkhir = $GET['takh'];
			}else{
				$notrans = '';
				$nama = '';				
			}
		}

		//$totalData = $Obj->GetCountDataPengelolaan($notrans, $nama, $arrSetting['3']['settingValue']);
		$itemViewed = 20;
		$currPage = 1;
		$startRec = 0 ;
		
		if(isset($_GET['page'])){
			$currPage = (string)$_GET['page']->StripHtmlTags()->SqlString()->Raw();
			$startRec =($currPage-1) * $itemViewed;
		}
		
		$data=$Obj->GetDataPengelolaan($startRec, $itemViewed, $notrans, $nama, $tglAwal, $tglAkhir, $arrSetting['3']['settingValue']);
		$totalData = $Obj->GetCount();
		$url = Dispatcher::Instance()->GetUrl(
			Dispatcher::Instance()->mModule, Dispatcher::Instance()->mSubModule, 
			Dispatcher::Instance()->mAction, Dispatcher::Instance()->mType . 
			'&notrans=' . Dispatcher::Instance()->Encrypt($notrans). 
			'&nama=' . Dispatcher::Instance()->Encrypt($nama).
			'&ta='.$tglAwal.
			'&takh='.$tglAkhir.
			'&cari=' . Dispatcher::Instance()->Encrypt(1)
		);
		Messenger::Instance()->SendToComponent('paging', 'Paging', 'view', 'html', 'paging_top', array($itemViewed,$totalData, $url, $currPage), Messenger::CurrentRequest);

		// combo tanggal
		Messenger::Instance()->SendToComponent('tanggal', 'Tanggal', 'view', 'html', 'takh', array($tglAkhir, $periode_awal, $periode_akhir), Messenger::CurrentRequest);
      Messenger::Instance()->SendToComponent('tanggal', 'Tanggal', 'view', 'html', 'ta', array($tglAwal, $periode_awal, $periode_akhir), Messenger::CurrentRequest);
		
		$return['data'] = $data;
		$return['start'] = $startRec+1;
		$return['search']['notrans'] = $notrans;
		$return['search']['nama'] = $nama;
		return $return;
	}
	
	
	function ParseTemplate ($data = NULL)
	{
		// render message box
		if($this->Pesan){
			$this->mrTemplate->SetAttribute('warning_box', 'visibility', 'visible');
			$this->mrTemplate->AddVar('warning_box', 'ISI_PESAN', $this->Pesan);
			$this->mrTemplate->AddVar('warning_box', 'NOTEBOX', $this->css);
		}

		$search = $data['search'];
		$this->mrTemplate->AddVar('content', 'NOTRANS', $search['notrans']);
		$this->mrTemplate->AddVar('content', 'NAMA', $search['nama']);
		
		$this->mrTemplate->AddVar('content', 'URL_SEARCH', Dispatcher::Instance()->GetUrl('pengelolaan_bhp', 'pengelolaan', 'view', 'html'));
		$this->mrTemplate->AddVar('content', 'URL_ADD', Dispatcher::Instance()->GetUrl('pengelolaan_bhp', 'inputPengelolaan', 'view', 'html'));
			 
		if (empty($data['data'])){
			$this->mrTemplate->addVar('data_grid', 'IS_DATA_EMPTY', 'YES');
			return;
		}else{	
			$this->mrTemplate->addVar('data_grid', 'IS_DATA_EMPTY', 'NO');
			
			// mulai bikin tombol delete
			$label = "Register Transaksi Harian";
			$urlDelete = Dispatcher::Instance()->GetUrl('pengelolaan_bhp', 'deletePengelolaan', 'do', 'html');
			$urlReturn = Dispatcher::Instance()->GetUrl('pengelolaan_bhp', 'pengelolaan', 'view', 'html');
			Messenger::Instance()->Send('confirm', 'confirmDelete', 'do', 'html', array($label, $urlDelete, $urlReturn),Messenger::NextRequest);
			$this->mrTemplate->AddVar('content', 'URL_DELETE', Dispatcher::Instance()->GetUrl('confirm', 'confirmDelete', 'do', 'html'));
			//selesai bikin tombol delete
      
      	$no = $data['start'];
			$data = $data['data'];
			for ($i=0; $i<sizeof($data); $i++){
				$data[$i]['number'] = $no;
				$data[$i]['class_name'] = ($no % 2 != 0)?'table-common-even':'';
				
				if($i == 0) $this->mrTemplate->AddVar('content', 'FIRST_NUMBER', $no);				
				if($i == sizeof($data)-1) $this->mrTemplate->AddVar('content', 'LAST_NUMBER', $no);
				
				$idEnc = Dispatcher::Instance()->Encrypt($data[$i]['trans_id']);
				
				$data[$i]['trans_tanggal'] = IndonesianDate($data[$i]['trans_tanggal'], 'yyyy-mm-dd');
				$data[$i]['url_detail'] = Dispatcher::Instance()->GetUrl('pengelolaan_bhp','detilPengelolaan', 'popup', 'html') . '&id=' . $idEnc;
				$data[$i]['url_cetak']=Dispatcher::Instance()->GetUrl('pengelolaan_bhp','cetakPengelolaan', 'view', 'html') . '&id=' . $idEnc;
				
				$this->mrTemplate->AddVars('data_list', $data[$i], 'DATA_');
				$this->mrTemplate->parseTemplate('data_list', 'a');	 
				$no++;
			}
		}	
	}
}
?>
