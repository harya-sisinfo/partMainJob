<?php echo Modules::run('breadcrump'); ?>
<style type="text/css">
	.ui-autocomplete { height: 200px; overflow-y: scroll; overflow-x: hidden;}
	.ui-autocomplete-loading 
	{
		background: white url("<?php echo site_url('ugmfw-assets/images/spin.gif'); ?>") right center no-repeat;
	}
</style>
<form rel="ajax" id="form_<?php echo $isProses?>" action="<?php echo $url_action ?>" class="panel form-horizontal xhr dest_subcontent-element" method="post">
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
	<div class="panel-heading">
		<span class="panel-title"><?php echo $form_label?></span>
	</div>

	<div class="panel-body">
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Tanggal Sewa </label>
					<div class="col-sm-6">
						<div class="input-daterange input-group" id="periode-range">
							<input type="text" class="form-control" name="tanggal_mulai" placeholder="Tanggal Awal" value="<?= set_value('tanggal_mulai') ?>" autocomplete="off">
							<span class="input-group-addon">s.d</span>
							<input type="text" class="form-control" name="tanggal_selesai" placeholder="Tanggal Akhir" value="<?= set_value('tanggal_selesai') ?>" autocomplete="off">
						</div>
						<?php echo form_error('tanggal_mulai', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div><font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>

		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Jam Sewa</label>
					<div class="col-sm-5">
						<div class="input-group">
							<input type="text" class="form-control" placeholder="Jam Mulai" name="jamMulai" autocomplete="off"
							value="<?= set_value('jamMulai') ?>"><span
							class="input-group-addon"><i class="fa fa-clock-o"></i></span><span class="input-group-addon">s.d</span><input type="text" placeholder="Jam Selesai" class="form-control" name="jamSelesai" autocomplete="off"
							value="<?= set_value('jamSelesai') ?>"><span
							class="input-group-addon"><i class="fa fa-clock-o"></i></span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Jumlah Hari</label>
					<div class="col-sm-4">
						<input type="text" placeholder="Jumlah hari peminjaman" class="form-control" name="jmlHari" value="<?= set_value('jmlHari') ?>">
					</div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Nama Penyewa</label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="Nama Penyewa" class="form-control" name="penyewa" value="<?= set_value('penyewa') ?>">
						<?php echo form_error('penyewa', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>
					<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Telp</label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="Telp" class="form-control" name="kontak" value="<?= set_value('Telp') ?>">
						<?php echo form_error('kontak', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>
						<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Alamat</label>
					<div class="col-sm-8">
						<textarea class="form-control" placeholder="Alamat" name="alamat"><?= set_value('alamat') ?></textarea>
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
						<textarea class="form-control" placeholder="Keterangan" name="keterangan"><?= set_value('Keterangan') ?></textarea>
					</div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Mitra	</label>
					<div class="col-sm-8">
						<input type="hidden" name="mkId" id="mkId" value="<?= set_value('mkId') ?>">
						<input type="hidden" name="mkNama" id="mkNama" value="<?= set_value('mkNama') ?>">
						<input type="hidden" name="mkAlamat" id="mkAlamat" value="<?= set_value('mkAlamat') ?>">
						<input type="hidden" name="mkTelepon" id="mkTelepon" value="<?= set_value('mkTelepon') ?>">
						<input 	type="text" name="mitra" class="form-control" placeholder="Cari mitra" value="<?= set_value('mitra') ?>" />
						<?php echo form_error('mkId', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>
					<font class="col-sm-1" color="red">*</font>
					<div class="col-sm-2 small text-left text-muted"><i>Autocomplate</i></div>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Unit	</label>
					<div class="col-sm-8">
						<select class="form-control" name="unit" id="unit">
							<?php
							if(!empty($dataUnitKerja)){
								foreach($dataUnitKerja as $key_unit => $row_unit){
									if(!empty(set_value('unit'))){
										$select_unit = 'selected="selected"';
									}else{
										$select_unit = '';
									}
									echo "<option ".$select_unit." value='".$row_unit['id']."' >".$row_unit['kode'].' - '.$row_unit['nama']."</option>";
								}
							}
							?>
						</select>
						<?php echo form_error('unit', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
					</div>
					<font class="col-sm-1" color="red">*</font>
				</div>
			</div>
		</div>
		<!-- ========================================================================================== -->
		
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>&nbsp;</label>
					<input type="hidden" name="idx" id="idx" value="1">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="panel">
					<div class="panel-heading">
						<span class="panel-title">Daftar Aset Sewa</span>
						<div class="panel-heading-controls">
							<a id="listBarang" rel='async' ajaxify="<?php echo modal("Pilih Barang",'aset_manajemen_barang','sewa_aset','list_barang')?>" href="" class="btn btn-sm btn-labeled btn-success"><i class="fa fa-plus btn-label-icon left"></i>Pilih Barang</a>
							<input type="hidden" name="detail_">
							<?php echo form_error('detail_', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
						</div>
						
					</div>
					<div class="panel-body">
						<div class="table-primary" style="overflow: auto;">
							<table id="tbl-rincian" class="table table-bordered">
								<thead>
									<tr>
										<th>No</th>
										<th>Kode Sewa</th>
										<th>Label</th>
										<th>Nilai Satuan (Rp)</th>
										<th>Nilai Potongan (Rp)</th>
										<th>Nilai Sub Total (Rp)</th>
										<th>Aksi</th>
									</tr>
								</thead>
								<tbody></tbody>
								<tfoot>
									<tr>
										<td colspan="5">&nbsp;</td>
										<td nowrap class="text-right">
											<div id="jumlah_total_label"><b>Jumlah : <?php echo !empty($jml_eksis)?number_format($jml_eksis,2,',','.'):0?></b></div>
											<input type="hidden" name="jumlah_total" id="jumlah_total" value="<?php echo !empty($jml_eksis)?$jml_eksis:0?>">
										</td>
										<td>
											&nbsp;
										</td>
									</tr>
								</tfoot>
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

		<!-- ========================================================================================== -->
	</div>
</form>

<script type="text/javascript">
	$(document).ready(function () {
		$("#tbl-rincian").tooltip({
			selector: '[data-toggle="tooltip"]'
		});
		$("select[name='kib_id']").select2({
			allowClear: true,
			placeholder: "Jenis aset"
		});
		$("select[name='unit']").select2({
			allowClear: true,
			placeholder: "Unit Penyewa"
		});
		$("select[name='mitra']").select2({
			allowClear: true,
			placeholder: "Mitra"
		});

		let options2 = {
			format: "yyyy-mm-dd",
			autoclose: true,
			orientation: $('body').hasClass('right-to-left') ? "auto right" : 'auto bottom'
		}
		$('#periode-range').datepicker(options2);
		
		$("input[name=jamMulai],input[name=jamSelesai]").timepicker({
			defaultTime: "0:00",
			minuteStep: 1,
			showSeconds: false,
			showMeridian: false,
			showInputs: false,
			orientation: $('body').hasClass('right-to-left') ? {x: 'right', y: 'auto'} : {x: 'auto', y: 'auto'}
		});

		$("input[name='tanggal_mulai'],input[name='tanggal_selesai']").change(function() {
			var startb = $("input[name='tanggal_mulai']").val();
			var endb = $("input[name='tanggal_selesai']").val();
			var diffb = new Date(endb) - new Date(startb);
			var daysb = diffb/1000/60/60/24;
			
			$("input[name='jmlHari']").val((daysb==0)?1:daysb);
		});

		$("input[name='mitra']").autocomplete({
			source: function (request, response) {
				$.getJSON(
					"<?php echo site_url('aset_manajemen_barang/sewa_aset/get_mitra/'); ?>",
					{term: request.term},
					response
					);
			},
			minLength: 4,
			focus: function (event, ui) {
				$("input[name='mitra']").val(ui.item.mkNama);
				return false;
			},
			select: function (event, ui) {
				$("input[name='mkId']").val(ui.item.mkId);
				$("input[name='mkNama']").val(ui.item.mkNama);
				$("input[name='mkAlamat']").val(ui.item.mkAlamat);
				$("input[name='mkTelepon']").val(ui.item.mkTelepon);
				return false;
			},
			search: function () {
				$(this).addClass('loading');
			},
			open: function () {
				$(this).removeClass('loading');
			}
		}).data("ui-autocomplete")._renderItem = function (ul, item) {
			if (item.mkId === null) {
				$("input[name='mkId']").val('');
				$("input[name='mkNama']").val('');
				$("input[name='mkAlamat']").val('');
				$("input[name='mkTelepon']").val('');

				return $("<li class='list-group-item hasil'>Nama mitra tidak ditemukan</li>")
				.appendTo(ul);
			} else {
				return $("<li class='list-group-item hasil'>")
				.append("<a> <b>" + item.mkNama + " </b> <br>(" + item.mkTelepon + " - " + item.mkAlamat + ")</a></li>")
				.appendTo(ul);
			}
		};
		$("#tbl-rincian").on("click", ".btn-hapus", function () {
			var self = $(this);
			var idx = $(this).val();
			var nominal_row = formatDisplayToInput($('#aset_sub_total_'+idx).val());
			bootbox.confirm({
				title: 'Konfirmasi',
				message: "Yakin data akan dihapus ?"+nominal_row,
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
						remove_rincian(self,nominal_row);
					}
				},
				className: "bootbox-sm"
			});
		});
		$('.nominal').on({
			focus: function () {
				this.value = formatDisplayToInput(this.value);
			},
			keypress: function (event) {
				return validasiInput(this.value, event);
			},
			blur: function () {
				if (this.value != "") {
					this.value = formatInputToDisplay(this.value);
				}
			}
		});
	});

// ================================
function actionHargaSewa(idx) {
	hitungPotongan(idx);
	hitungTotalHarga();
}
// ================================

function hitungTotalHarga() {
	var row_aset = $("input[name='aset_sub_total[]']").map(function(){return $(this).val();}).get();
	var total_harga = 0;
	$.each(row_aset, function( index, value ) {
		total_harga = parseInt(total_harga)  + parseInt(formatDisplayToInput(value));
	});

	$('#jumlah_total').val(total_harga);
	$("#jumlah_total_label").html("<b>Jumlah : "+ formatInputToDisplay(total_harga) +"<b>");
}


function hitungPotongan(idx) {
	var harga = parseInt(formatDisplayToInput($('#harga_sewa_'+idx).val()));
	var potongan = parseInt(formatDisplayToInput($('#harga_potongan_'+idx).val()));
	var sub_total = harga + potongan;
	$('#aset_sub_total_'+idx).val(formatInputToDisplay(sub_total));
	$('#harga_sewa_'+idx).val(formatInputToDisplay(harga));
	$('#harga_potongan_'+idx).val(formatInputToDisplay(potongan));
}

function remove_rincian(self,nominal) {
	self.closest('tr').remove();
	if ($("#tbl-rincian tbody tr").length == 0) {
		$("#tbl-rincian tbody").append('<tr class="warning"><td colspan="13">&nbsp;</td></tr>');
	} else {
		urutkan_item();
	}
	hitung_total('delete',parseInt(nominal));
}
function urutkan_item() {
	var no = 1;
	$('#tbl-rincian > tbody  > tr').each(function () {
		$(this).find("td:first").text(no);
		no++;
		$('input[name="idx"]').val(no);
	});
}
function total_harga_sewa(idx){
	var value = $('#harga_sewa_'+idx).val();
	alert(value);
}

function hitung_total(operator,nominal){
	// belum selesai menjumlahkan.
	var jumlah_total = parseInt($('#jumlah_total').val());

	if(operator=='add'){
		total = parseInt(jumlah_total) + parseInt(nominal);		
	}else if(operator=='delete'){
		total = parseInt(jumlah_total) - parseInt(nominal);
	}

	$('#jumlah_total').val(total);
	$("#jumlah_total_label").html("<b>Jumlah : "+ formatInputToDisplay(total) +"<b>");
}

</script>
