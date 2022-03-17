<?php echo Modules::run('breadcrump'); ?>

<form rel="ajax" id="" action="" class="panel form-horizontal xhr dest_subcontent-element" method="post">

	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
	<input type="hidden" name="lastStage" value="<?php echo $this->security->get_csrf_hash(); ?>" />

	<div class="panel-heading">
		<span class="panel-title">Tambah Mutasi Pembelian</span>
	</div>

	<div class="panel-body">
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Jenis Aset</label>
					<div class="col-sm-6">
						<select class="form-control" name="jenis_aset" id="jenis_aset">
                            <option value="1">TANAH</option>
						</select>
					</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Kode Aset</label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="Kode Aset" class="form-control" name="kode_aset" />
					</div>
					<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Nama Aset</label>
					<div class="col-sm-6">
						<input type="text" autocomplete="off" placeholder="Nama Aset" class="form-control" name="nama_aset" />
					</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Label Aset</label>
					<div class="col-sm-6">
						<input type="text" autocomplete="off" placeholder="Label Aset" class="form-control" name="label_aset" />
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
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Tanggal Pembelian</label>
					<div class="col-sm-3">
						<div class="input-group date day">
							<input type="text" name="tanggal_pembelian" class="form-control" placeholder="Tanggal Pembelian" autocomplete="off" value="" />
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
						<select class="form-control" name="sumber_dana" id="sumber_dana">
                            <option value="1">APBN</option>
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
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Luas Tanah</label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="Luas Tanah" class="form-control" name="luas_tanah" />
					</div>
					&#13217; <font color="red">*</font>
				</div>
			</div>
		</div>
        
        		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Nilai Kuitansi</label>
                    <div class="col-sm-1">Rp.</div>
					<div class="col-sm-4"> 
                        <input type="text" autocomplete="off" placeholder="Nilai Kuitansi" class="form-control" name="nilai_kuitansi" value="0,00" />
					</div>
					<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Nilai Perolehan per Meter</label>
                    <div class="col-sm-1">Rp.</div>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="Nilai Perolehan per Meter" class="form-control" name="nilai_perolehan" value="0,00" />
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
						<input type="text" autocomplete="off" placeholder="Total Perolehan" class="form-control" name="total_perolehan" value="0,00" />
					</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Lokasi</label>
					<div class="col-sm-6">
						<input type="text" autocomplete="off" placeholder="Lokasi" class="form-control" name="luas_tanah" />
					</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Kegunaan</label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="Kegunaan" class="form-control" name="luas_tanah" />
					</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Kepemilikan</label>
					<div class="col-sm-4">
						<select class="form-control" name="kepemilikan" id="kepemilikan">
                            <option value="1">Universitas</option>
                            <option value="2">Pihak III</option>
                            <option value="2">Dalam Sengketa</option>
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
						<select class="form-control" name="status_aset" id="status_aset">
                            <option value="1">Aktif</option>
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
					<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Unit Penanggung Jawab Aset</label>
					<div class="col-sm-6">
						<select class="form-control" name="unit_pj">
                            <option value="000000">Universitas</option>
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
        			<?= save_button() ?>
                    <button class="btn btn-flat btn-default" id=""><span class="btn-label-icon left fa fa-undo"></span>Batal</button>
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
        
        $("select[name='sumber_dana']").select2({
			allowClear: true,
			placeholder: "Sumber Dana"
		});
        
        $("select[name='kepemilikan']").select2({
			allowClear: true,
			placeholder: "Kepemilikan"
		});
        
        $("select[name='penguasaan']").select2({
			allowClear: true,
			placeholder: "Penguasaan"
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
			format: "dd-mm-yyyy",
			viewMode: "days",
			minViewMode: "days",
			autoclose: true
		});
	});
</script>