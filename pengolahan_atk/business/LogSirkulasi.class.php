<?php
class LogSirkulasi extends Database
{

	protected $mSqlFile;
   public $mBarangId;
   public $mRuangId;
   
	function __construct($connectionNumber=0)
   {
      $this->mSqlFile = 'module/'.Dispatcher::Instance()->mModule.'/business/LogSirkulasi.sql.php';
      $this->mBarangId = (int) $_SESSION['mod=pengolahanAtk']['barangId'];
      $this->mRuangId = (int) $_SESSION['mod=pengolahanAtk']['ruangId'];
		parent::__construct($connectionNumber);
      //$this->setDebugOn();
	}
   
   function ConvertCombo($combo)
   {
      foreach ($combo as $value) $return[$value['id']] = $value['name'];
      return $return;
   }
   
   // combo box
   function GetComboUnitKerja()
   {
      $result = $this->Open($this->mSqlQueries['get_combo_unit_kerja'], array());
      return $result;
   }
   
   function GetComboJenisLog()
   {
      $result = $this->Open($this->mSqlQueries['get_combo_jenis_log'], array());
      $array = eval('return '.str_replace('enum', 'array', $result[0]['Type']).';');
      foreach ($array as $key=>$val) $JnsLog[$key]['id'] = $JnsLog[$key]['name'] = $val;
      return $JnsLog;
   }
   
   function GetComboGudang() {
      return $this->Open($this->mSqlQueries['get_combo_gudang'], array());
   }
   // ---------
   
   function GetAtkDetail()
   {
      $result = $this->Open($this->mSqlQueries['get_atk_detail'], array($this->mBarangId, $this->mRuangId));
      return $result[0];
   }
   
   function GetLogListCount ($filter)
   {
      extract($filter);
      $arg = array
      (
         $this->mBarangId,
         $this->mRuangId,
         $logAtkStatus, $logAtkStatus,
         $logAtkUnitId, $logAtkUnitId,
         $logAtkTujuanRuangId, $logAtkTujuanRuangId,
         $tglAwal,$tglAkhir
      );
      
      $result = $this->Open($this->mSqlQueries['get_log_list_count'], $arg);
      return $result[0]['total'];
   }
   
   function GetLogList ($filter, $start, $limit)
   {
      extract($filter);
      $arg = array
      (
         $this->mBarangId,
         $this->mRuangId,
         $logAtkStatus, $logAtkStatus,
         $logAtkUnitId, $logAtkUnitId,
         $logAtkTujuanRuangId, $logAtkTujuanRuangId,
         $tglAwal,$tglAkhir,
         $start, $limit
      );
      
      $result = $this->Open($this->mSqlQueries['get_log_list'], $arg);
      foreach (array_keys($result) as $key)
         $result[$key]['logAtkTgl'] = date('d F Y', strtotime($result[$key]['logAtkTgl']));
      return $result;
   }
   
   function GetDataLogByIdForInput ($id)
   {
      $data = $this->GetAtkDetail();
      $result = $this->Open($this->mSqlQueries['get_data_log_by_id_for_input'], array($id));
      $result[0]['logAtkTgl'] = date('d F Y', strtotime($result[0]['logAtkTgl']));
      return $data + $result[0];
   }
   
   /////////
   // Do Function
   /////////
   
   function Update($data)
   {
      require_once 'AppPengolahanAtk.class.php';
      $Obj = new AppPengolahanAtk;
      extract($data);
      
      $jumlah_barang = $logAtkJmlBrg - $logAtkJmlBrgOld;
      extract($this->GetDataLogByIdForInput($id));
      
      $data = array
      (
         'gudang' => $logAtkRuangId,
         'barcode' => $invAtkDetBarcode,
         'kode_barang' => '',
         'nama_barang' => '',
         'label_barang' => '',
         'merk_barang' => '',
         'jumlah_barang' => $jumlah_barang,
         'sisa_barang' => '',
         'spesifikasi' => '',
         'status_pemeliharaan' => $logAtkStatus,
         'unit' => $logAtkUnitId,
         'gudang_tujuan' => $logAtkTujuanRuangId,
         'tanggal_mutasi_day' => '',
         'tanggal_mutasi_mon' => '',
         'tanggal_mutasi_year' => '',
         'keterangan' => "Penyesuaian data untuk logAtkId $id.",
         'logAtkFile' => '',
      );
      
      return $Obj->AddPemeliharaanAtk($data);
   }
   
   function Delete($id)
   {
      return pow(2, 24);
      if (!is_array($id)) $id = array($id);
      $sql1 = str_replace('%s', implode(',', array_fill(0, count($id), "%s")), $this->mSqlQueries['update_log_sirkulasi_before_delete']);
      $sql2 = $this->mSqlQueries['delete_log_sirkulasi'];
      
      $this->StartTrans();
      if ($this->Execute($sql1, $id) AND $this->Execute($sql2, array()))
      {
         $affectedRows = $this->Affected_Rows();
         $this->EndTrans(true);
         return count($id) - $affectedRows;
      }
      $this->EndTrans(false);
      
      // delete one by one
      $return = 0;
      $sql1 = $this->mSqlQueries['update_log_sirkulasi_before_delete'];
      foreach ($id as $value)
      {
         if (!$this->Execute($sql1, array($value))) {$return++; continue;}
         $this->Execute($sql2, array());
         if (!$this->Affected_Rows()) $return++;
      }
      return $return;
   }
}
?>
