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
					<label class="col-sm-2 control-label" style='text-align:left;'>Nama Penerima</label>
					<div class="col-sm-6">
						<input type="text" autocomplete="off" placeholder="Nama Penerima" class="form-control" name="nama_penerima" value="<?php echo  set_value('nama_penerima') ?>">
						<?php echo form_error('nama_penerima', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Unit PJ <font color="red">*</font></label>
					<div class="col-sm-8">
						<select class="form-control" name="unit_pj">
							<option value=""></option>
							<?php
							if (!empty($data_unit_pj)) {
								foreach ($data_unit_pj as $key_unit_pj => $row_unit_pj) {
									if (!empty(set_value('unit_pj'))) {
										$select_unit_pj = 'selected="selected"';
									} else {
										$select_unit_pj = '';
									}
									echo "<option " . $select_unit_pj . " value='" . $row_unit_pj['id'] . "' >" . $row_unit_pj['kodesatker'] . ' - ' . $row_unit_pj['unit'] . "</option>";
								}
							}
							?>
						</select>
						<?php echo form_error('unit_pj', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>

				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Tanggal <font color="red">*</font></label>
					<div class="col-sm-3">
						<div class="input-group date day">
							<input type="text" name="tanggal" class="form-control" placeholder="Tanggal" autocomplete="off" value="<?php echo  set_value('tanggal') ?>"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						</div>
						<?php echo form_error('tanggal', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Description <font color="red">*</font></label>
					<div class="col-sm-8">
						<textarea class="form-control" name="diskripsi" placeholder="Description"></textarea>
						<?php echo form_error('diskripsi', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>

				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<?php echo form_error('list_barang', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>

					<div class="col-sm-12">
						<div class="panel">
							<div class="panel-heading">
								<span class="panel-title"><b>List Barang</b>
									<font color="red">*</font>
								</span>
								<div class="panel-heading-controls">
									<a rel='async' id="listBarang" ajaxify="<?php echo modal("Pilih Barang", 'aset_manajemen_bhp', 'register_transaksi_harian', 'list_barang') ?>" href="" class="btn btn-sm btn-labeled btn-success"><i class="fa fa-search btn-label-icon left"></i>Pilih Barang</a>
								</div>
							</div>
							<div class="panel-body">
								<div class="table-primary">
									<div style="overflow: auto;">
										<table id="tbl-rincian" class="table table-bordered">
											<thead>
												<th>No</th>
												<th>Kode Barang</th>
												<th>Nama Barang</th>
												<th>Diskripsi</th>
												<th>Stok Barang</th>
												<th>Jumlah Diminta</th>
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
		</div>
		<!-- ========================================================================================== -->
	</div>

	<div class="row text-right">
		<div class="panel-footer">
			<a href="<?php echo $url_back ?>" class="btn btn-warning xhr dest_subcontent-element"><i class="fa fa-arrow-left btn-label-icon left"></i>Kembali</a>
			<?php echo save_button() ?>
		</div>
	</div>
</div>
</form>
<script type="text/javascript">
	$(document).ready(function() {
		$("select[name='unit_pj']").select2({
			allowClear: true,
			placeholder: "Pilih Unit PJ"
		});
		$("div.input-group.date.day").datepicker({
			format: "dd-mm-yyyy",
			viewMode: "days",
			minViewMode: "days",
			autoclose: true
		});
		
	});
</script>