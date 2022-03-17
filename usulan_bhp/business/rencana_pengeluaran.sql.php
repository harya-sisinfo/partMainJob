<?php

//===GET===
$sql['get_count_row']="
	SELECT FOUND_ROWS() AS total
";

$sql['get_data']="
SELECT 
  SQL_CALC_FOUND_ROWS
  unit_nama,
  program_id,
  program_nomor,
  program_nama,
  kegiatan_id,
  kegiatan_nomor,
  kegiatan_nama,
  jenis_kegiatan,
  subkegiatan_id,
  subkegiatan_nomor,
  subkegiatan_nama,
  kegiatandetail_id,
  is_approve,
  kegiatan_deskripsi,
  jenis_keg_id,
  jenis_kegiatan_nama,
  kegdetRABFile,
  kegThanggarId,
  kegUnitkerjaId,
  islock,
  nilai_pengadaan,
  nilai_nonpengadaan,
  nilai,
  nilai_approve_pengadaan,
  nilai_approve_nonpengadaan,
  nilai_approve ,
  status_approval
FROM (
SELECT 
  uk.unitkerjaNama AS unit_nama,
  prog.programId AS program_id,
  prog.programNomor AS program_nomor,
  prog.programNama AS program_nama,
  sp.subprogId AS kegiatan_id,
  sp.subprogNomor AS kegiatan_nomor,
  sp.subprogNama AS kegiatan_nama,
  sp.subprogJeniskegId AS jenis_kegiatan,
  kr.kegrefId AS subkegiatan_id,
  kr.kegrefNomor AS subkegiatan_nomor,
  kr.kegrefNama AS subkegiatan_nama,
  kd.kegdetId AS kegiatandetail_id,
  kd.kegdetIsAprove AS is_approve,
  kd.kegdetDeskripsi AS kegiatan_deskripsi,
  jk.jeniskegId AS jenis_keg_id,
  jk.jeniskegNama AS jenis_kegiatan_nama,
  kegdetRABFile,
  kegThanggarId,
  kegUnitkerjaId,
  uk.unitKerjaIsLock AS islock,
  SUM(
     IF(komp.kompIsPengadaan = 'Y',
        (rp.rncnpengeluaranSatuan * rp.rncnpengeluaranKomponenNominal * 
           IF(komp.kompFormulaHasil = '0',1,IFNULL(komp.kompFormulaHasil, 1))),
        0)) AS nilai_pengadaan,

  SUM(
     IF(komp.kompIsPengadaan IS NULL OR komp.kompIsPengadaan = 'T',
        (rp.rncnpengeluaranSatuan * rp.rncnpengeluaranKomponenNominal * 
           IF(komp.kompFormulaHasil = '0',1,IFNULL(komp.kompFormulaHasil, 1))),
        0)) AS nilai_nonpengadaan,
  SUM(
      rp.rncnpengeluaranSatuan * rp.rncnpengeluaranKomponenNominal * IF(
        komp.kompFormulaHasil = '0',
        1,
        IFNULL(komp.kompFormulaHasil, 1)
      )
   ) AS nilai,
     
    SUM(IF(komp.kompIsPengadaan = 'Y',
      IF(
        rp.rncnpengeluaranIsAprove = 'Ya',
        1,
        0
      ) * rp.rncnpengeluaranSatuanAprove * rp.rncnpengeluaranKomponenNominalAprove * IF(
        komp.kompFormulaHasil = '0',
        1,
        IFNULL(komp.kompFormulaHasil, 1)
      ),0))AS nilai_approve_pengadaan,
    SUM(IF((komp.kompIsPengadaan IS NULL OR komp.kompIsPengadaan = 'T'),
      IF(
        rp.rncnpengeluaranIsAprove = 'Ya',
        1,
        0
      ) * rp.rncnpengeluaranSatuanAprove * rp.rncnpengeluaranKomponenNominalAprove * IF(
        komp.kompFormulaHasil = '0',
        1,
        IFNULL(komp.kompFormulaHasil, 1)
      ),0))AS nilai_approve_nonpengadaan,
   SUM(
      IF(
        rp.rncnpengeluaranIsAprove = 'Ya',
        1,
        0
      ) * rp.rncnpengeluaranSatuanAprove * rp.rncnpengeluaranKomponenNominalAprove * IF(
        komp.kompFormulaHasil = '0',
        1,
        IFNULL(komp.kompFormulaHasil, 1)
      ))AS nilai_approve ,
      IF((SUM(IF(((komp.kompIsPengadaan ='T' OR komp.kompIsPengadaan IS NULL)
		     AND rp.`rncnpengeluaranId` IS NOT NULL),1,0)) > 0 
		     AND SUM(IF((komp.kompIsPengadaan ='Y'),1,0)) = 0),'N',
              IF((SUM(IF(((komp.kompIsPengadaan ='T' OR komp.kompIsPengadaan IS NULL)
                        AND rp.`rncnpengeluaranId` IS NOT NULL),1,0)) = 0 
                        AND SUM(IF((komp.kompIsPengadaan ='Y'),1,0)) > 0),'P',
                  IF((SUM(IF(((komp.kompIsPengadaan ='T' OR komp.kompIsPengadaan IS NULL)
                          AND rp.`rncnpengeluaranId` IS NOT NULL),1,0)) > 0 
                          AND SUM(IF((komp.kompIsPengadaan ='Y'),1,0)) > 0),'NP',''))) AS isPengadaan,
      IF(
	(SUM(IF(komp.`kompIsSBU` = 1,1,0))> 0 AND (SUM(IF(komp.`kompIsSBU` = 0,1,0)) = 0)),'U',
	  IF((SUM(IF(komp.`kompIsSBU` = 1,1,0))= 0 AND (SUM(IF(komp.`kompIsSBU` = 0,1,0)) > 0)),'K',
	     IF((SUM(IF(komp.`kompIsSBU` = 1,1,0))> 0 AND (SUM(IF(komp.`kompIsSBU` = 0,1,0)) > 0)),'UK','')	
	  )) AS is_sbk_sbu,
	IF(( SUM(IF(rncnpengeluaranIsAprove = 'Tidak',1,0)) > 0 ),0,   /* jika ada yang t maka tolak*/
	     IF(( SUM(IF(rncnpengeluaranIsAprove = 'Tidak',1,0)) = 0 AND 
	          SUM(IF(rncnpengeluaranIsAprove = 'Belum',1,0)) = 0 AND
              SUM(IF(rncnpengeluaranIsAprove = 'Ya',1,0)) > 0 ),1,   /* jika semu y  maka terima */
		      IF((SUM(rp.rncnpengeluaranSatuan * rp.rncnpengeluaranKomponenNominal * 
		           IF(komp.kompFormulaHasil = '0',1,IFNULL(komp.kompFormulaHasil, 1)))) > 0,2,
		           3)/* jika lebih dari nol maka masih proses jika nol berarti belum belanja*/
       )) AS status_approval
FROM
  kegiatan_detail kd 
  LEFT JOIN kegiatan_ref kr 
    ON (kr.kegrefId = kd.kegdetKegrefId) 
  LEFT JOIN sub_program sp 
    ON (sp.subprogId = kr.kegrefSubprogId) 
  LEFT JOIN program_ref prog 
    ON (prog.programId = sp.subprogProgramId) 
  LEFT JOIN kegiatan k 
    ON (k.kegId = kd.kegdetKegId) 
  LEFT JOIN unit_kerja_ref uk 
    ON (uk.unitkerjaId = k.kegUnitkerjaId) 
  LEFT JOIN jenis_kegiatan_ref jk 
    ON (sp.subprogJeniskegId = jk.jeniskegId)
  LEFT JOIN rencana_pengeluaran rp
    ON rp.`rncnpengeluaranKegdetId` = kd.`kegdetId`
  LEFT JOIN komponen komp
    ON( komp.`kompKode` = rp.`rncnpengeluaranKomponenKode`)   
WHERE 
  (kr.kegrefNomor LIKE '%s') 
  AND (kr.kegrefNama LIKE '%s')
GROUP BY kd.`kegdetId`    
ORDER BY program_nomor,
  kegiatan_nomor,
  subkegiatan_nomor,kegdetId ) rpen
