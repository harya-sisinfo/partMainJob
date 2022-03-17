<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').'module/'.Dispatcher::Instance()->mModule.'/business/RencanaPengeluaran.class.php';

class ViewTableRencanaPengeluaran extends HtmlResponse
{
   function TemplateModule()
   {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application','docroot').'module/'.Dispatcher::Instance()->mModule.'/template');
      $this->SetTemplateFile('view_table_rencana_pengeluaran.html');
   }
   
   function ProcessRequest()
   {
      $Obj = new RencanaPengeluaran();
		
		$dataId = $_GET->AsArray();
      $return['rencana_pengadaan'] = $Obj->GetDataDetil($dataId['dataId']);
      
      foreach($return['rencana_pengadaan'] as $key => $value){
      	$kodeAkun[] = array(
      		'id' => $value['kode'].' - '.$value['deskripsi'],
      		'name' => $value['kode'].' - '.$value['deskripsi']
      	); 
      }
      $return['combo_kode_akun'] = json_encode(array_unique($kodeAkun));
      return $return;
   }
   
	function ParseTemplate($data = NULL)
   {
      if ($data == null) return null;
      
      if (empty($data['rencana_pengadaan']))
		{
			$this->mrTemplate->AddVar('data', 'DATA_EMPTY', 'YES');
			return;
		} else {
			$this->mrTemplate->AddVar('data', 'DATA_EMPTY', 'NO');
			
			$data_json = $data['combo_kode_akun'];
			$data_detil = $data['rencana_pengadaan'];
			$countApprovalYa =0;
			$countApprovalTidak =0;
			$countApprovalBelum =0;

			$this->mrTemplate->AddVar('content', 'COMBO_KODE_AKUN', $data_json);
			
			for ($i=0; $i<sizeof($data_detil); $i++) {
				if(empty($data_detil[$i]['satuan_setuju'])){
					$data_detil[$i]['jumlah_setuju'] = 1*$data_detil[$i]['jumlah_usulan'];
				}else{
					$data_detil[$i]['jumlah_setuju'] = $data_detil[$i]['jumlah_setuju'];
				}
				
				$no += $i;
				$data_detil[$i]['number'] = $no;
				$data_detil[$i]['format_nominal_usulan'] = number_format($data_detil[$i]['nominal_usulan'], 0, ',', '.');
				$data_detil[$i]['format_jumlah_usulan'] = number_format($data_detil[$i]['jumlah_usulan'], 0, ',', '.');
				$data_detil[$i]['format_jumlah_setuju'] = number_format($data_detil[$i]['jumlah_setuju'], 0, ',', '.');
				$data_detil[$i]['format_formula'] = $data_detil[$i]['hasil_formula'];
				$data_detil[$i]['nominal_usulan'] = number_format($data_detil[$i]['nominal_usulan'], 0, ',', '');
				$data_detil[$i]['jumlah_usulan'] = number_format($data_detil[$i]['jumlah_usulan'], 0, ',', '');
				$data_detil[$i]['nominal_setuju'] = number_format($data_detil[$i]['nominal_setuju'], 0, ',', '');

				if ($data_detil[$i]['approval'] == "Ya") {
					$countApprovalYa += 1;
               $data_detil[$i]['class_name'] = 'table-common-even';
               $this->mrTemplate->AddVar('nominal', 'APPROVAL', 'SUDAH');
               $this->mrTemplate->AddVar('nominal', 'DATA_NUMBER', $data_detil[$i]['number'] );
			      $this->mrTemplate->AddVar('nominal', 'DATA_FORMAT_NOMINAL_SETUJU', number_format($data_detil[$i]['nominal_setuju'], 0, ',', '.'));
			      $this->mrTemplate->AddVar('nominal', 'DATA_NOMINAL_SETUJU', $data_detil[$i]['nominal_setuju']);
			      $this->mrTemplate->AddVar('nominal', 'DATA_ID', $data_detil[$i]['id']);
			      $this->mrTemplate->AddVar('satuan', 'DATA_NUMBER', $data_detil[$i]['number'] );
			      $this->mrTemplate->AddVar('satuan', 'DATA_ID', $data_detil[$i]['id']);
					$this->mrTemplate->AddVar('satuan', 'APPROVAL', 'SUDAH');
			      $this->mrTemplate->AddVar('satuan', 'DATA_FORMAT_SATUAN_SETUJU', $data_detil[$i]['satuan_setuju']);
               $this->mrTemplate->AddVar('keterangan', 'APPROVAL', 'SUDAH');
               $this->mrTemplate->AddVar('keterangan', 'DATA_NUMBER', $data_detil[$i]['number'] );
			      $this->mrTemplate->AddVar('keterangan', 'DATA_ID', $data_detil[$i]['id']);
			      $this->mrTemplate->AddVar('keterangan', 'DATA_NUMBER', $no);
			      $this->mrTemplate->AddVar('keterangan', 'DATA_KETERANGAN', $data_detil[$i]['keterangan']);
               
            } elseif ($data_detil[$i]['approval'] == "Tidak") {
			  		$countApprovalTidak += 1;
               $data_detil[$i]['class_name'] = '';
               $this->mrTemplate->AddVar('nominal', 'DATA_NUMBER', $data_detil[$i]['number'] );
               $this->mrTemplate->AddVar('nominal', 'APPROVAL', 'BELUM');
			      $this->mrTemplate->AddVar('nominal', 'DATA_FORMAT_NOMINAL_SETUJU', number_format($data_detil[$i]['nominal_setuju'], 0, ',', '.'));
			      $this->mrTemplate->AddVar('nominal', 'DATA_NOMINAL_SETUJU', number_format($data_detil[$i]['nominal_setuju'], 0, ',', ''));
			      $this->mrTemplate->AddVar('nominal', 'DATA_ID', $data_detil[$i]['id']);
			      $this->mrTemplate->AddVar('satuan', 'DATA_NUMBER', $data_detil[$i]['number'] );
			      $this->mrTemplate->AddVar('satuan', 'DATA_ID', $data_detil[$i]['id']);
				  	$this->mrTemplate->AddVar('satuan', 'APPROVAL', 'BELUM');
    		      $this->mrTemplate->AddVar('satuan', 'DATA_SATUAN_SETUJU', $data_detil[$i]['satuan_setuju']);
			      $this->mrTemplate->AddVar('satuan', 'DATA_FORMAT_SATUAN_SETUJU', $data_detil[$i]['satuan_setuju']);
			      $this->mrTemplate->AddVar('keterangan', 'DATA_NUMBER', $data_detil[$i]['number'] );
               $this->mrTemplate->AddVar('keterangan', 'APPROVAL', 'BELUM');
			      $this->mrTemplate->AddVar('keterangan', 'DATA_ID', $data_detil[$i]['id']);
			      $this->mrTemplate->AddVar('keterangan', 'DATA_NUMBER', $no);
			      $this->mrTemplate->AddVar('keterangan', 'DATA_KETERANGAN', $data_detil[$i]['keterangan']);
			      
            } else {
				  	$countApprovalBelum += 1;
               $data_detil[$i]['class_name'] = 'table-common-even';
      		  	$this->mrTemplate->AddVar('nominal', 'DATA_NUMBER', $data_detil[$i]['number'] );
              	$this->mrTemplate->AddVar('nominal', 'APPROVAL', 'BELUM');
			      $this->mrTemplate->AddVar('nominal', 'DATA_NUMBER', $no);
			      $this->mrTemplate->AddVar('nominal', 'DATA_NOMINAL_SETUJU', $data_detil[$i]['nominal_setuju']);
			      $this->mrTemplate->AddVar('nominal', 'DATA_NOMINAL_USULAN', $data_detil[$i]['nominal_usulan']);
			      $this->mrTemplate->AddVar('nominal', 'DATA_ID', $data_detil[$i]['id']);
               $this->mrTemplate->AddVar('satuan', 'DATA_NUMBER', $data_detil[$i]['number'] );
			      $this->mrTemplate->AddVar('satuan', 'DATA_ID', $data_detil[$i]['id']);
               $this->mrTemplate->AddVar('satuan', 'APPROVAL', 'BELUM');
			      $this->mrTemplate->AddVar('satuan', 'DATA_NUMBER', $no);
			      $this->mrTemplate->AddVar('satuan', 'DATA_SATUAN_SETUJU', empty($data_detil[$i]['satuan_usulan']) ? '1' : $data_detil[$i]['satuan_usulan']);
			      $this->mrTemplate->AddVar('keterangan', 'DATA_NUMBER', $data_detil[$i]['number'] );
			      $this->mrTemplate->AddVar('keterangan', 'APPROVAL', 'BELUM');
			      $this->mrTemplate->AddVar('keterangan', 'DATA_ID', $data_detil[$i]['id']);
			      $this->mrTemplate->AddVar('keterangan', 'DATA_NUMBER', $no);
			      $this->mrTemplate->AddVar('keterangan', 'DATA_KETERANGAN', '-');
            }
				
				if($i == 0) $this->mrTemplate->AddVar('content', 'FIRST_NUMBER', $no);
				if($i == sizeof($data_detil)-1) $this->mrTemplate->AddVar('content', 'LAST_NUMBER', $no);

				$dp  = unserialize($data_detil[$i]['rumus_satuan']);
				if($dp['perhitungan'][0] != 0) $arrStrDP[$i][1] = $dp['perhitungan'][0].' '.$dp['perhitungan'][5];
				if($dp['perhitungan'][1] != 0) $arrStrDP[$i][2] = $dp['perhitungan'][1].' '.$dp['perhitungan'][6];
				if($dp['perhitungan'][2] != 0) $arrStrDP[$i][3] = $dp['perhitungan'][2].' '.$dp['perhitungan'][7];
				if($dp['perhitungan'][3] != 0) $arrStrDP[$i][4] = $dp['perhitungan'][3].' '.$dp['perhitungan'][8];
				if($dp['perhitungan'][4] != 0) $arrStrDP[$i][5] = $dp['perhitungan'][4].' '.$dp['perhitungan'][9];
								
				if(!empty($arrStrDP[$i])){
					$dasarPerhitunganSatuan ='<br />['.implode(' x ',$arrStrDP[$i]).']';
				} else {
					$dasarPerhitunganSatuan ='';
				}
					$data_detil[$i]['rumus_satuan']=$dasarPerhitunganSatuan;
				
				$this->mrTemplate->AddVars('data_items', $data_detil[$i], 'DATA_');
				$this->mrTemplate->parseTemplate('data_items', 'a');	 
			}
			
			if(($countApprovalTidak > 0) || $countApprovalBelum > 0){
				$this->mrTemplate->AddVar('tombol', 'APPROVAL', 'BELUM');
			} 
		}
	}
}
?>
