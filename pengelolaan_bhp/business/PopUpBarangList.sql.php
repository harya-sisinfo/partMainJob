<?php

$sql['template'] =
   ("
      INSERT INTO
      VALUES
      SELECT
      UPDATE
      SET
      DELETE
      FROM
      WHERE
      GROUP BY
      ORDER BY
      LIMIT
   ");
   
// Get query....
$sql['get_data_barang_count'] ="
	SELECT
		COUNT(DISTINCT(CONCAT(LPAD(golbrgKode, 1, '0'), '.', LPAD(bidangbrgKode, 2, '0'), '.', LPAD(kelbrgKode, 2, '0'), '.', LPAD(subkelbrgKode, 2, '0'), '.', LPAD(barangKode, 3, '0')))) AS total
	FROM inv_atk_det
		LEFT JOIN inv_atk_master
		  ON invAtkMstId = invAtkDetMstId
		LEFT JOIN barang_ref
		  ON invAtkMstBarangId = barangId
		LEFT JOIN sub_kelompok_barang_ref
		  ON subkelbrgId = barangSubkelbrgId
		LEFT JOIN kelompok_barang_ref
		  ON kelbrgId = subkelbrgKelbrgId
		LEFT JOIN bidang_barang_ref
		  ON bidangbrgId = kelbrgBidangbrgId
		LEFT JOIN golongan_barang_ref
		  ON golbrgId = bidangbrgGolbrgId
		LEFT JOIN inv_atk_brg
		  ON invDet = invAtkDetId
		LEFT JOIN inv_atk_gudang
		  ON invAtkGudangInvDtlbrgId = invBrgAtkId
		LEFT JOIN ruang
		  ON ruangId = invAtkGudangRuangId
	WHERE barangNama LIKE '%s'
		AND CONCAT(LPAD(golbrgKode, 1, '0'), '.', LPAD(bidangbrgKode, 2, '0'), '.', LPAD(kelbrgKode, 2, '0'), '.', LPAD(subkelbrgKode, 2, '0'), '.', LPAD(barangKode, 3, '0')) LIKE '%s' 
		AND ruangId !=  %s
		ORDER BY barangNama ASC
";

$sql['get_data_barang'] ="
	SELECT
		invAtkMstBarangId   AS mst_id,
		MAX(invAtkDetId)   AS barang_id,
		CONCAT(LPAD(golbrgKode, 1, '0'), '.', LPAD(bidangbrgKode, 2, '0'), '.', LPAD(kelbrgKode, 2, '0'), '.', LPAD(subkelbrgKode, 2, '0'), '.', LPAD(barangKode, 3, '0')) AS barang_barcode,
		barangNama    AS barang_nama,
		invAtkDetMerk AS barang_merk,
		SUM(invAtkGudangJumlah) AS stok,
		invAtkGudangRuangId AS ruang_id
	FROM inv_atk_det
		LEFT JOIN inv_atk_master
		  ON invAtkMstId = invAtkDetMstId
		LEFT JOIN barang_ref
		  ON invAtkMstBarangId = barangId
		LEFT JOIN sub_kelompok_barang_ref
		  ON subkelbrgId = barangSubkelbrgId
		LEFT JOIN kelompok_barang_ref
		  ON kelbrgId = subkelbrgKelbrgId
		LEFT JOIN bidang_barang_ref
		  ON bidangbrgId = kelbrgBidangbrgId
		LEFT JOIN golongan_barang_ref
		  ON golbrgId = bidangbrgGolbrgId
		LEFT JOIN inv_atk_brg
		  ON invDet = invAtkDetId
		LEFT JOIN inv_atk_gudang
		  ON invAtkGudangInvDtlbrgId = invBrgAtkId
		LEFT JOIN ruang
		  ON ruangId = invAtkGudangRuangId
	WHERE barangNama LIKE '%s'
		AND CONCAT(LPAD(golbrgKode, 2, '0'), '.', LPAD(bidangbrgKode, 2, '0'), '.', LPAD(kelbrgKode, 2, '0'), '.', LPAD(subkelbrgKode, 2, '0'), '.', LPAD(barangKode, 3, '0')) LIKE '%s' 
		AND ruangId !=  % s
	GROUP BY barang_barcode
	LIMIT %s, %s
";
?>
