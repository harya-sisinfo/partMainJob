<?php
$sql['check_date']="
SELECT IFNULL('%s' > MAX(ttpBukuTgl),-1) AS accept,DATE_FORMAT(MAX(ttpBukuTgl),'%%d-%%m-%%Y') AS tgl
FROM tutup_buku_ref
";

//==========GET DATA ======================================
$sql['get_data_user_by_user_name'] = "
SELECT 
   UserId AS user_id,
   UserName AS user_name,
   RealName AS real_name,
   a.Description AS description,
   Active AS is_active,
   a.GroupId AS group_id,
   GroupName AS group_name,
   unitkerjaId,
   unitkerjaNama,
   userunitkerjaRoleId AS role_id,
   unitkerjaKodeSistem
FROM 
   gtfw_user a
   JOIN gtfw_group b ON b.GroupId = a.GroupId
   LEFT JOIN user_unit_kerja ON UserId = userunitkerjaUserId
   LEFT JOIN unit_kerja_ref ON unitkerjaId = userunitkerjaUnitkerjaId
WHERE
   UserName = %s
";

$sql['get_count']="
SELECT FOUND_ROWS() AS total
";

$sql['get_count_data_lelang_barang'] = "
	SELECT
		COUNT(DISTINCT(transAtkMstId)) AS total
	FROM aset_transaksi_atk
		LEFT JOIN aset_transaksi_atk_detil
		  ON transAtkDetTransMstId = transAtkMstId
		LEFT JOIN inv_atk_det
		  ON invAtkDetId = transAtkDetAtkId
		LEFT JOIN inv_atk_brg
		  ON invDet = invAtkDetId
		LEFT JOIN inv_atk_gudang
		  ON invAtkGudangInvDtlbrgId = invBrgAtkId
		LEFT JOIN ruang
		  ON ruangId = invAtkGudangRuangId
		LEFT JOIN unit_kerja_ref
		  ON unitkerjaId = transAtkMstUnitKerjaId
	WHERE transAtkMstNomor LIKE '%s'
		AND transAtkNamaPenerima LIKE '%s' AND ruangId != '%s' AND (transAtkMstUnitKerjaId =  %s
        OR unitkerjaParentId =  %s
        OR '1' =  %s)
";
		
$sql['get_data_lelang_barang']="
	SELECT
		SQL_CALC_FOUND_ROWS
		transAtkMstId         AS trans_id,
		transAtkMstTglEntry   AS trans_tanggal,
		transAtkMstNomor      AS trans_nomor,
		TRIM(transAtkMstKeterangan) AS trans_keterangan,
		TRIM(transAtkNamaPenerima)  AS trans_nama,
		TRIM(unitkerjaNama)   AS trans_unit
	FROM aset_transaksi_atk
	LEFT JOIN aset_transaksi_atk_detil ON transAtkDetTransMstId = transAtkMstId
	LEFT JOIN inv_atk_det ON invAtkDetId = transAtkDetAtkId
   LEFT JOIN inv_atk_brg ON invDet = invAtkDetId
	LEFT JOIN inv_atk_gudang ON invAtkGudangInvDtlbrgId = invBrgAtkId
	LEFT JOIN ruang ON ruangId = invAtkGudangRuangId
	LEFT JOIN unit_kerja_ref ON unitkerjaId = transAtkMstUnitKerjaId
	WHERE 
      transAtkMstNomor LIKE '%s' AND 
      transAtkNamaPenerima LIKE '%s' AND 
      ruangId != '%s' AND 
      (unitkerjaKodeSistem = '%s' OR unitkerjaKodeSistem LIKE '%s') AND 
      transAtkMstTglEntry >= '%s' AND transAtkMstTglEntry <= '%s' AND
      transAtkMstKeterangan NOT LIKE 'No Usul%%'
	GROUP BY transAtkMstId
	ORDER BY transAtkMstTglEntry DESC
	LIMIT %s, %s
";
 
