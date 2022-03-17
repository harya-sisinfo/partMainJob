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
					<label class="col-sm-2 control-label" style='text-align:left;'>Unit Asal</label>
					<div class="col-sm-8">
						<select class="form-control" name="unit_asal" id="unit_asal">
							<?php
							if(!empty($dataUnitKerja)){
								foreach($dataUnitKerja as $key_unit_asal => $row_unit_asal){
									if(!empty(set_value('unit_asal'))){
										$select_unit_asal = 'selected="selected"';
									}else{
										if($unit_asal_id==$row_unit_asal['id']){
											$select_unit_asal = 'selected="selected"';
										}else{
											$select_unit_asal = '';
										}
									}
									echo "<option ".$select_unit_asal." value='".$row_unit_asal['id']."' >".$row_unit_asal['kode'].' - '.$row_unit_asal['nama']."</option>";
								}
							}
							?>
						</select>
						<?php echo form_error('unit_asal', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>
					<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Unit Tujuan</label>
					<div class="col-sm-8">
						<select class="form-control" name="unit_tujuan">
							<?php
							if(!empty($dataUnitKerja)){
								foreach($dataUnitKerja as $key_unit_tujuan => $row_unit_tujuan){
									if(!empty(set_value('unit_tujuan'))){
										$select_unit_tujuan = 'selected="selected"';
									}else{
										if($unit_tujuan_id==$row_unit_tujuan['id']){
											$select_unit_tujuan = 'selected="selected"';
										}else{
											$select_unit_tujuan = '';
										}
										
									}
									echo "<option ".$select_unit_tujuan." value='".$row_unit_tujuan['id']."' >".$row_unit_tujuan['kode'].' - '.$row_unit_tujuan['nama']."</option>";
								}
							}
							?>
						</select>
						<?php echo form_error('unit_tujuan', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>
					<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>No. BA Mutasi</label>
					<div class="col-sm-5">
						<input type="text" autocomplete="off" placeholder="BA Mutasi" class="form-control" name="ba_mutasi" value="<?php echo $ba_mutasi?>">
						<?php echo form_error('ba_mutasi', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>
					<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Tangga BA Mutasi</label>
					<div class="col-sm-3">
						<div class="input-group date day">
							<input type="text" name="tanggal_mutasi" class="form-control"
							placeholder="Tanggal BA Mutasi" autocomplete="off"
							value="<?php echo  (empty(set_value('tanggal_mutasi')))?$tanggal_mutasi:set_value('tanggal_mutasi') ?>"><span
							class="input-group-addon"><i class="fa fa-calendar"></i></span>
						</div>
						<?php echo form_error('tanggal_mutasi', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>
					<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>PIC</label>
					<div class="col-sm-6">
						<input type="text" autocomplete="off" placeholder="PIC" class="form-control" name="pic" value="<?php echo (empty(set_value('pic')))?$pic:set_value('pic')?>">
						<?php echo form_error('pic', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
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
					<div class="col-sm-6">
						<input type="text" autocomplete="off" placeholder="NIP" class="form-control" name="nip" value="<?php echo (empty(set_value('nip')))?$nip:set_value('nip')?>">
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
					<label class="col-sm-2 control-label" style='text-align:left;'>Keterangan Tambahan</label>
					<div class="col-sm-7">
						<textarea class="form-control" placeholder="Keterangan tambahan" name="keterangan"><?php echo (empty(set_value('keterangan')))?$keterangan:set_value('keterangan')?></textarea>
					</div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>File BA Mutasi </label>
					<div class="col-sm-8">
						<input type="hidden" name="old_ba_file" value="<?php echo $file_mutasi?>">
						<label class="custom-file px-file">
							<input type="file" class="custom-file-input" name="ba_file[]" onchange="checkFileUpload(this,['jpg','jpeg','pdf']);" >
							<span class="custom-file-control form-control">
								<?php
								if(!empty(set_value('ba_file[]'))){
									echo set_value('ba_file[]');
								}else{
									echo "Pilih file ... ";
								}
								
							?></span>
							<div class="px-file-buttons">

								<button type="button" class="btn px-file-clear">Clear</button>
								<button type="button" class="btn btn-primary px-file-browse">Browse</button>
							</div>

						</label>

						<p class='help-block'>
						<?php 
							if(!empty($file_mutasi)){
						?>
					
							<a href="<?php echo $url_file?>" class="btn btn-sm btn-labeled btn-primary" download><i class="fa fa-download btn-label-icon left"></i>Download</a>
						<?php
							}
						?>
							<i>Format file <b>*.jpg|.jpeg|.pdf</b></i>
						</p>
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

								<a id="listBarang" rel='async' ajaxify="" href="<?php echo modal("Pilih Barang",'aset_manajemen_barang','mutasi_antar_unit','list_barang')?>"class="btn btn-sm btn-labeled btn-success"><i class="fa fa-plus btn-label-icon left"></i>Pilih Barang</a>

								<input type="hidden" name="detail_" value="<?php echo !empty($detail_mutasi)?'detail not empty':''?>">
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
											<th>Tanggal Perolehan</th>
											<th>Harga Perolehan (Rp)</th>
											<th>Kondisi Barang Mutasi *</th>
											<th>Keterangan</th>
											<th>Aksi</th>
										</thead>
										<tbody>

											<?php
											$i=0;
											foreach ($detail_mutasi as $key_mutasi => $row_mutasi) {
												$i++;
												$view_list = '<tr>';

												$view_list .= '<td class="text-center">';
												$view_list .= $i;
												$view_list .= '</td>';

												$view_list .= '<td>';
												$view_list .= $row_mutasi['kode_barang'];
												$view_list .= '</td>';

												$view_list .= '<td>';
												$view_list .= $row_mutasi['nama_aset'];
												$view_list .= '</td>';

												$view_list .= '<td>';
												$view_list .= $row_mutasi['spesifikasi'];
												$view_list .= '</td>';

												$view_list .= '<td>';
												$view_list .= $row_mutasi['tgl_perolehan'];
												$view_list .= '</td>';

												$view_list .= '<td>';
												$view_list .= number_format($row_mutasi['nilai_perolehan']);
												$view_list .= '</td>';

												$view_list .= '<td>';
												// $view_list .= $row_mutasi['kondBrgId'];
												$view_list .= '<select class="form-control select_kondisi" name="kondisi[]" >';
												$view_list .= '<option value="1"'.(($row_mutasi['kondBrgId']=='1')?' selected="selected" ':'').'>Baik</option>';
												$view_list .= '<option value="2"'.(($row_mutasi['kondBrgId']=='2')?' selected="selected" ':'').'>Rusak Ringan</option>';
												$view_list .= '<option value="3"'.(($row_mutasi['kondBrgId']=='3')?' selected="selected" ':'').'>Rusak Berat</option>';
												$view_list .= '</select>';


												$view_list .= '</td>';

												$view_list .= '<td>';
												$view_list .= $row_mutasi['keteranganDet'];
												$view_list .= '</td>';

												$view_list .= '<td>';
												$view_list .= '<input type="hidden" name="barang_id[]"  value="' . $row_mutasi['barangId'] . '">';
												$view_list .= '<input type="hidden" name="kode_barang[]"  value="' . $row_mutasi['kode_barang'] . '">';
												$view_list .= '<input type="hidden" name="nama_barang[]"  value="' . $row_mutasi['nama_aset'] . '">' ;
												$view_list .= '<input type="hidden" name="merk[]"  value="' . $row_mutasi['merk'] . '">' ;
												$view_list .= '<input type="hidden" name="spesifikasi[]"  value="' . $row_mutasi['spesifikasi'] . '">' ;
												$view_list .= '<input type="hidden" name="tgl_perolehan[]"  value="' . $row_mutasi['tgl_perolehan'] . '">' ;
												$view_list .= '<input type="hidden" name="nilai_perolehan[]"  value="' . $row_mutasi['nilai_perolehan'] . '">' ;
												$view_list .= '<button type="button" class="btn btn-danger btn-sm btn-hapus" data-toggle="tooltip" data-placement="top" value="' . $row_mutasi['kondBrgId'] . '" title="hapus"><span class="icon fa fa-trash-o"></span></button>' ;

												$view_list .= '</td>';

												$view_list .= '</tr>';

												echo $view_list;
											}
											?>
										</tbody>
									</table>
									<?php //print_r($detail_mutasi)?>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>

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
		$(".select_kondisi").select2({
			placeholder: "Pilih kondisi"
		});

		$("select[name='unit_asal']").select2({
			allowClear: true,
			placeholder: "Unit Asal"
		}).change(function () {
			Tanggal = new Date();
			SaatIni = (Tanggal.getDate())+'-'+(Tanggal.getMonth()+1)+'-'+(Tanggal.getFullYear());
			var noBaMutasi = 'BA-MUT-'+(Tanggal.getFullYear())+(Tanggal.getMonth()+1)+(Tanggal.getDate())+(Tanggal.getHours())+(Tanggal.getMinutes())+(Tanggal.getSeconds());
			if (this.value != "") {
				$(this).addClass('loading');
				$("input[name='ba_mutasi']").val(noBaMutasi);
				$.get("<?php echo site_url('aset_manajemen_barang/mutasi_antar_unit/get_pic'); ?>",
				{
					unit_asal: $("select[name='unit_asal']").val()
				},
				function (data) {

					var obj = jQuery.parseJSON(data);
					$("input[name='pic']").val(obj.pic_nama);
					$("input[name='nip']").val(obj.pic_nip);
					$("select[name='unit_tujuan']").val(null).trigger('change');
					$("input[name='tanggal_mutasi']").val(SaatIni);
				});
			}
			$("#tbl-rincian tbody").html("");
		});

		$("select[name='unit_tujuan']").select2({
			allowClear: true,
			placeholder: "Unit Tujuan"
		}).change(function () {
			var unit_asal = $("select[name='unit_asal']").val();
			if(unit_asal==this.value){
				$("select[name='unit_asal']").val(null).trigger('change');
				$("input[name='pic']").val('');
				$("input[name='nip']").val('');
			} else {
				
				if (this.value != "") {
					$(this).addClass('loading');
					$.get("<?php echo site_url('aset_manajemen_barang/mutasi_antar_unit/get_pic'); ?>",
					{
						unit_asal: $("select[name='unit_asal']").val()
					},
					function (data) {
						var obj = jQuery.parseJSON(data);
						$("input[name='pic']").val(obj.pic_nama);
						$("input[name='nip']").val(obj.pic_nip);
						
					});
				}
			}
		});
		$("div.input-group.date.day").datepicker({
			format: "dd-mm-yyyy",
			viewMode: "days",
			minViewMode: "days",
			autoclose: true
		});
		$("#listBarang").on("click", function () {
			var unit_asal = $("select[name='unit_asal']").val();
			if (!(unit_asal)) {
				bootbox.alert({
					message: "Unit asal harus diisi!",
					className: "bootbox-sm"
				});
				return false;
			}
			var ajaxify = $(this).attr('href') + '/0/' + unit_asal;
			$(this).attr('ajaxify', ajaxify);
			return true;
		});
		$('.custom-file').pxFile();
		// ============================================
        // saat klik tombol hapus rincian
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
