<?php
class Usulan extends Database
{

   protected $mSqlFile;
   public $mFilePath = 'file/';

   function __construct ($connectionNumber=0) {
   		$this->mSqlFile = 'module/'.Dispatcher::Instance()->mModule.'/business/Usulan.sql.php';
   		parent::__construct($connectionNumber);
		#$this->SetDebugOn();
   }

	function GetComboStatusVerifikasi() {
      $comboStatus = array(
      	array(
      		'id' => 'Tidak', //'Belum',
      		'name' => 'Dalam Proses'
      	),/*
      	array(
      		'id' => 'Revisi',
      		'name' => 'Revisi'
      	),
      	array(
      		'id' => 'Tidak',
      		'name' => 'Ditolak'
      	),*/
      	array(
      		'id' => 'Ya',
      		'name' => 'Disetujui'
      	)
      );
      return $comboStatus;
   }	

   function GetDataPeriode()
   {
      $result = $this->Open($this->mSqlQueries['get_data_periode'], array());
      return $result;
   }
   
   function GetDataJenisPengadaan()
   {
      $result = $this->Open($this->mSqlQueries['get_data_jenis_pengadaan'], array());
      return $result;
   }

   function GetDataJenisBarang()
   {
      $result = $this->Open($this->mSqlQueries['get_data_jenis_barang'], array());
      return $result;
   }
   
   function GetDataUserById ($userId)
   {
      $result = $this->Open($this->mSqlQueries['get_data_user_by_id'], array($userId));
      return $result[0];
   }

   function GetDataUserByUsername ($name)
   {
      $result = $this->Open($this->mSqlQueries['get_data_user_by_username'], array($name));
      return $result[0];
   }

   # multiunit
   function GetComboUnit ()
   {
      $User = $this->GetDataUserByUsername($_SESSION['username']);      
      $array = array(
      	 $User['unitkerjaKodeSistem'],
         $User['unitkerjaKodeSistem'].'.%'
      );
      return $this->Open($this->mSqlQueries['get_combo_unit'],  $array);
   }

   function GetDataGridCount()
   {
      $result = $this->Open($this->mSqlQueries['get_data_grid_count'], array());
      if(empty($result)){
      	$result = 0;
      } else {
      	$result = $result[0]['total'];
      }
      return $result;
   }

   # multiunit
   function GetDataGrid ($filter, $start = NULL, $limit = NULL)
   {
      $User = $this->GetDataUserByUsername($_SESSION['username']);      

      extract($filter);
      $tglUsulanAwal = ($tglUsulanAwal) ? date('Y-m-d', $tglUsulanAwal) : '';
      $tglUsulanAkhir = ($tglUsulanAkhir) ? date('Y-m-d',$tglUsulanAkhir ) : '';
      if (!$unit) $unit = 'all';

      $arg = array
      (
         $tglUsulanAwal,$tglUsulanAwal,
         $tglUsulanAkhir,$tglUsulanAkhir,
         $unit, $unit,
         "%$kode%",
         /*$User['unit_kerja_id'], $User['unit_kerja_id'], $User['role_id'],*/
      	 $User['unitkerjaKodeSistem'],
      	 $User['unitkerjaKodeSistem'].'.%',
         $status, $status/*,
         $periode, $periode*/
      );

      $sql = $this->mSqlQueries['get_data_grid'];
      if ($start == NULL && $limit == NULL)
         $sql = str_replace('%LIMIT%', '', $sql);
      else
      {
         $sql = str_replace('%LIMIT%', 'LIMIT %s, %s', $sql);
         array_push($arg, $start, $limit);
      }

      $result = $this->Open($sql, $arg);

      return $result;
   }