$sql['get_data_pengelolaan_by_id']=" 	
   SELECT
     transAtkMstId              AS trans_id,
     transAtkMstTglEntry        AS trans_tanggal,
     transAtkMstNomor           AS trans_nomor,
     transAtkMstKeterangan      AS trans_keterangan,
     transAtkNamaPenerima       AS trans_nama,
     transAtkDetAtkId           AS barangId,
     CONCAT(LPAD(golbrgKode, 1, '0'), '.', LPAD(bidangbrgKode, 2, '0'), '.', LPAD(kelbrgKode, 2, '0'), '.', LPAD(subkelbrgKode, 2, '0'), '.', LPAD(barangKode, 3, '0'),'.',invAtkKode) AS barangKode,
     invAtkNama                 AS barangNama,
     transAtkDetKeteranganDetil AS barangDiskripsi,
     transAtkDetJmlAtk          AS jumlah,
     transAtkDetNilaiSatuan     AS lelangBiaya,
     (SELECT
        lelangBiaya*jumlah) AS total,
     unitkerjaNama              AS nama_unit
   FROM aset_transaksi_atk
     LEFT JOIN aset_transaksi_atk_detil
       ON transAtkDetTransMstId = transAtkMstId
     LEFT JOIN inv_atk_det
       ON invAtkDetId = transAtkDetAtkId
     LEFT JOIN inv_atk_master
       ON invAtkMstId = invAtkDetMstId
     LEFT JOIN inv_atk_jenis_ref
       ON invAtkMstJenisPersediaanId = invAtkJenisRefId
     LEFT JOIN barang_ref
       ON invAtkJenisBarangRefId = barangId
     LEFT JOIN sub_kelompok_barang_ref
       ON subkelbrgId = barangSubkelbrgId
     LEFT JOIN kelompok_barang_ref
       ON kelbrgId = subkelbrgKelbrgId
     LEFT JOIN bidang_barang_ref
       ON bidangbrgId = kelbrgBidangbrgId
     LEFT JOIN golongan_barang_ref
       ON golbrgId = bidangbrgGolbrgId
     LEFT JOIN unit_kerja_ref
       ON unitkerjaId = transAtkMstUnitKerjaId
	WHERE 
		transAtkMstId = '%s'	
	
";

$sql['get_atk_mst_nomor'] = "
	SELECT
		transAtkMstId,
		transAtkMstNomor
	FROM aset_transaksi_atk
	ORDER BY transAtkMstNomor DESC LIMIT 0,1
"; 

//do-----------------------------------------------------
$sql['do_add_lelang_barang'] = "
	INSERT INTO 
		aset_transaksi_atk(
			transAtkMstTglEntry, 
			transAtkMstNomor, 
			transAtkMstKeterangan, 
			transAtkNamaPenerima, 
			transAtkMstTgl, 
			transAtkMstUserid,
			transAtkMstUnitKerjaId
		)
			VALUES 
      (
			'%s','%s','%s','%s', NOW(), '%s', '%s'
		)
";

$sql['do_add_pengelolaan_det'] = "
	INSERT INTO 
		aset_transaksi_atk_detil(
			transAtkDetTransMstId,
         transAtkDetAtkId,
         transAtkDetKeteranganDetil,
         transAtkDetJmlAtk,
         transAtkDetNilaiSatuan
		)
			VALUES 
		(
			'%s',
			'%s',			
			'%s',
			'%s',
			'%s'
      )
";

//update
$sql['do_update_lelang_barang'] = "
	UPDATE 
		aset_transaksi_atk
	SET 
		transAtkMstTglEntry = '%s',
		transAtkMstKeterangan = '%s',
		transAtkNamaPenerima = '%s',
		transAtkMstTgl = NOW(),
		transAtkMstUnitKerjaId = '%s',
		transAtkMstUserid = '%s'
	WHERE 
		transAtkMstId = '%s'
";

$sql['do_update_pengelolaan_det'] = "
	INSERT INTO 
		aset_transaksi_atk_detil(
			transAtkDetTransMstId,
         transAtkDetAtkId,
         transAtkDetKeteranganDetil,
         transAtkDetJmlAtk,
         transAtkDetNilaiSatuan
		)
			VALUES 
		(
			'%s',
			'%s',			
			'%s',
			'%s',
			'%s'
      )
";

//delete
$sql['do_delete_pengelolaan_det'] = "
	DELETE
	FROM aset_transaksi_atk_detil
	WHERE transAtkDetTransMstId IN ('%s')
";

$sql['do_delete_lelang_barang'] = "
	DELETE
	FROM aset_transaksi_atk
	WHERE transAtkMstId = '%s'
";

$sql['do_delete_pengelolaan_by_array_id'] = "
	DELETE from aset_transaksi_atk
   WHERE 
     transAtkMstId  IN ('%s')
