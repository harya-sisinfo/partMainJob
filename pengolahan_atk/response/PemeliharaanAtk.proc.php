<?php
/*
 @ClassName : Proc Mutasi Barang
 @Copyright : PT Gamatechno Indonesia
 @Analyzed By : Nanang Ruswianto <nanang@gamatechno.com>
 @Designed By : Rosyid <rosyid@gamatechno.com>
 @Author By : Dyan Galih <galih@gamatechno.com>
 @Version : 1.0
 @StartDate : Okt 20, 2008
 @LastUpdate : Okt 20, 2008
 @Description :
*/


require_once GTFWConfiguration::GetValue( 'application', 'docroot').
   'module/pengolahan_atk/business/AppPengolahanAtk.class.php';

class ProcPemeliharaanAtk
{
	
	function addPemeliharaanAtk($page='html'){
		
      $_POST = $_POST->AsArray();
      $_POST['jumlah_barang'] = (int) $_POST['jumlah_barang'];
      $obj = new AppPengolahanAtk();
      $data = $obj->GetAtkByBarcode($_POST['barcode'],$_POST['gudang']);
      
      $msg = array();
      if ($_POST['kode_barang'] == '' || $_POST['gudang'] == '') $msg[] = 'Belum ada barang yang dipilih! Isi barcode dengan benar kemuadian pilih gudang dan ';
      elseif ($_POST['jumlah_barang'] == 0) $msg[] = 'Harap isi jumlah barang!';
      elseif ($data['sisa_barang'] != $_POST['jumlah_barang']+$_POST['sisa_barang'])
      {
         $msg[] = "Ada perubahan data di database, mohon diisi ulang.";
         if ($_POST['jumlah_barang'] > $data['sisa_barang']) $_POST['jumlah_barang'] = $data['sisa_barang'];
		}
      
      if (isset($_POST['btnbalik'])) $result = 'balik';
      elseif (!empty($msg))
      {
         $_POST['sisa_barang'] = $data['sisa_barang'];
         $msg = array($_POST, $msg, 'notebox-alert');
         Messenger::Instance()->Send(Dispatcher::Instance()->mModule, 'pemeliharaanAtk', 'view', 'html', $msg, Messenger::NextRequest);
         return Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'pemeliharaanAtk', 'view', 'html');
      }
      else $result = $obj->AddPemeliharaanAtk($_POST);
		
      if ($result === 'balik') $addParam = '';
		elseif($result) $addParam = '&err=' . Dispatcher::Instance()->Encrypt('add');
		else $addParam = '&err=' . Dispatcher::Instance()->Encrypt('noadd');
		return Dispatcher::Instance()->GetUrl('pengolahan_atk', 'pengolahanAtk', 'view', 'html').$addParam;
	}
	
	function update(){
		
	}
}
?>
