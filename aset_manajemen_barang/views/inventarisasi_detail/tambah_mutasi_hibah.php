<?php echo Modules::run('breadcrump'); ?>

<script type="text/javascript">
function changeKodeAset() {
    var selectBox = document.getElementById("kode_aset");
    var selectedValue = selectBox.options[selectBox.selectedIndex].value;
    //alert(selectedValue);
    
    var selectedText = selectBox.options[selectBox.selectedIndex].text;
    //alert(selectedText);
    
    document.getElementById("nama_aset").value = selectedText.substring(17);
    document.getElementById("label_aset").value = selectedText.substring(17);
}

function changeLuasTanah() {
    var LuasTanah = document.getElementById("luas_tanah").value;
    //alert(LuasTanah);
    
    var NilaiKuitansi = document.getElementById("nilai_kuitansi").value;
    //alert(NilaiKuitansi);
    
    if (NilaiKuitansi!=0) {
        NilaiPerolehan = (NilaiKuitansi/LuasTanah)*10;
        document.getElementById("nilai_perolehan").value = NilaiPerolehan;
    }
    
    if (NilaiPerolehan!=0) {
        document.getElementById("total_perolehan").value = LuasTanah*NilaiPerolehan;
    }
}

function changeNilaiKuitansi() {
    var LuasTanah = document.getElementById("luas_tanah").value;
    //alert(LuasTanah);
    
    var NilaiKuitansi = document.getElementById("nilai_kuitansi").value;
    //alert(NilaiKuitansi);
    
    if (NilaiKuitansi!=0) {
        NilaiPerolehan = (NilaiKuitansi/LuasTanah)*10;
        document.getElementById("nilai_perolehan").value = NilaiPerolehan;
    }
    
    if (NilaiPerolehan!=0) {
        document.getElementById("total_perolehan").value = LuasTanah*NilaiPerolehan;
    }
}

function changeJumlahAset() {
    var JumlahAset = document.getElementById("jumlah_aset").value;
    //alert(JumlahAset);
    
    var NilaiPerolehan = document.getElementById("nilai_perolehan").value;
    //alert(NilaiPerolehan);
    
    document.getElementById("total_perolehan").value = JumlahAset*NilaiPerolehan;
}

function changeNilaiPerolehan() {
    var JumlahAset = document.getElementById("jumlah_aset").value;
    //alert(JumlahAset);
    
    var NilaiPerolehan = document.getElementById("nilai_perolehan").value;
    //alert(NilaiPerolehan);
    
    document.getElementById("total_perolehan").value = (JumlahAset*NilaiPerolehan)*10;
}
</script>

