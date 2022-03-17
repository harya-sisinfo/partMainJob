<?php
$sql['get_kode_barang'] = "
   SELECT
	  CONCAT(LPAD(golbrgKode, 1, '0'), '.', LPAD(bidangbrgKode, 2, '0'), '.', LPAD(kelbrgKode, 2, '0'), '.', LPAD(subkelbrgKode, 2, '0'), '.', LPAD(barangKode, 3, '0')) AS kode_barang,
	  barangNama           AS nama_barang,
	  invAtkDetBarcode     AS barcode,
	  invAtkDetLabel       AS label_barang,
	  invAtkDetMerk        AS merk_barang,
	  SUM(invAtkGudangJumlah) AS sisa_barang,
	  invAtkDetSpesifikasi AS spesifikasi,
	  invAtkGudangRuangId AS gudang_id,
	  ruangNama AS ruang_nama
	FROM inv_atk_master
	  LEFT JOIN inv_atk_det
		 ON invAtkDetMstId = invAtkMstId
	  LEFT JOIN inv_atk_brg
		 ON invDet = invAtkDetId
	  LEFT JOIN inv_atk_gudang
		 ON invAtkGudangInvDtlbrgId = invBrgAtkId
	  LEFT JOIN barang_ref
		 ON barangId = invAtkMstBarangId
	  LEFT JOIN sub_kelompok_barang_ref
		 ON subkelbrgId = barangSubkelbrgId
	  LEFT JOIN kelompok_barang_ref
		 ON kelbrgId = subkelbrgKelbrgId
	  LEFT JOIN bidang_barang_ref
		 ON bidangbrgId = kelbrgBidangbrgId
	  LEFT JOIN golongan_barang_ref
		 ON golbrgId = bidangbrgGolbrgId
	  LEFT JOIN ruang
		 ON ruangId = invAtkGudangRuangId
	WHERE barangNama LIKE '%s' AND (invAtkGudangRuangId = %s OR %s = '')
	GROUP BY invAtkDetBarcode
";

$sql['get_cbx_satuan'] = "
SELECT
   satuanbrgId AS id,
   satuanbrgNama AS name
FROM
   satuan_barang_ref
ORDER BY satuanbrgNama
";

$sql['get_kode_barang_by_id'] = "
   SELECT
     invAtkJenisRefId     AS barang_id,
     CONCAT(LPAD(golbrgKode,1,'0'),'.',LPAD(bidangbrgKode,2,'0'),'.',LPAD(kelbrgKode,2,'0'),'.',LPAD(subkelbrgKode,2,'0'),'.',LPAD(barangKode,3,'0'),'.',invAtkKode) AS kode_barang,
     ruangId              AS gudang,
     ruangNama            AS gudang_nama,
     invAtkNama           AS nama_barang,
     invAtkDetBarcode     AS barcode,
     invAtkDetLabel       AS label_barang,
     invAtkDetMerk        AS merk_barang,
     SUM(invAtkGudangJumlah) AS sisa_barang,
     invAtkDetSpesifikasi AS spesifikasi,
     biaya                AS invAtkBiayaNominal,
	  satuanbrgNama
   FROM barang_ref
     JOIN sub_kelompok_barang_ref
       ON barangSubkelbrgId = subkelbrgId
     JOIN kelompok_barang_ref
       ON subkelbrgKelbrgId = kelbrgId
     JOIN bidang_barang_ref
       ON kelbrgBidangbrgId = bidangbrgId
     JOIN golongan_barang_ref
       ON bidangbrgGolbrgId = golbrgId
     LEFT JOIN inv_atk_jenis_ref
       ON invAtkJenisBarangRefId = barangId
     LEFT JOIN inv_atk_master
       ON invAtkMstJenisPersediaanId = invAtkJenisRefId
	  JOIN satuan_barang_ref 
      ON satuanbrgId = invAtkMstSatuanBarang 
     LEFT JOIN inv_atk_det
       ON invAtkDetMstId = invAtkMstId
     LEFT JOIN inv_atk_brg
       ON invDet = invAtkDetId
     LEFT JOIN inv_atk_gudang
       ON invAtkGudangInvDtlbrgId = invBrgAtkId
     JOIN ruang
       ON ruangId = invAtkGudangRuangId
   WHERE invAtkJenisRefId = %s
		 AND ruangId = %s
	GROUP BY invAtkJenisRefId, ruangId
