<?php

//===GET===
$sql['get_combo_unit_kerja'] = "
SELECT
   unitkerjaId AS id,
   unitkerjaNama AS name
FROM
   unit_kerja_ref
ORDER BY unitkerjaNama
";

$sql['get_combo_jenis_log'] = "
SHOW COLUMNS FROM log_atk LIKE 'logAtkStatus'
";

$sql['get_combo_gudang'] = "
SELECT 
   ruangId AS id, 
   ruangNama AS name 
FROM 
   ruang 
WHERE 
   ruangJenisRuangId = '-1'
";

$sql['get_atk_detail_old'] = "
SELECT
   ruangNama,
   CONCAT(LPAD(golbrgKode,1,'0'),'.',LPAD(bidangbrgKode,2,'0'),'.',LPAD(kelbrgKode,2,'0'),'.',LPAD(subkelbrgKode,2,'0'),'.',LPAD(barangKode,3,'0'),'.',invAtkKode) AS barangKode,
   invAtkNama as barangNama,
   SUM(invAtkGudangJumlah) + (SELECT SUM(logAtkJmlBrg) FROM log_atk WHERE logAtkAtkId = invBrgAtkId AND logAtkRuangId = invAtkGudangRuangId) - (SELECT SUM(logAtkJmlBrg) FROM log_atk WHERE logAtkAtkId = invBrgAtkId AND logAtkTujuanRuangId = invAtkGudangRuangId) AS jumlahTotal,
   SUM(invAtkGudangJumlah) AS jumlahSisa
FROM
   barang_ref
   JOIN inv_atk_jenis_ref ON invAtkJenisBarangRefId = barangId
   JOIN sub_kelompok_barang_ref ON barangSubkelbrgId = subkelbrgId
   JOIN kelompok_barang_ref ON subkelbrgKelbrgId = kelbrgId
   JOIN bidang_barang_ref ON kelbrgBidangbrgId = bidangbrgId
   JOIN golongan_barang_ref ON bidangbrgGolbrgId = golbrgId
   JOIN ruang
   LEFT JOIN inv_atk_master ON invAtkMstJenisPersediaanId = invAtkJenisRefId
   LEFT JOIN inv_atk_det ON invAtkDetMstId = invAtkMstId
   LEFT JOIN inv_atk_brg ON invDet = invAtkDetId
   LEFT JOIN inv_atk_gudang ON invAtkGudangInvDtlbrgId = invBrgAtkId
WHERE
   invAtkJenisRefId = '%s' AND
   ruangId = '%s'
GROUP BY
   invAtkJenisRefId,
   ruangId
";

$sql['get_atk_detail']="
SELECT 
   ruangNama, 
   CONCAT(CAST(c.kode AS CHAR),'.' ,invAtkKode) AS barangKode,
   invAtkNama AS barangNama,
   SUM(invAtkGudangJumlah) AS jumlahSisa
FROM inv_atk_gudang
LEFT JOIN ruang ON ruangId = invAtkGudangRuangId
LEFT JOIN inv_atk_brg ON invAtkGudangInvDtlbrgId = invBrgAtkId
LEFT JOIN inv_atk_det ON invAtkDetId = invDet
LEFT JOIN inv_atk_master ON invAtkMstId = invAtkDetMstId
LEFT JOIN inv_atk_jenis_ref ON invAtkJenisRefId = invAtkMstJenisPersediaanId
LEFT JOIN 
(
   SELECT barangId,CONCAT(
    LPAD(golbrgKode, 1, '0'),
    '.',
    LPAD(bidangbrgKode, 2, '0'),
    '.',
    LPAD(kelbrgKode, 2, '0'),
    '.',
    LPAD(subkelbrgKode, 2, '0'),
    '.',
    LPAD(barangKode, 3, '0')) AS kode
    FROM barang_ref
    LEFT JOIN sub_kelompok_barang_ref ON barangSubkelbrgId = subkelbrgId 
    LEFT JOIN kelompok_barang_ref ON subkelbrgKelbrgId = kelbrgId 
    LEFT JOIN bidang_barang_ref ON kelbrgBidangbrgId = bidangbrgId 
    LEFT JOIN golongan_barang_ref ON bidangbrgGolbrgId = golbrgId 
) c ON c.barangId = invAtkJenisBarangRefId
WHERE invAtkJenisRefId = '%s' 
  AND ruangId = '%s'
