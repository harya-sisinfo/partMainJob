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
					<label class="col-sm-2 control-label" style='text-align:left;'>Jenis Transaksi</label>
					<div class="col-sm-8">
						Penyusutan Aset
					</div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Tipe Transaksi</label>
					<div class="col-sm-8">
						Pengeluaran
					</div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Periode Penyusutan</label>
					<div class="col-sm-4">
                        <div class="input-group date month">
                            <input type="text" name="periode_penyusutan" class="form-control"
                                   placeholder="Bulan dan tahun periode penyusutan" autocomplete="off"
                                   value="<?= set_value('bulan', isset($objFormData) ? $objFormData['periode_penyusutan'] : '') ?>"><span
                                class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                        <?php echo form_error('periode_penyusutan', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
                    </div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Tanggal Transaksi</label>
					<div class="col-sm-3">
                        <div class="input-group date day">
                            <input type="text" name="tanggal_transaksi" class="form-control"
                                   placeholder="Tanggal transaksi" autocomplete="off"
                                   value="<?= set_value('bulan', isset($objFormData) ? $objFormData['tanggal_transaksi'] : '') ?>"><span
                                class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                        <?php echo form_error('tanggal_transaksi', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
                    </div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Jenis Aset</label>
					<div class="col-sm-8">
						<select class="form-control" name="kib_id">
							
						</select>
					</div>
					
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>BA Penyusutan</label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="BA Penyusutan" class="form-control" name="ba_penyusutan">
					</div>
					<font class="col-sm-1" color="red">*</font>
					<?php echo form_error('ba_penyusutan', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Catatan Transaksi</label>
					<div class="col-sm-8">
						<textarea class="form-control" placeholder="Catatan transaksi" name="catatan_transaksi"></textarea>
					</div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Penanggung Jawab</label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="Penanggung jawab" class="form-control" name="penanggung_jawab">
					</div>
					<font class="col-sm-1" color="red">*</font>
					<?php echo form_error('penanggung_jawab', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->			<div class="row text-right">
			<div class="panel-footer">
				<a href="<?php echo $url_back ?>" class="btn btn-warning xhr dest_subcontent-element"><i class="fa fa-arrow-left btn-label-icon left"></i>Kembali</a>
				<?= save_button() ?>
			</div>
		</div>

	</div>
</form>

<script type="text/javascript">
	$(document).ready(function () {
		$("select[name='kib_id']").select2({
			allowClear: true,
			placeholder: "Jenis aset"
		});

        $("div.input-group.date.month").datepicker({
            format: "MM yyyy",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });
        $("div.input-group.date.day").datepicker({
            format: "dd-mm-yyyy",
            viewMode: "days",
            minViewMode: "days",
            autoclose: true
        });
	});	
</script>