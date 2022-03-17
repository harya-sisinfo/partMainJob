<?php
$sql['get_combo_golongan'] = 
   "SELECT 
     `golbrgId` as `id`,
	  `golbrgNama` as `name`
   FROM 
      `golongan_barang_ref` 
	WHERE `golbrgId` IN ('1')
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
      WHERE
         (`golbrgId` = %d %golongan% 1) AND
         (`bidangbrgId`  = %d %bidang% 1) AND
         (`kelbrgId`  = %d %kelompok% 1) AND
         (`subkelbrgId`  = %d %subKelompok% 1) AND
         (CONCAT(LPAD(`golbrgKode`,1,0),'.',LPAD(`bidangbrgKode`,2,0),'.',LPAD(`kelbrgKode`,2,0),'.',LPAD(`subkelbrgKode`,2,0),'.',LPAD(`barangKode`,3,0),'.',`invAtkKode`) LIKE '%s' OR `invAtkNama` LIKE '%s') AND
         (`barangJenisbrgId` = %d %jenisBarang% 1)
   ");

$sql['get_data_jenis'] =
   ("
      SELECT 
         `kelbrgId` as `idKelompok`,
         `subkelbrgId` as `idSubKelompok`,
         `barangId` as `idBarang`,
         `invAtkJenisRefId` as `id`,
         CONCAT(LPAD(`golbrgKode`,1,0),'.',LPAD(`bidangbrgKode`,2,0),'.',LPAD(`kelbrgKode`,2,0),'.', LPAD(`subkelbrgKode`,2,0),'.',LPAD(`barangKode`,3,0),'.',`invAtkKode`) AS `kode`,
         `invAtkNama` as `nama`,
         `barangSatuanbrgId` as `satuan`,
         CONCAT(coaKode,' - ',coaNama) AS coa,
         `invAtkAktif` AS stat
      FROM 
         `golongan_barang_ref`
         JOIN `bidang_barang_ref` ON (`bidangbrgGolbrgId` = `golbrgId`)
         JOIN `kelompok_barang_ref` ON (`kelbrgBidangbrgId` = `bidangbrgId`)
         JOIN `sub_kelompok_barang_ref` ON (`subkelbrgKelbrgId` = `kelbrgId`)
         JOIN `barang_ref` ON (`barangSubkelbrgId` = `subkelbrgId`)
			JOIN `inv_atk_jenis_ref` ON (invAtkJenisBarangRefId = barangId)
         LEFT JOIN coa_ugm_ref ON coaKode = coaUGM
      WHERE
         (`golbrgId` = %d %golongan% 1) AND
         (`bidangbrgId`  = %d %bidang% 1) AND
         (`kelbrgId`  = %d %kelompok% 1) AND
         (`subkelbrgId`  = %d %subKelompok% 1) AND
         (CONCAT(LPAD(`golbrgKode`,1,0),'.',LPAD(`bidangbrgKode`,2,0),'.',LPAD(`kelbrgKode`,2,0),'.',LPAD(`subkelbrgKode`,2,0),'.',LPAD(`barangKode`,3,0),'.',`invAtkKode`) LIKE '%s' OR `invAtkNama` LIKE '%s') AND
         (`barangJenisbrgId` = %d %jenisBarang% 1)
      ORDER BY
         `kelbrgNama`,
         `subkelbrgNama`,
         `barangNama`, 
			`invAtkKode`
      LIMIT
         %d, %d
   ");

$sql['get_data_barang_count'] =
   ("
      SELECT 
         COUNT(`barangNama`) as `total`
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
	
$sql['get_data_last_number'] =
   ("
      SELECT 
         `kelbrgId` as `idKelompok`,
         `subkelbrgId` as `idSubKelompok`,
         `barangId` as `idBarang`,
         `invAtkJenisRefId` as `id`,
         CONCAT(LPAD(`golbrgKode`,1,0),'.',LPAD(`bidangbrgKode`,2,0),'.',LPAD(`kelbrgKode`,2,0),'.', LPAD(`subkelbrgKode`,2,0),'.',LPAD(`barangKode`,3,0),'.',`invAtkKode`) AS `kode`,
         `invAtkNama` as `nama`,
         `barangSatuanbrgId` as `satuan`
      FROM 
         `golongan_barang_ref`
         JOIN `bidang_barang_ref` ON (`bidangbrgGolbrgId` = `golbrgId`)
         JOIN `kelompok_barang_ref` ON (`kelbrgBidangbrgId` = `bidangbrgId`)
         JOIN `sub_kelompok_barang_ref` ON (`subkelbrgKelbrgId` = `kelbrgId`)
         JOIN `barang_ref` ON (`barangSubkelbrgId` = `subkelbrgId`)
			JOIN `inv_atk_jenis_ref` ON (invAtkJenisBarangRefId = barangId)
      WHERE	
			(CONCAT(LPAD(`golbrgKode`,1,0),'.',LPAD(`bidangbrgKode`,2,0),'.',LPAD(`kelbrgKode`,2,0),'.',LPAD(`subkelbrgKode`,2,0),'.',LPAD(`barangKode`,3,0)) LIKE '%s') 
      ORDER BY
         `kode` DESC
		LIMIT 1
   ");
	
$sql['get_data_detil_barang_by_id'] =
   ("
      SELECT 
         `barangId` as `barang_id`,
         `barangNama` as `nama_barang`,
         `invAtkJenisRefId` as `detil_barang_id`,
         CONCAT(LPAD(`golbrgKode`,1,0),'.',LPAD(`bidangbrgKode`,2,0),'.',LPAD(`kelbrgKode`,2,0),'.', LPAD(`subkelbrgKode`,2,0),'.',LPAD(`barangKode`,3,0)) AS `kode_barang`,
         `invAtkNama` as `detil_barang`,
         `invAtkAktif` AS stat
      FROM 
         `golongan_barang_ref`
         JOIN `bidang_barang_ref` ON (`bidangbrgGolbrgId` = `golbrgId`)
         JOIN `kelompok_barang_ref` ON (`kelbrgBidangbrgId` = `bidangbrgId`)
         JOIN `sub_kelompok_barang_ref` ON (`subkelbrgKelbrgId` = `kelbrgId`)
         JOIN `barang_ref` ON (`barangSubkelbrgId` = `subkelbrgId`)
			JOIN `inv_atk_jenis_ref` ON (invAtkJenisBarangRefId = barangId)
      WHERE
			invAtkJenisRefId = '%s'
   ");

$sql['export_excel']="
SELECT 
   TRIM(SUBSTR(golbrgNama,8)) AS gol, 
   TRIM(bidangbrgNama) AS bid,
   TRIM(kelbrgNama) AS kel,
   TRIM(subkelbrgNama) AS skel,
   TRIM(barangNama) AS brg,
   TRIM(invAtkNama) AS atk,
   CONCAT(coaKode,' - ',coaNama) AS coa,
   CONCAT(
      LEFT(barangId,1),'.',
      SUBSTR(barangId,2,2),'.',
      SUBSTR(barangId,4,2),'.',
      SUBSTR(barangId,6,2),'.',
      SUBSTR(barangId,8)
   ) AS kode,
   invAtkKode AS kodeBhp,
   IF(invAtkAktif = 'Y','Aktif','Tidak Aktif') AS stat
FROM inv_atk_jenis_ref
LEFT JOIN barang_ref ON barangId = invAtkJenisBarangRefId
LEFT JOIN coa_ugm_ref ON coaKode = coaUGM
LEFT JOIN golongan_barang_ref ON golbrgId = LEFT(barangId,1)
LEFT JOIN bidang_barang_ref ON bidangbrgId = LEFT(barangId,3)
LEFT JOIN kelompok_barang_ref ON kelbrgId = LEFT(barangId,5)
LEFT JOIN sub_kelompok_barang_ref ON subkelbrgId = LEFT(barangId,7)
ORDER BY barangSubkelbrgId,barangId,invAtkKode+0
";

$sql['do_insert_detil_bhp'] = "
	INSERT INTO 
		inv_atk_jenis_ref 
	SET 
		invAtkJenisBarangRefId = '%s',
		invAtkKode = '%s',
		invAtkNama = '%s',
		invAtkHargaSatuan = '1',
      invAtkAktif = '%s',
		invAtkUserId = '%s',
		invAtkTglUbah = NOW()
";

$sql['do_update_detil_bhp'] = "
	UPDATE 
		inv_atk_jenis_ref 
	SET
		invAtkNama = '%s',
      invAtkAktif = '%s',
		invAtkUserId = '%s',
		invAtkTglUbah = NOW()
	WHERE 
		invAtkJenisRefId = '%s'
";
?>
