<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').
   'module/pengolahan_atk/business/AppPengolahanAtk.class.php';

class PopupKodeBarangAtk extends HtmlResponse
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
      $this->SetTemplateFile('popup_kode_barang.html');
   }
   
   function ProcessRequest()
   {
		$obj = new AppPengolahanAtk();
		
      if (count($_POST)>0)
      {
         $gudang = (int)$_POST['gudang_id']->Raw();
         $nama = $_POST['nama']->Raw();
      }
			elseif (isset($_GET['cari']))
      {
         list($gudang,$nama) = explode('|', Dispatcher::Instance()->Decrypt($_GET['cari']));
      }
			else
      {
         $gudang = '';
         $nama = '';
      }
		
		$cari = Dispatcher::Instance()->Encrypt("$nama|$gudang");
      // End of inisialisasi filter
      
      // Render Combobox....
      $gudangId = $obj->GetComboGudang();
   	Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'gudang_id', array('gudang_id', $gudangId, $gudang, 'false', ''), Messenger::CurrentRequest);
		
      // Inisialisasi link URL
      $return['url']['search'] = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, Dispatcher::Instance()->mSubModule, Dispatcher::Instance()->mAction, Dispatcher::Instance()->mType);
      // ---------
      
      // Inisialisasi komponen paging
      $totalData = $obj->GetKodeBarangCount($gudang, $nama);
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
		$data = $obj->GetKodeBarang($gudang, $nama, $startRec, $itemViewed);
      // End of generate data
		$return['data'] = $data;
		return $return;
   }
    
	function ParseTemplate($data = NULL)
   {
      // Render URL
      $this->mrTemplate->AddVar('content', 'URL_SEARCH', $data['url']['search']);
      // ---------

      // Render filter box
		$exp = explode('|', $data['cari']);
      $this->mrTemplate->AddVar('content', 'GUDANG', $exp[1]);
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
				$row['sisa_barang'] = (int)$row['sisa_barang'];
				$this->mrTemplate->AddVars('data_item', $row, '');
				$this->mrTemplate->parseTemplate('data_item', 'a');
				$i++;
			}
		}
	}
}
?>