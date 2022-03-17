<?php

   $sql['check_date']="
   SELECT IFNULL('%s' > MAX(ttpBukuTgl),-1) AS accept,DATE_FORMAT(MAX(ttpBukuTgl),'%%d-%%m-%%Y') AS tgl
   FROM tutup_buku_ref
   ";
   
   $sql['penambahan_atk_mst'] ="
      INSERT IGNORE INTO `inv_atk_master`
      SET `invAtkMstJenisPersediaanId` = '%s',
         `invAtkMstSatuanBarang` = '%s',
         `invAtkMstKeteranganLain` = '%s',
         `invAtkMstUserId` = '%s'
   ";

   $sql['penambahan_atk_detil']="
      INSERT IGNORE INTO `inv_atk_det`
      SET
         `invAtkDetMstId` = %s,
         `invAtkDetBarcode` = '%s',
         `invAtkDetLabel` = '%s',
         `invAtkDetMerk` = '%s',
         `invAtkDetSpesifikasi` = '%s',
         `invAtkDetUserId` = '%s'
   ";

   $sql['penambahan_atk_biaya'] = "
      INSERT IGNORE INTO `inv_atk_biaya`
	   SET
	   `invAtkBiayaAktId` = %s,
	   `invAtkBiayaBiayaId` = '1' ,
	   `invAtkBiayaNominal` = '%s'
   ";

   $sql['update_atk_biaya'] = "
      UPDATE `inv_atk_biaya`
	   SET
	   `invAtkBiayaAktId` = %s,
	   `invAtkBiayaBiayaId` = '1' ,
	   `invAtkBiayaNominal` = '%s'
	   WHERE
	      invAtkBiayaId = '%s'
   ";

   $sql['get_atk_biaya_by_mst_id'] = "
      SELECT
         `invAtkBiayaId`
      FROM `inv_atk_biaya`
      WHERE invAtkBiayaAktId = '%s'
   ";
	
	$sql['get_atk_brg_by_mst_id'] = "
      SELECT
		  invBrgAtkId
		FROM inv_atk_brg
		  LEFT JOIN inv_atk_det
			 ON invAtkDetId = invdet
		WHERE invAtkDetMstId = '%s'
   ";

   $sql['penambahan_atk_brg']="
      INSERT IGNORE INTO `inv_atk_brg`
	   SET
	   `invDet` = '%s' ,
	   `tglPengadaan` = '%s',
	   `biaya` = '%s' ,
	   `jumlahSatuan` = '%s',
      `barcode` = '%s'
   ";
	
	$sql['perubahan_atk_brg']="
      UPDATE `inv_atk_brg`
	   SET
	   `biaya` = '%s' ,
	   `jumlahSatuan` = '%s',
	   `tglPengadaan` = DATE(NOW())
	   WHERE
	      invDet = '%s'	   
   ";

   $sql['get_atk_max_brg']="
      SELECT MAX(invBrgAtkId) AS invBrgAtkId FROM inv_atk_brg
   ";
	
	$sql['get_atk_max_gdg_by_det_id']="
      SELECT
		  invAtkGudangJumlah AS jumlah
		FROM inv_atk_gudang
		WHERE invAtkGudangInvDtlbrgId = %s
			 AND invAtkGudangRuangId = %s
   ";

   $sql['add_log_atk'] = "
      INSERT INTO `log_atk`
	   SET
	   `logAtkRuangId` = '%s' ,
	   `logAtkAtkId` = '%s' ,
	   `logAtkJmlBrg` = '%s' ,
	   `logAtkStatus` = '%s' ,
	   `logAtkTgl` = '%s',
      `logAtkHrgSatuan` = '%s',
	   `logAtkKeterangan` = '%s',
      `logAtkUserId` = '%s'
   ";
	
	$sql['add_log_atk_update'] = "
      INSERT INTO `log_atk`
	   SET
	   `logAtkRuangId` = '%s' ,
	   `logAtkAtkId` = '%s' ,
	   `logAtkJmlBrg` = '%s' ,
	   `logAtkStatus` = 'Adjustment Stock' ,
	   `logAtkTgl` = NOW(),
      `logAtkHrgSatuan` = '%s',
      `logAtkStokSebelum` = '%s',
	   `logAtkKeterangan` = '%s',
      `logAtkUserId` = '%s'
   ";

   $sql['penambahan_atk_gudang'] = "
      INSERT IGNORE INTO `inv_atk_gudang`
	   SET
	   `invAtkGudangRuangId` = '%s' ,
	   `invAtkGudangInvDtlbrgId` = '%s' ,
	   `invAtkGudangJumlah` = '%s' ,
	   `invAtkGudangNilaiSatuan` = '%s';
   ";
	
	$sql['update_atk_gudang_max'] = "
      UPDATE inv_atk_gudang
			SET invAtkGudangJumlah = '%s'
			WHERE invAtkGudangRuangId = '%s'
				 AND invAtkGudangInvDtlbrgId = '%s'
   ";
	
	$sql['perubahan_atk_gudang'] = "
      UPDATE `inv_atk_gudang`
	   SET
			`invAtkGudangJumlah` = '%s' ,
			`invAtkGudangNilaiSatuan` = '%s'
		WHERE
			`invAtkGudangRuangId` = '%s' AND 
			`invAtkGudangInvDtlbrgId` = '%s'
   ";

   $sql['get_atk_mst_id'] = "
      SELECT invAtkMstId FROM inv_atk_master WHERE invAtkMstJenisPersediaanId = '%s'
   ";

   $sql['get_atk_det_id'] = "
      SELECT invAtkDetId FROM inv_atk_det WHERE (invAtkDetBarcode='%s' OR ''='%s') AND invAtkDetMstId='%s';
   ";

   $sql['update_atk_detil'] = "
      UPDATE `inv_atk_det`
      SET
      `invAtkDetMstId` = '%s' ,
      `invAtkDetLabel` = '%s' ,
      `invAtkDetMerk` = '%s' ,
      `invAtkDetSpesifikasi` = '%s' ,
      `invAtkDetUserId` = '%s'
      WHERE
      `invAtkDetId` = '%s' ;
   ";
   
   $sql['get_kode_sistem'] = "
   SELECT unitkerjaKodeSistem
   FROM user_unit_kerja
   LEFT JOIN unit_kerja_ref ON unitkerjaId = userunitkerjaUnitkerjaId
   WHERE userunitkerjaUserId = '%s'
   ";
   
   $sql['get_count']="
   SELECT FOUND_ROWS() AS total
   ";
   
   $sql['get_data_usulan_bhp'] = "
   SELECT 
   		usulanBrgNoUsulan,
   		usulanBrgDetId,
   		unitkerjaNama,
   		a.invAtkNama,
   		usulanBrgDetJml,
   		(usulanBrgDetJml - usulanBrgDetJmlPengadaan) AS sisa,
   		IF(usulanBrgDetTglPakai = '0000-00-00','-',usulanBrgDetTglPakai) AS usulanBrgDetTglPakai,
   		c.Kode,
   		a.invAtkJenisRefId,
   		DATEDIFF(usulanBrgDetTglPakai,NOW()) AS counter
   FROM bhp_usulan_brg_det
   LEFT JOIN bhp_usulan_brg ON usulanBrgId = usulanBrgDetUsulanBrgId
   LEFT JOIN inv_atk_jenis_ref a ON a.invAtkJenisRefId = usulanBrgDetBrgId
   LEFT JOIN unit_kerja_ref ON unitkerjaId = usulanBrgUnitId
   LEFT JOIN (
	   	SELECT invAtkJenisRefId, CONCAT(LPAD(`golbrgKode`,1,0),'.',LPAD(`bidangbrgKode`,2,0),'.',LPAD(`kelbrgKode`,2,0),'.', LPAD(`subkelbrgKode`,2,0),'.',LPAD(`barangKode`,3,0),'.',`invAtkKode`) AS `kode`
		FROM `golongan_barang_ref`
		JOIN `bidang_barang_ref` ON (`bidangbrgGolbrgId` = `golbrgId`)
		JOIN `kelompok_barang_ref` ON (`kelbrgBidangbrgId` = `bidangbrgId`)
		JOIN `sub_kelompok_barang_ref` ON (`subkelbrgKelbrgId` = `kelbrgId`)
		JOIN `barang_ref` ON (`barangSubkelbrgId` = `subkelbrgId`)
		JOIN `inv_atk_jenis_ref` ON (invAtkJenisBarangRefId = barangId)
   ) c ON c.invAtkJenisRefId = a.invAtkJenisRefId
   WHERE 
   		usulanBrgNoUsulan LIKE '%s'AND
   		invAtkNama LIKE '%s' 
   		[SEARCH_UNIT]
   		AND (unitkerjaKodeSistem = '%s' OR unitkerjaKodeSistem LIKE '%s')
   		AND usulanBrgIsApprove = 'Ya'
   		AND (usulanBrgDetJmlPengadaan IS NULL OR usulanBrgDetJml > usulanBrgDetJmlPengadaan)
   ORDER BY counter ASC
   LIMIT %s,%s
   ";
   
   $sql['get_unit_kerja'] = "
   SELECT unitkerjaId AS id, unitkerjaNama AS name
   FROM unit_kerja_ref
   WHERE (unitkerjaKodeSistem = '%s' OR unitkerjaKodeSistem LIKE '%s')
   ";
   
   $sql['update_usulan_bhp_detail'] = "
   UPDATE bhp_usulan_brg_det
   SET usulanBrgDetJmlPengadaan = IF(usulanBrgDetJmlPengadaan IS NULL,'%s',usulanBrgDetJmlPengadaan+%d)
   WHERE usulanBrgDetId = '%s'
   ";

   $sql['get_gudang_by_mst_id']="
   SELECT 
      invAtkGudangId AS invGdgId,
      invAtkGudangInvDtlbrgId AS invBrgAtkId,
      invAtkGudangRuangId AS ruangId,
      invAtkGudangJumlah AS jml,
      invAtkGudangNilaiSatuan AS hrgSat
   FROM inv_atk_gudang
   JOIN inv_atk_brg ON invBrgAtkId = invAtkGudangInvDtlbrgId
   JOIN inv_atk_det ON invAtkDetId = invDet
   WHERE 
      invAtkGudangJumlah != 0 AND 
      invAtkDetMstId = '%s' AND 
      invAtkGudangRuangId = '%s'
   ORDER BY tglPengadaan ASC
   ";

   $sql['get_kode_sistem_by_ruang']="
   SELECT unitkerjaKodeSistem
   FROM ruang
   JOIN unit_kerja_ref ON unitkerjaId = ruangUnitId
   WHERE ruangId = '%s'
   ";

   $sql['get_hrg_sat_akhir']="
   SELECT logAtkHrgSatuan AS hrgSat 
   FROM log_atk
   LEFT JOIN inv_atk_brg ON invBrgAtkId = logAtkAtkId
   LEFT JOIN inv_atk_det ON invAtkDetId = invDet
   LEFT JOIN ruang ON ruangId = logAtkRuangId
   LEFT JOIN unit_kerja_ref ON unitkerjaId = ruangUnitId
   WHERE 
      invAtkDetMstId = '%s' AND 
      (unitkerjaKodeSistem = '%s' OR unitkerjaKodeSistem LIKE '%s') AND 
      logAtkStatus LIKE 'Penambahan%%'
   ORDER BY logAtkTgl DESC
   LIMIT 1
   ";

   $sql['update_gudang']="
   UPDATE inv_atk_gudang
   SET invAtkGudangJumlah = '%s'
   WHERE invAtkGudangId = '%s'
   ";
?>