";


$sql['get_kode_sistem'] = "
SELECT unitkerjaKodeSistem 
FROM user_unit_kerja
LEFT JOIN unit_kerja_ref ON unitkerjaId = userunitkerjaUnitkerjaId
WHERE userunitkerjaUserId = '%s'
";

$sql['get_combo_gudang'] = "
   SELECT 
      ruangId AS id, 
      CONCAT(ruangNama,' (',ruangKode,') - ',unitkerjaNama) AS name 
   FROM ruang
   LEFT JOIN unit_kerja_ref ON unitkerjaId = ruangUnitId
   [ALL] JOIN inv_atk_gudang ON invAtkGudangRuangId = ruangId
   WHERE 
      ruangJenisRuangId = '-1' AND 
      (unitkerjaKodeSistem = '%s' OR unitkerjaKodeSistem LIKE '%s')
    GROUP BY ruangId
    ORDER BY unitkerjaNama, ruangNama
";

$sql['get_gudang_by_id']="
SELECT
  CONCAT(ruangNama,' (',ruangKode,') - ',unitkerjaNama) AS ruangNama
FROM ruang
JOIN unit_kerja_ref ON unitkerjaId = ruangUnitId
WHERE ruangId = '%s'
";

$sql['get_combo_lokasi'] = "
   SELECT 
      ruangId AS id,
      ruangNama AS name
   FROM 
      ruang
";

$sql['get_combo_unit'] = "
   SELECT 
      unitkerjaId AS id,
      unitkerjaNama AS name
   FROM 
      unit_kerja_ref
   ORDER BY name
";

$sql['get_lokasi_barang'] = "
   SELECT 
      ruangId AS id,
      ruangNama AS name
   FROM 
      ruang
   WHERE 
      ruangUnitId = '%s'
";

