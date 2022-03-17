<?php
$sql['check_date']="
SELECT IFNULL('%s' > MAX(ttpBukuTgl),-1) AS accept,DATE_FORMAT(MAX(ttpBukuTgl),'%%d-%%m-%%Y') AS tgl
FROM tutup_buku_ref
";

$sql['get_kode_sistem']="
SELECT unitkerjaKodeSistem
FROM user_unit_kerja
LEFT JOIN unit_kerja_ref ON unitkerjaId = userunitkerjaUnitkerjaId
WHERE userunitkerjaUserId = %s
";

$sql['get_data_bhp_pindah_ruang']="
SELECT 
	SQL_CALC_FOUND_ROWS
	bhpMutasiMstId,
	bhpMutasiMstTglMutasi AS tgl,
	bhpMutasiMstBA AS notrans,
   CONCAT(r1.ruangNama,' (',r1.ruangKode,')') AS gdgasal,
	u1.unitkerjaNama AS unitasal,
	CONCAT(r2.ruangNama,' (',r1.ruangKode,')') AS gdgtujuan,
   u2.unitkerjaNama AS unittujuan,
	invAtkNama AS namabarang,
	SUM(bhpMutasiDetJmlBhp) AS jumlah,
	bhpMutasiMstPIC AS pic,
  IF('%s' = u2.unitkerjaKodeSistem,1,0) AS penerima
FROM bhp_mutasi_mst
LEFT JOIN bhp_mutasi_det ON bhpMutasiDetMstId = bhpMutasiMstId
LEFT JOIN unit_kerja_ref u1 ON u1.unitkerjaId = bhpMutasiMstUnitIdAsal
LEFT JOIN unit_kerja_ref u2 ON u2.unitkerjaId = bhpMutasiMstUnitIdTuj
LEFT JOIN ruang r1 ON r1.ruangId = bhpMutasiMstGudangIdAsal
LEFT JOIN ruang r2 ON r2.ruangId = bhpMutasiMstGudangIdTuj
LEFT JOIN inv_atk_jenis_ref ON invAtkJenisRefId = bhpMutasiDetInvAtkId
WHERE 
   bhpMutasiMstBA LIKE '%s' AND 
   invAtkNama LIKE '%s' AND
   (r1.ruangNama LIKE '%s' OR u1.unitkerjaNama LIKE '%s') AND
   (r2.ruangNama LIKE '%s' OR u2.unitkerjaNama LIKE '%s') AND
   (
    (u1.unitkerjaKodeSistem = '%s' OR u1.unitkerjaKodeSistem LIKE '%s') 
    OR u2.unitkerjaKodeSistem = '%s'
   )
GROUP BY bhpMutasiMstId
ORDER BY bhpMutasiMstTglMutasi DESC
LIMIT %s,%s
";

$sql['get_detil_by_id']="
SELECT
   bhpMutasiMstBA AS noBA,
   bhpMutasiMstTglMutasi AS tglMutasi,
   u1.unitkerjaNama AS unitAsal,
   r1.ruangNama AS gudangAsal,
   u2.unitkerjaNama AS unitTujuan,
   r2.ruangNama AS gudangTujuan,
   bhpMutasiMstPIC AS pic,
   bhpMutasiMstKet AS keterangan,
   CONCAT(
      MID(invAtkJenisBarangRefId, 1, 1), '.', MID(invAtkJenisBarangRefId, 2, 2), '.',
      MID(invAtkJenisBarangRefId, 4, 2), '.', MID(invAtkJenisBarangRefId, 6, 2), '.', 
      MID(invAtkJenisBarangRefId, 8, 3),'.',invAtkKode
   ) AS kdBrg,
   invAtkNama AS namaBrg,
   bhpMutasiDetJmlBhp AS jumlah
FROM bhp_mutasi_mst
JOIN bhp_mutasi_det ON bhpMutasiDetMstId = bhpMutasiMstId
JOIN ruang r1 ON r1.ruangId = bhpMutasiMstGudangIdAsal
JOIN unit_kerja_ref u1 ON u1.unitkerjaId = bhpMutasiMstUnitIdAsal
JOIN ruang r2 ON r2.ruangId = bhpMutasiMstGudangIdTuj
JOIN unit_kerja_ref u2 ON u2.unitkerjaId = bhpMutasiMstUnitIdTuj
JOIN inv_atk_jenis_ref ON invAtkJenisRefId = bhpMutasiDetInvAtkId
WHERE bhpMutasiMstId = '%s'
";

