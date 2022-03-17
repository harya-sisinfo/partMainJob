<?php echo Modules::run('breadcrump'); ?>

<script type="text/javascript">
function changeKodeBarang() {
    var selectBox = document.getElementById("kode_barang");
    var selectedValue = selectBox.options[selectBox.selectedIndex].value;
    //alert(selectedValue);
    
    var selectedText = selectBox.options[selectBox.selectedIndex].text;
    //alert(selectedText);
    
    document.getElementById("nama_barang").value = selectedText.substring(17);
    document.getElementById("label_barang").value = selectedText.substring(17);
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

function changeJumlahBarang() {
    var JumlahBarang = document.getElementById("jumlah_barang").value;
    //alert(JumlahBarang);
    
    var NilaiPerolehan = document.getElementById("nilai_perolehan").value;
    //alert(NilaiPerolehan);
    
    document.getElementById("total_perolehan").value = JumlahBarang*NilaiPerolehan;
}

function changeNilaiPerolehan() {
    var JumlahBarang = document.getElementById("jumlah_barang").value;
    //alert(JumlahBarang);
    
    var NilaiPerolehan = document.getElementById("nilai_perolehan").value;
    //alert(NilaiPerolehan);
    
    document.getElementById("total_perolehan").value = (JumlahBarang*NilaiPerolehan)*10;
}
</script>
</script>

<form method="post" action="<?php echo $form_action; ?>" class="panel form-horizontal xhr dest_subcontent-element" >
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	<input type="hidden" name="lastStage" value="<?php echo $this->security->get_csrf_hash(); ?>" />
    <input type="hidden" name="id_unit" value="<?php echo $dataUnit->id; ?>" />

	<div class="panel-heading">
		<span class="panel-title"><b>Tambah</b> Barang Titipan</span>
	</div>

	<div class="panel-body">
        <?php //echo "<pre>"; print_r($jenisBarang); echo "</pre>"; ?>
        <?php //echo "<pre>"; print_r($sumberDana); echo "</pre>"; ?>
        <?php //echo "<pre>"; print_r($kodeBarang); echo "</pre>"; ?>
        <?php //echo "<pre>"; print_r($dataUnit); echo "</pre>"; ?>
        <?php //echo "Golongan: ".$golongan; ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Jenis Barang</label>
					<div class="col-sm-6">
						<select class="form-control" name="jenis_barang" id="jenis_barang" onchange="submit();">
                            <?php
                                if (!empty($jenisBarang)) {
                                    foreach ($jenisBarang as $jenis) {
                                        $selected = '';
                                        if ($jenis->id == $golongan)
                                        $selected = ' selected="selected" ';
                                        echo ' <option value="' . $jenis->id . '" '.$selected.' >' . $jenis->nama . '</option>';
                                    }
                                }
                            ?>
						</select>
                        <input type="hidden" name="tombol_proses" value="pilih" />
					</div>
				</div>
			</div>
		</div>
        
        <?php
        if (($golongan==1) or ($golongan==7)) {
        ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Barcode Barang</label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="Barcode Barang" class="form-control" name="barcode_barang" id="barcode_barang" />
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
					<label class="col-sm-2 control-label" style='text-align:left;'>Kode Barang</label>
					<div class="col-sm-6">
                        <select class="form-control select-kode-barang" name="kode_barang" id="kode_barang" onchange="changeKodeBarang();" >
                            <option></option>
                            <?php
                                if (!empty($kodeBarang)) {
                                    foreach ($kodeBarang as $kode) {
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
        
        <?php
        if (($golongan==1) or ($golongan==7)) {
        ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Jenis</label>
  			       <div class="col-sm-4">
        				<div class="radio">
        					<label>
        						<input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" class="px" />
        						<span class="lbl">Gedung</span>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="optionsRadios" id="optionsRadios2" value="option2" class="px" />
        						<span class="lbl">Non-Gedung</span>
        					</label>
        				</div> <!-- / .radio -->
        			</div> <!-- / .col-sm-4 -->
				</div>
			</div>
		</div>
        <?php
        }
        ?>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Nama Barang</label>
					<div class="col-sm-6">
						<input type="text" autocomplete="off" placeholder="Nama Barang" class="form-control" name="nama_barang" id="nama_barang" />
					</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Label Barang</label>
					<div class="col-sm-6">
						<input type="text" autocomplete="off" placeholder="Label Barang" class="form-control" name="label_barang" id="label_barang" readonly="readonly" />
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
        
        <?php
        if (($golongan==1) or ($golongan==7)) {
        ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Tipe</label>
					<div class="col-sm-6">
						<input type="text" autocomplete="off" placeholder="Tipe" class="form-control" name="tipe" />
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
					<label class="col-sm-2 control-label" style='text-align:left;'>Nomor Referensi</label>
					<div class="col-sm-6">
						<input type="text" autocomplete="off" placeholder="Nomor Referensi" class="form-control" name="nomor_referensi" />
					</div>
					<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
        
        <?php
        if (($golongan==1) or ($golongan==7)) {
        ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Jumlah Barang</label>
					<div class="col-sm-2">
						<input type="text" autocomplete="off" placeholder="Jumlah Barang" class="form-control" name="jumlah_barang" id="jumlah_barang" onkeypress="changeJumlahBarang();" />
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
        
        if (($golongan==1) or ($golongan==2) or ($golongan==5) or ($golongan==7)) {
            $label_perolehan = 'Nilai Perolehan per Meter';
        ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Luas Tanah</label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="Luas Tanah" class="form-control" name="luas_tanah" id="luas_tanah" onkeypress="changeLuasTanah();" />
                        <input type="hidden" name="jumlah_barang" value="1" />
                        <input type="hidden" name="satuan" value="1" />
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
					<label class="col-sm-2 control-label" style='text-align:left;'>Jumlah Barang</label>
					<div class="col-sm-2">
						<input type="text" autocomplete="off" placeholder="Jumlah Barang" class="form-control" name="jumlah_barang" id="jumlah_barang" onkeypress="changeJumlahBarang();" />
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

        if (($golongan==1) or ($golongan==2) or ($golongan==5) or ($golongan==7)) {
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
        if (($golongan==1) or ($golongan==7)) {
        ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Nilai Residu</label>
                    <div class="col-sm-1">Rp.</div>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="Nilai Residu" class="form-control" name="nilai_residu" id="total_perolehan" value="0" />
					</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>NJOP</label>
                    <div class="col-sm-1">Rp.</div>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="NJOP" class="form-control" name="njop" id="njop" value="0" />
					</div>
				</div>
			</div>
		</div>
        
        <?php
        }
        
        if (($golongan==1) or ($golongan==2) or ($golongan==4) or ($golongan==5) or ($golongan==7)) {
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
        
        if (($golongan==1) or ($golongan==2) or ($golongan==5) or ($golongan==7)) {
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

        if (($golongan==1) or ($golongan==7)) {
        ?>
        <hr />
		<div class="row">
			<div class="col-sm-12">
			     <div class="row form-group">
				    <label class="col-sm-2 control-label" style='text-align:left;'>LANDASAN LEGAL</label>
				</div>
				
                <div class="row form-group">
				    <label class="col-sm-2 control-label" style='text-align:left;'>Sertifikat</label>
                    <div class="col-sm-2">
        				<div class="radio">
        					<label>
        						<input type="radio" name="sertifikat" id="sertifikat1" value="sertifikat1" class="px" checked="" />
        						<span class="lbl">Tidak Ada</span>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="sertifikat" id="sertifikat2" value="sertifikat2" class="px" />
        						<span class="lbl">Ada</span>
        					</label>
        				</div> <!-- / .radio -->
        			</div> <!-- / .col-sm-4 -->
                    <div class="col-sm-4">
                        <input type="text" autocomplete="off" placeholder="Sertifikat" class="form-control" name="ada_sertifikat" />
                    </div>
				</div>
                
				<div class="row form-group">
				    <label class="col-sm-2 control-label" style='text-align:left;'>Akta Jual Beli</label>
                    <div class="col-sm-2">
        				<div class="radio">
        					<label>
        						<input type="radio" name="akta" id="akta1" value="akta1" class="px" checked="" />
        						<span class="lbl">Tidak Ada</span>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="akta" id="akta2" value="akta2" class="px" />
        						<span class="lbl">Ada</span>
        					</label>
        				</div> <!-- / .radio -->
        			</div> <!-- / .col-sm-4 -->
                    <div class="col-sm-4">
                        <input type="text" autocomplete="off" placeholder="Akta Jual Beli" class="form-control" name="ada_akta" />
                    </div>
				</div>
                
				<div class="row form-group">
				    <label class="col-sm-2 control-label" style='text-align:left;'>BPKB</label>
                    <div class="col-sm-2">
        				<div class="radio">
        					<label>
        						<input type="radio" name="bpkb" id="bpkb1" value="bpkb1" class="px" checked="" />
        						<span class="lbl">Tidak Ada</span>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="bpkb" id="bpkb2" value="bpkb2" class="px" />
        						<span class="lbl">Ada: </span>
        					</label>
        				</div> <!-- / .radio -->
        			</div> <!-- / .col-sm-4 -->
                    <div class="col-sm-4">
                        <input type="text" autocomplete="off" placeholder="BPKB" class="form-control" name="ada_bpkb" />
                    </div>
				</div>
                
				<div class="row form-group">
				    <label class="col-sm-2 control-label" style='text-align:left;'>STNK</label>
                    <div class="col-sm-2">
        				<div class="radio">
        					<label>
        						<input type="radio" name="stnk" id="stnk1" value="stnk1" class="px" checked="" />
        						<span class="lbl">Tidak Ada</span>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="stnk" id="stnk2" value="stnk2" class="px" />
        						<span class="lbl">Ada: </span>
        					</label>
        				</div> <!-- / .radio -->
        			</div> <!-- / .col-sm-4 -->
                    <div class="col-sm-4">
                        <input type="text" autocomplete="off" placeholder="STNK" class="form-control" name="ada_stnk" />
                    </div>
				</div>
			</div>
		</div>
        
        <hr />
		<div class="row">
			<div class="col-sm-12">
			     <div class="row form-group">
				    <label class="col-sm-2 control-label" style='text-align:left;'>BUKTI KENDARAAN</label>
				</div>
				
                <div class="row form-group">
				    <label class="col-sm-2 control-label" style='text-align:left;'>No Mesin</label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="No Mesin" class="form-control" name="no_mesin" />
					</div>
				</div>
                
                <div class="row form-group">
				    <label class="col-sm-2 control-label" style='text-align:left;'>No Rangka</label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="No Rangka" class="form-control" name="no_rangka" />
					</div>
				</div>
                
                <div class="row form-group">
				    <label class="col-sm-2 control-label" style='text-align:left;'>No Polisi</label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="No Polisi" class="form-control" name="no_polisi" />
					</div>
				</div>
                
                <div class="row form-group">
				    <label class="col-sm-2 control-label" style='text-align:left;'>No Lain</label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="No Lain" class="form-control" name="no_lain" />
					</div>
				</div>
                
                <div class="row form-group">
				    <label class="col-sm-2 control-label" style='text-align:left;'>Warna</label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="Warna" class="form-control" name="warna" />
					</div>
				</div>
			</div>
		</div>
        
        <hr />
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Memiliki Pagar</label>
					<div class="col-sm-4">
						<select class="form-control" name="pagar" id="pagar">
                            <option value="0">Tidak</option>
                            <option value="1">Ya</option>
						</select>
					</div>
				</div>
			</div>
		</div>
        
        <hr />
		<div class="row">
			<div class="col-sm-12">
			     <div class="row form-group">
				    <label class="col-sm-2 control-label" style='text-align:left;'>BATAS-BATAS</label>
				</div>
				
                <div class="row form-group">
				    <label class="col-sm-2 control-label" style='text-align:left;'>Utara</label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="Utara" class="form-control" name="utara" />
					</div>
				</div>
                
                <div class="row form-group">
				    <label class="col-sm-2 control-label" style='text-align:left;'>Selatan</label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="Selatan" class="form-control" name="selatan" />
					</div>
				</div>
                
                <div class="row form-group">
				    <label class="col-sm-2 control-label" style='text-align:left;'>Timur</label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="Timur" class="form-control" name="timur" />
					</div>
				</div>
                
                <div class="row form-group">
				    <label class="col-sm-2 control-label" style='text-align:left;'>Barat</label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="Barat" class="form-control" name="barat" />
					</div>
				</div>
			</div>
		</div>
        <hr />
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
                            <option value="2">Hak Guna</option>
                            <option value="1">Hak Penuh</option>
						</select>
					</div>
				</div>
			</div>
		</div>
        
        <?php
        if (($golongan==1) or ($golongan==3) or ($golongan==4) or ($golongan==6) or ($golongan==7) or ($golongan==8)) {
        ?> 
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Kondisi Barang</label>
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
        
        if (($golongan==1) or ($golongan==3) or ($golongan==4) or ($golongan==6) or ($golongan==7) or ($golongan==8)) {
        ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Lokasi Kampus</label>
					<div class="col-sm-4">
                        <select class="form-control select-lokasi-kampus" name="lokasi_kampus" id="lokasi_kampus" >
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
        
        if (($golongan==1) or ($golongan==3) or ($golongan==6) or ($golongan==7) or ($golongan==8)) {
        ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Lokasi Barang</label>
					<div class="col-sm-4">
                        <select class="form-control select-lokasi-barang" name="lokasi_barang" id="lokasi_barang" >
                            <option></option>
                            <?php
                                if (!empty($lokasiBarang)) {
                                    foreach ($lokasiBarang as $lokasi) {
                                        echo ' <option value="' . $lokasi->id . '">'. $lokasi->nama . '</option>';
                                    }
                                }
                            ?>
                        </select>
					</div>
                    <font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
        <?php
        }
        ?>
        
		<!--<div class="row">
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
		</div>-->
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Status Barang</label>
					<div class="col-sm-4">
                        <select class="form-control select-status-barang" name="status_barang" id="status_barang" >
                            <option></option>
                            <?php
                                if (!empty($statusBarang)) {
                                    //foreach ($statusAset as $status) {
                                        //echo ' <option value="' . $status->id . '">'. $status->nama . '</option>';
                                    //}
                                    
                                    foreach ($statusBarang as $status) {
                                        $selected = '';
                                        if ($status->id == $def_status_barang)
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
					<label class="col-sm-2 control-label" style='text-align:left;'>Unit Penanggung Jawab Barang</label>
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
					<div class="col-sm-6">
						<label class="custom-file px-file">
							<input type="file" class="custom-file-input" name="to_doc[]" onchange="checkFileUpload(this,['jpg','jpeg','pdf']);" multiple >
							<span class="custom-file-control form-control">

								<?php 
								if(!empty($data_doc['to_doc_nama'])){
									?><?php 
									echo (set_value('to_doc[]')) ? set_value('to_doc[]') : $data_doc['to_doc_nama']; ?>
									<?php 
								}else{
									echo "Pilih file ... ";
								}
							?></span>
							<div class="px-file-buttons">
								<button type="button" class="btn px-file-clear">Clear</button>
								<button type="button" class="btn btn-primary px-file-browse">Browse</button>
							</div>
						</label>

						<p class='help-block'><i>Format file <b>*.jpg|.jpeg|.pdf</b></i></p>
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
        $("select[name='jenis_barang']").select2({
			allowClear: true,
			placeholder: "Jenis Barang"
		});
        
        $("select[name='kode_barang']").select2({
			allowClear: true,
			placeholder: "Kode Barang"
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
        
        $("select[name='lokasi_kampus']").select2({
			allowClear: true,
			placeholder: "Lokasi Kampus"
		});
        
        $("select[name='lokasi_barang']").select2({
			allowClear: true,
			placeholder: "Lokasi Barang"
		});
        
        $("select[name='kib']").select2({
			allowClear: true,
			placeholder: "Kartu Identitas Barang"
		});
        
		$("select[name='status_barang']").select2({
			allowClear: true,
			placeholder: "Status Barang"
		});
        
		$("select[name='unit_pj']").select2({
			allowClear: true,
			placeholder: "Unit Penanggung Jawab Barang"
		});

		$("div.input-group.date.day").datepicker({
			format: "yyyy-mm-dd",
			viewMode: "days",
			minViewMode: "days",
			autoclose: true
		});
	});
</script>