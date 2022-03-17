<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').
   'module/pengelolaan_bhp/business/Pengelolaan.class.php';

require_once GTFWConfiguration::GetValue('application', 'docroot') . 'main/function/date.php';

class PopupDetilPengelolaan extends HtmlResponse {
   
	function TemplateModule()
   {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application','docroot').
         'module/pengelolaan_bhp/template');
      $this->SetTemplateFile('popup_detil_pengelolaan.html');
   }
   
   function ProcessRequest()
   {
   	$Obj = new Pengelolaan();
   	
      // inisialisasi header
      $data = $Obj->GetDataPengelolaanById($_GET['id']);
      $return['data'] = $data;
      // ---------
      return $return;
   }
   
	function ParseTemplate($data = NULL)
   {  
		$dataForm = $data['data'][0];
  
		// render header
		$dataForm['trans_tanggal'] = IndonesianDate($dataForm['trans_tanggal'], 'yyyy-mm-dd');
      $this->mrTemplate->AddVars('content', $dataForm, '');
      // ---------
      
      // Render data grid
		if (empty($data['data']))
		{
			$this->mrTemplate->AddVar('data', 'DATA_EMPTY', 'YES');
			return;
		}
			else $this->mrTemplate->AddVar('data', 'DATA_EMPTY', 'NO');
      
		$i = 1;
      foreach ($data['data'] as $value)
      {
		$value['nomor'] = $i;
		
		$totalBiaya +=$value['total'];
		$value['lelangBiaya']=number_format($value['lelangBiaya'], 2, ',', '.');
		$value['total']=number_format($value['total'], 2, ',', '.');
         $this->mrTemplate->AddVars('data_item', $value, '');
         $this->mrTemplate->parseTemplate('data_item', 'a');
         
         $i++;
      }
      // ---------
		$this->mrTemplate->AddVar('data', 'TOTAL_BIAYA', number_format($totalBiaya, 2, ', ', '.'));
   }
}
?>
