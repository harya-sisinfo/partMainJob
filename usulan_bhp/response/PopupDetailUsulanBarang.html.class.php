<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot') . 'module/'.Dispatcher::Instance()->mModule.'/business/Usulan.class.php';
   
class PopupDetailUsulanBarang extends HtmlResponse
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
      $this->SetTemplateFile('popup_detail_usulan_barang.html');
   }
   
   function ProcessRequest()
   {
      $Obj = new Usulan;
      
      // inisialisasi default value
      $data = $Obj->GetUsulanDetailById($_GET['id']->Integer()->Raw());
      if (!$data) $data = null;
      $return['data'] = $data;
      
      return $return;
   }
   
	function ParseTemplate($data = NULL)
   {
      if ($data == null) return null;
      
      // render data detail
      $detailBarang = $data['data']['detail_barang'];
      unset($data['data']['detail_barang']);

      $this->mrTemplate->AddVars('content', $data['data'], '');
      // ---------
      
      // render list barang
      if (empty($detailBarang))
         $this->mrTemplate->AddVar('data', 'DATA_EMPTY', 'YES');
      else
      {
         $this->mrTemplate->AddVar('data', 'DATA_EMPTY', 'NO');
         foreach ($detailBarang as $key=>$value)
         {
            $total_usulan += $value['usulanBrgNilaiUsulan'];
            $value['usulanBrgNilaiUsulan'] = number_format($value['usulanBrgNilaiUsulan'],0,',','.');
            
            switch ($value['usulanBrgDetStatusVerifikasi']) {
               case 'Belum':
                 $value['usulanBrgDetStatusVerifikasi'] = 'Dalam Proses';
                 break;
               case 'Revisi':
                 $value['usulanBrgDetStatusVerifikasi'] = 'Revisi';
                 break;
               case 'Tidak':
                 $value['usulanBrgDetStatusVerifikasi'] = 'Ditolak';
                 break;
               case 'Ya':
                 $value['usulanBrgDetStatusVerifikasi'] = 'Disetujui';
                 break;
            }
            
            $this->mrTemplate->AddVars('data_item', $value, '');
            $this->mrTemplate->AddVar('data_item', 'NOMOR', $key + 1);
            $this->mrTemplate->AddVar('data_item', 'CLASS_NAME', ($key % 2) ? '' : 'table-common-even');
            
            $this->mrTemplate->parseTemplate('data_item', 'a');
         }
      }
      // ---------
      $this->mrTemplate->AddVar('content', 'TOTAL_PENGADAAN', number_format($total_usulan,0,',','.'));
   }
}
?>
