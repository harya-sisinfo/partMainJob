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

<form method="post" action="<?php echo $form_action; ?>" class="panel form-horizontal xhr dest_subcontent-element" >
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	<input type="hidden" name="lastStage" value="<?php echo $this->security->get_csrf_hash(); ?>" />
    <input type="hidden" name="id_unit" value="<?php echo $dataUnit->id; ?>" />
    
    <table style="clear: both" class="table table-bordered table-striped">
        <tbody>
            <?php //echo "<pre>"; print_r($detailAset); echo "</pre>"; ?>
            <tr>
            	<td style="width: 25%;"><b>Jenis Aset</b></td>
            	<td><?php echo $detailAset->golbrgNama; ?></td>
            </tr>
            
            <tr>
            	<td><b>Nomor Referensi / Kuitansi</b></td>
            	<td><?php echo $detailAset->noref; ?></td>
            </tr>
            
            <tr>
            	<td><b>Kode Aset</b></td>
            	<td><b><?php echo $detailAset->invDetKodeBarang; ?></b></td>
            </tr>
            
            <tr>
            	<td><b>Nama Aset</b></td>
            	<td><?php echo $detailAset->namaAset; ?></td>
            </tr>
            
            <tr>
            	<td><b>Merk</b></td>
            	<td>
                    <input type="text" autocomplete="off" class="form-control" name="merk" value="<?php echo $detailAset->invDetMerek; ?>" />
                </td>
            </tr>
            
            <tr>
            	<td><b>Spesifikasi</b></td>
            	<td>
                    <textarea class="form-control" name="spesifikasi" rows="4" cols="60"><?php echo $detailAset->invDetSpesifikasi; ?></textarea>
                </td>
            </tr>
            
            <tr>
            	<td><b>Tanggal Pembelian</b></td>
            	<td><?php echo $detailAset->invDetTglPembelian; ?></td>
            </tr>
            
            <tr>
            	<td><b>Tanggal Pembukuan</b></td>
            	<td><?php echo $detailAset->invDetTglBuku; ?></td>
            </tr>
            
            <tr>
            	<td><b>Sumber Dana</b></td>
            	<td>
                    <select class="form-control select-sumber-dana" name="sumber_dana" id="sumber_dana" >
                        <option></option>
                        <?php
                            if (!empty($sumberDana)) {
                                foreach ($sumberDana as $dana) {
                                    $selected = '';
                                    if ($dana->id == $detailAset->invDetSumberDana)
                                    $selected = ' selected="selected" ';
                                    echo ' <option value="' . $dana->id . '" '.$selected.' >' . $dana->nama . '</option>';
                                }
                            }
                        ?>
                    </select>
                </td>
            </tr>
            
            <?php
            $golongan = $detailAset->golbrgId;
            
            if (($golongan==2) or ($golongan==5)) {
                $label_perolehan = 'Nilai Perolehan per Meter';
                $nilai_perolehan = $detailAset->invNilaiFaktur/$detailAset->invLuasTanah;
            ?>
            <tr>
            	<td><b>Luas Tanah</b></td>
            	<td><?php echo $detailAset->invLuasTanah; ?>&nbsp;&#13217;</td>
            </tr>
            <?php
            } else {
                $label_perolehan = 'Nilai Perolehan per Satuan';
                $nilai_perolehan = $detailAset->invDetNilaiPerolehanSatuan/1;
            ?>
            <tr>
            	<td><b>Jumlah Aset</b></td>
            	<td><?php echo '1 '.$detailAset->satuanbrgNama; ?></td>
            </tr>
            
            <tr>
            	<td><b>Umur Ekonomis</b></td>
            	<td><?php echo $detailAset->invDetUmurEkonomis; ?> bulan</td>
            </tr>
            <?php
            }
    
            if (($golongan==2) or ($golongan==5)) {
            ?>
            <tr>
            	<td><b>Nilai Kuitansi</b></td>
            	<td>Rp<?php echo number_format($detailAset->invNilaiFaktur,2,',','.'); ?></td>
            </tr>
            <?php
            }
            ?>
            
            <tr>
            	<td><b><?php echo $label_perolehan; ?></b></td>
            	<td>Rp<?php echo number_format(($nilai_perolehan),2,',','.'); ?></td>
            </tr>
            
            <tr>
            	<td><b>Total Perolehan</b></td>
            	<td><b>Rp<?php echo number_format($detailAset->invNilaiFaktur,2,',','.'); ?></b></td>
            </tr>
            
            <?php
            if (($golongan==2) or ($golongan==4) or ($golongan==5)) {
            ?>
            <tr>
            	<td><b>Lokasi</b></td>
            	<td>
                    <input type="text" autocomplete="off" class="form-control" name="lokasi" value="<?php echo $detailAset->invDetLokasi; ?>" />
                </td>
            </tr>
            <?php
            }
            
            if (($golongan==2) or ($golongan==5)) {
            ?>
            <tr>
            	<td><b>Kegunaan</b></td>
            	<td>
                    <input type="text" autocomplete="off" class="form-control" name="kegunaan" value="<?php echo $detailAset->invDetKegunaan; ?>" />
                </td>
            </tr>
            <?php
            }
            ?>
            
            <tr>
            	<td><b>Kepemilikan</b></td>
            	<td>
                    <select class="form-control select-kepemilikan" name="kepemilikan" id="kepemilikan" >
                        <option></option>
                        <?php
                            if (!empty($kepemilikan)) {
                                foreach ($kepemilikan as $milik) {
                                    $selected = '';
                                    if ($milik->id == $detailAset->invDetKepemilikan)
                                    $selected = ' selected="selected" ';
                                    echo ' <option value="' . $milik->id . '" '.$selected.' >' . $milik->nama . '</option>';
                                }
                            }
                        ?>
                    </select>
                </td>
            </tr>
            
            <tr>
            	<td><b>Penguasaan</b></td>
            	<td>
				    <select class="form-control" name="penguasaan" id="penguasaan">
                        <option value="1">Hak Penuh</option>
                        <option value="2">Hak Guna</option>
					</select>
                </td>
            </tr>
            
            <?php
            if ($golongan!=5) {
            ?>
            <tr>
            	<td><b>Kondisi Aset</b></td>
            	<td><?php echo $detailAset->kondisi; ?></td>
            </tr>
            <?php
            }
            
            if (($golongan==4) or ($golongan==6) or ($golongan==8)) {
            ?>
            <tr>
            	<td><b>Lokasi Kampus</b></td>
            	<td><?php echo $detailAset->kampusNama; ?></td>
            </tr>
            <?php
            }
            
            if (($golongan==3) or ($golongan==6) or ($golongan==8)) {
            ?>
            <tr>
            	<td><b>Lokasi Aset</b></td>
            	<td>
					<div class="col-sm-10" style="padding-left: 0;">
						<input type="text" readonly class="form-control" placeholder="Lokasi Aset" name="lokasi_aset" id="lokasi_aset" value="<?php echo $detailAset->lokasiAset; ?>" />
						<input type="hidden" autocomplete="off" class="form-control" name="id" />
                        <input type="hidden" autocomplete="off" class="form-control" name="gedung" />
                        <input type="hidden" autocomplete="off" class="form-control" name="ruang" />
						<?php echo form_error('lokasi_aset', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
                    </div>
					<div class="col-sm-2">
						<a id="listBarang" rel='async' ajaxify="<?php echo modal("Lokasi Aset", 'aset_manajemen_barang', 'inventarisasi_detail', 'list_lokasi_aset')?>" class="btn btn-sm btn-labeled btn-info"><i class="fa fa-search btn-label-icon left"></i>Cari</a>
					</div>
                </td>
            </tr>

            <?php
            }
            ?>
            
            <tr>
            	<td><b>Kartu Identitas Barang</b></td>
            	<td>
					<select class="form-control" name="kib" id="kib">
                        <option value="1">Ada</option>
                        <option value="0">Tidak Ada</option>
					</select>
                </td>
            </tr>
            
            <tr>
            	<td><b>Status Aset</b></td>
            	<td><?php echo $detailAset->statusAset; ?></td>
            </tr>
            
            <tr>
            	<td><b>Keterangan Lain</b></td>
            	<td>
                    <textarea class="form-control" name="keterangan_lain" rows="4" cols="60"><?php echo $detailAset->invDetKeteranganLain; ?></textarea>
                </td>
            </tr>
            
            <tr>
            	<td><b>Unit Penanggung Jawab Aset</b></td>
            	<td><?php echo $detailAset->unitkerjaNama; ?></td>
            </tr>
            
            <tr>
            	<td><b>Foto Barang</b></td>
            	<td>
                    <input type="file" class="custom-file-input" name="to_doc[]" onchange="checkFileUpload(this,['jpg','jpeg','pdf']);" multiple />
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
                </td>
            </tr>
        </tbody>
    </table>

	<div class="panel-body">	
    	<div class="row text-center">
            <div class="col-sm-8">
        		<div class="panel-footer">
                    <button class="btn btn-flat btn-primary" type="submit" name="tombol_proses" value="simpan"><span class="btn-label-icon left fa fa-save"></span><b>Simpan</b></button>
                    <button class="btn btn-flat btn-default" type="submit" name="tombol_proses" value="batal"><span class="btn-label-icon left fa fa-undo"></span><b>Batal</b></button>
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
	});
</script>