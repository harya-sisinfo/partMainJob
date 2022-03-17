<div id='modal-data-basic'>
	<style type="text/css">
	.btn{
		margin-left:3px;
		margin-top:3px;   
	}
</style>
<div id="validation_approval_mutasi_barang"  style="display:none;"></div>

<form enctype="multipart/form-data" id="form_approval_mutasi_barang" method="post" action="<?php echo $url_action; ?>">
	<input id="csrf_approval_mutasi_barang" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" >
	<input type="hidden" class="form-control" name="status_unit" value="<?php echo $status_unit?>">
	<div class="panel">
		<div class="panel-heading">
			<span class="panel-title">Detail Mutasi</span>
		</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-sm-12">
					<div class="row form-group">
						<label class="col-sm-3 control-label" style="text-align:left;">Unit Asal</label>
						<div class="col-sm-4">
							<?php echo $unit_asal?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="row form-group">
						<label class="col-sm-3 control-label" style="text-align:left;">Unit Tujuan</label>
						<div class="col-sm-4">
							<?php echo $unit_tujuan?>
							<input type="hidden" name="unit_tujuan_id" value="<?php echo $unit_tujuan_id?>">
							<input type="hidden" name="lokasi_tujuan_id" value="<?php echo $lokasi_tujuan_id?>">
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="row form-group">
						<label class="col-sm-3 control-label" style="text-align:left;">BA Mutasi</label>
						<div class="col-sm-4">
							<?php echo $ba_mutasi?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="row form-group">
						<label class="col-sm-3 control-label" style="text-align:left;">Tanggal BA Mutasi</label>
						<div class="col-sm-4">
							<?php echo $tgl_mutasi?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="row form-group">
						<label class="col-sm-3 control-label" style="text-align:left;">PIC</label>
						<div class="col-sm-4">
							<?php echo $pic?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">

					<div class="row form-group">
						<label class="col-sm-3 control-label" style="text-align:left;">Keterangan Tambahan</label>
						<div class="col-sm-4">
							<?php echo $keterangan?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="row form-group">
						<label class="col-sm-3 control-label" style="text-align:left;">File Dokumen BA Mutasi</label>
						<div class="col-sm-4">
							<?php 
							if(!empty($file_mutasi)){
							?>
							<a href="<?php echo $url_file?>" download class="btn btn-sm btn-flat btn-labeled btn-success" data-toggle="tooltip" data-placement="top" title="Download file" >
								<span class="btn-label-icon left fa fa-download"></span>&nbsp;Download
							</a>
							<?php 
							}else{
								?>
								<small>Belum terdapat file yang di unggah.</small>
								<?php
							}
							?>
						</div>
					</div>
				</div>
			</div>

			<?php
				if($status_unit=='unit_asal'){
			?>
			<div class="row">
				<div class="col-sm-12">

					<div class="row form-group">
						<label class="col-sm-3 control-label" style="text-align:left;">Status Approval</label>
						<div class="col-sm-4">
							<select class="form-control" name="status_approval" id="status_approval">
								<option></option>
								<option value="Y">Ya</option>
								<option value="T">Tidak</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<?php }else{
				?>
			<div class="row">
				<div class="col-sm-12">
					<div class="row form-group">
						<label class="col-sm-3 control-label" style="text-align:left;">PIC</label>
						<div class="col-sm-4">
							<input type="text" id="pic_unit_tujuan" class="form-control" name="pic_unit_tujuan" value="<?php echo $pic2?>">
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="row form-group">
						<label class="col-sm-3 control-label" style="text-align:left;">NIP</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" id="nip_unit_tujuan" name="nip_unit_tujuan" value="<?php echo $nip2?>">
						</div>
					</div>
				</div>
			</div>
				<?php
			} ?>
		</div>
		<div class="row text-right">
			<div class="panel-footer">
				<?php 
				if($status_unit == 'unit_tujuan'){
				?>
				<input type="hidden" name="input_draft" id="input_draft">
				<button class="btn btn-flat btn-primary" id="btn_draft"><span class="btn-label-icon left fa fa-bookmark"></span>Draft</button>
				<?php } ?>
				<button class="btn btn-flat btn-success" id="btn_proses"><span class="btn-label-icon left fa fa-check-circle"></span>Proses</button>
			</div>
		</div>
	</div>

