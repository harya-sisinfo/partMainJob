<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').
   'module/pengelolaan_bhp/business/PopUpBarangList.class.php';

require_once GTFWConfiguration::GetValue( 'application', 'docroot') . 'module/setting/business/Setting.class.php';

class PopupBarang extends HtmlResponse
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
         'module/'.Dispatcher::Instance()->mModule.'/template');
      $this->SetTemplateFile('pop_up_barang.html');
   }
   
   function ProcessRequest()
   {
      $Obj = new Barang();
      $Obj2 = new Setting();
		
		$arrSetting = $Obj2->GetSetting();
      // inisialisasi messaging
		$msg = Messenger::Instance()->Receive(__FILE__);
		$this->Pesan = $msg[0][1];
		$this->css = $msg[0][2];
      // End
     
      // Inisialisasi filter
      if (count($_POST)>0)
      {
         $barcode = $_POST['CARI_BARCODDE']->Raw();
         $nama = $_POST['CARI_NAMA']->Raw();
      }
			elseif (isset($_GET['cari']))
      {
         list($barcode,$nama) = explode('|', Dispatcher::Instance()->Decrypt($_GET['cari']));
      }
			else
      {
         $barcode = '';
         $nama = '';
      }
		
		$cari = Dispatcher::Instance()->Encrypt("$nama|$barcode");
      // End of inisialisasi filter
      
      // Render Combobox....
      
      // Inisialisasi link URL
      $return['url']['search'] = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, Dispatcher::Instance()->mSubModule, Dispatcher::Instance()->mAction, Dispatcher::Instance()->mType);
      // ---------
      
      // Inisialisasi komponen paging
      $totalData = $Obj->GetDataBarangCount($nama, $barcode, $arrSetting['3']['settingValue']);
		$itemViewed = 10;
      $currPage = 1;
      if (isset($_GET['page'])) $currPage = $_GET['page']->Integer()->Raw();
      if ($currPage < 1) $currPage = 1;
      $startRec = ($currPage-1) * $itemViewed;
      
      $url = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, Dispatcher::Instance()->mSubModule, Dispatcher::Instance()->mAction, Dispatcher::Instance()->mType) . '&cari=' . Dispatcher::Instance()->Encrypt($cari);
		$dest = "popup-subcontent";
      Messenger::Instance()->SendToComponent('paging', 'paging', 'view', 'html', 'paging_top', array($itemViewed, $totalData, $url, $currPage, $dest), Messenger::CurrentRequest);
      $return['start'] = $startRec+1;
		$return['cari'] = $cari;
      // ---------
      
      // Generate data structure
		$barang = $Obj->GetDataBarang($nama, $barcode, $arrSetting['3']['settingValue'], $startRec, $itemViewed);
      // End of generate data
		$return['data'] = $barang;
		return $return;
   }
    
	function ParseTemplate($data = NULL)
   {
      // Render URL
      $this->mrTemplate->AddVar('content', 'URL_SEARCH', $data['url']['search']);
      // ---------

      // Render filter box
		$exp = explode('|', $data['cari']);
      $this->mrTemplate->AddVar('content', 'BARCODE', $exp[1]);
      $this->mrTemplate->AddVar('content', 'NAMA', $exp[0]);
		// ---------

      // Render tabel data
		if (count($data['data']) == 0)
		{
			$this->mrTemplate->AddVar('data', 'DATA_EMPTY', 'YES');
			return;
		}
			else 
		{
			$this->mrTemplate->AddVar('data', 'DATA_EMPTY', 'NO');
			$i = $data['start'];
			foreach($data['data'] as $row){
				$row['number'] = $i;
				if($i%2)
				{
					$row['class_name'] = "";
				} 
					else 
				{
					$row['class_name'] = 'table-common-even';
				}
				$row['stok'] = (int)$row['stok'];
				$row['nama_barang'] = addslashes($row['nama_barang']);
				$this->mrTemplate->AddVars('data_item', $row, 'DATA_');
				$this->mrTemplate->parseTemplate('data_item', 'a');
				$i++;
			}
		}
	}
}
?>