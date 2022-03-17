<?php

//===GET===
$sql['get_data_periode'] = "
	SELECT 
		periodeId AS id,
		CONCAT(periodeNama,' - ',DATE_FORMAT(periodeTanggalAwal,'%Y')) AS `name`
	FROM 
		aset_ref_periode_usulan
";

$sql['get_data_jenis_pengadaan'] = "
	SELECT 
		jenisPengadaanId AS id,
		jenisPengadaanNama AS `name`
	FROM 
		aset_ref_jenis_pengadaan
	WHERE
		jenisPengadaanIsActive = 'Ya'
";

$sql['get_data_jenis_barang'] = "
   SELECT 
      kategoriDhsId AS `id`,
      kategoriDhsNama AS `name`
   FROM
      aset_ref_kategori_dhs_barang_jasa 
   WHERE kategoriDhsIsActive = 'Ya'
";

$sql['get_data_user_by_id'] = "
SELECT 
   UserId AS user_id,
   UserName AS user_name,
   RealName AS real_name,
   a.Description AS description,
   Active AS is_active,
   a.GroupId AS group_id,
   GroupName AS group_name,
   userunitkerjaUnitkerjaId AS unit_kerja_id,
   userunitkerjaRoleId AS role_id
FROM 
   gtfw_user a
   JOIN gtfw_group b ON b.GroupId = a.GroupId
   LEFT JOIN user_unit_kerja ON UserId = userunitkerjaUserId
WHERE
   UserId = %s
";

$sql['get_data_user_by_username'] = "
SELECT 
   UserId AS user_id,
   UserName AS user_name,
   RealName AS real_name,
   a.Description AS description,
   Active AS is_active,
   a.GroupId AS group_id,
   GroupName AS group_name,
   userunitkerjaUnitkerjaId AS unit_kerja_id,
   userunitkerjaRoleId AS role_id,
   unitkerjaKodeSistem
FROM 
   gtfw_user a
   JOIN gtfw_group b ON b.GroupId = a.GroupId
   LEFT JOIN user_unit_kerja ON UserId = userunitkerjaUserId
   LEFT JOIN unit_kerja_ref ON unitkerjaId = userunitkerjaUnitkerjaId
WHERE
   UserName = '%s'
";

$sql['get_combo_unit'] = "
   SELECT 
      unitkerjaId AS id,
      unitkerjaNama AS name
   FROM unit_kerja_ref
   WHERE
      (unitkerjaKodeSistem = '%s' OR unitkerjaKodeSistem LIKE '%s')
   GROUP BY id
   ORDER BY name
";

$sql['get_data_grid_count'] = "
	SELECT FOUND_ROWS() AS total
";

