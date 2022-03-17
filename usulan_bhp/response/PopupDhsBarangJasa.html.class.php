<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').'module/' . Dispatcher::Instance()->mModule . '/business/DhsBarangJasa.class.php';

class PopupDhsBarangJasa extends HtmlResponse
{

	function TemplateBase()
   {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application', 'docroot') . 'main/template/');
      $this->SetTemplateFile('document-common-popup.html');
      $this->SetTemplateFile('layout-common-popup.html');
   }
   
   function TemplateModule()
   {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application','docroot').'module/'.Dispatcher::Instance()->mModule.'/template');
      $this->SetTemplateFile('popup_dhs_barang_jasa.html');
   }
   
   function ProcessRequest()
   {
      $Obj = new DhsBarangJasa;
      					
		if(isset($_POST['barangNama'])) {
			$nama = $_POST['barangNama'];
		} elseif(isset($_GET['cari'])) {
			$nama = Dispatcher::Instance()->Decrypt($_GET['barangNama']);
		} else {
			$nama = '';
		}
      
      #set default pagging
      $limit = 20;
      $page = 0;
      $offset = 0;
      
      if(isset($_GET['page'])){
      	$page = (string) $_GET['page']->StripHtmlTags()->SqlString()->Raw();
      	$offset = ($page - 1) * $limit;
      }
      
      #fetch data
      $arrData = array(
      	'%'.$nama.'%',
      	$limit,
      	$offset
      );
      
      $return['data'] = $Obj->GetDataDhs($arrData);
      
      #fethc numrows
      $numrows = $Obj->GetDataDhsCount();// fetch here;
      
      #pagging url
      $url = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule,
      	Dispatcher::Instance()->mSubModule,
      	Dispatcher::Instance()->mAction,
      	Dispatcher::Instance()->mType
      	.'&barangNama=' . Dispatcher::Instance()->Encrypt($nama)
      	.'&cari=' . Dispatcher::Instance()->Encrypt(1)
      );
      
      $destination_id = "popup-subcontent"; # options: {popup-subcontent,subcontent-element}
      
      #send data to pagging component
      Messenger::Instance()->SendToComponent('paging', 'Paging', 'view', 'html', 'paging_top', array($limit,$numrows, $url, $page, $destination_id), Messenger::CurrentRequest);
      
      #send data to parse method
      $return['start'] = $offset+1;
      $return['page'] = $page;
      $return['numrows'] = $numrows;
      
      $arrUrl = array(
      	'url_search' => Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, Dispatcher::Instance()->mSubModule, Dispatcher::Instance()->mAction, Dispatcher::Instance()->mType)
      );
      
      $return['url'] = $arrUrl;
      return $return;
   }
   
	function ParseTemplate($data = NULL)
   {
   	// parse url
   	$this->mrTemplate->AddVars('content', $data['url'], '');
		
		// parse data 
		if (count($data['data']) === 0){
			$this->mrTemplate->AddVar('data', 'DATA_EMPTY', 'YES');
			return;
		} else {
			$this->mrTemplate->AddVar('data', 'DATA_EMPTY', 'NO');
			
			$dataBarang = $data['data'];
			
			$i = $data['start'];
			foreach ($dataBarang as $key => $value){
				$value['number'] = $i;
				$value['baranghps_format'] = number_format($value['barangHps'], '0', '', '.');
            $this->mrTemplate->AddVars('data_item', $value, 'DATA_');
            $this->mrTemplate->parseTemplate('data_item', 'a');
            $i++;
			}
		} 
   	
   }
}
?>
