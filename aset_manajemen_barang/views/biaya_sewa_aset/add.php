<?php echo Modules::run('breadcrump'); ?>

<form rel="ajax" id="form_<?php echo $isProses?>" action="<?php echo $url_action ?>" class="panel form-horizontal xhr dest_subcontent-element" method="post">

	<div class="panel-heading">
		<span class="panel-title"><?php echo $form_label?></span>
	</div>

	<div class="panel-body">
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label"  style='text-align:left;'>Barang Inventaris</label>
					<div class="col-sm-7">
						<input type="text" class="form-control" readonly="readonly" placeholder="Barang Inventaris" name="biaya_sewa_label">
						<input type="hidden" class="form-control" name="label_id">
						<input type="hidden" class="form-control" name="label_lokasi">
						<input type="hidden" class="form-control" name="label_pemilik">
						<input type="hidden" class="form-control" name="label_kondisi">

						<?php echo form_error('biaya_sewa_label', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>
					<font class="col-sm-1" color="red">*</font>
					<div class="col-sm-2">

						<a id="listBarang" rel='async' ajaxify="<?php echo modal("Pilih Barang",'aset_manajemen_barang','biaya_sewa_aset','list_barang_inventaris')?>" class="btn btn-sm btn-labeled btn-success"><i class="fa fa-plus btn-label-icon left"></i>Pilih Barang</a>
					</div>
					<?php echo form_error('biaya_sewa_label_id', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Kode Aset</label>
					<div class="col-sm-8">
						<input type="text" readonly="readonly" autocomplete="off" placeholder="Kode Aset" class="form-control" name="biaya_sewa_kode_aset">
					</div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Kode</label>
					<div class="col-sm-4">
                        <input type="text" autocomplete="off" placeholder="Kode" class="form-control" name="biaya_sewa_kode">
                    	<?php echo form_error('biaya_sewa_kode', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
                    </div>
                    <font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Nilai Sewa</label>
					<div class="col-sm-4">
                        <input type="text" autocomplete="off" placeholder="Nilai Sewa" class="form-control nominal" name="biaya_sewa">
                        <?php echo form_error('biaya_sewa', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
                    </div>
                        <font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Biaya Kerusakan</label>
					<div class="col-sm-4">
                        <input type="text" autocomplete="off" placeholder="Biaya Kerusakan" class="form-control nominal" name="biaya_sewa_rusak">
                    </div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Penggantian</label>
					<div class="col-sm-4">
                        <input type="text" autocomplete="off" placeholder="Penggantian" class="form-control nominal" name="biaya_sewa_denda">
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

<script type="text/javascript">
	$(document).ready(function () {
		$(".nominal").focus(function() {
			this.value = formatDisplayToInput(this.value);
		});

		$(".nominal").keypress(function(event) {
			return validasiInput(this.value,event);
		});

		$(".nominal").blur(function() {
			if (this.value != ''){
				this.value = formatInputToDisplay(this.value);
			}						
		});
	});	
</script>