   function GetUsulanById ($id)
   {
      $result = $this->Open($this->mSqlQueries['get_data_by_id'], array($id));
      if (empty($result)) return false;
      $return = $result[0];
      $return['usulanBrgTglUsulan'] = strtotime($return['usulanBrgTglUsulan']);
      $return['tglUsulan1Display'] = date('d F Y', $return['usulanBrgTglUsulan']);

      // get detail barang list
      $result = $this->Open($this->mSqlQueries['get_update_detail_barang'], array($id));
      if (!empty($result)) foreach ($result as $key=>$value){
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
         $return['detail_barang']["item_$key"] = $value;
      }
         
      // ---------

      return $return;
   }

   function GetUsulanDetailById ($id)
   {
      $result = $this->Open($this->mSqlQueries['get_detail_by_id'], array($id));
      if (empty($result)) return false;
      $return = $result[0];

      $result = $this->Open($this->mSqlQueries['get_detail_barang'], array($id));
      if (!empty($result)) $return['detail_barang'] = $result;
      // ---------

      return $return;
   }

   function GetLastMstId ()
   {
      $result = $this->Open($this->mSqlQueries['get_last_mst_id'], array());
      return (int) $result[0]['id'];
   }

   function GetListBarangToApprove ($brgId)
   {
      $result = $this->Open($this->mSqlQueries['get_list_barang_to_approve'], array($brgId));
      if (empty($result)) return array();

      $return = array();
      foreach ($result as $value)
      {
         $value['usulanBrgMstTglUsulan'] = date('d F Y', strtotime($value['usulanBrgMstTglUsulan']));
         $value['hargaLama'] = $value['usulanBrgTotal1'] / $value['usulanBrgDetJml'];
         $value['hargaBaru'] = $value['usulanBrgTotal2'] / $value['usulanBrgDetJml'];
         $return[$value['id']] = $value;
      }
      return $return;
   }

   function GetDataGridForPrint ($id)
   {
      if (!is_array($id)) {$id = array($id); $sql = $this->mSqlQueries['get_data_grid_for_print'];}
      else $sql = str_replace('%s', implode(',', array_fill(0, count($id), "%s")), $this->mSqlQueries['get_data_grid_for_print']);
      $result = $this->Open($sql, $id);
      if ($result == false) return array('dataGrid'=>array(), 'barangUsulan'=>array());
      $return['dataGrid'] = $result;

      $sql = str_replace('%s', implode(',', array_fill(0, count($id), "%s")), $this->mSqlQueries['get_data_barang_usulan_for_print']);
      $result = $this->Open($sql, $id);
      if ($result !== false) foreach ($result as $value)
         $return['barangUsulan'][$value['usulanBrgDetMstId']][] = $value;
      else $return['barangUsulan'] = array();

      return $return;
   }

	function GetDataGridForPrintRtf ()
   {
      $result = $this->Open($this->mSqlQueries['get_data_grid_for_print_rtf'], array());
      return $result;
   }

   /////////
   // Do Query
   /////////

   function Add ($data)
   {
      $this->StartTrans();
      extract($data);
      
      $userId = Security::Instance()->mAuthentication->GetCurrentUser()->GetUserId();

      $periode = ($periode)?$periode:null;
      $arg = array
      (
         date('Y-m-d', $usulanBrgTglUsulan),
         $usulanBrgNoUsulan,
         $usulanBrgUnitId,
         $userId
      );

      if (!$this->Execute($this->mSqlQueries['add_usulan'], $arg))
      {
         $this->EndTrans(false);
         return 1;
      }
	  
      $mstId = $this->LastInsertId();
      $fileUpload = 'usulan-'.date('Ymd').'.zip';
      if (!empty($detail_barang)) foreach ($detail_barang as $value)
      {
         extract($value);

         $arg = array
         (
            $mstId,
            $usulanBrgDetBrgId,
            $usulanBrgDetJml,
            ($brgHps*$usulanBrgDetJml),
            trim($spesifikasi),
            $brgKode, /*akun dipakai untuk Kode barang*/
            /*$usulanBrgDetJenisPengadaanId,*/
            $usulanBrgDetBrgId,
            $satuan,
            $brgHps,
            $fileUpload,
         	$tglPakai,
            '',
            $userId
         );

         if (!$this->Execute($this->mSqlQueries['add_detail_barang'], $arg))
         {
            $this->EndTrans(false);
            exit;
            return 2;
         }
      }
      
      $this->EndTrans(true);
      return 0;
   }