$sql['get_data_grid'] = "
   SELECT
      SQL_CALC_FOUND_ROWS 
      *  FROM
         (
            SELECT
               `usulanBrgId` AS id,
               `usulanBrgTglUsulan` AS tglUsulan,
               `usulanBrgNoUsulan` AS nomorUsulan,
               /*periodeId,
               CONCAT(periodeNama,' - ',DATE_FORMAT(periodeTanggalAwal, '%%Y')) AS periode,*/
               unitkerjaNama,
               COUNT(DISTINCT `usulanBrgDetId`) AS jumlahItem,
               SUM(usulanBrgNilaiUsulan) AS totalUsulan,
               usulanBrgIsApprove,
               /*IF(( SUM(IF(`usulanBrgDetStatusVerifikasi` = 'Belum',1,0)) > 0 ),'Belum',
                  IF(( SUM(IF(usulanBrgDetStatusVerifikasi = 'Revisi',1,0)) > 0 ),'Revisi',
                     IF((( SUM(IF(usulanBrgDetStatusVerifikasi = 'Ya',1,0)) > 0) AND
                        ( SUM(IF(usulanBrgDetStatusVerifikasi = 'Tidak',1,0)) > 0)) OR
                        ( SUM(IF(usulanBrgDetStatusVerifikasi = 'Ya',1,0)) > 0) ,'Ya',
                     IF(( SUM(IF(usulanBrgDetStatusVerifikasi = 'Tidak',1,0)) > 0) AND
                        ( SUM(IF(usulanBrgDetStatusVerifikasi = 'Ya',1,0)) = 0) AND
                        ( SUM(IF(usulanBrgDetStatusVerifikasi = 'Revisi',1,0)) = 0) AND
                        ( SUM(IF(usulanBrgDetStatusVerifikasi = 'Belum',1,0)) = 0) ,'Tidak',
                        'Belum' 
                     )))
               ) AS `status`,*/
               usulanBrgIsApprove AS `status`,
               concat(us.UserName,' (',us.REalName,')') AS userName,
               concat(usr.UserName,' (',usr.REalName,')') AS verifikator
            FROM
               `bhp_usulan_brg`
               LEFT JOIN `bhp_usulan_brg_det` ON `usulanBrgDetUsulanBrgId` = `usulanBrgId`
               LEFT JOIN inv_atk_jenis_ref ON invAtkJenisRefId = usulanBrgDetBrgId
               JOIN unit_kerja_ref ON unitkerjaId = `usulanBrgUnitId`
               JOIN gtfw_user us ON us.UserId = bhp_usulan_brg.`usulanBrgUserId`
               /*JOIN aset_ref_periode_usulan ON periodeId = `usulanBrgPeriodeId`*/
               LEFT JOIN gtfw_user usr ON usr.UserId = usulanBrgUserApprove
            WHERE
               (usulanBrgTglUsulan >= %s OR %s = '') AND
               (usulanBrgTglUsulan <= (%s + INTERVAL 1 DAY) OR %s = '') AND
               (bhp_usulan_brg.`usulanBrgUnitId` = %s OR %s = 'all') AND
               (invAtkNama LIKE '%s') AND
               (unitkerjaKodeSistem = '%s' OR unitkerjaKodeSistem LIKE '%s') 
               /*(unitkerjaId = '%%s' OR unitkerjaParentId = '%%s' OR '1' = '%%s')*/
            GROUP BY
               `usulanBrgId`
         ) DYN
   WHERE
      (STATUS = '%s' OR 'all' = '%s')
      /*AND (periodeId = '%%s' OR 'all' = '%%s')*/
   ORDER BY unitkerjaNama, tglUsulan DESC
   %LIMIT%
";

$sql['get_data_by_id'] = "
SELECT
   `usulanBrgTglUsulan`,
   `usulanBrgNoUsulan`,
   `usulanBrgUnitId`,
   unitkerjaNama AS usulanBrgUnitNama/*,
   `usulanBrgPeriodeId` AS periode,
   `usulanBrgMAKNama`,
   `usulanBrgMAK`,
   `usulanBrgMAKNominal`,
   `usulanBrgMAKId`,
   periodeNama*/
FROM
   `bhp_usulan_brg`
   JOIN unit_kerja_ref ON  unitkerjaId = `usulanBrgUnitId`
   /*LEFT JOIN aset_ref_periode_usulan ON periodeId = `usulanBrgPeriodeId`*/
WHERE
   `usulanBrgId` = %s
";

$sql['get_detail_by_id'] = "
SELECT
   usulanBrgId,
   `usulanBrgTglUsulan`,
   `usulanBrgNoUsulan`,
   unitkerjaNama,
   /*`usulanBrgPeriodeId` AS periode,
   periodeNama,*/ 
   usulanBrgIsApprove
FROM
   `bhp_usulan_brg`
   JOIN unit_kerja_ref ON unitkerjaId = `usulanBrgUnitId`
  /* LEFT JOIN aset_ref_periode_usulan ON periodeId = `usulanBrgPeriodeId`*/
WHERE
   `usulanBrgId` = %s
";

$sql['get_update_detail_barang_old'] = "
   SELECT 
      `usulanBrgDetId`,
      `usulanBrgDetKodeAkun`,
      usulanBrgDetJenisPengadaanId,
      usulanBrgDetBrgKelompokId,
      usulanBrgDetBrgId,
      dhsBarangJasaNama,
      `usulanBrgDetBrgSpesifikasi`,
      usulanBrgDetBrgSatuan,
      `usulanBrgNilaiHps`,
      `usulanBrgDetJml`,
      usulanBrgDetStatusVerifikasi,
      usulanBrgDetCatatan
   FROM
      `bhp_usulan_brg_det`
      JOIN aset_ref_dhs_barang_jasa ON dhsBarangJasaId = usulanBrgDetBrgId
      JOIN aset_ref_kategori_dhs_barang_jasa ON kategoriDhsId = `usulanBrgDetBrgKelompokId`
      JOIN aset_ref_jenis_pengadaan ON jenisPengadaanId = `usulanBrgDetJenisPengadaanId`
      LEFT JOIN satuan_barang_ref ON satuanbrgId = dhsBarangJasaSatuanbrgId
   WHERE
      usulanBrgDetUsulanBrgId = %s