<!--<form method="post" action="<?php echo $form_action; ?>" class="panel form-horizontal xhr dest_subcontent-element" >-->
<form rel="ajax-file" enctype="multipart/form-data" id="add_mutasi_hibah" action="<?php echo $form_action; ?>" class="panel form-horizontal xhr dest_subcontent-element" method="post">
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	<input type="hidden" name="lastStage" value="<?php echo $this->security->get_csrf_hash(); ?>" />
    <input type="hidden" name="id_unit" value="<?php echo $dataUnit->id; ?>" />
    <input type="hidden" name="form" value="hibah" />

	<div class="panel-heading">
		<span class="panel-title"><b>Tambah</b> Mutasi Hibah</span>
	</div>

	<div class="panel-body">
        <?php //echo "<pre>"; print_r($jenisAset); echo "</pre>"; ?>
        <?php //echo "<pre>"; print_r($sumberDana); echo "</pre>"; ?>
        <?php //echo "<pre>"; print_r($kodeAset); echo "</pre>"; ?>
        <?php //echo "<pre>"; print_r($dataUnit); echo "</pre>"; ?>
        <?php //echo "Golongan: ".$golongan; ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Jenis Aset</label>
					<div class="col-sm-6">
						<select class="form-control" name="jenis_aset" id="jenis_aset" onchange="submit();">
                            <?php
                                if (!empty($jenisAset)) {
                                    foreach ($jenisAset as $jenis) {
                                        $selected = '';
                                        if ($jenis->id == $golongan)
                                        $selected = ' selected="selected" ';
                                        echo ' <option value="' . $jenis->id . '" '.$selected.' >' . substr($jenis->nama, 7) . '</option>';
                                    }
                                }
                            ?>
						</select>
                        <input type="hidden" name="tombol_proses" value="pilih" />
					</div>
				</div>
			</div>
		</div>
        
        <!--
        <div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Kode Aset</label>
					<div class="col-sm-6">
                        <select class="form-control select-kode-aset" name="kode_aset" id="kode_aset" onchange="changeKodeAset();" >
                            <option></option>
                            <?php
                                if (!empty($kodeAset)) {
                                    foreach ($kodeAset as $kode) {
                                        echo ' <option value="' . $kode->id . '">'. $kode->kode . ' | ' . $kode->nama . '</option>';
                                    }
                                }
                            ?>
                        </select>
					</div>
					<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
        -->
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Kode aset</label>
					<div class="col-sm-6">
						<input type="text" readonly class="form-control" placeholder="Kode Aset" name="kode_tampil" id="kode_tampil" />
						<input type="hidden" autocomplete="off" class="form-control" name="kode_aset" />
						<?php echo form_error('kode_aset', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>
                    <font class="col-sm-1" color="red">*</font>
					<div class="col-sm-2">
						<a id="listBarang" rel='async' ajaxify="<?php echo modal("Pilih Kode Aset", 'aset_manajemen_barang', 'inventarisasi_detail', 'list_kode_aset')?>" class="btn btn-sm btn-labeled btn-info"><i class="fa fa-search btn-label-icon left"></i>Cari</a>
					</div>
					
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Nama Aset</label>
					<div class="col-sm-6">
						<input type="text" autocomplete="off" placeholder="Nama Aset" class="form-control" name="nama_aset" id="nama_aset" />
					</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Label Aset</label>
					<div class="col-sm-6">
						<input type="text" autocomplete="off" placeholder="Label Aset" class="form-control" name="label_aset" id="label_aset" readonly="readonly" />
					</div>
					<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Merk</label>
					<div class="col-sm-6">
						<input type="text" autocomplete="off" placeholder="Merk" class="form-control" name="merk" />
					</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Spesifikasi</label>
					<div class="col-sm-6">
                        <textarea class="form-control" name="spesifikasi" rows="4" cols="60"></textarea>
					</div>
				</div>
			</div>
		</div>
        <?php $date = date("Y-m-d");//echo $date; ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Tanggal Pembelian</label>
					<div class="col-sm-3">
						<div class="input-group date day">
							<input type="text" name="tanggal_pembelian" class="form-control" placeholder="Tanggal Pembelian" autocomplete="off" value="<?php echo $date; ?>" />
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						</div>
					</div>
					<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Tanggal Pembukuan</label>
					<div class="col-sm-3">
						<div class="input-group date day">
							<input type="text" name="tanggal_pembukuan" class="form-control" placeholder="Tanggal Pembukuan" autocomplete="off" value="" />
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						</div>
					</div>
					<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Sumber Dana</label>
					<div class="col-sm-6">
                        <select class="form-control select-sumber-dana" name="sumber_dana" id="sumber_dana" >
                            <option></option>
                            <?php
                                if (!empty($sumberDana)) {
                                    //foreach ($sumberDana as $dana) {
                                        //echo ' <option value="' . $dana->id . '">'. $dana->nama . '</option>';
                                    //}
                                    
                                    foreach ($sumberDana as $dana) {
                                        $selected = '';
                                        if ($dana->id == $def_sumber_dana)
                                        $selected = ' selected="selected" ';
                                        echo ' <option value="' . $dana->id . '" '.$selected.' >' . $dana->nama . '</option>';
                                    }
                                }
                            ?>
                        </select>
					</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Pemberi Hibah</label>
					<div class="col-sm-6">
						<input type="text" autocomplete="off" placeholder="Pemberi Hibah" class="form-control" name="pemberi_hibah" />
					</div>
					<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Nomor Referensi</label>
					<div class="col-sm-6">
						<input type="text" autocomplete="off" placeholder="Nomor Referensi" class="form-control" name="nomor_referensi" />
					</div>
					<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
        
        <?php
        if (($golongan==2) or ($golongan==5)) {
            $label_perolehan = 'Nilai Perolehan per Meter';
        ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Luas Tanah</label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="Luas Tanah" class="form-control" name="luas_tanah" id="luas_tanah" onkeypress="changeLuasTanah();" />
                        <input type="hidden" name="jumlah_aset" value="1" />
					</div>
					&#13217; <font color="red">*</font>
				</div>
			</div>
		</div>
        <?php
        } else {
            $label_perolehan = 'Nilai Perolehan per Satuan';
        ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Jumlah Aset</label>
					<div class="col-sm-2">
						<input type="text" autocomplete="off" placeholder="Jumlah Aset" class="form-control" name="jumlah_aset" id="jumlah_aset" onkeypress="changeJumlahAset();" />
					</div>
					<div class="col-sm-4">
                        <select class="form-control select-satuan" name="satuan" id="satuan" >
                            <option></option>
                            <?php
                                if (!empty($satuanBarang)) {
                                    foreach ($satuanBarang as $satuan) {
                                        echo ' <option value="' . $satuan->id . '">'. $satuan->nama . '</option>';
                                    }
                                }
                            ?>
                        </select>
					</div>
				</div>
			</div>
		</div>
        <?php
        }

        if (($golongan==2) or ($golongan==5)) {
        ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Nilai Kuitansi</label>
                    <div class="col-sm-1">Rp.</div>
					<div class="col-sm-4"> 
                        <input type="text" autocomplete="off" placeholder="Nilai Kuitansi" class="form-control" name="nilai_kuitansi" id="nilai_kuitansi" value="0" onkeypress="changeNilaiKuitansi();" />
					</div>
					<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
        <?php
        }
        ?>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'><?php echo $label_perolehan; ?></label>
                    <div class="col-sm-1">Rp.</div>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="<?php echo $label_perolehan; ?>" class="form-control" name="nilai_perolehan" id="nilai_perolehan" value="0" onkeypress="changeNilaiPerolehan();" />
					</div>
					<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Total Perolehan</label>
                    <div class="col-sm-1">Rp.</div>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="Total Perolehan" class="form-control" name="total_perolehan" id="total_perolehan" value="0" readonly="readonly" />
					</div>
				</div>
			</div>
		</div>
        
        <?php
        if (($golongan==2) or ($golongan==4) or ($golongan==5)) {
        ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Lokasi</label>
					<div class="col-sm-6">
						<input type="text" autocomplete="off" placeholder="Lokasi" class="form-control" name="lokasi" />
					</div>
				</div>
			</div>
		</div>
        <?php
        }
        
        if (($golongan==2) or ($golongan==5)) {
        ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Kegunaan</label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="Kegunaan" class="form-control" name="kegunaan" />
					</div>
				</div>
			</div>
		</div>
        <?php
        }
        ?>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Kepemilikan</label>
					<div class="col-sm-4">
                        <select class="form-control select-kepemilikan" name="kepemilikan" id="kepemilikan" >
                            <option></option>
                            <?php
                                if (!empty($kepemilikan)) {
                                    //foreach ($kepemilikan as $milik) {
                                        //echo ' <option value="' . $milik->id . '">'. $milik->nama . '</option>';
                                    //}
                                    
                                    foreach ($kepemilikan as $milik) {
                                        $selected = '';
                                        if ($milik->id == $def_kepemilikan)
                                        $selected = ' selected="selected" ';
                                        echo ' <option value="' . $milik->id . '" '.$selected.' >' . $milik->nama . '</option>';
                                    }
                                }
                            ?>
                        </select>
					</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Penguasaan</label>
					<div class="col-sm-4">
						<select class="form-control" name="penguasaan" id="penguasaan">
                            <option value="1">Hak Penuh</option>
                            <option value="2">Hak Guna</option>
						</select>
					</div>
				</div>
			</div>
		</div>
        
        <?php
        if ($golongan!=5) {
        ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Kondisi Aset</label>
					<div class="col-sm-4">
						<select class="form-control" name="kondisi" id="kondisi">
                            <option value="1">Baik</option>
                            <option value="2">Rusak Ringan</option>
                            <option value="3">Rusak Berat</option>
						</select>
					</div>
				</div>
			</div>
		</div>
        <?php
        }
        
        if (($golongan==3) or ($golongan==4) or ($golongan==6) or ($golongan==8)) {
        ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Lokasi Kampus</label>
					<div class="col-sm-4">
                        <select class="form-control" name="lokasi_kampus" id="lokasi_kampus" >
                            <option></option>
                            <?php
                                if (!empty($lokasiKampus)) {
                                    foreach ($lokasiKampus as $kampus) {
                                        echo ' <option value="' . $kampus->id . '">'. $kampus->nama . '</option>';
                                    }
                                }
                            ?>
                        </select>
					</div>
				</div>
			</div>
		</div>
        <?php
        }
        
        if (($golongan==3) or ($golongan==6) or ($golongan==8)) {
        ?>
        
        <?php //echo "<pre>"; print_r($lokasiAset); echo "</pre>"; ?>
        <!--
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Lokasi Aset</label>
					<div class="col-sm-6">
                        <select class="form-control select-lokasi-aset" name="lokasi_aset" id="lokasi_aset" >
                            <option></option>
                            <?php
                                if (!empty($lokasiAset)) {
                                    foreach ($lokasiAset as $lokasi) {
                                        echo ' <option value="' . $lokasi->id . '">'. $lokasi->kode . ' - ' . $lokasi->gedung . ' | ' . $lokasi->ruang .'</option>';
                                    }
                                }
                            ?>
                        </select>
					</div>
                    <font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
        -->
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Lokasi Aset</label>
					<div class="col-sm-6">
						<input type="text" readonly class="form-control" placeholder="Lokasi Aset" name="lokasi_aset" id="lokasi_aset" />
						<input type="hidden" autocomplete="off" class="form-control" name="id" />
                        <input type="hidden" autocomplete="off" class="form-control" name="gedung" />
                        <input type="hidden" autocomplete="off" class="form-control" name="ruang" />
						<?php echo form_error('lokasi_aset', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>
                    <font class="col-sm-1" color="red">*</font>
					<div class="col-sm-2">
						<a id="listBarang" rel='async' ajaxify="<?php echo modal("Lokasi Aset", 'aset_manajemen_barang', 'inventarisasi_detail', 'list_lokasi_aset')?>" class="btn btn-sm btn-labeled btn-info"><i class="fa fa-search btn-label-icon left"></i>Cari</a>
					</div>
					
				</div>
			</div>
		</div>
        
        <?php
        }
        ?>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Kartu Identitas Barang</label>
					<div class="col-sm-4">
						<select class="form-control" name="kib" id="kib">
                            <option value="1">Ada</option>
                            <option value="0">Tidak Ada</option>
						</select>
					</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Status Aset</label>
					<div class="col-sm-4">
                        <select class="form-control select-status-aset" name="status_aset" id="status_aset" >
                            <option></option>
                            <?php
                                if (!empty($statusAset)) {
                                    //foreach ($statusAset as $status) {
                                        //echo ' <option value="' . $status->id . '">'. $status->nama . '</option>';
                                    //}
                                    
                                    foreach ($statusAset as $status) {
                                        $selected = '';
                                        if ($status->id == $def_status_aset)
                                        $selected = ' selected="selected" ';
                                        echo ' <option value="' . $status->id . '" '.$selected.' >' . $status->nama . '</option>';
                                    }
                                }
                            ?>
                        </select>
					</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Keterangan Lain</label>
					<div class="col-sm-6">
                        <textarea class="form-control" name="keterangan_lain" rows="4" cols="60"></textarea>
					</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Unit Penanggung Jawab Aset</label>
					<div class="col-sm-6">
                        <select class="form-control select-unit-pj" name="unit_pj" id="unit_pj" >
                            <option></option>
                            <?php
                                if (!empty($unitPJ)) {
                                    foreach ($unitPJ as $unit) {
                                        $selected = '';
                                        if ($unit->kode == $def_unit_pj)
                                        $selected = ' selected="selected" ';
                                        echo ' <option value="' . $unit->id . '" '.$selected.' >' . $unit->nama . '</option>';
                                    }
                                }
                            ?>
                        </select>
					</div>
					<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Foto</label>
					<div class="col-sm-8">
						<label class="custom-file px-file">
							<input type="file" class="custom-file-input" name="foto" onchange="checkFileUpload(this,['jpg','jpeg','pdf']);" />
							<span class="custom-file-control form-control">
							<?php
								if (!empty(set_value('foto'))) {
									echo set_value('foto');
								} else {
									echo "Pilih file ... ";
								}
							?>
                            </span>
							<div class="px-file-buttons">
								<button type="button" class="btn px-file-clear">Clear</button>
								<button type="button" class="btn btn-primary px-file-browse">Browse</button>
							</div>
						</label>
						<p class='help-block'><i>Format file <b>*.jpg|.jpeg|.png|.bmp|.pdf</b></i></p>
					</div>
				</div>
			</div>
		</div>
				
    	<div class="row text-center">
            <div class="col-sm-8">
        		<div class="panel-footer">
                    <button class="btn btn-flat btn-primary" type="submit" name="tombol_proses" value="simpan"><span class="btn-label-icon left fa fa-save"></span><b>Simpan</b></button>
                    <button class="btn btn-flat btn-default" id=""><span class="btn-label-icon left fa fa-undo"></span><b>Batal</b></button>
        		</div>
            </div>
    	</div>

    </div>
</form>

<script type="text/javascript">
	$(document).ready(function () {
        $("select[name='jenis_aset']").select2({
			allowClear: true,
			placeholder: "Jenis Aset"
		});
        
        $("select[name='kode_aset']").select2({
			allowClear: true,
			placeholder: "Kode Aset"
		});
        
        $("select[name='sumber_dana']").select2({
			allowClear: true,
			placeholder: "Sumber Dana"
		});
        
        $("select[name='satuan']").select2({
			allowClear: true,
			placeholder: "Satuan"
		});
        
        $("select[name='kepemilikan']").select2({
			allowClear: true,
			placeholder: "Kepemilikan"
		});
        
        $("select[name='penguasaan']").select2({
			allowClear: true,
			placeholder: "Penguasaan"
		});
        
        $("select[name='kondisi']").select2({
			allowClear: true,
			placeholder: "Kondisi"
		});
        
        $("select[name='lokasi_kampus']").select2({
			allowClear: true,
			placeholder: "Lokasi Kampus"
		});
        
        $("select[name='lokasi_aset']").select2({
			allowClear: true,
			placeholder: "Lokasi Aset"
		});
        
        $("select[name='kib']").select2({
			allowClear: true,
			placeholder: "Kartu Identitas Barang"
		});
        
		$("select[name='status_aset']").select2({
			allowClear: true,
			placeholder: "Status Aset"
		});
        
		$("select[name='unit_pj']").select2({
			allowClear: true,
			placeholder: "Unit Penanggung Jawab Aset"
		});

		$("div.input-group.date.day").datepicker({
			format: "yyyy-mm-dd",
			viewMode: "days",
			minViewMode: "days",
			autoclose: true
		});
        
        $('.custom-file').pxFile();
	});
</script>