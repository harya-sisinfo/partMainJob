<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').
   'module/'.Dispatcher::Instance()->mModule.'/business/LogSirkulasi.class.php';

//set Label for module
require_once GTFWConfiguration::GetValue( 'application', 'docroot') .
   'module/label/response/Label.proc.class.php';

class ViewLogList extends HtmlResponse
{
   function TemplateModule()
   {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application','docroot').
         'module/'.Dispatcher::Instance()->mModule.'/template');
      $this->SetTemplateFile('view_log_list.html');
   }
   
   function ProcessRequest()
   {
      if (isset($_GET['barangId'])) $_SESSION['mod=pengolahanAtk']['barangId'] = $_GET['barangId']->Integer()->Raw();
      if (isset($_GET['ruangId'])) $_SESSION['mod=pengolahanAtk']['ruangId'] = $_GET['ruangId']->Integer()->Raw();
      $Obj = new LogSirkulasi;
      $GET = $_GET->AsArray();
      
      // Retrieving data about atk
      $return['detail'] = $Obj->GetAtkDetail();
      // ---------
      
      // inisialisasi messaging
		$msg = Messenger::Instance()->Receive(__FILE__);
      $this->Data = $msg[0][0];
		$this->Pesan = $msg[0][1];
		$this->css = $msg[0][2];
      // ---------
      
      // inisialisasi filter
      $filter['logAtkStatus'] = 'all';
      $filter['tglAwal'] = $tglAwal = date('Y').'-01-01';
      $filter['tglAkhir'] = $tglAkhir = date('Y-m-d');
      if (count($_POST) > 0){
         $filter = $_POST->AsArray();
         $filter['tglAwal'] = $filter['ta_year'].'-'.$filter['ta_mon'].'-'.$filter['ta_day'];
         $filter['tglAkhir'] = $filter['takh_year'].'-'.$filter['takh_mon'].'-'.$filter['takh_day'];
      }
      elseif (isset($_GET['page']) && !empty($this->Data))
         $filter = $this->Data;
      
      switch ($filter['logAtkStatus'])
      {
         case 'Mutasi Ke Unit Lain': $filter['logAtkTujuanRuangId'] = 'all'; break;
         case 'Mutasi Ke Gudang Lain': $filter['logAtkUnitId'] = 'all'; break;
         default:
            $filter['logAtkUnitId'] = 'all';
            $filter['logAtkTujuanRuangId'] = 'all';
      }
      
      Messenger::Instance()->Send(Dispatcher::Instance()->mModule, Dispatcher::Instance()->mSubModule, Dispatcher::Instance()->mAction, Dispatcher::Instance()->mType, array($filter), Messenger::UntilFetched);
      $return['filter'] = $filter;
      // ---------
      
      // render Combo Box
      $comboJenisLog = $Obj->GetComboJenisLog();
      Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'jenis_log', array('logAtkStatus', $comboJenisLog, $filter['logAtkStatus'], true, ' id="comboLogStatus"'), Messenger::CurrentRequest);
      
      $comboUnitKerja = $Obj->GetComboUnitKerja();
      Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'unit_tujuan', array('logAtkUnitId', $comboUnitKerja, $filter['logAtkUnitId'], true, ''), Messenger::CurrentRequest);
      
      $comboGudang = $Obj->GetComboGudang();
      Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'gudang_tujuan', array('logAtkTujuanRuangId', $comboGudang, $filter['logAtkTujuanRuangId'], true, ''), Messenger::CurrentRequest);

      Messenger::Instance()->SendToComponent('tanggal', 'Tanggal', 'view', 'html', 'takh', array($filter['tglAkhir'], $periode_awal, $periode_akhir), Messenger::CurrentRequest);

      Messenger::Instance()->SendToComponent('tanggal', 'Tanggal', 'view', 'html', 'ta', array($filter['tglAwal'], $periode_awal, $periode_akhir), Messenger::CurrentRequest);
      // ---------
      
      // Inisialisasi komponen paging
      $totalData = (int) $Obj->GetLogListCount($filter);
      $itemViewed = 10;
      if (isset($_GET['page'])) $currPage = $_GET['page']->Integer()->Raw();
      if (!isset($currPage) OR $currPage < 1) $currPage = 1;
      $startRec = ($currPage - 1) * $itemViewed;
      
      $url = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, Dispatcher::Instance()->mSubModule, Dispatcher::Instance()->mAction, Dispatcher::Instance()->mType).'&srch='.$GET['srch'];

      Messenger::Instance()->SendToComponent('paging', 'paging', 'view', 'html', 'paging_top', array($itemViewed, $totalData, $url, $currPage), Messenger::CurrentRequest);
      $return['start'] = $startRec+1;
      // ---------
      
      // Generate data structure
      if ($totalData > 0)
         $return['dataGrid'] = $Obj->GetLogList($filter, $startRec, $itemViewed);
      else $return['dataGrid'] = array();
      // ---------
      
      // inisialisasi url
      $return['url']['back'] = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'pengolahanAtk', 'view', 'html').'&srch='.$GET['srch'];
      $return['url']['search'] = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, Dispatcher::Instance()->mSubModule, Dispatcher::Instance()->mAction, Dispatcher::Instance()->mType).'&srch='.$GET['srch'];
      $return['url']['edit'] = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'logInput', 'view', 'html');
      $return['url']['delete'] = Dispatcher::Instance()->GetUrl('confirm', 'confirmDelete', 'do', 'html');
      // ---------
      
      // Inisialisasi komponen confirmDelete
		$label = "Manajemen Pengolahan BHP";
		$urlDelete = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'logDelete', 'do', 'html');
		$urlReturn = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'logList', 'view', 'html');
		Messenger::Instance()->Send('confirm', 'confirmDelete', 'do', 'html', array($label, $urlDelete, $urlReturn),Messenger::NextRequest);
      // ---------
      
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
   
   function ParseTemplate($data = NULL)
   {
      //set Label
      $this->TemplateLabeling();
      
      // Render message box
      if($this->Pesan)
      {
			$this->mrTemplate->SetAttribute('warning_box', 'visibility', 'visible');
			$this->mrTemplate->AddVar('warning_box', 'ISI_PESAN', $this->Pesan);
			$this->mrTemplate->AddVar('warning_box', 'CLASS_PESAN', $this->css);
		}
      // ---------
      $data['detail']['jumlahSisa'] = number_format($data['detail']['jumlahSisa']);
      // Render Atk Detail
      $this->mrTemplate->AddVars('content', $data['detail'], '');
      // ---------
      
      // Render URL
      $this->mrTemplate->AddVars('content', $data['url'], 'URL_');
      $this->mrTemplate->AddVars('hidden1', $data['url'], 'URL_');
      // ---------
      
      // Render Filter Box
      $this->mrTemplate->AddVars('content', $data['filter'], '');
      // ---------
      
      // Render data grid
		if (empty($data['dataGrid']))
		{
			$this->mrTemplate->AddVar('data', 'DATA_EMPTY', 'YES');
			return;
		}
		else $this->mrTemplate->AddVar('data', 'DATA_EMPTY', 'NO');
      
		$i = $data['start'];
      foreach ($data['dataGrid'] as $value)
      {
			$value['nomor'] = $i;
			$value['class_name'] = ($i % 2) ? 'table-common-even' : '';
         $value['url_edit'] = $data['url']['edit'] . "&id=". $value['id'];
         $value['logAtkJmlBrg'] = number_format($value['logAtkJmlBrg']);
         $value['hrgsat'] = number_format($value['hrgsat'],2,',','.');
         $this->mrTemplate->AddVars('data_item', $value, '');
         $this->mrTemplate->parseTemplate('data_item', 'a');
         
         $i++;
      }
      // ---------
   }
}
?>