<div class="table-light table-primary" style="overflow: auto;">
	<table class="table table-bordered table-striped">
		<thead>
			<tr>
				<th class="text-center">No</th>
				<th class="text-center">Kode Barang</th>
				<th class="text-center">Nama Barang</th>
				<th class="text-center">Merk</th>
				<th class="text-center">Spesifikasi</th>
				<th class="text-center">Tanggal Perolehan</th>
				<th class="text-center">Satuan</th>
				<th class="text-center">harga Perolehan</th>
				<th class="text-center">Nilai Buku</th>
				<th class="text-center">Kondisi</th>
				<th class="text-center">Keterangan</th>
				<?php
				if($status_unit=='unit_tujuan'){
					?>
					<th class="text-center">Status Pemeriksaan</th>
					<?php
				}
				?>

			</tr>
		</thead>
		<tbody>
			<?php 
			$i=1;
			foreach ($list as $key => $row) {
				$detail_id_barang 	= $row['mutasiDetId'];

				?>
				<tr>
					<td><?php echo $i++?></td>
					<td><?php echo $row['kode_aset']?></td>
					<td><?php echo $row['nama_aset']?></td>
					<td><?php echo $row['merk']?></td>
					<td><?php echo $row['spesifikasi']?></td>
					<td><?php echo $row['tgl_perolehan']?></td>
					<td><?php echo $row['satuan']?></td>
					<td><?php echo $row['nilai_perolehan']?></td>
					<td><?php echo $row['nilai_buku']?></td>
					<td><?php echo $row['kondisi']?></td>
					<td><?php echo $row['keteranganDet']?></td>
					<?php
					if($status_unit == 'unit_tujuan'){

						?>
					<td class="text-center">
						
						<input type="hidden" name="id_detail[]" value="<?php echo $row['mutasiDetId']?>" >
						<input type="hidden" name="id_barang[]" value="<?php echo $row['barangId']?>" >
						<input type="hidden" name="kode[]" value="<?php echo $row['kode_aset']?>" >
						<input type="hidden" name="kondisi[]" value="<?php echo $row['kondBrgId']?>" >
						<?php
						switch ($row['statTerima']) {
							case 'S':
								$cs_s 	= 'checked="checked"'; 
								$cs_ts 	= ''; 
								break;

							case 'TS':
								$cs_ts 	= 'checked="checked"'; 
								$cs_s 	= ''; 	
								break;
							
							default:
								$cs_s 	= ''; 
								$cs_ts 	= ''; 
								break;
						}
						?>
						<input type="radio" name="status_pemeriksaan_<?php echo $row['mutasiDetId']?>" <?php echo $cs_s?> value="S"> S &nbsp;
						<input type="radio" name="status_pemeriksaan_<?php echo $row['mutasiDetId']?>" <?php echo $cs_ts?> value="TS"> TS
					</td>
						<?php
					}
					?>
				</tr>
				<?php 
			}
			?>
		</tbody>
	</table>
</div>
</div>
</form>
<script type="text/javascript">
	$(document).ready(function () {
		$('[data-toggle="tooltip"]').tooltip();

		$.fn.modal.Constructor.prototype.enforceFocus = function () {};
		$("select[name='status_approval']").select2({
			allowClear: true,
			placeholder: 'Status Approval',
			dropdownParent: $("#modal-data-basic")
		});
		<?php 
			if($status_unit == 'unit_tujuan'){
		?>
		$("#btn_draft").click(function() {
			$("#input_draft").val("Draft");			
			//$("form").submit();
		});
		$("#btn_proses").click(function() {
			$("#input_draft").val("");			
			//$("form").submit();
		});
		<?php 
			}
		?>
	});
	var form_ = document.getElementById('form_approval_mutasi_barang');

	form_.addEventListener('submit', function (e) {
		e.preventDefault();
		$('.busy-indicator').show();

		$('#validation_approval_mutasi_barang').show();
		$('#validation_approval_mutasi_barang').html('Proses');
		var xhr_ = new XMLHttpRequest,
		fd_ = new FormData(form_);
		var to = setTimeout(function () {
			$('#validation_approval_mutasi_barang').html('Timeout.');
			$('.busy-indicator').hide();
			xhr_.abort();
		}, 20000);

		var action = $('#form_approval_mutasi_barang').attr('action');
		xhr_.open('post', action, true);
		xhr_.upload.onprogress = function (e) {
			if (e.lengthComputable) {
				$('#validation_approval_mutasi_barang').html('Proses berlangsung ' + Math.round(e.loaded * 100) + ' %.');
			}
		};
		xhr_.upload.onloadstart = function (e) {
			$('#validation_approval_mutasi_barang').html('Proses memulai.');
		};
		xhr_.upload.onloadend = function (e) {
			$('#validation_approval_mutasi_barang').html('Proses pengecekan.');
		};
		xhr_.onload = function (oEvent) {
			$('.busy-indicator').hide();
			if (xhr_.status === 200) {
				clearTimeout(to);
				var j = JSON.parse(xhr_.responseText);
				if (j.status === 'success') {
					$.growl.notice({message: j.msg, size: 'large'});
					$('#validation_approval_mutasi_barang').hide();
					$('#csrf_approval_mutasi_barang').attr('name', j.csrf_name);
					$('#csrf_approval_mutasi_barang').val(j.csrf_value);
					setTimeout(function () {
                        // $(j.dest).load(j.url);
                        location.reload();
                    }, 4000);
				} else {
					$.growl.error({message: j.msg, size: 'large'});
					$(j.dest).html(j.html);
					$('#csrf_approval_mutasi_barang').attr('name', j.csrf_name);
					$('#csrf_approval_mutasi_barang').val(j.csrf_value);
				}
			} else {
				$.growl.error({message: "Mohon maaf terjadi kesalahan, kami akan muat ulang halaman.", size: 'large'});
				setTimeout(function () {
					location.reload();
				}, 4000);
			}
		};
		xhr_.send(fd_);
	});
</script>