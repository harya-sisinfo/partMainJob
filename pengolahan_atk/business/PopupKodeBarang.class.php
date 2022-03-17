<?php
class KodeBarang extends Database
{
   protected $mSqlFile= 'module/pengolahan_atk/business/popup_kode_barang.sql.php';

	function __construct($connectionNumber=0)
   {
		parent::__construct($connectionNumber);
	}

	function GetComboGolongan()
   {
		$result = $this->Open($this->mSqlQueries['get_combo_golongan'], array());
      foreach (array_keys($result) as $key)
         if (strpos($result[$key]['name'], 'KIB D:') !== 0)
            $return[] = $result[$key];

      return $return;
	}

	function GetComboJnsBarang()
   {
		$result = $this->Open($this->mSqlQueries['get_combo_jenis_barang'], array());
		return $result;
	}

	function GetComboStnBarang()
   {
		$result = $this->Open($this->mSqlQueries['get_combo_satuan_barang'], array());
		return $result;
	}

   function GetDataJenisCount($data)
   {
      extract ($data);
      if (is_numeric($CARI_GOLONGAN))
         $newQuery = str_replace('%golongan%', 'AND', $this->mSqlQueries['get_data_jenis_count']);
      else $newQuery = str_replace('%golongan%', 'OR', $this->mSqlQueries['get_data_jenis_count']);

      if (is_numeric($CARI_BIDANG_ID))
         $newQuery = str_replace('%bidang%', 'AND', $newQuery);
      else $newQuery = str_replace('%bidang%', 'OR', $newQuery);

      if (is_numeric($CARI_KELOMPOK_ID))
         $newQuery = str_replace('%kelompok%', 'AND', $newQuery);
      else $newQuery = str_replace('%kelompok%', 'OR', $newQuery);

      if (is_numeric($CARI_SUB_KELOMPOK_ID))
         $newQuery = str_replace('%subKelompok%', 'AND', $newQuery);
      else $newQuery = str_replace('%subKelompok%', 'OR', $newQuery);

      if (is_numeric($CARI_JENIS_BARANG))
         $newQuery = str_replace('%jenisBarang%', 'AND', $newQuery);
      else $newQuery = str_replace('%jenisBarang%', 'OR', $newQuery);

      $arg = array
      (
         $CARI_GOLONGAN,
         $CARI_BIDANG_ID,
         $CARI_KELOMPOK_ID,
         $CARI_SUB_KELOMPOK_ID,
         "%$CARI_NAMA_BARANG%",
         "%$CARI_NAMA_BARANG%",
         $CARI_JENIS_BARANG
      );

      $result = $this->Open($newQuery, $arg);
      if (is_array($result))
         return $result[0]['total'];
      else return 0;
   }

   function GetDataJenis($data, $start = 0, $limit = 1000)
   {
      extract ($data);
      if (is_numeric($CARI_GOLONGAN))
         $newQuery = str_replace('%golongan%', 'AND', $this->mSqlQueries['get_data_jenis']);
      else $newQuery = str_replace('%golongan%', 'OR', $this->mSqlQueries['get_data_jenis']);

      if (is_numeric($CARI_BIDANG_ID))
         $newQuery = str_replace('%bidang%', 'AND', $newQuery);
      else $newQuery = str_replace('%bidang%', 'OR', $newQuery);

      if (is_numeric($CARI_KELOMPOK_ID))
         $newQuery = str_replace('%kelompok%', 'AND', $newQuery);
      else $newQuery = str_replace('%kelompok%', 'OR', $newQuery);

      if (is_numeric($CARI_SUB_KELOMPOK_ID))
         $newQuery = str_replace('%subKelompok%', 'AND', $newQuery);
      else $newQuery = str_replace('%subKelompok%', 'OR', $newQuery);

      if (is_numeric($CARI_JENIS_BARANG))
         $newQuery = str_replace('%jenisBarang%', 'AND', $newQuery);
      else $newQuery = str_replace('%jenisBarang%', 'OR', $newQuery);

      $arg = array
      (
         $CARI_GOLONGAN,
         $CARI_BIDANG_ID,
         $CARI_KELOMPOK_ID,
         $CARI_SUB_KELOMPOK_ID,
         "%$CARI_NAMA_BARANG%",
         "%$CARI_NAMA_BARANG%",
         $CARI_JENIS_BARANG,
         $start,
         $limit
      );
      $result = $this->Open($newQuery, $arg);
      if (count($result) == 0) return array();
      foreach ($result as $value)
         $return[$value['idKelompok']][$value['idSubKelompok']][$value['idBarang']][$value['id']] = $value;
      return $return;
   }
	