";

$sql['get_log_list_count'] = "
SELECT
   COUNT(*) AS total
FROM
   log_atk
   JOIN inv_atk_brg ON invBrgAtkId = logAtkAtkId
   JOIN inv_atk_det ON invAtkDetId = invDet
   JOIN inv_atk_master ON invAtkMstId = invAtkDetMstId
   JOIN inv_atk_gudang ON invAtkGudangInvDtlbrgId = invBrgAtkId AND invAtkGudangRuangId = logAtkRuangId
WHERE
   invAtkMstJenisPersediaanId = %s AND
   logAtkRuangId = %s AND
   (logAtkStatus = %s OR 'all' = %s) AND
   (logAtkUnitId = %s OR 'all' = %s) AND
   (logAtkTujuanRuangId = %s OR 'all' = %s) AND
   logAtkTgl >= '%s' AND logAtkTgl <= '%s'
";

$sql['get_log_list'] = "
SELECT
   logAtkId as id,
   logAtkTgl,
   invAtkDetBarcode,
   logAtkJmlBrg,
   logAtkHrgSatuan AS hrgsat,
   CASE
      WHEN logAtkStatus = 'Mutasi Ke Unit Lain' THEN ukr2.unitkerjaNama
      WHEN logAtkStatus = 'Mutasi Ke Gudang Lain' THEN CONCAT('<em>',ruangNama,'</em> - ',ukr2.unitkerjaNama)
      WHEN logAtkStatus = 'Register Transaksi Harian' THEN CONCAT('<em>',ata.transAtkNamaPenerima,'</em> - ',ukr3.unitkerjaNama)
      ELSE ''
   END AS tujuanBarang,
   logAtkKeterangan,
   CASE 
   	  WHEN logAtkStatus = 'Penambahan Stock Dengan Usulan' THEN CONCAT(logAtkStatus,IFNULL(CONCAT(' (',logAtkKeterangan,')'),''))
      WHEN logAtkStatus = 'Mutasi Ke Gudang Lain' THEN CONCAT(logAtkStatus,IFNULL(CONCAT(' (',logAtkBAMutasi,')'),''))
      WHEN logAtkStatus = 'Register Transaksi Harian' THEN CONCAT(logAtkStatus,IFNULL(CONCAT(' (',logAtkNoTrans,')'),''))
   	  ELSE logAtkStatus
   END AS logAtkStatus,
   IF(logAtkStatus = 'Penambahan Stock Dengan Pengadaan',IFNULL(v.nomor_spp,vp.nomor_spp),'') AS nospp
FROM
   log_atk
   JOIN inv_atk_brg ON invBrgAtkId = logAtkAtkId
   JOIN inv_atk_det ON invAtkDetId = invDet
   JOIN inv_atk_master ON invAtkMstId = invAtkDetMstId
   JOIN inv_atk_gudang ON invAtkGudangInvDtlbrgId = invBrgAtkId /*AND invAtkGudangRuangId = logAtkRuangId*/
   #LEFT JOIN unit_kerja_ref ON unitkerjaId = logAtkUnitId
   LEFT JOIN ruang ON ruangId = logAtkTujuanRuangId
   LEFT JOIN unit_kerja_ref ukr2 ON ukr2.unitkerjaId = ruangUnitId
   LEFT JOIN inv_map_simpel ims ON ims.invBrgAtkId = logAtkAtkId
   LEFT JOIN inv_map_emonev ime ON ime.invBrgAtkId = logAtkAtkId
   LEFT JOIN v_pembelian_verified v ON v.id = ims.pembLgsngId
   LEFT JOIN v_perintah_bayar_pengadaan vp ON vp.id = ime.pembPengdnId
   LEFT JOIN aset_transaksi_atk ata ON ata.transAtkMstNomor = logAtkNoTrans
   LEFT JOIN unit_kerja_ref ukr3 ON ukr3.unitkerjaId = ata.transAtkMstUnitKerjaId
