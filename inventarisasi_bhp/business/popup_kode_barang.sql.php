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
	WHERE jenisbrgId = 2
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

$sql['get_data_barang_count'] =
   ("
      SELECT 
         COUNT(`barangId`) as `total`
      FROM 
         `golongan_barang_ref`
         JOIN `bidang_barang_ref` ON (`bidangbrgGolbrgId` = `golbrgId`)
         JOIN `kelompok_barang_ref` ON (`kelbrgBidangbrgId` = `bidangbrgId`)
         JOIN `sub_kelompok_barang_ref` ON (`subkelbrgKelbrgId` = `kelbrgId`)
         LEFT JOIN `barang_ref` ON (`barangSubkelbrgId` = `subkelbrgId`)
			-- JOIN `inv_atk_jenis_ref` ON (invAtkJenisBarangRefId = barangId)
      WHERE
         (`golbrgId` = %d %golongan% 1) AND
         (`bidangbrgId`  = %d %bidang% 1) AND
         (`kelbrgId`  = %d %kelompok% 1) AND
         (`subkelbrgId`  = %d %subKelompok% 1) AND
         (CONCAT(LPAD(`golbrgKode`,1,0),'.',LPAD(`bidangbrgKode`,2,0),'.',LPAD(`kelbrgKode`,2,0),'.',LPAD(`subkelbrgKode`,2,0),'.',LPAD(`barangKode`,3,0)) LIKE '%s' OR `barangNama` LIKE '%s') AND
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
         LEFT JOIN `barang_ref` ON (`barangSubkelbrgId` = `subkelbrgId`)
			-- JOIN `inv_atk_jenis_ref` ON (invAtkJenisBarangRefId = barangId)
      WHERE
         (`golbrgId` = %d %golongan% 1) AND
         (`bidangbrgId`  = %d %bidang% 1) AND
         (`kelbrgId`  = %d %kelompok% 1) AND
         (`subkelbrgId`  = %d %subKelompok% 1) AND
         (CONCAT(LPAD(`golbrgKode`,1,0),'.',LPAD(`bidangbrgKode`,2,0),'.',LPAD(`kelbrgKode`,2,0),'.',LPAD(`subkelbrgKode`,2,0),'.',LPAD(`barangKode`,3,0)) LIKE '%s' OR `barangNama` LIKE '%s') AND
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

?>