";

$sql['get_update_detail_barang'] = "
SELECT 
      `usulanBrgDetId` AS id,
      `usulanBrgDetKodeAkun` AS brgKode,
      usulanBrgDetBrgId,
      invAtkNama as brgNama,
      `usulanBrgNilaiHps` AS brgHps,
      IFNULL(`usulanBrgDetJmlPengadaan`, 'Dalam Proses') AS `usulanBrgDetJmlApprove`,
      `usulanBrgNilaiUsulan`,
      `usulanBrgDetJml`,
      `usulanBrgDetBrgSpesifikasi` AS spesifikasi,
      usulanBrgDetTglPakai AS tglPakai,
      usulanBrgDetStatusVerifikasi,
      usulanBrgDetCatatan
   FROM
      `bhp_usulan_brg_det`
      JOIN inv_atk_jenis_ref ON invAtkJenisRefId = usulanBrgDetBrgId
   WHERE
      usulanBrgDetUsulanBrgId = %s
";

$sql['get_detail_barang'] = "
   SELECT 
      `usulanBrgDetId`,
      `usulanBrgDetKodeAkun`,
      invAtkNama as dhsBarangJasaNama,
      `usulanBrgNilaiHps`,
      IFNULL(`usulanBrgDetJmlPengadaan`, 'Dalam Proses') AS `usulanBrgDetJmlApprove`,
      `usulanBrgNilaiUsulan`,
      `usulanBrgDetJml`,
      `usulanBrgDetBrgSpesifikasi`,
      usulanBrgDetStatusVerifikasi,
      usulanBrgDetCatatan
   FROM
      `bhp_usulan_brg_det`
      JOIN inv_atk_jenis_ref ON invAtkJenisRefId = usulanBrgDetBrgId
   WHERE
      usulanBrgDetUsulanBrgId = %s
";

$sql['get_last_mst_id'] = "
SELECT
   MAX(usulanBrgMstId) as id
FROM
   usulan_brg_mst
";

$sql['get_list_barang_to_approve'] = "
SELECT
   usulanBrgDetId AS id,
   unitkerjaId AS usulanUnitId,
   unitkerjaNama,
   usulanBrgMstNoUsulan,
   usulanBrgMstTglUsulan,
   usulan_brg_det.*
FROM
   usulan_brg_det
   JOIN usulan_brg_mst ON usulanBrgMstId = usulanBrgDetMstId
   JOIN unit_kerja_ref ON unitkerjaId = usulanBrgMstUnitId
WHERE
   usulanBrgDetId NOT IN (SELECT usulanKodeBrg FROM usulan_det_brg_rkp) AND
   usulanBrgDetBrgId = %s
";

$sql['get_data_grid_for_print'] = "
SELECT
   usulanBrgMstId,
   usulanBrgMstTglUsulan,
   usulanBrgMstNoUsulan,
   unitkerjaNama,
   CONCAT(RealName,' (',UserName,')') as pengusul,
   usulanBrgMstPIC,
   usulanBrgMstKet
FROM
   usulan_brg_mst
   JOIN unit_kerja_ref ON unitkerjaId = usulanBrgMstUnitId
   JOIN gtfw_user ON UserId = usulanBrgMstUserId
WHERE
   usulanBrgMstId IN (%s)
";

$sql['get_data_grid_for_print_rtf'] = "
SELECT
   usulanBrgMstId,
   usulanBrgMstTglUsulan,
   usulanBrgMstNoUsulan,
   unitkerjaNama,
   CONCAT(RealName,' (',UserName,')') as pengusul,
   usulanBrgMstPIC,
   usulanBrgMstKet
FROM
   usulan_brg_mst
   JOIN unit_kerja_ref ON unitkerjaId = usulanBrgMstUnitId
   JOIN gtfw_user ON UserId = usulanBrgMstUserId
