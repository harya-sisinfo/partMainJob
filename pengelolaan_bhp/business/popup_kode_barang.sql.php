<?php
$sql['get_user_kode_sistem']="
SELECT unitkerjaKodeSistem
FROM user_unit_kerja
JOIN unit_kerja_ref ON unitkerjaId = userunitkerjaUnitkerjaId
WHERE userunitkerjaUserId = '%s'
";

$sql['get_combo_golongan'] = 
   "SELECT 
     `golbrgId` as `id`,
	  `golbrgNama` as `name`
   FROM 
      `golongan_barang_ref` 
	WHERE `golbrgId` IN ('1', '2')
   ORDER BY `golbrgNama`
";

$sql['get_combo_jenis_barang'] = 
   "SELECT 
     `jenisbrgId` as `id`,
	  `jenisbrgNama` as `name`
   FROM 
      `jenis_barang_ref`
   ORDER BY `jenisbrgNama`
";

$sql['get_combo_satuan_barang'] = 
   "SELECT 
     `satuanbrgId` as `id`,
	  `satuanbrgNama` as `name`
   FROM 
      `satuan_barang_ref`
   ORDER BY `satuanbrgNama`
";

$sql['get_data_jenis_count'] =
   ("
      SELECT 
         COUNT(`invAtkNama`) as `total`
      FROM 
         `golongan_barang_ref`
         JOIN `bidang_barang_ref` ON (`bidangbrgGolbrgId` = `golbrgId`)
         JOIN `kelompok_barang_ref` ON (`kelbrgBidangbrgId` = `bidangbrgId`)
         JOIN `sub_kelompok_barang_ref` ON (`subkelbrgKelbrgId` = `kelbrgId`)
         JOIN `barang_ref` ON (`barangSubkelbrgId` = `subkelbrgId`)
			JOIN `inv_atk_jenis_ref` ON (invAtkJenisBarangRefId = barangId)
         LEFT JOIN inv_atk_master
            ON invAtkMstJenisPersediaanId = invAtkJenisRefId
        LEFT JOIN inv_atk_det
          ON invAtkDetMstId = invAtkMstId
        LEFT JOIN inv_atk_brg
          ON invAtkDetId = invDet
        LEFT JOIN inv_atk_gudang
          ON invAtkGudangInvDtlbrgId = invBrgAtkId
        LEFT JOIN ruang
          ON ruangId = invAtkGudangRuangId
      WHERE invAtkGudangJumlah != ''
          AND
         (`golbrgId` = %d %golongan% 1) AND
         (`bidangbrgId`  = %d %bidang% 1) AND
         (`kelbrgId`  = %d %kelompok% 1) AND
         (`subkelbrgId`  = %d %subKelompok% 1) AND
         (CONCAT(LPAD(`golbrgKode`,1,0),'.',LPAD(`bidangbrgKode`,2,0),'.',LPAD(`kelbrgKode`,2,0),'.',LPAD(`subkelbrgKode`,2,0),'.',LPAD(`barangKode`,3,0),'.',`invAtkKode`) LIKE '%s' OR `invAtkNama` LIKE '%s') AND
         (`barangJenisbrgId` = %d %jenisBarang% 1)
      GROUP BY invAtkJenisRefId
   ");

$sql['get_data_jenis'] =
   ("
      SELECT
   		SQL_CALC_FOUND_ROWS
         `kelbrgId` as `idKelompok`,
         `subkelbrgId` as `idSubKelompok`,
         `barangId` as `idBarang`,
         `invAtkJenisRefId`  AS `id`,
         MAX(invAtkDetId)   AS barang_id,
         CONCAT(LPAD(`golbrgKode`,1,0),'.',LPAD(`bidangbrgKode`,2,0),'.',LPAD(`kelbrgKode`,2,0),'.', LPAD(`subkelbrgKode`,2,0),'.',LPAD(`barangKode`,3,0),'.',`invAtkKode`) AS `kode`,
         invAtkNama          AS nama,
         invAtkDetMerk       AS barang_merk,
         SUM(invAtkGudangJumlah) AS stok,
         invAtkGudangRuangId AS ruang_id
      FROM `golongan_barang_ref`
      JOIN `bidang_barang_ref` ON (`bidangbrgGolbrgId` = `golbrgId`)
      JOIN `kelompok_barang_ref` ON (`kelbrgBidangbrgId` = `bidangbrgId`)
      JOIN `sub_kelompok_barang_ref` ON (`subkelbrgKelbrgId` = `kelbrgId`)
      JOIN `barang_ref` ON (`barangSubkelbrgId` = `subkelbrgId`)
			JOIN `inv_atk_jenis_ref` ON (invAtkJenisBarangRefId = barangId)
      LEFT JOIN inv_atk_master ON invAtkMstJenisPersediaanId = invAtkJenisRefId
      LEFT JOIN inv_atk_det ON invAtkDetMstId = invAtkMstId
      LEFT JOIN inv_atk_brg ON invAtkDetId = invDet
      LEFT JOIN inv_atk_gudang ON invAtkGudangInvDtlbrgId = invBrgAtkId
      LEFT JOIN ruang ON ruangId = invAtkGudangRuangId
      LEFT JOIN unit_kerja_ref ON unitkerjaId = ruangUnitId
      WHERE 
        invAtkGudangJumlah != '' AND
        (`golbrgId` = %d %golongan% 1) AND
        (`bidangbrgId`  = %d %bidang% 1) AND
        (`kelbrgId`  = %d %kelompok% 1) AND
        (`subkelbrgId`  = %d %subKelompok% 1) AND
        (CONCAT(LPAD(`golbrgKode`,1,0),'.',LPAD(`bidangbrgKode`,2,0),'.',LPAD(`kelbrgKode`,2,0),'.',LPAD(`subkelbrgKode`,2,0),'.',LPAD(`barangKode`,3,0),'.',`invAtkKode`) LIKE '%s' OR `invAtkNama` LIKE '%s') AND
        (`barangJenisbrgId` = %d %jenisBarang% 1) AND
        (unitkerjaKodeSistem = '%s' OR unitkerjaKodeSistem LIKE '%s') AND
        DATE_FORMAT(tglPengadaan,'%%Y-%%m-%%d') <= '%s' AND # tgl RTH
        ruangUnitId = '%s' # unit PJ
      GROUP BY invAtkJenisRefId
      ORDER BY `kelbrgNama`,`subkelbrgNama`,`invAtkNama`,`invAtkKode`
      LIMIT %d, %d
   ");

$sql['get_data_barang_count'] =
   ("
      SELECT 
         COUNT(DISTINCT `barangNama`) as `total`
      FROM 
         `golongan_barang_ref`
         JOIN `bidang_barang_ref` ON (`bidangbrgGolbrgId` = `golbrgId`)
         JOIN `kelompok_barang_ref` ON (`kelbrgBidangbrgId` = `bidangbrgId`)
         JOIN `sub_kelompok_barang_ref` ON (`subkelbrgKelbrgId` = `kelbrgId`)
         JOIN `barang_ref` ON (`barangSubkelbrgId` = `subkelbrgId`)
			JOIN `inv_atk_jenis_ref` ON (invAtkJenisBarangRefId = barangId)
      WHERE
         (`golbrgId` = %d %golongan% 1) AND
         (`bidangbrgId`  = %d %bidang% 1) AND
         (`kelbrgId`  = %d %kelompok% 1) AND
         (`subkelbrgId`  = %d %subKelompok% 1) AND
         (CONCAT(LPAD(`golbrgKode`,1,0),'.',LPAD(`bidangbrgKode`,2,0),'.',LPAD(`kelbrgKode`,2,0),'.',LPAD(`subkelbrgKode`,2,0),'.',LPAD(`barangKode`,3,0),'.',`invAtkKode`) LIKE '%s' OR `invAtkNama` LIKE '%s') AND
         (`barangJenisbrgId` = %d %jenisBarang% 1)
   ");

$sql['get_count']="
SELECT FOUND_ROWS() AS total
";

$sql['get_data_barang'] =
   ("
      SELECT 
         `kelbrgId` as `idKelompok`,
         `subkelbrgId` as `idSubKelompok`,
         `barangId` as `id`,
         CONCAT(LPAD(`golbrgKode`,1,0),'.',LPAD(`bidangbrgKode`,2,0),'.',LPAD(`kelbrgKode`,2,0),'.',LPAD(`subkelbrgKode`,2,0),'.',LPAD(`barangKode`,3,0)) as `kode`,
         `barangNama` as `nama`,
         `barangSatuanbrgId` as `satuan`
      FROM 
         `golongan_barang_ref`
         JOIN `bidang_barang_ref` ON (`bidangbrgGolbrgId` = `golbrgId`)
         JOIN `kelompok_barang_ref` ON (`kelbrgBidangbrgId` = `bidangbrgId`)
         JOIN `sub_kelompok_barang_ref` ON (`subkelbrgKelbrgId` = `kelbrgId`)
         JOIN `barang_ref` ON (`barangSubkelbrgId` = `subkelbrgId`)
			JOIN `inv_atk_jenis_ref` ON (invAtkJenisBarangRefId = barangId)
      WHERE
         (`golbrgId` = %d %golongan% 1) AND
         (`bidangbrgId`  = %d %bidang% 1) AND
         (`kelbrgId`  = %d %kelompok% 1) AND
         (`subkelbrgId`  = %d %subKelompok% 1) AND
         (CONCAT(LPAD(`golbrgKode`,1,0),'.',LPAD(`bidangbrgKode`,2,0),'.',LPAD(`kelbrgKode`,2,0),'.',LPAD(`subkelbrgKode`,2,0),'.',LPAD(`barangKode`,3,0),'.',`invAtkKode`) LIKE '%s' OR `invAtkNama` LIKE '%s') AND
         (`barangJenisbrgId` = %d %jenisBarang% 1)
		GROUP BY kode
      ORDER BY
         `kelbrgNama`,
         `subkelbrgNama`,
         `barangNama`
      LIMIT
         %d, %d
   ");
	
$sql['get_kelompok_list'] =
   ("
      SELECT 
         `kelbrgId` as `id`,
         CONCAT(LPAD(`golbrgKode`,1,0),'.',LPAD(`bidangbrgKode`,2,0),'.',LPAD(`kelbrgKode`,2,0)) as `kode`,
         `kelbrgNama` as `nama`
      FROM 
         `golongan_barang_ref`
         JOIN `bidang_barang_ref` ON (`bidangbrgGolbrgId` = `golbrgId`)
         JOIN `kelompok_barang_ref` ON (`kelbrgBidangbrgId` = `bidangbrgId`)
      ORDER BY
         `kelbrgNama`
   ");

$sql['get_sub_kelompok_list'] =
   ("
      SELECT
         `kelbrgId` as `idKelompok`,
         `subkelbrgId` as `id`,
         CONCAT(LPAD(`golbrgKode`,1,0),'.',LPAD(`bidangbrgKode`,2,0),'.',LPAD(`kelbrgKode`,2,0),'.',LPAD(`subkelbrgKode`,2,0)) as `kode`,
         `subkelbrgNama` as `nama`
      FROM 
         `golongan_barang_ref`
         JOIN `bidang_barang_ref` ON (`bidangbrgGolbrgId` = `golbrgId`)
         JOIN `kelompok_barang_ref` ON (`kelbrgBidangbrgId` = `bidangbrgId`)
         JOIN `sub_kelompok_barang_ref` ON (`subkelbrgKelbrgId` = `kelbrgId`)
      ORDER BY
         `subkelbrgNama`
   ");

?>
