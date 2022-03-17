<?php
require_once GTFWConfiguration::GetValue('application', 'docroot').'module/'.Dispatcher::Instance()->mModule.'/business/RencanaPengeluaran.class.php';

class PopupRencanaPengadaan extends HtmlResponse
{
	protected $data;
		
	function TemplateBase()
   {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application', 'docroot') . 'main/template/');
      $this->SetTemplateFile('document-common-popup.html');
      $this->SetTemplateFile('layout-common-popup.html');
   }
	
	function TemplateModule()
	{
		$this->SetTemplateBasedir(GTFWConfiguration::GetValue('application', 'docroot') . 'module/'.Dispatcher::Instance()->mModule.'/template');
		$this->SetTemplateFile('popup_rencana_pengadaan.html');
	}
	
	function ProcessRequest()
	{
	
		$obj = new RencanaPengeluaran();
		$this->data  = $_GET->AsArray();
	
		if (isset($_POST['btnTampilkan']))
		{
			if (is_object($_POST['data']))
			{
				$this->data = $_POST['data']->AsArray();
			}
				else
			{
				$this->data = $_POST['data'];
			}
			
		}elseif(isset($_GET['page'])){
			$this->data['kode']			= Dispatcher::Instance()->Decrypt($_GET['kode']);
			$this->data['nama']			= Dispatcher::Instance()->Decrypt($_GET['nama']);
		} else {
			$this->data['kode']			= "";
			$this->data['nama']			= "";
		}
		
		// setting paging
		$itemViewed = 20;
		$currPage 	= 1;
		$startRec 	= 0;

		if (isset($_GET['page']))
		{
			$currPage = (string)$_GET['page']->StripHtmlTags()->SqlString()->Raw();
			$startRec = ($currPage - 1) * $itemViewed;
		}

		// get data
		$dataList 	= $obj->GetData($startRec, $itemViewed, $this->data);
		
		// get total data
		$totalData 	= $obj->GetCount();
				
		// get url from data
		$url 	= Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 
				  Dispatcher::Instance()->mSubModule, Dispatcher::Instance()->mAction, 
				  Dispatcher::Instance()->mType).
				  "&kode=".$this->data['kode'].
				  "&nama=".$this->data['nama'];

		$dest = "popup-subcontent";
		// send paging to template
		Messenger::Instance()->SendToComponent('paging', 'Paging', 'view', 'html', 'paging_top', 
				array(
					$itemViewed,
					$totalData,
					$url,
					$currPage,
					$dest
					) , Messenger::CurrentRequest);
					
