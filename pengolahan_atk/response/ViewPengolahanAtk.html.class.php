<?php
/*
 @ClassName : View Mutasi Barang
 @Copyright : PT Gamatechno Indosnesia
 @Analyzed By : Nanang Ruswianto <nanang@gamatechno.com>
 @Designed By : Rosyid <rosyid@gamatechno.com>
 @Author By : Dyan Galih <galih@gamatechno.com>
 @Version : 1.0
 @StartDate : Okt 24, 2008
 @LastUpdate : Okt 27, 2008
 @Description :
*/

require_once GTFWConfiguration::GetValue( 'application', 'docroot') . 'module/pengolahan_atk/business/AppPengolahanAtk.class.php';

require_once GTFWConfiguration::GetValue( 'application', 'docroot') . 'module/setting/business/Setting.class.php';
//set Label for module
require_once GTFWConfiguration::GetValue( 'application', 'docroot') .
'module/label/response/Label.proc.class.php';
class ViewPengolahanAtk extends HtmlResponse {

   function TemplateModule() {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application','docroot').'module/pengolahan_atk/template');
      $this->SetTemplateFile('view_pengolahan_atk.html');
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

   function ProcessRequest() {
      $Obj = new AppPengolahanAtk();
      $Obj2 = new Setting();
      $GET = $_GET->AsArray();

      //set combo kib
      $arrSetting = $Obj2->GetSetting();
		$arrComboGudang = $Obj->GetComboGudang();
      $arrStockBarang = $Obj->GetStockBarang();

      if (count($_POST) == 0){
         $_POST = array();
         $_POST['gudang'] = 'all';
         $_POST['nama_barang_atk'] = '';
         $_POST['stock_barang'] = 'all';
      }

      // set default pagging
      $limit = 20;
      $page = 0;
      $offset = 0;

      if(isset($_GET['page'])){
         $GET = $_GET->AsArray();
         $page = (string) $_GET['page']->StripHtmlTags()->SqlString()->Raw();
         $offset = ($page - 1) * $limit;
         $_POST['gudang'] = $GET['gudang'];
         $_POST['gdgNama'] = $GET['gdgNama'];
         $_POST['nama_barang_atk'] = $GET['barang'];
         $_POST['stock_barang'] = $GET['stock'];
      }

		$return['search'] = $_POST;
      if(isset($_POST['cari'])||isset($_GET['page'])||!empty($GET['srch'])){
         if(!empty($GET['srch'])){
            $srch = explode('|', $GET['srch']);
            $_POST['gudang'] = $srch[0];
            $_POST['nama_barang_atk'] = $srch[1];
            $_POST['stock_barang'] = $srch[2];
            $_POST['gdgNama'] = $Obj->GetGudangById($srch[0]);
            $return['search'] = array(
               'gudang' => $srch[0],
               'gdgNama' => $_POST['gdgNama'],
               'nama_barang_atk' => $srch[1],
               'stock_barang' => $srch[2]
            );
            $page = $srch[3];
            if(!empty($page)) $offset = ($page-1)*$limit;
         }
         $return['srch'] = $_POST['gudang'].'|'.$_POST['nama_barang_atk'].'|'.$_POST['stock_barang'].'|'.$page;
         $return['data'] = $Obj->GetAtkList($_POST['gudang'],$_POST['nama_barang_atk'],$_POST['stock_barang'],$offset,$limit);
         $numrows = $Obj->GetCount($_POST['gudang'],$_POST['nama_barang_atk'],$_POST['stock_barang']);
      }else $return['notif'] = '-- Klik Pencarian Terlebih Dahulu --';

      Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'stock_barang', array('stock_barang', $arrStockBarang, $_POST['stock_barang'], true, 'class="srch"'), Messenger::CurrentRequest);
      //Messenger::Instance()->SendToComponent('combobox', 'Combobox', 'view', 'html', 'gudang', array('gudang', $arrComboGudang, $_POST['gudang'], true, ''), Messenger::CurrentRequest);

      $url = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule,
         'PengolahanAtk',
         Dispatcher::Instance()->mAction,
         Dispatcher::Instance()->mType).
         '&gudang='.$_POST['gudang'].
         '&gdgNama='.$_POST['gdgNama'].
         '&barang='.$_POST['nama_barang_atk'].
         '&stock='.$_POST['stock_barang']
         ;

      $destination_id = "subcontent-element"; # options: {popup-subcontent,subcontent-element}

      Messenger::Instance()->SendToComponent('paging', 'Paging', 'view', 'html', 'paging_top',
      array($limit,$numrows, $url, $page, $destination_id),
      Messenger::CurrentRequest);

      $return['start'] = $offset+1;
      $return['page'] = $page;
      $return['numrows'] = $numrows;