//query list akan memakan banyak resource. Untuk sementara bisa dipakai. Kalau data sudah banyak perlu di optimize kan.
/*
$sql['atk_list'] = "
   SELECT * FROM (
SELECT
      stockSetId AS atk_id, 
      invAtkDetBarcode AS barcode,
      CONCAT(LPAD(golbrgKode,1,'0'),'.',LPAD(bidangbrgKode,2,'0'),'.',LPAD(kelbrgKode,2,'0'),'.',LPAD(subkelbrgKode,2,'0'),'.',LPAD(barangKode,3,'0')) as kode_barang,
      barangNama AS nama_barang,
      GROUP_CONCAT(invAtkDetMerk SEPARATOR ',') AS merk,
      IFNULL(stockSetMin,'Blum Di setting') AS stok_minimal,
      b.jumlah AS jumlah_stok,
      ruangNama AS lokasi_gudang,
      stcokSetKet AS keterangan,
      ruangId
   FROM 
      inv_atk_master a
      LEFT JOIN inv_atk_det ON invAtkDetMstId = invAtkMstId
      LEFT JOIN (
      SELECT 
         invAtkDetMstId,
         IFNULL(sum(invAtkDetJumlah),0) AS jumlah
      FROM
         inv_atk_det
      GROUP BY 
         invAtkDetMstId
      ) b ON invAtkMstId = b.invAtkDetMstId
      LEFT JOIN barang_ref ON invAtkMstBarangId = barangId
      LEFT JOIN sub_kelompok_barang_ref ON barangSubkelbrgId = subkelbrgId
      LEFT JOIN kelompok_barang_ref ON subkelbrgKelbrgId = kelbrgId
      LEFT JOIN bidang_barang_ref ON kelbrgBidangbrgId = bidangbrgId
      LEFT JOIN golongan_barang_ref ON bidangbrgGolbrgId = golbrgId
      LEFT JOIN ruang ON invAtkDetRuangId = ruangId
      LEFT JOIN setting_stok_atk ON stockSetBrgId = invAtkMstBarangId AND invAtkDetRuangId = stockSetRuangId
      GROUP BY invAtkMstBarangId, invAtkDetRuangId
      UNION
      SELECT 
         stockSetId AS atk_id,
         '' AS barcode,
         CONCAT(LPAD(golbrgKode,1,'0'),'.',LPAD(bidangbrgKode,2,'0'),'.',LPAD(kelbrgKode,2,'0'),'.',LPAD(subkelbrgKode,2,'0'),'.',LPAD(barangKode,3,'0')) as kode_barang,
         barangNama AS nama_barang,
         '' AS merk,
         IFNULL(stockSetMin,'Blum Di setting') AS stok_minimal,
         'Blum ada stok' AS jumlah_stok,
         ruangNama AS lokasi_gudang,
         stcokSetKet AS keterangan,
         ruangId
      FROM setting_stok_atk
      LEFT JOIN barang_ref ON stockSetBrgId = barangId
      LEFT JOIN sub_kelompok_barang_ref ON barangSubkelbrgId = subkelbrgId
      LEFT JOIN kelompok_barang_ref ON subkelbrgKelbrgId = kelbrgId
      LEFT JOIN bidang_barang_ref ON kelbrgBidangbrgId = bidangbrgId
      LEFT JOIN golongan_barang_ref ON bidangbrgGolbrgId = golbrgId
      LEFT JOIN ruang ON stockSetRuangId = ruangId
      JOIN (
         SELECT 
         invAtkMstBarangId,
         invAtkDetRuangId
      FROM
         inv_atk_master
      JOIN inv_atk_det ON invAtkDetMstId =  invAtkMstId
      GROUP BY invAtkMstBarangId, invAtkDetRuangId
      ) a ON stockSetBrgId!=invAtkMstBarangId OR stockSetRuangId!=invAtkDetRuangId
      ) a
   WHERE 
      a.ruangId LIKE '%s'
   AND 
      nama_barang LIKE '%s'
   %s
";
*/
$sql['atk_list_old'] = "
SELECT * FROM
(
   (
      SELECT
         NULL AS atk_id,
         invAtkDetBarcode AS barcode,
         CONCAT(LPAD(golbrgKode,1,'0'),'.',LPAD(bidangbrgKode,2,'0'),'.',LPAD(kelbrgKode,2,'0'),'.',
         LPAD(subkelbrgKode,2,'0'),'.',LPAD(barangKode,3,'0'),'.',invAtkKode) AS kode_barang,
         invAtkNama AS nama_barang,
         GROUP_CONCAT(DISTINCT invAtkDetMerk SEPARATOR ',') AS merk,
         ('Belum di Setting') AS stok_minimal,
         SUM(invAtkGudangJumlah) AS jumlah_stok,
         ruangNama AS lokasi_gudang,
         NULL AS keterangan,
         invAtkJenisRefId AS barangId,
         ruangId,
         unitkerjaKodeSistem,
         unitkerjaNama AS unit,
         (SUM(invAtkGudangJumlah) DIV 0) AS hasil
      FROM
         inv_atk_master
         LEFT JOIN inv_atk_det ON invAtkDetMstId = invAtkMstId
         LEFT JOIN inv_atk_brg ON invDet = invAtkDetId
         LEFT JOIN inv_atk_gudang ON invAtkGudangInvDtlbrgId = invBrgAtkId
         LEFT JOIN inv_atk_jenis_ref ON invAtkJenisRefId = invAtkMstJenisPersediaanId
         LEFT JOIN barang_ref ON barangId = invAtkJenisBarangRefId
         LEFT JOIN sub_kelompok_barang_ref ON subkelbrgId = barangSubkelbrgId
         LEFT JOIN kelompok_barang_ref ON kelbrgId = subkelbrgKelbrgId
         LEFT JOIN bidang_barang_ref ON bidangbrgId = kelbrgBidangbrgId
         LEFT JOIN golongan_barang_ref ON golbrgId = bidangbrgGolbrgId
         LEFT JOIN ruang ON ruangId = invAtkGudangRuangId
         LEFT JOIN unit_kerja_ref ON unitkerjaId = ruangUnitId
         LEFT JOIN setting_stok_atk ON stockSetJenisPersediaanId = invAtkMstJenisPersediaanId AND stockSetRuangId = invAtkGudangRuangId
      WHERE stockSetId IS NULL
      GROUP BY
         invAtkMstJenisPersediaanId,
         invAtkGudangRuangId
      ORDER BY
         jumlah_stok
   )
   UNION
   (
      SELECT
         stockSetId AS atk_id,
         invAtkDetBarcode AS barcode,
         CONCAT(LPAD(golbrgKode,1,'0'),'.',LPAD(bidangbrgKode,2,'0'),'.',LPAD(kelbrgKode,2,'0'),'.',LPAD(subkelbrgKode,2,'0'),'.',LPAD(barangKode,3,'0'),'.',invAtkKode) AS kode_barang,
         invAtkNama AS nama_barang,
         GROUP_CONCAT(DISTINCT invAtkDetMerk SEPARATOR ',') AS merk,
         stockSetMin AS stok_minimal,
         IFNULL(SUM(invAtkGudangJumlah), 'Belum ada Stok') AS jumlah_stok,
         ruangNama AS lokasi_gudang,
         stcokSetKet AS keterangan,
         invAtkJenisRefId AS barangId,
         ruangId,
         unitkerjaKodeSistem,
         unitkerjaNama AS unit,
         (SUM(invAtkGudangJumlah) DIV stockSetMin) AS hasil
      FROM
         setting_stok_atk
         LEFT JOIN
         (
            SELECT * FROM inv_atk_master
            JOIN inv_atk_det ON invAtkDetMstId = invAtkMstId
            JOIN inv_atk_brg ON invDet = invAtkDetId
            JOIN inv_atk_gudang ON invAtkGudangInvDtlbrgId = invBrgAtkId
         ) barang_gudang ON invAtkMstJenisPersediaanId = stockSetJenisPersediaanId AND invAtkGudangRuangId = stockSetRuangId
	 LEFT JOIN inv_atk_jenis_ref ON invAtkJenisRefId = stockSetJenisPersediaanId         
         LEFT JOIN barang_ref ON barangId = invAtkJenisBarangRefId
         LEFT JOIN sub_kelompok_barang_ref ON subkelbrgId = barangSubkelbrgId
         LEFT JOIN kelompok_barang_ref ON kelbrgId = subkelbrgKelbrgId
         LEFT JOIN bidang_barang_ref ON bidangbrgId = kelbrgBidangbrgId
         LEFT JOIN golongan_barang_ref ON golbrgId = bidangbrgGolbrgId
         LEFT JOIN ruang ON ruangId = stockSetRuangId
         LEFT JOIN unit_kerja_ref ON unitkerjaId = ruangUnitId
      GROUP BY
         stockSetJenisPersediaanId,
         stockSetRuangId
      ORDER BY
         invAtkGudangJumlah
   )
) a
WHERE 
   (ruangId = %s OR 'all' = %s) AND
   (unitkerjaKodeSistem = '%s' OR unitkerjaKodeSistem LIKE '%s') AND
   nama_barang LIKE '%s' AND
   round(jumlah_stok) BETWEEN %s AND %s