		$return['data']		= $dataList;
		$return['start']		= $startRec + 1;
		return $return;
	}
	
	function ParseTemplate($data = NULL)
	{
		$url_search		= Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'rencanaPengadaan', 'popup', 'html');
		$this->mrTemplate->AddVar('content', 'URL_SEARCH', $url_search);
		$this->mrTemplate->AddVar('content', 'SEARCH_NAME', $this->data['nama']);
		$this->mrTemplate->AddVar('content', 'SEARCH_KODE', $this->data['kode']);

		if (empty($data['data'])){
			$this->mrTemplate->AddVar('data_grid', 'IS_DATA_EMPTY', 'YES');
		}else{
			$this->mrTemplate->AddVar('data_grid', 'IS_DATA_EMPTY', 'NO');
			
			$dataGrid = $data['data'];
			$i = 0;
			$index = 0; // inisialisasi data yang akan dikirim
			$no = 1;
			$program_id = ''; //inisialisasi program
			$kegiatan_id = ''; //inisialisasi kegiatan
			$index_program = ''; //inisialisasi index program yang aktif saat ini
			$index_kegiatan = ''; //inisialisasi index kegiatan yang aktif saat ini
			
			$dataList = array();

			//parsing  tampilan dan membuat menjadi array bertingkat yang ditempatkan pada $dataList
			$nilai_digunakan = 0;
			for ($i = 0;$i < sizeof($dataGrid);)
			{
			
				//=========strat setting tampilan=======================
				$view_program_nomor = $dataGrid[$i]['program_nomor'];
				$view_kegiatan_nomor = $dataGrid[$i]['kegiatan_nomor'];
				
				//===========kondisi kalo berupa data sub_kegiatan ===========================
				if (
					($program_id == $dataGrid[$i]['program_id']) 
					&& ($kegiatan_id == $dataGrid[$i]['kegiatan_id']))
				{
					$idEnc = Dispatcher::Instance()->Encrypt($dataGrid[$i]['subkegiatan_id']);
					$dataGrid[$i]['subkegiatan_id'] = $idEnc;
					
					//===========start pengaturan tampilan kode;=======================
					$jenis_keg_id	= $dataGrid[$i]['jenis_keg_id'];
					$jenis_keg_nm	= $dataGrid[$i]['jenis_kegiatan_nama'];
					$dataGrid[$i]['subkegiatan_kode'] = $dataGrid[$i]['subkegiatan_nomor'];
					
					if (($dataGrid[$i]['is_approve'] == 'Ya')){
						$dataGrid[$i]['url_aksi'] = "<a title=\"Pilih\"><img onClick=\"javascript: select_this('".$dataGrid[$i]['subkegiatan_kode']."', '".$dataGrid[$i]['subkegiatan_nama']."', '".((int)$dataGrid[$i]['nilai_approve_pengadaan']-$nilai_digunakan)."', '".$dataGrid[$i]['kegiatandetail_id']."');\"  src=\"images/button-check.gif\" alt=\"Pilih\"></a>";
					} else $dataGrid[$i]['url_aksi'] = '';
					
					$dataGrid[$i]['class_name'] = '';

					if(empty($dataGrid[$i]['kegdetRABFile'])){
						$dataGrid[$i]['class_name']='table-yellow';
					}
					
					$dataKirim[$index] = $dataGrid[$i];
					$dataList[$index_program]['data'][$index_kegiatan]['data'][$index] = $dataKirim[$index];
					$i++;
				}
				elseif ($program_id != $dataGrid[$i]['program_id'])
				{ //klo informasi program 	====================

					$program_id = $dataGrid[$i]['program_id'];
					$dataKirim[$index]['class_name'] = 'table-common-even1';
					$dataKirim[$index]['subkegiatan_kode'] = '<b>' . $view_program_nomor .'</b>'; //nambah"0."
					$dataKirim[$index]['subkegiatan_nama'] = '<b>' . $dataGrid[$i]['program_nama'] . '</b>';
					$dataKirim[$index]['url_aksi'] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					$dataKirim[$index]['subkegiatan_id'] = '';
					$dataKirim[$index]['nomor'] = '<b>' . $no . '</b>';
					$dataKirim[$index]['nilai'] = '';
					$index_program = $index;
					$dataList[$index_program] = $dataKirim[$index];
					$no++;
				}
				elseif ($kegiatan_id != $dataGrid[$i]['kegiatan_id'])
				{ //klo informasi kegiatan  =============================

					$kegiatan_id = $dataGrid[$i]['kegiatan_id'];
					$dataKirim[$index]['class_name'] = 'table-common-even2';

					//kode tambahan
					$jenis_keg_id = $dataGrid[$i]['jenis_keg_id'];
					$dataKirim[$index]['subkegiatan_kode'] = '<i>' . $view_kegiatan_nomor . '</i>';
					$dataKirim[$index]['subkegiatan_nama'] = '<i>' . $dataGrid[$i]['kegiatan_nama'] .'</i>';
					$dataKirim[$index]['url_tambah'] = '';
					$dataKirim[$index]['subkegiatan_id'] = '';
					$dataKirim[$index]['nomor'] = '';
					$index_kegiatan = $index;
					$dataList[$index_program]['data'][$index_kegiatan] = $dataKirim[$index];
				}
				$index++;
			} //end for dataGrid

			//proses SUM penghitungan nilai pada program dan kegiatan
			foreach($dataList as $keyprogram => $program)
			{
				foreach($program['data'] as $keykegiatan => $kegiatan)
				{
					foreach($kegiatan['data'] as $key => $data)
					{
						$dataKirim[$keykegiatan]['nilai']+= $dataKirim[$key]['nilai'];
						$dataKirim[$keykegiatan]['nilai_approve']+= $dataKirim[$key]['nilai_approve'];
					}
					$dataKirim[$keyprogram]['nilai']+= $dataKirim[$keykegiatan]['nilai'];
					$dataKirim[$keyprogram]['nilai_approve']+= $dataKirim[$keykegiatan]['nilai_approve'];
				}
			}
			
			for ($j = 0;$j < sizeof($dataKirim);$j++)
			{
				$dataKirim[$j]['nilai_sisa'] = number_format(($dataKirim[$j]['nilai_approve_pengadaan']-$nilai_digunakan), 2, ',', '.');
				$dataKirim[$j]['nilai_digunakan'] = number_format($nilai_digunakan, 2, ',', '.');
				$dataKirim[$j]['nilai_approve_pengadaan'] = number_format($dataKirim[$j]['nilai_approve_pengadaan'], 2, ',', '.');
				$this->mrTemplate->AddVars('data_item', $dataKirim[$j], 'DATA_');
				$this->mrTemplate->parseTemplate('data_item', 'a');
			}

		}
	}
}
?>