$sql['get_data_usulan_bhp'] = "
SELECT
usulanBrgNoUsulan,
usulanBrgDetId,
unitkerjaNama,
unitkerjaId,
a.invAtkNama,
usulanBrgDetJml,
usulanBrgDetJmlPengadaan AS pengadaan,
IF(usulanBrgDetTglPakai = '0000-00-00','-',usulanBrgDetTglPakai) AS usulanBrgDetTglPakai,
c.Kode,
a.invAtkJenisRefId,
IF(usulanBrgDetTglPakai = '0000-00-00',999999,DATEDIFF(usulanBrgDetTglPakai,NOW())) AS counter
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
AND (usulanBrgDetJmlPengadaan IS NOT NULL AND usulanBrgDetJmlPengadaan > 0)
ORDER BY counter ASC
LIMIT %s,%s
";

$sql['get_data_usulan_bhp_by_id']="
SELECT 
	usulanBrgDetBrgId AS barangId, 
	IF(usulanBrgDetJmlPengadaan <= usulanBrgDetJml, usulanBrgDetJmlPengadaan, usulanBrgDetJml) AS stok_pindah
FROM bhp_usulan_brg_det
WHERE usulanBrgDetId = %s
";

$sql['get_data_unitkerja'] = "
SELECT
SQL_CALC_FOUND_ROWS
LPAD((if(tempUnitKode IS NULL,a.unitkerjaKode,tempUnitKode))*100,4,'0') AS kodesatker,
(if(tempUnitNama IS NULL,a.unitkerjaNama,tempUnitNama)) AS satker,
(if(tempUnitId IS NULL,a.unitkerjaId,a.unitkerjaId)) AS id,
a.unitkerjaKode AS kodeunit,
(if(tempUnitNama IS NULL,a.unitkerjaNama,a.unitkerjaNama)) AS unit,
a.unitkerjaParentId AS parentId,
(SELECT COUNT(b.unitkerjaId) FROM unit_kerja_ref b WHERE b.unitkerjaParentId = a.unitkerjaId) AS isParent
FROM unit_kerja_ref a
LEFT JOIN
(SELECT
unitkerjaId AS tempUnitId,
unitkerjaKode AS tempUnitKode,
unitkerjaNama AS tempUnitNama,
unitkerjaParentId AS tempParentId
FROM unit_kerja_ref
WHERE unitkerjaParentId = 0) tmpUnitKerja ON (a.unitkerjaParentId=tempUnitId)
WHERE
(a.unitkerjaKode LIKE '%s' OR
LPAD((if(tempUnitKode IS NULL,a.unitkerjaKode,tempUnitKode))*100,4,'0') LIKE '%s' ) AND
(a.unitkerjaNama LIKE '%s' OR tempUnitNama LIKE '%s') 
[KODE_SISTEM]
GROUP BY a.unitkerjaId
ORDER BY a.unitkerjaKodeSistem
LIMIT %s, %s
";

$sql['get_combo_unit_kerja'] = "
SELECT unitkerjaId AS id, unitkerjaNama AS name
FROM unit_kerja_ref
WHERE unitkerjaKodeSistem = '%s' OR unitkerjaKodeSistem LIKE '%s'
";

$sql['get_combo_gudang']="
SELECT ruangId AS id, CONCAT(ruangKode,' - ',ruangNama) AS name
FROM ruang
WHERE 
   ruangJenisRuangId = '-1' AND 
   ruangUnitId = '%s'
   [EXCLUDE]

";

$sql['get_count']="
SELECT FOUND_ROWS() AS total
";

# POPUP KODE BARANG
$sql['get_data_jenis']="
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
   [VAR_JML_USULAN]
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
WHERE 
   invAtkGudangJumlah != '' AND 
   ruangUnitId = %s AND 
   ruangId = %s AND
   tglPengadaan <= '%s' [SEARCH_BRG_USULAN] AND
   (`golbrgId` = %d %golongan% 1) AND
   (`bidangbrgId`  = %d %bidang% 1) AND
   (`kelbrgId`  = %d %kelompok% 1) AND
   (`subkelbrgId`  = %d %subKelompok% 1) AND
   (CONCAT(LPAD(`golbrgKode`,1,0),'.',LPAD(`bidangbrgKode`,2,0),'.',LPAD(`kelbrgKode`,2,0),'.',LPAD(`subkelbrgKode`,2,0),'.',LPAD(`barangKode`,3,0),'.',`invAtkKode`) LIKE '%s' OR `invAtkNama` LIKE '%s') AND
   (`barangJenisbrgId` = %d %jenisBarang% 1)
GROUP BY invAtkJenisRefId
ORDER BY `kelbrgNama`, `subkelbrgNama`, `invAtkNama`,`invAtkKode`
LIMIT %d, %d
";