ORDER BY
   hasil,
   IF(atk_id, 1, 0),
   jumlah_stok/(IF(ROUND(stok_minimal) = 0, 1, stok_minimal))
LIMIT %s,%s
";

$sql['atk_list_count_old'] = "
SELECT COUNT(kode_barang) AS total 
FROM
(
   (
      SELECT
         NULL AS atk_id,
         invAtkDetBarcode AS barcode,
         CONCAT(LPAD(golbrgKode,1,'0'),'.',LPAD(bidangbrgKode,2,'0'),'.',LPAD(kelbrgKode,2,'0'),'.',
         LPAD(subkelbrgKode,2,'0'),'.',LPAD(barangKode,3,'0'),'.',invAtkKode) AS kode_barang,
         invAtkNama AS nama_barang,
         GROUP_CONCAT(DISTINCT invAtkDetMerk SEPARATOR ',') AS merk,
         ('Belum di Setting') AS stok_minimal,
         SUM(invAtkGudangJumlah) AS jumlah_stok,
         ruangNama AS lokasi_gudang,
         NULL AS keterangan,
         invAtkJenisRefId AS barangId,
         ruangId,
         unitkerjaKodeSistem,
         (SUM(invAtkGudangJumlah) DIV 0) AS hasil
      FROM
         inv_atk_master
         LEFT JOIN inv_atk_det ON invAtkDetMstId = invAtkMstId
         LEFT JOIN inv_atk_brg ON invDet = invAtkDetId
         LEFT JOIN inv_atk_gudang ON invAtkGudangInvDtlbrgId = invBrgAtkId
         LEFT JOIN inv_atk_jenis_ref ON invAtkJenisRefId = invAtkMstJenisPersediaanId
         LEFT JOIN barang_ref ON barangId = invAtkJenisBarangRefId
         LEFT JOIN sub_kelompok_barang_ref ON subkelbrgId = barangSubkelbrgId
         LEFT JOIN kelompok_barang_ref ON kelbrgId = subkelbrgKelbrgId
         LEFT JOIN bidang_barang_ref ON bidangbrgId = kelbrgBidangbrgId
         LEFT JOIN golongan_barang_ref ON golbrgId = bidangbrgGolbrgId
         LEFT JOIN ruang ON ruangId = invAtkGudangRuangId
         LEFT JOIN unit_kerja_ref ON unitkerjaId = ruangUnitId
         LEFT JOIN setting_stok_atk ON stockSetJenisPersediaanId = invAtkMstJenisPersediaanId AND stockSetRuangId = invAtkGudangRuangId
      WHERE stockSetId IS NULL
      GROUP BY
         invAtkMstJenisPersediaanId,
         invAtkGudangRuangId
      ORDER BY
         jumlah_stok
   )
   UNION
   (
      SELECT
         stockSetId AS atk_id,
         invAtkDetBarcode AS barcode,
         CONCAT(LPAD(golbrgKode,1,'0'),'.',LPAD(bidangbrgKode,2,'0'),'.',LPAD(kelbrgKode,2,'0'),'.',LPAD(subkelbrgKode,2,'0'),'.',LPAD(barangKode,3,'0'),'.',invAtkKode) AS kode_barang,
         invAtkNama AS nama_barang,
         GROUP_CONCAT(DISTINCT invAtkDetMerk SEPARATOR ',') AS merk,
         stockSetMin AS stok_minimal,
         IFNULL(SUM(invAtkGudangJumlah), 'Belum ada Stok') AS jumlah_stok,
         ruangNama AS lokasi_gudang,
         stcokSetKet AS keterangan,
         invAtkJenisRefId AS barangId,
         ruangId,
         unitkerjaKodeSistem,
         (SUM(invAtkGudangJumlah) DIV stockSetMin) AS hasil
      FROM
         setting_stok_atk
         LEFT JOIN
         (
            SELECT * FROM inv_atk_master
            JOIN inv_atk_det ON invAtkDetMstId = invAtkMstId
            JOIN inv_atk_brg ON invDet = invAtkDetId
            JOIN inv_atk_gudang ON invAtkGudangInvDtlbrgId = invBrgAtkId
         ) barang_gudang ON invAtkMstJenisPersediaanId = stockSetJenisPersediaanId AND invAtkGudangRuangId = stockSetRuangId
   LEFT JOIN inv_atk_jenis_ref ON invAtkJenisRefId = stockSetJenisPersediaanId         
         LEFT JOIN barang_ref ON barangId = invAtkJenisBarangRefId
         LEFT JOIN sub_kelompok_barang_ref ON subkelbrgId = barangSubkelbrgId
         LEFT JOIN kelompok_barang_ref ON kelbrgId = subkelbrgKelbrgId
         LEFT JOIN bidang_barang_ref ON bidangbrgId = kelbrgBidangbrgId
         LEFT JOIN golongan_barang_ref ON golbrgId = bidangbrgGolbrgId
         LEFT JOIN ruang ON ruangId = stockSetRuangId
         LEFT JOIN unit_kerja_ref ON unitkerjaId = ruangUnitId
      GROUP BY
         stockSetJenisPersediaanId,
         stockSetRuangId
      ORDER BY
         invAtkGudangJumlah
   )
) a
WHERE 
   (ruangId = %s OR 'all' = %s) AND
   (unitkerjaKodeSistem = '%s' OR unitkerjaKodeSistem LIKE '%s') AND
   nama_barang LIKE '%s' AND
   round(jumlah_stok) BETWEEN %s AND %s
