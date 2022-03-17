<?php echo Modules::run('breadcrump'); ?>

<form rel="ajax" id="form_<?php echo $isProses?>" action="<?php echo $url_action ?>" class="panel form-horizontal xhr dest_subcontent-element" method="post">
<input id="csrf_add_kode_penerimaan" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" >
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
                                   value="<?= set_value('periode_penyusutan') ?>"><span
                                class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>
                        <?php echo form_error('periode_penyusutan', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
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
                                   value="<?= set_value('tanggal_transaksi') ?>"><span
                                class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
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
							<?php 
							foreach ($dataJenisAset as $key_ja => $row_ja) {
								if(set_value('kib_id')==$row_ja['kib_id']){
									$selected_ja = "selected='selected'";;
								}else{
									$selected_ja = '';
								}
							echo "<option value='".$row_ja['kib_id']."' ".$selected_ja.">".$row_ja['kib_nama']."</option>"; 
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
					<label class="col-sm-2 control-label" style='text-align:left;'>BA Penyusutan</label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="BA Penyusutan" class="form-control" name="ba_penyusutan" value="<?= set_value('ba_penyusutan')?>">
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
		}).change(function () {
			if($("input[name='periode_penyusutan']").val() != ""){
            	var periode_ = $("input[name='periode_penyusutan']").val().split('-');
            	var periode = '/'+periode_[0]+'/'+periode_[1]+'/';
			}else{
            	var periode = '/';
			}
            var label_ba_penyusutan = 'Aset'+periode;
            $("input[name='ba_penyusutan']").val(label_ba_penyusutan);
        });

        $("div.input-group.date.month").datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        }).change(function () {
			if($("input[name='periode_penyusutan']").val() != ""){
            	var periode_ = $("input[name='periode_penyusutan']").val().split('-');
            	var periode = '/'+periode_[0]+'/'+periode_[1]+'/';
			}else{
            	var periode = '/';
			}
            var label_ba_penyusutan = 'Aset'+periode;
            $("input[name='ba_penyusutan']").val(label_ba_penyusutan);
        });
        $("div.input-group.date.day").datepicker({
            format: "dd-mm-yyyy",
            viewMode: "days",
            minViewMode: "days",
            autoclose: true
        });
	});	
</script>