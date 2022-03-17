<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').
   'module/pengelolaan_bhp/business/PopupKodeBarang.class.php';

//set Label for module
require_once GTFWConfiguration::GetValue( 'application', 'docroot') .
'module/label/response/Label.proc.class.php';

class PopupKodeBarang extends HtmlResponse
{

	function TemplateBase()
   {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application', 'docroot') . 'main/template/');
      $this->SetTemplateFile('document-common-popup.html');
      $this->SetTemplateFile('layout-common-popup.html');
   }

   function TemplateModule()
   {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application','docroot').
         'module/pengelolaan_bhp/template');
      $this->SetTemplateFile('pop_up_kode_barang.html');
   }

   function ProcessRequest()
   {
      $Obj = new KodeBarang;
      $GET = $_GET->AsArray();

      // inisialisasi messaging
		$msg = Messenger::Instance()->Receive(__FILE__);
		$this->Pesan = $msg[0][1];
		$this->css = $msg[0][2];
      // End

      // Inisialisasi filter
      $CARI_GOLONGAN = '';
      $CARI_BIDANG_ID = '';
      $CARI_BIDANG_NAME = '';
      $CARI_KELOMPOK_ID = '';
      $CARI_KELOMPOK_NAME = '';
      $CARI_SUB_KELOMPOK_ID = '';
      $CARI_SUB_KELOMPOK_NAME = '';
      $CARI_NAMA_BARANG = '';
      $CARI_JENIS_BARANG = '';

      if (isset($_POST['CARI_GOLONGAN']))
      {
         $CARI_GOLONGAN = $_POST['CARI_GOLONGAN']->Raw();
         $CARI_BIDANG_ID = $_POST['CARI_BIDANG_ID']->Raw();
         $CARI_BIDANG_NAME = $_POST['CARI_BIDANG_NAME']->Raw();
         $CARI_KELOMPOK_ID = $_POST['CARI_KELOMPOK_ID']->Raw();
         $CARI_KELOMPOK_NAME = $_POST['CARI_KELOMPOK_NAME']->Raw();
         $CARI_SUB_KELOMPOK_ID = $_POST['CARI_SUB_KELOMPOK_ID']->Raw();
         $CARI_SUB_KELOMPOK_NAME = $_POST['CARI_SUB_KELOMPOK_NAME']->Raw();
         $CARI_NAMA_BARANG = $_POST['CARI_NAMA_BARANG']->Raw();
         $CARI_JENIS_BARANG = $_POST['CARI_JENIS_BARANG']->Raw();
      }
      elseif (isset($_GET['cari']))
         @list($CARI_GOLONGAN, $CARI_BIDANG_ID, $CARI_BIDANG_NAME, $CARI_KELOMPOK_ID, $CARI_KELOMPOK_NAME, $CARI_SUB_KELOMPOK_ID, $CARI_SUB_KELOMPOK_NAME, $CARI_NAMA_BARANG, $CARI_JENIS_BARANG) = explode('|', $_GET['cari']->Raw());
      $return['cari'] = array
      (
         'CARI_GOLONGAN' => $CARI_GOLONGAN,
         'CARI_BIDANG_ID' => $CARI_BIDANG_ID,
         'CARI_BIDANG_NAME' => $CARI_BIDANG_NAME,
         'CARI_KELOMPOK_ID' => $CARI_KELOMPOK_ID,
         'CARI_KELOMPOK_NAME' => $CARI_KELOMPOK_NAME,
         'CARI_SUB_KELOMPOK_ID' => $CARI_SUB_KELOMPOK_ID,
         'CARI_SUB_KELOMPOK_NAME' => $CARI_SUB_KELOMPOK_NAME,
         'CARI_NAMA_BARANG' => $CARI_NAMA_BARANG,
         'CARI_JENIS_BARANG' => $CARI_JENIS_BARANG
      );
      $cari = implode('|',$return['cari']);
      // End of inisialisasi filter

      // Render Combobox....
      $GolList = $Obj->GetComboGolongan();
      Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'cari_golongan', array('CARI_GOLONGAN', $GolList, $CARI_GOLONGAN, true, 'id="POP_UP_GOLONGAN"'), Messenger::CurrentRequest);

      $JnsBarangList = $Obj->GetComboJnsBarang();
      Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'cari_jenis_barang', array('CARI_JENIS_BARANG', $JnsBarangList, $CARI_JENIS_BARANG, true, ''), Messenger::CurrentRequest);
      // ---------

      // Inisialisasi link URL
      $return['url']['search'] = Dispatcher::Instance()->GetUrl('pengelolaan_bhp','KodeBarang','popup','html').'&tgl='.$GET['tgl'].'&uid='.$GET['uid'];
      $return['url']['pop_up_bidang'] = Dispatcher::Instance()->GetUrl('popup','Bidang','popup','html');
      $return['url']['pop_up_kelompok'] = Dispatcher::Instance()->GetUrl('popup','Kelompok','popup','html');
      $return['url']['pop_up_sub_kelompok'] = Dispatcher::Instance()->GetUrl('popup','SubKelompok','popup','html');
      // ---------

      // Inisialisasi komponen paging
      //$totalData = $Obj->GetDataJenisCount($return['cari']);
      $itemViewed = 20;
      $currPage = 1;
      if (isset($_GET['page'])) $currPage = $_GET['page']->Integer()->Raw();
      if ($currPage < 1) $currPage = 1;
      $startRec = ($currPage-1) * $itemViewed;

      $url = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, Dispatcher::Instance()->mSubModule, Dispatcher::Instance()->mAction, Dispatcher::Instance()->mType).
         '&cari=' . Dispatcher::Instance()->Encrypt($cari).
         '&tgl='.$GET['tgl'].'&uid='.$GET['uid']
      ;
	  $dest = "popup-subcontent";
	  $jenis = $Obj->GetDataJenis($return['cari'], $GET['tgl'], $GET['uid'], $startRec, $itemViewed);
	  $return['jenis'] = $jenis;
	  $totalData = $Obj->GetCount();
	  
      Messenger::Instance()->SendToComponent('paging', 'paging', 'view', 'html', 'paging_top', array($itemViewed, $totalData, $url, $currPage, $dest), Messenger::CurrentRequest);
      $return['start'] = $startRec+1;
      // ---------

      // Generate data structure
      //$jenis = $Obj->GetDataJenis($return['cari'], $startRec, $itemViewed);
	  //$return['jenis'] = $jenis;
      $barang = $Obj->GetDataBarang($return['cari']);
      $return['barang'] = $barang;
      $kelompok = $Obj->GetKelompokList();
      $return['kelompok'] = array();
      $subKelompok = $Obj->GetSubKelompokList();
      $return['subKelompok'] = array();

      $isFirstPage = ($currPage == 1);
      $isLastPage = ($currPage == ceil($totalData/$itemViewed));
      $isEmpty = ($totalData == 0 OR count($barang) == 0);
      $isCari = ($cari == 'all||||||||all' OR $cari == '||||||||');

      foreach ($kelompok as $idKelompok => $value)
      {
         if (!(isset($kelCache) OR isset($barang[$idKelompok]) OR $isEmpty OR $isFirstPage)) continue;
         $kelCache[$idKelompok] = $value;

         if (isset($barang[$idKelompok]))
         {
            if ($isCari) $return['kelompok'] += $kelCache;
            else $return['kelompok'] += array($idKelompok => $value);
            $kelCache = array();
         }
      }
      if (is_array($kelCache) AND count($kelCache) > 0 AND $isCari AND $isLastPage)
         $return['kelompok'] += $kelCache;

      foreach($return['kelompok'] as $idKelompok => $value)
      {
         if (!isset($subKelompok[$idKelompok])) continue;
         $return['subKelompok'][$idKelompok]  = array();
         foreach ($subKelompok[$idKelompok] as $idSubKelompok => $value)
         {
            if (!(isset($subKelCache) OR isset($barang[$idKelompok][$idSubKelompok]) OR $isFirstPage)) continue;
            $subKelCache[$idSubKelompok] = $value;

            if (isset($barang[$idKelompok][$idSubKelompok]))
            {
               if ($isCari) $return['subKelompok'][$idKelompok] += $subKelCache;
               else  $return['subKelompok'][$idKelompok] += array($idSubKelompok => $value);
               $subKelCache = array();
            }
         }

         if (is_array($subKelCache) AND count($subKelCache) > 0 AND $isCari AND $isLastPage)
         {
            $return['subKelompok'][$idKelompok] += $subKelCache;
            $subKelCache = array();
         }
      }
      // End of generate data
      return $return;
   }

   function TemplateLabeling(){
      $ObjLabel = new GetModuleLabel();
      $arrLabel = $ObjLabel->GetLabel();

      //patTemplate name yang memiliki label2 dinamic
      $arrContent=array("content");

      for($i=0;$i<count($arrContent);$i++){
         for($j=0;$j<count($arrLabel);$j++){
            $this->mrTemplate->AddVar($arrContent[$i],$arrLabel[$j]['labelCode'],$arrLabel[$j]['labelName']);
         }
      }
   }

	function filter($string){
		return str_replace(array("'","\""),array('\x27','\x22'),$string);
	}
	
	function ParseTemplate($data = NULL)
   {
      //set Label
      $this->TemplateLabeling();

      // Render URL
      $this->mrTemplate->AddVars('content', $data['url'], 'URL_');
      // ---------

      // Render filter box
      $this->mrTemplate->AddVars('content', $data['cari'], '');
      // ---------

      // Render tabel data
		if (count($data['kelompok']) === 0)
		{
			$this->mrTemplate->AddVar('data', 'DATA_EMPTY', 'YES');
			return;
		}
		else $this->mrTemplate->AddVar('data', 'DATA_EMPTY', 'NO');

		$i = $data['start'];
      foreach ($data['kelompok'] as $idKelompok=>$value)
      {
			// Kelompok
         if (!isset($data['jenis'][$idKelompok]) OR count($data['jenis'][$idKelompok]) === 0)
            continue;

         $send = array();
			$send['number'] = '';
			$send['kode'] = $value['kode'];
			$send['nama'] = $this->filter($value['nama']);
			$send['class_name'] = 'table-common-even1';
         $this->mrTemplate->AddVars('data_item', $send, 'DATA_');

         $this->mrTemplate->parseTemplate('data_item', 'a');

         foreach ($data['subKelompok'][$idKelompok] as $idSubKelompok=>$value)
			{
				// Sub Kelompok
            if (!isset($data['jenis'][$idKelompok][$idSubKelompok]) OR count($data['jenis'][$idKelompok][$idSubKelompok]) === 0)
               continue;

            $send = array();
				$send['number'] = '';
				$send['kode'] = $value['kode'];
				$send['nama'] = $this->filter($value['nama']);
				$send['class_name'] = 'table-common-even';
            $this->mrTemplate->AddVars('data_item', $send, 'DATA_');

            $this->mrTemplate->parseTemplate('data_item', 'a');

   			foreach ($data['barang'][$idKelompok][$idSubKelompok] as $idBarang => $value)
   			{
   				// Barang
					if (!isset($data['jenis'][$idKelompok][$idSubKelompok][$idBarang]) OR count($data['jenis'][$idKelompok][$idSubKelompok][$idBarang]) === 0)
               continue;
					
               $send = $value;
   				$send['number'] = '';
   				$send['class_name'] = '';
					$send['nama'] = addslashes($send['nama']);
					$this->mrTemplate->AddVars('data_item', $send, 'DATA_');
   				$this->mrTemplate->parseTemplate('data_item', 'a');
					
					foreach ($data['jenis'][$idKelompok][$idSubKelompok][$idBarang] as $value)
					{
						// jenis
						$send = $value;
						$send['number'] = $i;
						$send['class_name'] = '';
                  $send['nama_js'] = $this->filter(trim($value['nama']));
						
						$this->mrTemplate->AddVars('data_item', $send, 'DATA_');
						$this->mrTemplate->AddVars('icon_pilih', $send, 'DATA_');

						$this->mrTemplate->SetAttribute('icon_pilih', 'visibility', 'visible');
						$this->mrTemplate->parseTemplate('data_item', 'a');
						$this->mrTemplate->SetAttribute('icon_pilih', 'visibility', 'hidden');
						$i++;
					}
   			}
			}
      }
      // ---------
   }
}
?>