ORDER BY
   hasil,
   IF(atk_id, 1, 0),
   jumlah_stok/(IF(ROUND(stok_minimal) = 0, 1, stok_minimal))
";

$sql['atk_list']="
SELECT
  SQL_CALC_FOUND_ROWS
  ssa.`stockSetId` AS atk_id,
  iajr.`invAtkJenisRefId` AS barangId,
  iajr.`invAtkNama` AS nama_barang,
  CONCAT(
    MID(iajr.`invAtkJenisBarangRefId`, 1, 1), '.', MID(iajr.`invAtkJenisBarangRefId`, 2, 2), '.',
    MID(iajr.`invAtkJenisBarangRefId`, 4, 2), '.', MID(iajr.`invAtkJenisBarangRefId`, 6, 2), '.',
    MID(iajr.`invAtkJenisBarangRefId`, 8, 3), '.', iajr.`invAtkKode`
  ) AS kode_barang,
  iad.`invAtkDetBarcode` AS barcode,
  GROUP_CONCAT(DISTINCT iad.`invAtkDetMerk` SEPARATOR ', ') AS merk,
  r.`ruangNama` AS lokasi_gudang,
  iag.`invAtkGudangRuangId` AS ruangId,
  SUM(iag.invAtkGudangJumlah) AS jumlah_stok,
  IFNULL(ssa.`stockSetMin`,'Belum di Setting') AS stok_minimal,
  IFNULL(ssa.`stockSetMin`,5) AS stokMin,
  ukr.`unitkerjaNama` AS unit
