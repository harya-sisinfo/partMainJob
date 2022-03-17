<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').'module/'.Dispatcher::Instance()->mModule.'/business/Usulan.class.php';

class ViewListUsulanBarang extends HtmlResponse
{
   function TemplateModule()
   {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue( 'application', 'docroot').'module/'.Dispatcher::Instance()->mModule.'/template');
      $this->SetTemplateFile('view_list_usulan_barang.html');
   }
   
   function ProcessRequest()
   {
  		$Obj = new Usulan;
  		$tmp = $Obj->GetDataUserByUsername($_SESSION['username']);
        $return['roleId'] = $tmp['role_id'];
              
  		$msg = Messenger::Instance()->Receive(__FILE__);
        $this->Data = $msg[0][0];
  		$this->Pesan = $msg[0][1];
  		$this->css = $msg[0][2];
  		
		  $filter = array(
         'status' => 'all',
         'periode' => 'all'
      );
      if (isset($_POST['btncari'])) $filter = $_POST->AsArray();
      elseif (isset($_GET['page']) && is_array($this->Data)) $filter = $this->Data;
      
      Messenger::Instance()->Send('mr_usulan_barang', 'usulanList', 'view', 'html', array($filter), Messenger::UntilFetched);
      $return['filter'] = $filter;
		
      $comboUnit = $Obj->GetComboUnit();
		
      /*if (count($comboUnit) === 1)
      {
         $filter['unit'] = $comboUnit[key($comboUnit)]['id'];
         Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'unit', array('unit', $comboUnit, $filter['unit'], false, ''), Messenger::CurrentRequest);
      }
      else*/ Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'unit', array('unit', $comboUnit, $filter['unit'], true, ''), Messenger::CurrentRequest);
		
  		$comboStatus = $Obj->GetComboStatusVerifikasi();
  		Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'status', array('status', $comboStatus, $filter['status'], true, ''), Messenger::CurrentRequest);	

      $comboPeriode = $Obj->GetDataPeriode();
      Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'periode', array('periode', $comboPeriode, $filter['periode'], true, ''), Messenger::CurrentRequest);  
     		
     	#set default pagging
     	$limit = 20;
     	$page = 0;
     	$offset = 0;
     	
     	if(isset($_GET['page'])){
     		$page = (string) $_GET['page']->StripHtmlTags()->SqlString()->Raw();
     		$offset = ($page - 1) * $limit;
     	}
     	
     	#fetch data
     	$return['data'] = $Obj->GetDataGrid($filter, $offset, $limit);
     	
     	#fethc numrows
     	$numrows = $Obj->GetDataGridCount();
     	
     	#pagging url
     	$url = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule,
     		Dispatcher::Instance()->mSubModule,
     		Dispatcher::Instance()->mAction,
     		Dispatcher::Instance()->mType);
     	
     	$destination_id = "subcontent-element"; # options: {popup-subcontent,subcontent-element}
     	
     	#send data to pagging component
     	Messenger::Instance()->SendToComponent('paging', 'Paging', 'view', 'html', 'paging_top', array($limit,$numrows, $url, $page, $destination_id), Messenger::CurrentRequest);
     	
     	#send data to parse method
     	$return['start'] = $offset+1;
     	$return['page'] = $page;
     	$return['numrows'] = $numrows;
     	
  		$return['url'] = array(
  			'search' => Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, Dispatcher::Instance()->mSubModule, Dispatcher::Instance()->mAction, Dispatcher::Instance()->mType),
  			'add' => Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'inputUsulanBarang', 'view', 'html'),
  			'detil' => Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'detailUsulanBarang', 'popup', 'html'),
  			'update' => Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'updateUsulanBarang', 'view', 'html'),
        'approve' => Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'approveUsulanBarang', 'view', 'html')
  		);

      return $return;
   }
   
   function ParseTemplate($data = NULL)
   {
   	if($this->Pesan){
       	$this->mrTemplate->SetAttribute('warning_box', 'visibility', 'visible');
			$this->mrTemplate->AddVar('warning_box', 'ISI_PESAN', $this->Pesan);
			$this->mrTemplate->AddVar('warning_box', 'CLASS_PESAN', $this->css);
		}
		
    $this->mrTemplate->AddVars('content', $data['filter'], '');
   	$this->mrTemplate->AddVars('content', $data['url'], 'URL_');
   	
		if (count($data['data']) == 0)
		{
			$this->mrTemplate->AddVar('data', 'DATA_EMPTY', 'YES');
			return;
		}
		else $this->mrTemplate->AddVar('data', 'DATA_EMPTY', 'NO');
      
		$i = $data['start'];
      foreach ($data['data'] as $value)
      {
			$value['nomor'] = $i;
			$value['class_name'] = ($i % 2) ? 'table-common-even' : '';
         
         switch ($value['status']) {
            case 'Belum':
              $value['status'] = 'Dalam Proses';
              break;
            case 'Revisi':
              $value['status'] = 'Revisi';
              break;
            case 'Tidak':
              //$value['status'] = 'Ditolak';
            	$value['status'] = 'Dalam Proses';
              break;
            case 'Ya':
              $value['status'] = 'Disetujui';
              break;
         }

         if($value['usulanBrgIsApprove'] == 'Ya'){
            $value['display'] = 'none';
         } else {
            $value['display'] = '';
         }

         $value['url_edit'] = $data['url']['update'].'&id='.$value['id'];
         $value['url_detail'] = $data['url']['detil'].'&id='.$value['id'];
         $value['url_approve'] = $data['url']['approve'].'&id='.$value['id'];

         $totalUsulan += $value['totalUsulan'];
         $value['totalUsulan'] = number_format($value['totalUsulan'], 0, ',', '.');
         $this->mrTemplate->AddVars('data_item', $value, '');
			   $this->mrTemplate->parseTemplate('data_item', 'a');
         
         $i++;
      }
      $this->mrTemplate->AddVar('content', 'TOTAL_USULAN', number_format($totalUsulan, 0, ',', '.'));
   }
}
?>