";

$sql['get_mst_id'] = "
	SELECT invAtkMstId FROM inv_atk_master WHERE invAtkMstJenisPersediaanId = '%s'
";

$sql['get_brg_by_mst_id'] = "
	SELECT
		invBrgAtkId
	FROM inv_atk_brg
	LEFT JOIN inv_atk_det
		ON invAtkDetId = invdet
	WHERE invAtkDetMstId = '%s'
";

$sql['get_max_gdg_by_det_id']="
	SELECT
	invAtkGudangJumlah AS jumlah
	FROM inv_atk_gudang
	WHERE invAtkGudangInvDtlbrgId = %s
		AND invAtkGudangRuangId = %s
";

$sql['get_last_stock']="
SELECT SUM(invAtkGudangJumlah) AS stok
FROM inv_atk_gudang iag
JOIN inv_atk_brg iab ON iab.`invBrgAtkId` = iag.`invAtkGudangInvDtlbrgId`
JOIN ruang r ON r.ruangId = iag.`invAtkGudangRuangId`
JOIN inv_atk_det iad ON iad.`invAtkDetId` = iab.`invDet`
JOIN inv_atk_master iam ON iam.`invAtkMstId` = iad.`invAtkDetMstId`
WHERE
   iam.`invAtkMstJenisPersediaanId` = '%s' AND
   iab.`tglPengadaan` <= '%s' AND
   r.`ruangUnitId` = '%s'

";

$sql['get_gdg_by_mst_id']="
SELECT 
	invBrgAtkId,
	invAtkGudangId,
	invAtkGudangRuangId AS ruangId,
	invAtkGudangJumlah AS jmlBrg, 
	invAtkGudangNilaiSatuan AS hrgSatuan
FROM inv_atk_gudang
JOIN inv_atk_brg ON invBrgAtkId = invAtkGudangInvDtlbrgId
JOIN inv_atk_det ON invAtkDetId = invDet
JOIN inv_atk_master ON invAtkMstId = invAtkDetMstId
JOIN ruang ON ruangId = invAtkGudangRuangId
JOIN unit_kerja_ref ON unitkerjaId = ruangUnitId
WHERE 
   invAtkMstJenisPersediaanId = '%s' AND 
   tglPengadaan <= '%s' AND
   ruangUnitId = '%s' AND
   invAtkGudangJumlah != 0 AND
   (unitkerjaKodeSistem = '%s' OR unitkerjaKodeSistem LIKE '%s')
ORDER BY tglPengadaan ASC
";

$sql['get_gudang_ready']="
SELECT 
   invBrgAtkId,
   invAtkGudangId,
   invAtkGudangRuangId AS ruangId,
   invAtkGudangJumlah AS jmlBrg, 
   invAtkGudangNilaiSatuan AS hrgSatuan
FROM inv_atk_gudang
JOIN inv_atk_brg ON invBrgAtkId = invAtkGudangInvDtlbrgId
JOIN inv_atk_det ON invAtkDetId = invDet
JOIN inv_atk_master ON invAtkMstId = invAtkDetMstId
JOIN ruang ON ruangId = invAtkGudangRuangId
WHERE 
   invAtkMstJenisPersediaanId = '%s' AND 
   tglPengadaan <= '%s' AND
   ruangUnitId = '%s' AND
   invAtkGudangJumlah > 0
ORDER BY tglPengadaan ASC
LIMIT 1
";

$sql['update_gudang_max'] = "
UPDATE inv_atk_gudang
SET invAtkGudangJumlah = '%s'
WHERE 
   invAtkGudangRuangId = '%s' AND 
   invAtkGudangInvDtlbrgId = '%s' AND
   invAtkGudangJumlah >= '%s'
";

$sql['add_log_update'] = "
	INSERT INTO `log_atk`
	SET
		`logAtkRuangId` = '%s' ,
		`logAtkAtkId` = '%s' ,
		`logAtkJmlBrg` = '%s' ,
		`logAtkStatus` = 'Register Transaksi Harian',
		`logAtkTgl` = (SELECT transAtkMstTglEntry FROM aset_transaksi_atk WHERE transAtkMstNomor = '%s'),
		`logAtkHrgSatuan` = '%s',
		`logAtkNoTrans`= '%s',
		`logAtkKeterangan` = '%s',
      `logAtkUserId` = '%s'
";
?>