FROM inv_atk_gudang iag
JOIN ruang r ON r.ruangId = iag.`invAtkGudangRuangId`
JOIN unit_kerja_ref ukr ON ukr.unitkerjaId = r.`ruangUnitId`
JOIN inv_atk_brg iab ON iab.`invBrgAtkId` = iag.`invAtkGudangInvDtlbrgId`
JOIN inv_atk_det iad ON iad.`invAtkDetId` = iab.`invDet`
JOIN inv_atk_master iam ON iam.`invAtkMstId` = iad.`invAtkDetMstId`
JOIN inv_atk_jenis_ref iajr ON iajr.`invAtkJenisRefId` = iam.`invAtkMstJenisPersediaanId`
LEFT JOIN setting_stok_atk ssa ON ssa.`stockSetJenisPersediaanId` = iajr.`invAtkJenisRefId` AND ssa.`stockSetRuangId` = iag.`invAtkGudangRuangId`
WHERE
  (iag.`invAtkGudangRuangId` = '%s' OR 'all' = '%s') AND
  (ukr.`unitkerjaKodeSistem` = '%s' OR ukr.`unitkerjaKodeSistem` LIKE '%s')
GROUP BY iajr.`invAtkJenisRefId`,iag.`invAtkGudangRuangId`
HAVING (nama_barang LIKE '%s' OR kode_barang LIKE '%s') [JMLSTOK]
ORDER BY jumlah_stok DESC
LIMIT %d,%d
";