WHERE 
	 is_approve = 'Ya' AND status_approval = 1 AND rpen.isPengadaan IN('NP')
LIMIT %s, %s
";

$sql['get_data_detil'] = "
	SELECT
	   rncnpengeluaranId as id,
	   rncnpengeluaranKomponenKode as kode,
	   rncnpengeluaranKomponenNama as nama,
	   rncnpengeluaranKomponenNominal * IF(kompFormulaHasil = '0',1,IFNULL( kompFormulaHasil,1)) as nominal_usulan,
	   rncnpengeluaranSatuan as satuan_usulan,
	   (rncnpengeluaranSatuan * rncnpengeluaranKomponenNominal * IF(kompFormulaHasil = '0',1,
	   IFNULL( kompFormulaHasil,1))) as jumlah_usulan,
	   rncnpengeluaranKomponenNominalAprove * IF(kompFormulaHasil = '0',1,
	   IFNULL( kompFormulaHasil,1)) as nominal_setuju,
	   rncnpengeluaranSatuanAprove  as satuan_setuju,
	   (rncnpengeluaranSatuanAprove * rncnpengeluaranKomponenNominalAprove * IF(kompFormulaHasil = '0',1,
	   IFNULL( kompFormulaHasil,1))) as jumlah_setuju,
       rncnpengeluaranKomponenDeskripsi as deskripsi,
	   rncnpengeluaranIsAprove as approval,
	   (IF(rncnpengeluaranIsAprove ='Tidak','Ditolak',
       IF(rncnpengeluaranIsAprove ='Ya','Disetujui',
          IF(rncnpengeluaranIsAprove ='Belum','Sedang diproses','')))
   ) as status_approve_label,
	   IF(kompFormulaHasil = '0',1,kompFormulaHasil) as hasil_formula,
	   IFNULL(rncnpengeluaranKeterangan, '-') AS keterangan,
	    rncnpengeluaranRumusSatuan AS rumus_satuan
	FROM
		rencana_pengeluaran
		LEFT JOIN kegiatan_detail ON (kegdetId = rncnpengeluaranKegdetId)
		LEFT JOIN komponen ON kompKode = rncnpengeluaranKomponenKode
	WHERE
	  (rncnpengeluaranKegdetId=%s) AND kompIsPengadaan='Y'
	ORDER BY
	  rncnpengeluaranKomponenKode
";

?>
