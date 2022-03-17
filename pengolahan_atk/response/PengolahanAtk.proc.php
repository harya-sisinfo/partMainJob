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

class ProcPengolahanAtk
{
	function __construct ()
   {
      $this->pageView = Dispatcher::Instance()->GetUrl('pengolahan_atk', 'PengolahanAtk', 'view', 'html');
   }
   
	function deletePengolahanAtk(){
		return Dispatcher::Instance()->GetUrl('pengolahan_atk', 'PengolahanAtk', 'view', 'html');
	}
	
	function editPengolahanAtk(){
      if ($_POST['cari'] == 'Batal') return $this->pageView;
      
		$stok_minimal = $_POST['stok_minimal'];
		$keterangan = $_POST['keterangan'];
		$atkId = $_POST['id_atk'];
		$obj = new AppPengolahanAtk();
		$result = $obj->UpdateSettingAtk($atkId, $stok_minimal, $keterangan);
		
		if($result)
			$addParam = '&err=' . Dispatcher::Instance()->Encrypt('update');
		else
			$addParam = '&err=' . Dispatcher::Instance()->Encrypt('noupdate');
			
			return $this->pageView . $addParam;
	}
	
	function addPengolahanAtk(){
      if ($_POST['cari'] == 'Batal') return $this->pageView;
		
		$stok_minimal = $_POST['stok_minimal'];
		$keterangan = $_POST['keterangan'];
		$barang_id = $_POST['barang_id'];
		$gudang = $_POST['gudang'];
				
		$obj = new AppPengolahanAtk();
		
		$result = $obj->AddSettingStokAtk($barang_id,$gudang,$stok_minimal, $keterangan);
		
		if($result)
			$addParam = '&err=' . Dispatcher::Instance()->Encrypt('add');
		else
			$addParam = '&err=' . Dispatcher::Instance()->Encrypt('noadd');
			
			return Dispatcher::Instance()->GetUrl('pengolahan_atk', 'PengolahanAtk', 'view', 'html').$addParam;
	}
}
?>