      return $return;
   }

   function ParseTemplate($data = NULL) {
      
      // message
      if (isset($_GET['err'])) $data['msg'] = $_GET['err']->Raw();
      if(isset($data['msg'])) {
         if($data['msg'] == 'add') {
            $isiPesan = 'Proses Penambahan Data Berhasil';
            $class = 'notebox-done';
         }
         elseif($data['msg'] == 'noadd') {
            $isiPesan = 'Proses Penambahan Data Gagal';
            $class = 'notebox-warning';
         }elseif($data['msg'] == 'update') {
            $isiPesan = 'Proses Perubahan Data Berhasil';
            $class = 'notebox-done';
         }
         elseif($data['msg'] == 'noupdate') {
            $isiPesan = 'Proses Perubahan Data Gagal';
            $class = 'notebox-warning';
         }elseif($data['msg'] == 'del') {
            $isiPesan = 'Data Berhasil Dihapus';
            $class = 'notebox-done';
         }
         elseif($data['msg'] == 'nodel') {
            $isiPesan = 'Data Gagal Dihapus';
            $class = 'notebox-warning';
         }

         $this->mrTemplate->SetAttribute('warning_box', 'visibility', 'visible');
         $this->mrTemplate->AddVar('warning_box', 'ISI_PESAN', $isiPesan);
         $this->mrTemplate->AddVar('warning_box', 'CLASS_NAME', $class);
      }

		$gudang = $data['search']['gudang'];
      $gdgNama = $data['search']['gdgNama'];
		$barang = $data['search']['nama_barang_atk'];
		$stok = $data['search']['stock_barang'];

      //$urlTambah = Dispatcher::Instance()->GetUrl('pengolahan_atk', 'pengolahanAtkInput', 'view', 'html');
      //$this->mrTemplate->addVar('content','URL_TAMBAH',$urlTambah);


      $urlPemeliharaan = Dispatcher::Instance()->GetUrl('pengolahan_atk', 'penambahanAtk', 'view', 'html');
      $this->mrTemplate->addVar('content','URL_PEMELIHARAAN',$urlPemeliharaan.'&srch='.$data['srch']);

      $urlAction = Dispatcher::Instance()->GetUrl('pengolahan_atk', 'pengolahanAtk', 'view', 'html');
      $this->mrTemplate->addVar('content','URL_ACTION',$urlAction);

      $urlPopup = Dispatcher::Instance()->GetUrl('popup', 'Ruang', 'popup', 'html').'&rtype=-1&bhp=1';
      $this->mrTemplate->addVar('content','URL_POPUP_RUANG',$urlPopup);

      $this->mrTemplate->addVar('content','NAMA_BARANG_ATK',$barang);
      $this->mrTemplate->addVar('content','GUDANG',$gudang);
      $this->mrTemplate->addVar('content','GDGNAMA',$gdgNama);

		$urlCetak = Dispatcher::Instance()->GetUrl('pengolahan_atk', 'cetakAtk', 'view', 'html').'&gudang='.$gudang.'&barang='.$barang.'&stok='.$stok;
      $this->mrTemplate->addVar('content','URL_CETAK',$urlCetak);


		//set Label
      $this->TemplateLabeling();

      if(empty($data['data'])){
         $this->mrTemplate->AddVar('data','DATA_EMPTY','YES');
         $notif = (!empty($data['notif']))?$data['notif']:'-- Data Tidak Ditemukan --';
         $this->mrTemplate->AddVar('data','NOTIF',$notif);
         return;
      }else
         $this->mrTemplate->AddVar('data','DATA_EMPTY','NO');

         $dataGrid = $data['data'];

         for($i=0;$i<count($dataGrid);$i++){
            $dataGrid[$i]['no'] = $i+$data['start'];
            $dataGrid[$i]['class_name'] = ($i % 2) ? '' : 'table-common-even';
            /*if (empty($dataGrid[$i]['atk_id'])) $dataGrid[$i]['url_edit'] = $urlTambah;
            else */$dataGrid[$i]['url_edit'] = Dispatcher::Instance()->GetUrl('pengolahan_atk', 'pengolahanAtkEdit', 'view', 'html').'&idEdit='.Dispatcher::Instance()->Encrypt($dataGrid[$i]['atk_id'].'&barangId='.$dataGrid[$i]['barangId'].'&ruangId='.$dataGrid[$i]['ruangId'].'&srch='.$data['srch']);
            $dataGrid[$i]['url_log'] = Dispatcher::Instance()->GetUrl('pengolahan_atk', 'logList', 'view', 'html').'&barangId='.$dataGrid[$i]['barangId'].'&ruangId='.$dataGrid[$i]['ruangId'].'&srch='.$data['srch'];
            if($dataGrid[$i]['jumlah_stok']<=0){
					$dataGrid[$i]['display'] = 'none';				
				}
				$dataGrid[$i]['url_penyesuaian'] = Dispatcher::Instance()->GetUrl('pengolahan_atk', 'penyesuaianStok', 'view', 'html').'&barangId='.$dataGrid[$i]['barangId'].'&ruangId='.$dataGrid[$i]['ruangId'].'&srch='.$data['srch'];

            $stok_minimal = round($dataGrid[$i]['stok_minimal']);
            if ($stok_minimal == 0) $stok_minimal = 1;
            //if (round($dataGrid[$i]['jumlah_stok']) <= $stok_minimal*1.2) $dataGrid[$i]['class_name'] = 'table-common-warning';
            if (round($dataGrid[$i]['jumlah_stok']) <= $stok_minimal) $dataGrid[$i]['class_name'] = 'table-common-warning';
            //$this->mrTemplate->AddVars('data_item',$dataGrid[$i],'PB_');
				if(is_numeric($dataGrid[$i]['stok_minimal'])){
					$dataGrid[$i]['stok_minimal'] = number_format($dataGrid[$i]['stok_minimal']);
            }
				
				if( is_numeric($dataGrid[$i]['jumlah_stok'])){
					$dataGrid[$i]['jumlah_stok'] = number_format($dataGrid[$i]['jumlah_stok'],0,',','.');
				}
				
				$this->mrTemplate->AddVars('data_item',$dataGrid[$i],'PB_');
            $this->mrTemplate->parseTemplate('data_item','a');
         }

   }
}
?>