$sql['get_data_barang']="
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
";

$sql['get_kelompok_list']="
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
";

$sql['get_sub_kelompok_list']="
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
";

$sql['get_last_insert_id']="
SELECT LAST_INSERT_ID() AS id
";

# ========= UPDATE STOK GUDANG ============ #

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
   r.`ruangId` = '%s'
";

$sql['get_gudang_ready']="
SELECT 
   invBrgAtkId,
   invAtkGudangId,
   invAtkGudangRuangId AS ruangId,
   invAtkGudangJumlah AS jmlBrg, 
   invAtkGudangNilaiSatuan AS hrgSatuan,
   ruangUnitId AS unitId
FROM inv_atk_gudang
JOIN inv_atk_brg ON invBrgAtkId = invAtkGudangInvDtlbrgId
JOIN inv_atk_det ON invAtkDetId = invDet
JOIN inv_atk_master ON invAtkMstId = invAtkDetMstId
JOIN ruang ON ruangId = invAtkGudangRuangId
WHERE 
   invAtkMstJenisPersediaanId = '%s' AND 
   tglPengadaan <= '%s' AND
   ruangId = '%s' AND
   invAtkGudangJumlah > 0
ORDER BY tglPengadaan ASC
LIMIT 1
";

$sql['get_mst_id'] = "
SELECT invAtkMstId 
FROM inv_atk_master 
WHERE invAtkMstJenisPersediaanId = '%s'
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

$sql['get_atk_det_id'] = "
SELECT invAtkDetId 
FROM inv_atk_det
JOIN inv_atk_master ON invAtkMstId = invAtkDetMstId
WHERE 
   (invAtkDetBarcode = '%s' OR '' = '%s') 
   AND invAtkMstJenisPersediaanId  ='%s'
";

$sql['update_gudang_max'] = "
UPDATE inv_atk_gudang
SET invAtkGudangJumlah = '%s'
WHERE 
   invAtkGudangRuangId = '%s' AND 
   invAtkGudangInvDtlbrgId = '%s' AND
   invAtkGudangJumlah >= '%s'
";

$sql['penambahan_atk_brg']="
INSERT INTO `inv_atk_brg`
SET
   `invDet` = '%s',
   `tglPengadaan` = '%s',
   `biaya` = '%s',
   `jumlahSatuan` = '%s',
   `barcode` = '%s'
";

$sql['penambahan_atk_gudang'] = "
INSERT INTO `inv_atk_gudang`
SET
   `invAtkGudangRuangId` = '%s' ,
   `invAtkGudangInvDtlbrgId` = '%s' ,
   `invAtkGudangJumlah` = '%s' ,
   `invAtkGudangNilaiSatuan` = '%s';
";

$sql['add_log_update'] = "
INSERT INTO `log_atk`
SET
  `logAtkRuangId` = '%s' ,
  `logAtkAtkId` = '%s' ,
  `logAtkJmlBrg` = '%s' ,
  `logAtkStatus` = '%s',
  `logAtkTgl` = '%s',
  `logAtkHrgSatuan` = '%s',
  `logAtkBAMutasi` = '%s',
  `logAtkNoTrans`= '%s',
  `logAtkKeterangan` = '%s',
  `logAtkStokSebelum` = '%s',
  `logAtkUnitId` = (SELECT ruangUnitId FROM ruang WHERE ruangId = '%s'),
  `logAtkTujuanRuangId` = '%s',
  `logAtkUserId` = '%s'
";

# ========= INSERT BHP PINDAH GUDANG ========== #

$sql['add_bhp_mutasi_mst']="
INSERT INTO bhp_mutasi_mst
SET
  `bhpMutasiMstUnitIdAsal` = '%s',
  `bhpMutasiMstGudangIdAsal` = '%s',
  `bhpMutasiMstUnitIdTuj` = '%s',
  `bhpMutasiMstGudangIdTuj` = '%s',
  `bhpMutasiMstBhpUsulanDetId` = '%s',
  `bhpMutasiMstTglMutasi` = '%s',
  `bhpMutasiMstPIC` = '%s',
  `bhpMutasiMstBA` = '%s',
  `bhpMutasiMstNoTrans` = '%s',
  `bhpMutasiMstKet` = '%s',
  `bhpMutasiMstUserId` = '%s',
  `bhpMutasiMstTimestamp` = NOW()
";

$sql['add_bhp_mutasi_det']="
INSERT INTO bhp_mutasi_det
SET
  `bhpMutasiDetMstId` = '%s',
  `bhpMutasiDetInvAtkId` = '%s',
  `bhpMutasiDetJmlBhp` = '%s'
";

?>