$sql['atk_list_count']="
SELECT FOUND_ROWS() as total
";

$sql['get_stock_by_gudang'] = "
   SELECT 
      invAtkStockMinimal AS stok_minimal,
      invAtkKeteranganLain AS keterangan
   FROM
      inv_atk
   WHERE 
      invAtkRuangId = '%s'
";

$sql['get_setting_stock_atk_by_id_jenis'] = "
   SELECT stockSetJenisPersediaanId AS idJenis
   FROM setting_stok_atk
   WHERE 
      stockSetJenisPersediaanId = '%d' AND
      stockSetRuangId = '%d'
";

$sql['set_add_setting_stock_atk'] = "
   INSERT INTO setting_stok_atk(
      stockSetJenisPersediaanId,
      stockSetRuangId,
      stockSetMin,
      stcokSetKet)
   VALUES(
      '%s',
      '%s',
      '%s',
      '%s'
   )
";

$sql['atk_by_id'] = "
   SELECT
     stockSetId,
     invAtkDetBarcode     AS barcode,
     CONCAT(LPAD(golbrgKode,1,'0'),'.',LPAD(bidangbrgKode,2,'0'),'.',LPAD(kelbrgKode,2,'0'),'.',LPAD(subkelbrgKode,2,'0'),'.',LPAD(barangKode,3,'0'),'.',invAtkKode) AS kode_barang,
     invAtkNama           AS nama_barang,
     stockSetMin          AS stok_minimal,
     stcokSetKet          AS keterangan,
     invAtkDetLabel       AS label_barang,
     invAtkDetMerk        AS merk,
     invAtkDetSpesifikasi AS spesifikasi,
     ruangId              AS gudang,
     ruangNama            AS gudang_nama
   FROM setting_stok_atk
     LEFT JOIN inv_atk_jenis_ref
       ON stockSetJenisPersediaanId = invAtkJenisRefId
     LEFT JOIN barang_ref
       ON invAtkJenisBarangRefId = barangId
   LEFT JOIN sub_kelompok_barang_ref ON barangSubkelbrgId = subkelbrgId
   LEFT JOIN kelompok_barang_ref ON subkelbrgKelbrgId = kelbrgId
   LEFT JOIN bidang_barang_ref ON kelbrgBidangbrgId = bidangbrgId
   LEFT JOIN golongan_barang_ref ON bidangbrgGolbrgId = golbrgId
   LEFT JOIN ruang ON stockSetRuangId = ruangId
   LEFT JOIN
   (
      SELECT 
         invAtkMstBarangId,
         invAtkGudangRuangId,
         invAtkDetLabel,
         invAtkDetMerk,
         invAtkDetSpesifikasi,
         invAtkDetBarcode
      FROM
         inv_atk_master
         LEFT JOIN inv_atk_det ON invAtkDetMstId = invAtkMstId
         LEFT JOIN inv_atk_brg ON invDet = invAtkDetId
         LEFT JOIN inv_atk_gudang ON invAtkGudangInvDtlbrgId = invBrgAtkId
      GROUP BY
         invAtkMstBarangId,
         invAtkGudangRuangId
   ) a ON stockSetBrgId = invAtkMstBarangId AND stockSetRuangId = invAtkGudangRuangId
   WHERE stockSetId = '%s'
";

$sql['update_atk'] = "
   UPDATE 
      setting_stok_atk
   SET 
      stockSetMin = '%s',
      stcokSetKet = '%s'
   WHERE 
      stockSetId = '%s'
";

