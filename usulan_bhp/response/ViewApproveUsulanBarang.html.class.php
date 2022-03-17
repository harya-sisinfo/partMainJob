<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot') . 'module/'.Dispatcher::Instance()->mModule.'/business/Usulan.class.php';
   
class ViewApproveUsulanBarang extends HtmlResponse
{
   function TemplateModule()
   {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application','docroot').'module/'.Dispatcher::Instance()->mModule.'/template');
      $this->SetTemplateFile('view_approve_usulan_barang.html');
   }
   
   function ProcessRequest()
   {
      $Obj = new Usulan;
      
      // inisialisasi default value
      $data = $Obj->GetUsulanDetailById($_GET['id']->Integer()->Raw());
      if (!$data) $data = null;
      $return['data'] = $data;
      $comboApproval = array(
          array(
              'id' => 'Ya',
              'name' => 'Ya'
            ),
          array(
              'id' => 'Tidak',
              'name' => 'Tidak'
            )
        );

    Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'statusApproval', array('statusApproval', $comboApproval, $data['usulanBrgIsApprove'], '', ''), Messenger::CurrentRequest);

      $return['url'] = array(
          'action' => Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'usulanApprove', 'do', 'html'),
          'back' => Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'listUsulanBarang', 'view', 'html')
        );
      return $return;
   }
   
	function ParseTemplate($data = NULL)
   {
      if ($data == null) return null;
      
      // render data detail
      $detailBarang = $data['data']['detail_barang'];
      unset($data['data']['detail_barang']);

      $this->mrTemplate->AddVars('content', $data['url'], 'URL_');
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
