<?php echo Modules::run('breadcrump'); ?>
<form rel="ajax-file" enctype="multipart/form-data" id="form_<?php echo $isProses?>" action="<?php echo $url_action ?>" class="panel form-horizontal xhr dest_subcontent-element" method="post">
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
	<div class="panel-heading">
		<span class="panel-title"><b><?php echo $form_label?></b></span>
	</div>
	<div class="panel-body">
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>No. SIP</label>
					<div class="col-sm-8">
						
						<input type="text" autocomplete="off" placeholder="No. SIP" class="form-control" name="noref" value="<?php echo  set_value('noref') ?>">
						<?php echo form_error('noref', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>
					<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Tanggal Berlaku SIP</label>
					<div class="col-sm-3">
						<div class="input-group date day">
							<input type="text" name="tgl" class="form-control"
							placeholder="Tanggal Berlaku SIP" autocomplete="off"
							value="<?php echo  set_value('tgl') ?>"><span
							class="input-group-addon"><i class="fa fa-calendar"></i></span>
						</div>
						<?php echo form_error('tgl', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>
					<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>File BA/SK </label>
					<div class="col-sm-8">
						<label class="custom-file px-file">
							<input type="file" class="custom-file-input" name="fileSip[]" onchange="checkFileUpload(this,['jpg','jpeg','pdf']);" >
							<span class="custom-file-control form-control">
								<?php
								if(!empty(set_value('fileSip[]'))){
									echo set_value('fileSip[]');
								}else{
									echo "Pilih file ... ";
								}
								
							?></span>
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
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Status</label>
					<div class="col-sm-3">
						<select name="stat" class="form-control">
							<option value="Y" <?php echo (!empty($stat)&&$stat=="Y")?'selected="selected"':''?> >Aktif</option>
							<option value="N" <?php echo (!empty($stat)&&$stat=="N")?'selected="selected"':''?> >Expired</option>
						</select>
						<?php echo form_error('stat', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>
					<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Keterangan</label>
					<div class="col-sm-3">
						<textarea class="form-control" placeholder="Keterangan" name="ket"><?php echo set_value('ket')?></textarea>
						<?php echo form_error('ket', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="panel">
			<div class="panel-heading"><span class="panel-title">Data Pemberi Izin</span></div>
			<div class="panel-body">
				<!-- ========================================================================================== -->
				<div class="row">
					<div class="col-sm-12">
						<div class="row form-group">
							<label class="col-sm-2 control-label" style='text-align:left;'>Nama</label>
							<div class="col-sm-3">
								<input type="text" autocomplete="off" placeholder="Nama pemberi izin" class="form-control" name="pnama" value="<?php echo  set_value('pnama') ?>">
								<?php echo form_error('pnama', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
							</div>
							<font class="col-sm-1" color="red">*</font>
						</div>
					</div>
				</div>
				<!-- ========================================================================================== -->
				<div class="row">
					<div class="col-sm-12">
						<div class="row form-group">
							<label class="col-sm-2 control-label" style='text-align:left;'>NIP</label>
							<div class="col-sm-3">
								<input type="text" autocomplete="off" placeholder="NIM pemberi izin" class="form-control" name="pnip" value="<?php echo  set_value('pnip') ?>">
								<?php echo form_error('pnip', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
							</div>
							<font class="col-sm-1" color="red">*</font>
						</div>
					</div>
				</div>
				<!-- ========================================================================================== -->
				<div class="row">
					<div class="col-sm-12">
						<div class="row form-group">
							<label class="col-sm-2 control-label" style='text-align:left;'>Jabatan</label>
							<div class="col-sm-3">
								<input type="text" autocomplete="off" placeholder="Jabatan pemberi izin" class="form-control" name="pjab" value="<?php echo  set_value('pjab') ?>">
								<?php echo form_error('pjab', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
							</div>
							<font class="col-sm-1" color="red">*</font>
						</div>
					</div>
				</div>
				<!-- ========================================================================================== -->
				<div class="row">
					<div class="col-sm-12">
						<div class="row form-group">
							<label class="col-sm-2 control-label" style='text-align:left;'>Unit Kerja</label>
							<div class="col-sm-5">
								<select class="form-control" name="punit" id="punit">
									<option></option>
									<?php
									if(!empty($data_punit)){
										foreach($data_punit as $key_punit => $row_punit){
											if(set_value('punit')==$row_punit['id']){
												$select_punit = 'selected="selected"';
											}else{
												$select_punit = '';
											}
											echo "<option ".$select_punit." value='".$row_punit['id']."' >".$row_punit['kode'].' - '.$row_punit['nama']."</option>";
										}
									}
									?>
								</select>
								<?php echo form_error('punit', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
							</div>
							<font class="col-sm-1" color="red">*</font>
						</div>
					</div>
				</div>
				<!-- ========================================================================================== -->
			</div>
		</div>
		<!-- ========================================================================================== -->

		<!-- ========================================================================================== -->
		<div class="panel">
			<div class="panel-heading"><span class="panel-title">Data Pengguna</span></div>
			<div class="panel-body">
				<!-- ========================================================================================== -->
				<div class="row">
					<div class="col-sm-12">
						<div class="row form-group">
							<label class="col-sm-2 control-label" style='text-align:left;'>Nama</label>
							<div class="col-sm-3">
								<input type="text" autocomplete="off" placeholder="Nama pengguna" class="form-control" name="nama" value="<?php echo  set_value('nama') ?>">
								<?php echo form_error('nama', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
							</div>
							<font class="col-sm-1" color="red">*</font>
						</div>
					</div>
				</div>
				<!-- ========================================================================================== -->
				<div class="row">
					<div class="col-sm-12">
						<div class="row form-group">
							<label class="col-sm-2 control-label" style='text-align:left;'>NIP</label>
							<div class="col-sm-3">
								<input type="text" autocomplete="off" placeholder="NIM pengguna" class="form-control" name="nip" value="<?php echo  set_value('nip') ?>">
								<?php echo form_error('nip', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
							</div>
							<font class="col-sm-1" color="red">*</font>
						</div>
					</div>
				</div>
				<!-- ========================================================================================== -->
				<div class="row">
					<div class="col-sm-12">
						<div class="row form-group">
							<label class="col-sm-2 control-label" style='text-align:left;'>Jabatan</label>
							<div class="col-sm-3">
								<input type="text" autocomplete="off" placeholder="Jabatan Pengguna " class="form-control" name="jab" value="<?php echo  set_value('jab') ?>">
								<?php echo form_error('jab', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
							</div>
							<font class="col-sm-1" color="red">*</font>
						</div>
					</div>
				</div>
				<!-- ========================================================================================== -->
				<div class="row">
					<div class="col-sm-12">
						<div class="row form-group">
							<label class="col-sm-2 control-label" style='text-align:left;'>No. Telp</label>
							<div class="col-sm-3">
								<input type="text" autocomplete="off" placeholder="NIM pengguna" class="form-control" name="telp" value="<?php echo  set_value('telp') ?>">
								<?php echo form_error('telp', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
							</div>
							<font class="col-sm-1" color="red">*</font>
						</div>
					</div>
				</div>
				<!-- ========================================================================================== -->
				<!-- ========================================================================================== -->
				<div class="row">
					<div class="col-sm-12">
						<div class="row form-group">
							<label class="col-sm-2 control-label" style='text-align:left;'>Unit Kerja</label>
							<div class="col-sm-5">
								<select class="form-control" name="unit" id="unit">
									<option></option>
									<?php
									if(!empty($data_unit)){
										foreach($data_unit as $key_unit => $row_unit){
											if(set_value('unit')==$row_unit['id']){
												$select_unit = 'selected="selected"';
											}else{
												$select_unit = '';
											}
											echo "<option ".$select_unit." value='".$row_unit['id']."' >".$row_unit['kode'].' - '.$row_unit['nama']."</option>";
										}
									}
									?>
								</select>
								<?php echo form_error('unit', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
							</div>
							<font class="col-sm-1" color="red">*</font>
						</div>
					</div>
				</div>
				<!-- ========================================================================================== -->
			</div>
		</div>
		<!-- ========================================================================================== -->
		
		<!-- ========================================================================================== -->
		<div class="panel">
			<div class="panel-heading">
				<span class="panel-title">List Barang</span>
				<div class="panel-heading-controls"><a id="listBarang" rel='async' ajaxify="" href="<?php echo modal("Pilih Barang",'aset_manajemen_barang','penggunaan_aset','list_barang')?>"class="btn btn-sm btn-labeled btn-success"><i class="fa fa-plus btn-label-icon left"></i>Pilih Barang</a></div>
			</div>
			<div class="panel-body">
				<div class="table-primary">
					<div style="overflow: auto;">
						<table id="tbl-rincian" class="table table-bordered">
							<thead>
								<th>No</th>
								<th>Kode Barang</th>
								<th>Nama Barang</th>
								<th>Merk</th>
								<th>Tanggal Perolehan</th>
								<th>Harga Perolehan (Rp)</th>
								<th>Unit Kerja</th>
								<th>Keterangan</th>
								<th>Aksi</th>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- ========================================================================================== -->
		</div>
		<div class="row text-right">
			<div class="panel-footer">
				<a href="<?php echo $url_back ?>" class="btn btn-warning xhr dest_subcontent-element"><i class="fa fa-arrow-left btn-label-icon left"></i>Kembali</a>
				<?= save_button() ?>
			</div>
		</div>
	</form>
	<script type="text/javascript">
		$(document).ready(function () {
			$("div.input-group.date.day").datepicker({
				format: "dd-mm-yyyy",
				viewMode: "days",
				minViewMode: "days",
				autoclose: true
			});
			$("#listBarang").on("click", function () {
				var ajaxify = $(this).attr('href') + '/0';
				$(this).attr('ajaxify', ajaxify);
				return true;
			});
			$('.custom-file').pxFile();
			$("select[name='stat']").select2({ placeholder: "Pilih Status"});
			$("select[name='punit']").select2({ placeholder: "Unit kerja pemberi izin"});
			$("select[name='unit']").select2({ placeholder: "Unit kerja pengguna"});


			$("#tbl-rincian").on("click", ".btn-hapus", function () {
				var self = $(this);
				var nominal_row = $(this).val();
				bootbox.confirm({
					title: 'Konfirmasi',
					message: "Yakin data akan dihapus ?",
					buttons: {
						'confirm': {
							label: 'Ya',
							className: 'btn-danger'
						},
						'cancel': {
							label: 'Tidak',
							className: 'btn-default'
						}
					},
					callback: function (result) {
						if (result) {
							remove_rincian(self);
						}
					},
					className: "bootbox-sm"
				});
			});
		});
		function remove_rincian(self) {
			self.closest('tr').remove();
			if ($("#tbl-rincian tbody tr").length == 0) {
				$("#tbl-rincian tbody").append('<tr class="warning"><td colspan="13">&nbsp;</td></tr>');
				$("input[name='detail_'").val('');
			} else {
				urutkan_item();
			}
		}
		function urutkan_item() {
			var no = 1;
			$('#tbl-rincian > tbody  > tr').each(function () {
				$(this).find("td:first").text(no);
				no++;
			});

		}
	</script>