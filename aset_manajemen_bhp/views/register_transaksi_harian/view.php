<?php echo Modules::run('breadcrump'); ?>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

<div class="row">
	<div class="col-sm-12">
		<div class="panel">
			<div class="panel-heading"><span class="panel-title">Filter</span></div>
			<div class="panel-body">
				<div id="form-filter" class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label" style="text-align:left;">Nomor Transaksi</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="notransaksi">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" style="text-align:left;">Nama Penerima</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="nama">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" style="text-align:left;">Periode</label>
						<div class="col-sm-4">
							<div class="input-daterange input-group" id="periode-range">
								<input type="text" class="form-control" name="tanggal_mulai" placeholder="Tanggal Awal" value="<?php echo !empty(set_value('tanggal_mulai')) ? set_value('tanggal_mulai') : '' ?>" autocomplete="off">
								<span class="input-group-addon">s.d</span>
								<input type="text" class="form-control" name="tanggal_selesai" placeholder="Tanggal Akhir" value="<?php echo !empty(set_value('tanggal_selesai')) ? set_value('tanggal_selesai') : ''  ?>" autocomplete="off">
							</div>
						</div>
					</div>
					<div class="panel-footer text-right">
						<button id="btn-reset" class="btn btn-default"><span class="btn-label-icon left fa fa-refresh"></span>Reset</button>
						<button id="btn-filter" class="btn btn-primary"><span class="btn-label-icon left fa fa-search"></span>Tampilkan</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="panel">
	<div class="panel-heading">
		<span class="panel-title">Daftar Register Transaksi Harian </span>
		<div class="panel-heading-controls">
			<button class="btn btn-sm btn-flat btn-labeled btn-success" id="btn-tambah">
				<span class="btn-label-icon left fa fa-plus"></span> Tambah
			</button>
		</div>
	</div>
	<div class="panel-body">
		<div class="table-light table-primary" id="table-container">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table table-striped table-bordered" id="jq-datatables-register">
				<thead>
					<tr>
						<th width="35px" class="no-sort text-center">No</th>
						<th class="text-center">Tanggal</th>
						<th class="text-center">Nomor Transaksi</th>
						<th class="text-center">Penerima</th>
						<th class="text-center">Unit</th>
						<th class="text-center">Keterangan</th>
						<th width="180px" class="no-sort text-center">Aksi</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
</div>
<span class="sr-only">Toggle Dropdown</span>
<script>
	$(document).ready(function() {
		let options2 = {
			format: "yyyy-mm-dd",
			autoclose: true,
			orientation: $('body').hasClass('right-to-left') ? "auto right" : 'auto bottom'
		}
		$('#periode-range').datepicker(options2);

		var dtTable;
		$("#jq-datatables-register").tooltip({
			selector: '[data-toggle="tooltip"]'
		});
		$("#btn-filter").click(function() {
			dtTable.draw();
		});
		$("#btn-reset").click(function() {
			$("#form-filter select").each(function() {
				$(this).val("").trigger("change");
			});
			$("#form-filter input[type='text']").each(function() {
				$(this).val("");
			});
			dtTable.draw();
		});
		$("#btn-tambah").click(function() {
			lastStage = '?notransaksi=' + $("input[name='notransaksi']").val() +
				'&nama=' + $("input[name='nama']").val() +
				'&tanggal_mulai=' + $("input[name='tanggal_mulai']").val() +
				'&tanggal_selesai=' + $("input[name='tanggal_selesai']").val();
			$.ajax({
				url: "<?php echo $url_add ?>",
				type: "GET",
				success: function(output) {
					top.location = '<?php echo $url_add ?>' + lastStage;
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(xhr.status + " " + thrownError);
				}
			});
		});
		dtTable = $('#jq-datatables-register').DataTable({
			"processing": false,
			"serverSide": true,
			"scrollCollapse": true,
			"order": [
				[1, "DESC"]
			],
			"ajax": {
				url: "<?php echo $linkDataTable ?>",
				type: "get",
				data: function(data) {
					data.notransaksi = $("input[name='notransaksi']").val();
					data.nama = $("input[name='nama']").val();
					data.tanggal_mulai = $("input[name='tanggal_mulai']").val();
					data.tanggal_selesai = $("input[name='tanggal_selesai']").val();
					
				},
				error: function() {
					$(".jq-datatables-register-error").html("");
					$("#jq-datatables-register").append('<tbody class="jq-datatables-register-error"><tr><th colspan="6">No data found in the server</th></tr></tbody>');
					$("#jq-datatables-register_processing").css("display", "hidden");
				}
			},
			"columnDefs": [{
					targets: [0],
					orderable: false,
					className: "text-center"
				},
				{
					targets: [6],
					orderable: false,
					className: "text-center"
				}
			],
			"language": {
				"lengthMenu": "Per halaman _MENU_",
				"zeroRecords": "Data tidak ditemukan",
				"info": "Menampilkan _START_ s.d _END_ dari total _TOTAL_",
				"infoEmpty": "Menampilkan 0 s.d _END_ dari total _TOTAL_",
				"paginate": {
					"first": "Pertama",
					"last": "Terakhir",
					"next": "&gt;",
					"previous": "&lt;"
				}
			},
			"createdRow": function(row, data, dataIndex) {
				$('td:eq(6)', row).css('min-width', '80px');
			}
		});

		$('#jq-datatables-register_wrapper .table-caption').text('List Data');
		$('#jq-datatables-register_wrapper .dataTables_filter input').attr('placeholder', 'Cari...');

	});
</script>