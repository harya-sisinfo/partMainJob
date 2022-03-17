<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').
   'module/pengadaan/business/Komponen.class.php';

//set Label for module
require_once GTFWConfiguration::GetValue( 'application', 'docroot') .
'module/label/response/Label.proc.class.php';

class ViewKomponenInput extends HtmlResponse
{
   function TemplateModule()
   {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application','docroot').
         'module/pengadaan/template');
      $this->SetTemplateFile('view_komponen_input.html');
   }
   
   function ProcessRequest()
   {
      $Obj = new Komponen;
      
      // inisialisasi messaging
		$msg = Messenger::Instance()->Receive(__FILE__);
      $this->Data = $msg[0][0];
		$this->Pesan = $msg[0][1];
		$this->css = $msg[0][2];
      // ---------
      
      // inisialisasi default value
      $data = array();
      if (is_array($this->Data))
         $data = $this->Data;
      elseif (isset($_GET['id']))
      {
         $result = $Obj->GetPengadaanDetById($_GET['id']->Integer()->Raw());
         if ($result === false) unset($_GET['id']);
         else $data = $result;
      }
      
      $return['data'] = $data;
      // ---------
      
      // render combo box
      $KIB = $Obj->GetCbxJenisKib();
      $tmp = $Obj->GetPengadaanDetail();
      if ($tmp['jenis_pengadaan'] == 'BHP') $KIB = array(end($KIB));
      else array_pop($KIB);
      Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'jenis_kib', array('golbrgId', $KIB, $data['golbrgId'], false, 'id="jenisKib"'), Messenger::CurrentRequest);
      
      $satuanBarang = $Obj->GetCbxSatuan();
      Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'satuan_barang', array('pengadaanDetSatuan', $satuanBarang, $data['pengadaanDetSatuan'], false, 'id="pop_up_satuan_barang_name"'), Messenger::CurrentRequest);
      
      $pagar = $Obj->GetCbxPagar();
      Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'pagar', array('pengadaanDetPagar', $pagar, $data['pengadaanDetPagar'], false, ''), Messenger::CurrentRequest);
      
      $sifatBangunan = $Obj->GetCbxSifatBangunan();
      Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'sifat_bangunan', array('pengadaanDetSifatBangunan', $sifatBangunan, $data['pengadaanDetSifatBangunan'], false, ''), Messenger::CurrentRequest);
      
      $IMB = $Obj->GetCbxIMB();
      Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'IMB', array('pengadaanDetIMB', $IMB, $data['pengadaanDetIMB'], false, ''), Messenger::CurrentRequest);
      
      $kepemilikan = $Obj->GetCbxKepemilikan();
      Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'kepemilikan', array('pengadaanDetPmlkBrg', $kepemilikan, $data['pengadaanDetPmlkBrg'], false, ''), Messenger::CurrentRequest);
      
      $kepenguasaan = $Obj->GetCbxPenguasaanBarang();
      Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'kepenguasaan', array('pengadaanDetPenguasaan', $kepenguasaan, $data['pengadaanDetPenguasaan'], false, ''), Messenger::CurrentRequest);
      
      $kondisiBarang = $Obj->GetCbxKondisi();
      Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'kondisi_barang', array('pengadaanDetKondisi', $kondisiBarang, $data['pengadaanDetKondisi'], false, ''), Messenger::CurrentRequest);
      // ---------
      
      // inisialisasi link address
      $return['link']['url_action'] = Dispatcher::Instance()->GetUrl('pengadaan', 'komponenInput', 'do', 'html');
      if (isset($_GET['id'])) $return['link']['url_action'] .= '&id='.$_GET['id']->Integer()->Raw();
      $return['link']['url_pop_up_kode_aset'] = Dispatcher::Instance()->GetUrl('inventarisasi', 'popUpKodefikasi', 'view', 'html');
      // ---------
      
      $return['list_biaya_lain'] = $Obj->GetListBiayaLain();
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
      if ($data == null) return null;
      
      // set Label
      $this->TemplateLabeling();
      $this->mrTemplate->AddVar('content', 'JUDUL', (isset($_GET['id']))?'Ubah':'Tambah');
      // ---------
      
      // render message box
      if($this->Pesan)
      {
         $msg = '';
         if (count($this->Pesan) > 1) foreach ($this->Pesan as $value)
            $msg .= "\t$value<br/>\n";
         else $msg .= $this->Pesan[0];
         
			$this->mrTemplate->SetAttribute('warning_box', 'visibility', 'visible');
			$this->mrTemplate->AddVar('warning_box', 'ISI_PESAN', $msg);
			$this->mrTemplate->AddVar('warning_box', 'CLASS_PESAN', $this->css);
		}
      // ---------
      
      // render link
      $this->mrTemplate->AddVars('content', $data['link'], '');
      // ---------
      
      // render form default value
      $biayaLain = $data['data']['biaya_lain'];
      unset($data['data']['biaya_lain']);
      $this->mrTemplate->AddVars('content', $data['data'], '');
      // ---------
      
      // render biaya lain
      if (empty($data['list_biaya_lain']))
         $this->mrTemplate->AddVar('biaya_lain', 'BIAYA_LAIN_EMPTY', 'YES');
      else
      {
         $this->mrTemplate->AddVar('biaya_lain', 'BIAYA_LAIN_EMPTY', 'NO');
         foreach ($data['list_biaya_lain'] as $value)
         {
            $this->mrTemplate->AddVar('biaya_item', 'BIAYAID', $value['id']);
            $this->mrTemplate->AddVar('biaya_item', 'BIAYANAME', $value['name']);
            $this->mrTemplate->AddVar('biaya_item', 'BIAYANILAI', $biayaLain[$value['id']]);
            $this->mrTemplate->parseTemplate('biaya_item', 'a');
         }
      }
      // ---------
   }
}
?>