$sql['get_barang_by_barcode'] = "
   SELECT
		barangId             AS barangId,		  CONCAT(LPAD(golbrgKode,1,'0'),'.',LPAD(bidangbrgKode,2,'0'),'.',LPAD(kelbrgKode,2,'0'),'.',LPAD(subkelbrgKode,2,'0'),'.',LPAD(barangKode,3,'0')) AS kode_barang,
		ruangId              AS gudang,
		ruangNama            AS gudang_nama,
		barangNama           AS nama_barang,
		invAtkDetBarcode     AS barcode,
		invAtkDetLabel       AS label_barang,
		invAtkDetMerk        AS merk_barang,
		SUM(invAtkGudangJumlah) AS sisa_barang,
		invAtkDetSpesifikasi AS spesifikasi
   FROM 
      inv_atk_master
      LEFT JOIN inv_atk_det ON invAtkDetMstId = invAtkMstId
      LEFT JOIN inv_atk_brg ON invDet = invAtkDetId
      LEFT JOIN inv_atk_gudang ON invAtkGudangInvDtlbrgId = invBrgAtkId
      LEFT JOIN barang_ref ON barangId = invAtkMstBarangId
      LEFT JOIN sub_kelompok_barang_ref ON subkelbrgId = barangSubkelbrgId
      LEFT JOIN kelompok_barang_ref ON kelbrgId = subkelbrgKelbrgId
      LEFT JOIN bidang_barang_ref ON bidangbrgId = kelbrgBidangbrgId
      LEFT JOIN golongan_barang_ref ON golbrgId = bidangbrgGolbrgId
      LEFT JOIN ruang ON ruangId = invAtkGudangRuangId
   WHERE
      invAtkDetBarcode = '%s'
   AND 
      invAtkGudangRuangId = '%s'
   GROUP BY
      invAtkDetBarcode
";

$sql['get_inv_atk_gudang_ada_barang'] = "
   SELECT
      invBrgAtkId,
      invAtkGudangId,
      invAtkGudangJumlah
   FROM
      inv_atk_gudang
      JOIN inv_atk_brg ON invBrgAtkId = invAtkGudangInvDtlbrgId
      JOIN inv_atk_det ON invAtkDetId = invDet
   WHERE
      invAtkDetBarcode = %s AND
      invAtkGudangRuangId = %s AND
      (invAtkGudangJumlah > 0 OR %s = 1)
";

$sql['insert_into_log'] = "
   INSERT INTO log_atk(
      logAtkRuangId,
      logAtkAtkId,
      logAtkJmlBrg,
      logAtkStatus,
      logAtkUnitId,
      logAtkTujuanRuangId,
      logAtkTgl,
      logAtkBAMutasi,
      logAtkKeterangan,
      logAtkFile
   )VALUES(
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s'
   )
";

$sql['add_log_atk'] = "
INSERT INTO
   log_atk
   (
      logAtkRuangId,
      logAtkAtkId,
      logAtkJmlBrg,
      logAtkStatus,
      logAtkUnitId,
      logAtkTujuanRuangId,
      logAtkTgl,
      logAtkKeterangan,
      logAtkFile
   )
VALUES
(
   %s,
   %s,
   %s,
   %s,
   %s,
   %s,
   %s,
   %s,
   %s
)
";

$sql['update_inv_atk_gudang'] = "
UPDATE
   inv_atk_gudang
SET
   invAtkGudangJumlah = invAtkGudangJumlah + %s
WHERE
   invAtkGudangId = %s
";

$sql['update_inv_atk_gudang_tujuan'] = "
UPDATE
   inv_atk_gudang
SET
   invAtkGudangJumlah = invAtkGudangJumlah + %s
WHERE
   invAtkGudangInvDtlbrgId = %s AND
   invAtkGudangRuangId = %s
";

$sql['add_inv_atk_gudang'] = "
INSERT INTO
   inv_atk_gudang
   (
      invAtkGudangJumlah,
      invAtkGudangInvDtlbrgId,
      invAtkGudangRuangId
   )
VALUES
(
   %s,
   %s,
   %s
)
";

?>