";

$sql['get_data_barang_usulan_for_print'] = "
SELECT 
   usulanBrgDetMstId,
   barangNama,
   usulanBrgDetJml,
   usulanBrgDetJmlPengadaan,
   usulanBrgSpesifikasi AS spesifikasi
FROM
   usulan_brg_det
   JOIN barang_ref ON barangId = usulanBrgDetBrgId
WHERE
   usulanBrgDetMstId IN (%s)
";

////////
// Do Query
////////

$sql['add_usulan'] = "
   INSERT INTO 
      `bhp_usulan_brg` 
   SET 
      `usulanBrgTglUsulan` = '%s',
      `usulanBrgNoUsulan` = '%s',
      `usulanBrgUnitId` = '%s',
      `usulanBrgUserId` = '%s',
      `usulanBrgTglUbah` = NOW()
";

$sql['add_detail_barang'] = "
   INSERT INTO 
      `bhp_usulan_brg_det`
   SET
      `usulanBrgDetUsulanBrgId` = '%s',
      `usulanBrgDetBrgId` = '%s',
      `usulanBrgDetJml` = '%s',
      `usulanBrgNilaiUsulan` = '%s',
      `usulanBrgDetBrgSpesifikasi` = '%s',
      `usulanBrgDetKodeAkun` = '%s',
      /*`usulanBrgDetJenisPengadaanId` = '%%s',*/
      `usulanBrgDetBrgKelompokId` = (SELECT barangSubkelbrgId FROM barang_ref WHERE barangId = '%s'),
      `usulanBrgDetBrgSatuan` = '%s',
      `usulanBrgNilaiHps` = '%s',
      `usulanBrgDetBrgFile` = '%s',
      usulanBrgDetTglPakai = '%s',
      usulanBrgDetStatusVerifikasi = 'Belum',
      usulanBrgDetCatatan = '%s',
      usulanBrgTanggal = NOW(),
      usulanBrgUserId = '%s'
";
//`usulanBrgDetBrgSatuan` = '%s',
$sql['delete_detail_barang'] = "
   DELETE FROM
      bhp_usulan_brg_det
   WHERE
      usulanBrgDetUsulanBrgId = '%s' AND (usulanBrgDetStatusVerifikasi != 'Tidak' AND usulanBrgDetStatusVerifikasi != 'Ya' )
";

$sql['update_usulan'] = "
UPDATE
   bhp_usulan_brg
SET
   `usulanBrgTglUsulan` = '%s',
   `usulanBrgNoUsulan` = '%s',
   `usulanBrgUnitId` = '%s',
   `usulanBrgUserId` = '%s',
   `usulanBrgTglUbah` = NOW()
WHERE
   usulanBrgId = %s
";

$sql['delete_usulan'] = "
DELETE FROM
   bhp_usulan_brg
WHERE
   usulanBrgMstId IN (%VALUE%)
";

$sql['update_approval'] = "
UPDATE 
   bhp_usulan_brg 
SET
   usulanBrgIsApprove = '%s',
   usulanBrgTglApprove = NOW(),
   usulanBrgUserApprove = '%s'
WHERE usulanBrgId = '%s'
";

$sql['update_approval_detil'] = "
UPDATE 
   bhp_usulan_brg_det 
SET
   usulanBrgDetTanggalUpdate = NOW(),
   usulanBrgUserIdVerifikasi = '%s'
WHERE usulanBrgDetUsulanBrgId = '%s' AND (usulanBrgDetStatusVerifikasi = 'Belum' OR usulanBrgDetStatusVerifikasi = 'Revisi')
";

$sql['add_rekap_detail'] = "
INSERT INTO
   usulan_det_brg_rkp (usulanRkpId, usulanKodeBrg, usulanUnitId)
VALUES
(
   (SELECT MAX(usulanRkpId) FROM usulan_brg_rkp),
   %s,
   %s
)
";

$sql['update_detail_barang'] = "
UPDATE
   usulan_brg_det
SET
   usulanBrgDetJmlPengadaan = %s,
   usulanBrgStjTotal1 = %s,
   usulanBrgStjTotal2 = %s
WHERE
   usulanBrgDetId = %s
";
?>
