<?php echo Modules::run('breadcrump'); ?>
<form rel="ajax-file" enctype="multipart/form-data" id="form_add" action="<?php echo $url_action ?>" class="panel form-horizontal xhr dest_subcontent-element" method="post">
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
	<div class="panel-heading">
		<span class="panel-title"><b><?php echo $form_label ?></b></span>
	</div>
	<div class="panel-body">
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Tanggal Usulan&nbsp;<font color="red">*</font></label>
					<div class="col-sm-5">
						<div class="input-group date day">
							<input type="text" name="tanggal_usulan" class="form-control" placeholder="Masukan Tanggal Usulan" autocomplete="off" value="<?php echo  set_value('tanggal_usulan') ?>"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						</div>
						<?php echo form_error('tanggal_usulan', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Nomor Usulan&nbsp;<font color="red">*</font></label>
					<div class="col-sm-5">
						<input type="text" autocomplete="off" placeholder="Masukan Nomor Usulan " class="form-control" name="nomor_usulan" value="<?php echo  set_value('nomor_usulan', ($nomor_usulan != '') ? $nomor_usulan : '') ?>">
						<?php echo form_error('nomor_usulan', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Unit Kerja&nbsp;<font color="red">*</font></label>
					<div class="col-sm-5">
						<select class="form-control select-unit-kerja" name="unit_kerja" id="unit_kerja">
							<?php
							foreach ($unit_kerja as $key_unit => $row_unit) {
							?><option value="<?php echo $row_unit['unitkerjaId'] ?>"><?php echo $row_unit['unitkerjaKode'] . ' - ' . $row_unit['unitkerjaNama'] ?></option>
							<?php } ?>
						</select>
						<?php echo form_error('unit_kerja', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>List Barang</label>
					<div class="col-sm-10">
						<div class="row">
							<div class="col-sm-3">
								<a id="listBarang" rel='async' ajaxify="<?php echo modal("Pilih Barang", 'aset_manajemen_bhp', 'usulan_bhp', 'list_barang') ?>" href="" class="btn btn-sm btn-labeled btn-success"><i class="fa fa-plus btn-label-icon left"></i>Pilih Barang</a>
								<input type="hidden" name="detail_">
								<?php echo form_error('detail_', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
							</div>
						</div>
						<div class="row">&nbsp;</div>
						<div class="row col-sm-12">
							<div class="table-primary">
								<div style="overflow: auto;">
									<table id="tbl-rincian" class="table table-bordered">
										<thead>
											<th>No</th>
											<th>Kode Barang</th>
											<th>Nama Barang</th>
											<th>Spesifikasi</th>
											<th>File</th>
											<th>Tanggal Pakai</th>
											<th>Jumlah Usulan</th>
											<th>Satuan</th>
											<th>Harga Satuan (Rp)</th>
											<th>Sub Total (Rp)</th>
											<th>Aksi</th>
										</thead>
										<tbody></tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row text-right">
			<div class="panel-footer">
				<a href="<?php echo $url_back ?>" class="btn btn-warning xhr dest_subcontent-element"><i class="fa fa-arrow-left btn-label-icon left"></i>Kembali</a>
				<?= save_button() ?>
			</div>
		</div>
	</div>
</form>
<script>
	$(document).ready(function() {
		$("div.input-group.date.day").datepicker({
			format: "dd-mm-yyyy",
			viewMode: "days",
			minViewMode: "days",
			autoclose: true
		});
		$("select[name='unit_kerja']").select2({
			allowClear: true,
			placeholder: "Pilih Unit Kerja Pengusul"
		});
		$("input[name='tanggal_usulan']").change(function() {
			tanggal_ = $("input[name='tanggal_usulan']").val().split("-");
			tanggal = tanggal_[2] + tanggal_[1] + tanggal_[0];
			waktu = String(new Date().getSeconds()) + String(new Date().getMinutes()) + String(new Date().getHours());
			nomor_usulan = 'usul.' + tanggal + '.' + waktu;
			$("input[name='nomor_usulan']").val(nomor_usulan);
		});
		$("#tbl-rincian").on("click", ".btn-hapus", function() {
			var self = $(this);
			var idx = $(this).val();
			var nominal_row = formatDisplayToInput($('#aset_sub_total_' + idx).val());
			bootbox.confirm({
				title: 'Konfirmasi',
				message: "Yakin data akan dihapus ?" + nominal_row,
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
				callback: function(result) {
					if (result) {
						remove_rincian(self, nominal_row);
					}
				},
				className: "bootbox-sm"
			});
		});
	});
</script>