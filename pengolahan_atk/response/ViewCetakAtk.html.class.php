<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot') . 'module/pengolahan_atk/business/AppPengolahanAtk.class.php';

class ViewCetakAtk extends HtmlResponse {

   function TemplateModule() {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application','docroot').'module/pengolahan_atk/template');
      $this->SetTemplateFile('view_cetak_atk.html');
   }
   
   function TemplateBase() {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application', 'docroot') . 'main/template/');
      $this->SetTemplateFile('document-print.html');
      $this->SetTemplateFile('layout-common-print.html');
   }
   
   function ProcessRequest() {
      $Obj = new AppPengolahanAtk();
		
      //set combo kib
         $dataGudang = $_GET['gudang']->Raw();
         $dataBarang = $_GET['barang']->Raw();
         $dataStok = $_GET['stok']->Raw();

      $return['data'] = $Obj->GetAtkList($dataGudang,$dataBarang,$dataStok,0,100000);
   
      return $return;  
   }
	
	function ParseTemplate($data = NULL) {
	
      if(empty($data['data'])){
         $this->mrTemplate->AddVar('data','DATA_EMPTY','YES');
         return;
      }else
         $this->mrTemplate->AddVar('data','DATA_EMPTY','NO');
         
         $dataGrid = $data['data'];
         $no = 1;

         for($i=0;$i<count($dataGrid);$i++){
            $dataGrid[$i]['no'] = $no+$i;
            $dataGrid[$i]['class_name'] = ($i % 2) ? '' : 'table-common-even';
            
            $stok_minimal = round($dataGrid[$i]['stok_minimal']);
            if ($stok_minimal == 0) $stok_minimal = 1;
            if (round($dataGrid[$i]['jumlah_stok']) <= $stok_minimal*1.2) $dataGrid[$i]['class_name'] = 'table-common-warning';
            if (round($dataGrid[$i]['jumlah_stok']) <= $stok_minimal) $dataGrid[$i]['class_name'] = 'table-common-warning';
            
            $this->mrTemplate->AddVars('data_item',$dataGrid[$i],'PB_');
            $this->mrTemplate->parseTemplate('data_item','a');
         }
   }
   
}
?>
