<?php
	$sql['get_data_dhs_barang_jasa'] = "
		SELECT 
			SQL_CALC_FOUND_ROWS 
			dhsBarangJasaId AS barangId,
			dhsBarangJasaNama AS barangNama,
			satuanbrgNama AS barangSatuan,
			dhsBarangJasaHps AS barangHps,
			dhsBarangJasaSpesifikasi,
			kategoriDhsId,
			dhsBarangJasaBarangId,
			dhsBarangJasaJenisPengadaanId,
			jenisPengadaanIsEditable
		FROM
			aset_ref_dhs_barang_jasa 
			LEFT JOIN aset_ref_kategori_dhs_barang_jasa 
				ON kategoriDhsId = dhsBarangJasaKategoriId 
			LEFT JOIN satuan_barang_ref 
				ON satuanbrgId = dhsBarangJasaSatuanbrgId 
			LEFT JOIN aset_ref_jenis_pengadaan
				ON jenisPengadaanId = dhsBarangJasaJenisPengadaanId
		WHERE dhsBarangJasaNama LIKE '%s' 
		ORDER BY jenisPengadaanId, dhsBarangJasaNama
	";
	
	$sql['get_data_count_dhs_barang_jasa'] = "
		SELECT FOUND_ROWS() AS total
	";
?>