WHERE
   invAtkMstJenisPersediaanId = %s AND
   logAtkRuangId = %s AND
   (logAtkStatus = %s OR 'all' = %s) AND
   (logAtkUnitId = %s OR 'all' = %s) AND
   (logAtkTujuanRuangId = %s OR 'all' = %s) AND
   logAtkTgl >= '%s' AND logAtkTgl <= '%s'
ORDER BY
   logAtkTgl DESC
LIMIT
   %s, %s
";

$sql['get_data_log_by_id_for_input'] = "
SELECT
   log_atk.*,
   logAtkId AS id,
   invAtkDetBarcode,
   invAtkGudangJumlah,
   IFNULL((SELECT invAtkGudangJumlah FROM inv_atk_gudang WHERE invAtkGudangRuangId = logAtkTujuanRuangId AND invAtkGudangInvDtlbrgId = invBrgAtkId), 999999) AS tujuanJml,
   CASE
      WHEN logAtkStatus = 'Mutasi Ke Unit Lain' THEN unitkerjaNama
      WHEN logAtkStatus = 'Mutasi Ke Gudang Lain' THEN ruangNama
      ELSE ''
   END AS tujuanBarang
FROM
   log_atk
   JOIN inv_atk_brg ON invBrgAtkId = logAtkAtkId
   JOIN inv_atk_det ON invAtkDetId = invDet
   JOIN inv_atk_master ON invAtkMstId = invAtkDetMstId
   JOIN inv_atk_gudang ON invAtkGudangInvDtlbrgId = invBrgAtkId AND invAtkGudangRuangId = logAtkRuangId
   LEFT JOIN unit_kerja_ref ON unitkerjaId = logAtkUnitId
   LEFT JOIN ruang ON ruangId = logAtkTujuanRuangId
WHERE
   logAtkId = %s
LIMIT 1
";

/////////
// DO Query
/////////



// yang dibawah ini belum disesuaikan dengan struktur DB yang baru
// karena sesuai change request, proses perubahan data log bukan update, tapi insert data log baru
$sql['update_log_sirkulasi'] = "
UPDATE
   log_atk
   JOIN inv_atk_det ON invAtkDetId = logAtkAtkId
SET
   logAtkJmlBrg = logAtkJmlBrg + %s,
   invAtkDetJumlah = invAtkDetJumlah + %s
WHERE
   logAtkId = %s
";

$sql['update_gudang_tujuan'] = "
UPDATE
   log_atk
   JOIN inv_atk_det ON (invAtkDetRuangId = logAtkTujuanRuangId)
   JOIN inv_atk_master ON (invAtkMstId = invAtkDetMstId)
SET
   invAtkDetJumlah = invAtkDetJumlah + %s
WHERE
   logAtkId = %s AND
   invAtkMstBarangId = %s
";

$sql['update_log_sirkulasi_before_delete'] = "
UPDATE
   log_atk
   JOIN inv_atk_det AS det_asal ON (det_asal.invAtkDetId = logAtkAtkId)
   JOIN inv_atk_master AS mst_asal ON (mst_asal.invAtkMstId = det_asal.invAtkDetMstId)
   LEFT JOIN inv_atk_det AS det_tujuan ON (det_tujuan.invAtkDetRuangId = logAtkTujuanRuangId AND logAtkStatus = 'Mutasi Ke Gudang Lain')
   LEFT JOIN inv_atk_master AS mst_tujuan ON (mst_tujuan.invAtkMstBarangId = mst_asal.invAtkMstBarangId AND logAtkStatus = 'Mutasi Ke Gudang Lain')
SET
   logAtkRuangId = IF(det_tujuan.invAtkDetJumlah - logAtkJmlBrg < 0, logAtkRuangId, NULL),
   det_asal.invAtkDetJumlah = IF(det_tujuan.invAtkDetJumlah - logAtkJmlBrg < 0, det_asal.invAtkDetJumlah, det_asal.invAtkDetJumlah + logAtkJmlBrg),
   det_tujuan.invAtkDetJumlah = IF(det_tujuan.invAtkDetJumlah - logAtkJmlBrg < 0, det_tujuan.invAtkDetJumlah, det_tujuan.invAtkDetJumlah - logAtkJmlBrg)
WHERE
   logAtkId IN (%s)
";

$sql['delete_log_sirkulasi'] = "
DELETE FROM
   log_atk
WHERE
   logAtkRuangId IS NULL
";
?>