   function Update($data, $mstId)
   {
      $this->StartTrans();
      extract($data);
      
      $userId = Security::Instance()->mAuthentication->GetCurrentUser()->GetUserId();

      $periode = ($periode)?$periode:null;
      $arg = array
      (
         date('Y-m-d', $usulanBrgTglUsulan),
         $usulanBrgNoUsulan,
         $usulanBrgUnitId,
         $userId,
         $mstId
      );
      
      if (!$this->Execute($this->mSqlQueries['update_usulan'], $arg))
      {
         $this->EndTrans(false);
         return 1;
      }
      
      if (!$this->Execute($this->mSqlQueries['delete_detail_barang'], array($mstId)))
      {
         $this->EndTrans(false);
         return 2;
      }

      $fileUpload = 'usulan-'.date('Ymd').'.zip';
      if (!empty($detail_barang)) foreach ($detail_barang as $value)
      {
         extract($value);

//          switch ($usulanBrgDetStatusVerifikasi) {
//             case 'Dalam Proses':
//               $usulanBrgDetStatusVerifikasi = 'Belum';
//               break;
//             case 'Revisi':
//               $usulanBrgDetStatusVerifikasi = 'Revisi';
//               break;
//             case 'Ditolak':
//               $usulanBrgDetStatusVerifikasi = 'Tidak';
//               break;
//             case 'Disetujui':
//               $usulanBrgDetStatusVerifikasi = 'Ya';
//               break;
//          }

         //if($usulanBrgDetStatusVerifikasi != 'Tidak' AND $usulanBrgDetStatusVerifikasi != 'Ya'){
            $arg = array
            (
               $mstId,
               $usulanBrgDetBrgId,
               $usulanBrgDetJml,
               ($brgHps*$usulanBrgDetJml),
               trim($spesifikasi),
               $brgKode,
               /*$usulanBrgDetJenisPengadaanId,
               $usulanBrgDetBrgKelompokId,*/
               $usulanBrgDetBrgId,
               $satuan,
               $brgHps,
               $fileUpload,
               $tglPakai,	
               '',
               $userId
            );

            if (!$this->Execute($this->mSqlQueries['add_detail_barang'], $arg))
            {
               $this->EndTrans(false);
               return 3;
            }
         //}
      }

      $this->EndTrans(true);
      return 0;
   }

   function Delete ($id)
   {
      $sql = str_replace('%VALUE%', implode(',', array_fill(0, count($id), "%s")), $this->mSqlQueries['delete_usulan']);
      if ($this->Execute($sql, $id)) return 0;

      // delete one by one
      $return = 0;
      $sql = str_replace('%VALUE%', '%s', $this->mSqlQueries['delete_usulan']);
      foreach ($id as $value) if (!$this->Execute($sql, array($value))) $return++;
      return $return;
   }

   function Approval ($data)
   {
      extract($data);
      $userId = Security::Instance()->mAuthentication->GetCurrentUser()->GetUserId();
      
      $this->StartTrans();
      $arg = array
      (
         $statusApproval,
         $userId,
         $usulanBrgId
      );

      if (!$this->Execute($this->mSqlQueries['update_approval'], $arg))
      {
          $this->EndTrans(false);
         return 1;
      }

      $arg2 = array
      (
         $userId,
         $usulanBrgId
      );

      if (!$this->Execute($this->mSqlQueries['update_approval_detil'], $arg2))
      {
         $this->EndTrans(false);
         return 2;
      }

      $this->EndTrans(true);
      return 0;
   }
}
?>
