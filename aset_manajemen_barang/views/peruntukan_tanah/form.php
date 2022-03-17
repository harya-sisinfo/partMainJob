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
					<label class="col-sm-2 control-label" style='text-align:left;'>Kode Inventarisasi</label>
					<div class="col-sm-7">
						<input type="text" class="form-control" placeholder="Kode Inventarisasi" name="kode_inventarisasi">
					</div>
					<div class="col-sm-2">
					<a id="listBarang" rel='async' ajaxify="" href="<?php echo modal("Pilih Barang",'aset_manajemen_barang','peruntukan_tanah','list_invintaris_barang',0)?>"class="btn btn-sm btn-labeled btn-success"><i class="fa fa-plus btn-label-icon left"></i>Pilih Kode Inventarisasi</a>
					</div>
					<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Nama</label>
					<div class="col-sm-8">
						
					</div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Kode Tanah Pemerintah</label>
					<div class="col-sm-4">
                        <input type="text" autocomplete="off" placeholder="Kode Tanah Pemerintah" class="form-control" name="kodepemerintah">
                        <?php echo form_error('kodepemerintah', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
                    </div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Nomor Sertipikat</label>
					<div class="col-sm-4">
                        <input type="text" autocomplete="off" placeholder="Nomor Sertipikat" class="form-control" name="sertipikat">
                        <?php echo form_error('sertipikat', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
                    </div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Peruntukan</label>
					<div class="col-sm-8">
						<select class="form-control" name="peruntukan">
							
						</select>
					</div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Panjang (m<sup>2</sup>)</label>
					<div class="col-sm-8">
						<input type="text" autocomplete="off" placeholder="Panjang" class="form-control" name="panjang">
                        <?php echo form_error('panjang', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Lebar (m<sup>2</sup>)</label>
					<div class="col-sm-8">
						<input type="text" autocomplete="off" placeholder="Lebar" class="form-control" name="lebar">
                        <?php echo form_error('lebar', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>
					
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Luas</label>
					<div class="col-sm-8">
						<input type="text" autocomplete="off" placeholder="Luas" class="form-control" name="luas">
                        <?php echo form_error('luas', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>					
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Status Penggunaan</label>
					<div class="col-sm-8">
						<select class="form-control" name="status_pengguna">
							
						</select>
					</div>
					
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Atas Nama</label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="BA Penyusutan" class="form-control" name="unitnama">
					</div>
					
					<?php echo form_error('unitnama', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Alamat</label>
					<div class="col-sm-8">
						<textarea class="form-control" placeholder="Alamat" name="alamat"></textarea>
					</div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Keterangan</label>
					<div class="col-sm-8">
						<textarea class="form-control" placeholder="Keterangan" name="keterangan"></textarea>
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
		$("select[name='kode']").select2({
			allowClear: true,
			placeholder: "Kode inventaris"
		});
		$("select[name='peruntukan']").select2({
			allowClear: true,
			placeholder: "Peruntukan"
		});
		$("select[name='status_pengguna']").select2({
			allowClear: true,
			placeholder: "Status Pengguna"
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