	function GetDataBarangCount($data)
   {
      extract ($data);
      if (is_numeric($CARI_GOLONGAN))
         $newQuery = str_replace('%golongan%', 'AND', $this->mSqlQueries['get_data_barang_count']);
      else $newQuery = str_replace('%golongan%', 'OR', $this->mSqlQueries['get_data_barang_count']);

      if (is_numeric($CARI_BIDANG_ID))
         $newQuery = str_replace('%bidang%', 'AND', $newQuery);
      else $newQuery = str_replace('%bidang%', 'OR', $newQuery);

      if (is_numeric($CARI_KELOMPOK_ID))
         $newQuery = str_replace('%kelompok%', 'AND', $newQuery);
      else $newQuery = str_replace('%kelompok%', 'OR', $newQuery);

      if (is_numeric($CARI_SUB_KELOMPOK_ID))
         $newQuery = str_replace('%subKelompok%', 'AND', $newQuery);
      else $newQuery = str_replace('%subKelompok%', 'OR', $newQuery);

      if (is_numeric($CARI_JENIS_BARANG))
         $newQuery = str_replace('%jenisBarang%', 'AND', $newQuery);
      else $newQuery = str_replace('%jenisBarang%', 'OR', $newQuery);

      $arg = array
      (
         $CARI_GOLONGAN,
         $CARI_BIDANG_ID,
         $CARI_KELOMPOK_ID,
         $CARI_SUB_KELOMPOK_ID,
         "%$CARI_NAMA_BARANG%",
         "%$CARI_NAMA_BARANG%",
         $CARI_JENIS_BARANG
      );

      $result = $this->Open($newQuery, $arg);
      if (is_array($result))
         return $result[0]['total'];
      else return 0;
   }

   function GetDataBarang($data, $start = 0, $limit = 1000)
   {
      extract ($data);
      if (is_numeric($CARI_GOLONGAN))
         $newQuery = str_replace('%golongan%', 'AND', $this->mSqlQueries['get_data_barang']);
      else $newQuery = str_replace('%golongan%', 'OR', $this->mSqlQueries['get_data_barang']);

      if (is_numeric($CARI_BIDANG_ID))
         $newQuery = str_replace('%bidang%', 'AND', $newQuery);
      else $newQuery = str_replace('%bidang%', 'OR', $newQuery);

      if (is_numeric($CARI_KELOMPOK_ID))
         $newQuery = str_replace('%kelompok%', 'AND', $newQuery);
      else $newQuery = str_replace('%kelompok%', 'OR', $newQuery);

      if (is_numeric($CARI_SUB_KELOMPOK_ID))
         $newQuery = str_replace('%subKelompok%', 'AND', $newQuery);
      else $newQuery = str_replace('%subKelompok%', 'OR', $newQuery);

      if (is_numeric($CARI_JENIS_BARANG))
         $newQuery = str_replace('%jenisBarang%', 'AND', $newQuery);
      else $newQuery = str_replace('%jenisBarang%', 'OR', $newQuery);

      $arg = array
      (
         $CARI_GOLONGAN,
         $CARI_BIDANG_ID,
         $CARI_KELOMPOK_ID,
         $CARI_SUB_KELOMPOK_ID,
         "%$CARI_NAMA_BARANG%",
         "%$CARI_NAMA_BARANG%",
         $CARI_JENIS_BARANG,
         $start,
         $limit
      );
      $result = $this->Open($newQuery, $arg);
      if (count($result) == 0) return array();
      foreach ($result as $value)
         $return[$value['idKelompok']][$value['idSubKelompok']][$value['id']] = $value;
      return $return;
   }

   function GetKelompokList()
   {
		$result = $this->Open($this->mSqlQueries['get_kelompok_list'], array());
      if (count($result) == 0) return array();
      foreach ($result as $value)
         $return[$value['id']] = $value;
      return $return;
   }

   function GetSubKelompokList()
   {
		$result = $this->Open($this->mSqlQueries['get_sub_kelompok_list'], array());
      foreach ($result as $value)
         $return[$value['idKelompok']][$value['id']] = $value;
      return $return;
   }
}
?>