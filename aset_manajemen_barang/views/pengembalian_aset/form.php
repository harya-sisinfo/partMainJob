<?php echo Modules::run('breadcrump'); ?>
<form rel="ajax" id="form_update" action="<?php echo $url_action ?>" class="panel form-horizontal xhr dest_subcontent-element" method="post">
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

<input type="hidden" name="sewa_id" value="<?php echo $sewa_id?>">
<input type="hidden" name="sewa_id" value="<?php echo $sewa_id?>">

	<div class="panel-heading">
		<span class="panel-title">Form Pengembalian Aset</span>
	</div>

	<div class="panel-body">
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Tangga Pengembalian</label>
					<div class="col-sm-3">
						<div class="input-group date day">
							<input type="text" name="tanggal" class="form-control"
							placeholder="Tanggal Pengembalian" autocomplete="off"
							value="<?php echo  set_value('tanggal') ?>"><span
							class="input-group-addon"><i class="fa fa-calendar"></i></span>
						</div>
						<?php echo form_error('tanggal', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
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
					<div class="col-sm-8">
						<textarea name="keterangan" class="form-control" rows="8" cols="40" id="0" ><?php echo  $keterangan ?></textarea>
					</div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Aset Sewa</label>
					<div class="col-sm-10">
						<div class="table-light table-primary" style="overflow: auto;">
							<table class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>No</th>
										<th>Kode Sewa</th>
										<th>Label</th>
										<th>Biaya Kerusakan</th>
										<th>Biaya Penggantian</th>
										<th>Keterangan</th>
									</tr>
								</thead>
								<tbody>
									<?php 
									if(!empty($content)){
										$i=0;
										foreach ($content as $key => $row) {
										
									
									?>
									<tr>
										<td><?php echo $i++?></td>
										<td><?php echo $row['asetKode']?></td>
										<td><?php echo $row['asetLabel']?></td>
										<td>
											<input 
												type="hidden" 
												name="sewa_detail_id[]"
												value="<?php echo $row['sewa_detail_id']?>" class="form-control">
											<input 
												type="hidden" 
												name="asetId[]"
												value="<?php echo $row['asetId']?>" class="form-control">

											<input 
												type="text" 
												name="rusak[]"
												value="<?php echo $row['rusak']?>" 
												class="form-control"></td>
										<td>
											<input 
												type="text" 
												name="ganti[]"
												value="<?php echo $row['ganti']?>" 
												class="form-control">
										</td>
										<td><textarea class="form-control" name="subKeterangan[]"></textarea></td>
									</tr>
									<?php }}?>
								</tbody>
							</table>
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

<script type="text/javascript">
	$(document).ready(function () {
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
	});	
</script>