<?php echo Modules::run('breadcrump'); ?>

<form rel="ajax" id="form_<?php echo $isProses?>" action="<?php echo $url_action ?>" class="panel form-horizontal xhr dest_subcontent-element" method="post">
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
<input type="hidden" class="form-control" value="<?php echo $id?>" name="id" >
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
						<input type="text" readonly class="form-control" placeholder="Kode Inventarisasi" name="kode" value="<?php echo $content['kode']?>" id="kode">
						<input type="hidden" readonly class="form-control" placeholder="Kode Inventarisasi" name="kodeinv" value="<?php echo $content['kodeinv']?>" id="kodeinv">
						<input type="hidden" value="<?php echo $content['invdet']?>" autocomplete="off" class="form-control" name="invdet">
						<?php echo form_error('kode', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>
					<div class="col-sm-2">
						<a id="listBarang" rel='async' ajaxify="<?php echo modal("Pilih Barang",'aset_manajemen_barang','peruntukan_tanah','list_inventaris_barang')?>" class="btn btn-sm btn-labeled btn-success"><i class="fa fa-plus btn-label-icon left"></i>Pilih Inventaris</a>
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
					<div class="col-sm-7">
						<input type="text" class="form-control" readonly="readonly" value="<?php echo $content['nama_aset']?>" name="nama">
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
						<input type="text" autocomplete="off" placeholder="Kode Tanah Pemerintah" class="form-control" name="kodepemerintah" value="<?php echo $content['kodepemerintah']?>">

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
						<input type="text" autocomplete="off" placeholder="Nomor Sertipikat" class="form-control" name="sertipikat" value="<?php echo $content['sertipikat']?>">

					</div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Peruntukan</label>
					<div class="col-sm-3">
						<select class="form-control" name="peruntukan">
							<option></option>
							<?php
							foreach ($dataPeruntukanTanah as $key_pt => $row_pt) {
								if($row_pt['id'] == $content['peruntukan']){
									$select_peruntukan = 'selected="selected"';  
								}else{
									$select_peruntukan = "";  
								}	
								?>
								<option <?php echo $select_peruntukan ?> value="<?php echo $row_pt['id']?>"><?php echo $row_pt['name']?></option>
								<?php 
							}
							?>
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
					<div class="col-sm-3">
						<input type="text" autocomplete="off" placeholder="Panjang" class="form-control nominal" name="panjang" value="<?php echo $content['panjang']?>">

					</div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Lebar</label>
					<div class="col-sm-3">
						<input type="text" autocomplete="off" placeholder="Lebar" class="form-control nominal" name="lebar" value="<?php echo $content['lebar']?>">

					</div>
					
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Luas (m<sup>2</sup>)</label>
					<div class="col-sm-3">
						<input type="text" autocomplete="off" placeholder="Luas" class="form-control nominal" name="luas" value="<?php echo $content['luas']?>">

					</div>					
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Status Penggunaan</label>
					<div class="col-sm-3">
						<select class="form-control" name="penggunaan" id="penggunaan">
							<option></option>
							<?php
							foreach ($dataStatusPengguna as $key_sp => $row_sp) {
								if($row_sp['id']){
									$select_penggunaan = 'selected="selected"';
								}
								?>
								<option <?php echo $select_penggunaan?>  value="<?php echo $row_sp['id']?>"><?php echo $row_sp['name']?></option>
								<?php 
							}
							?>
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
					<div class="col-sm-8">
						<input type="text" autocomplete="off" value="<?php echo $content['unitnama']?>" placeholder="BA Penyusutan" class="form-control" name="unitnama">
					</div>				
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Alamat</label>
					<div class="col-sm-8">
						<textarea class="form-control" placeholder="Alamat" name="alamat"><?php echo $content['alamat']?></textarea>
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
						<textarea class="form-control" placeholder="Keterangan" name="keterangan"><?php echo $content['keterangan']?></textarea>
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
		$("select[name='penggunaan']").select2({
			allowClear: true,
			placeholder: "Pilih Status Penggunaan"
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

		$(".nominal").focus(function () {
			this.value = formatDisplayToInput(this.value);
		});
		$(".nominal").keypress(function (event) {
			return validasiInput(this.value, event);
		});

		$(".nominal").blur(function () {
			if (this.value != '') {
				this.value = formatInputToDisplay(this.value);
			}
		});
		$("input[name='lebar'],input[name='panjang']").keyup(function () {
			var lebar = ($("input[name='lebar']").val())?parseFloat($("input[name='lebar']").val().split(".").join("")):0;
			var panjang = ($("input[name='panjang']").val())?parseFloat($("input[name='panjang']").val().split(".").join("")):0;
			var luas = parseFloat(lebar*panjang);
			$("input[name='luas']").val(formatInputToDisplay(luas));
		});
	});


</script>