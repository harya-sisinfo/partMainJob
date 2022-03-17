<?php echo Modules::run('breadcrump'); ?>
<div class="row">
	<div class="col-sm-12">
		<div class="panel">
			<div class="panel-heading"><span class="panel-title">Filter</span></div>
			<div class="panel-body">
				<div id="form-filter" class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label" style="text-align:left;">Nama Gudang</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" placeholder="Cari berdasarkan nama gudang" name="gudang">
						</div>

					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" style="text-align:left;">nama / Kode Barang</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" placeholder="Cari berdasarkan nama / kode barang" name="kode_barang">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" style="text-align:left;">Status Stok Barang</label>
						<div class="col-sm-4">
							<select name="status_stok" id="status_stok">
								<option value=""></option>
							</select>
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
		<span class="panel-title">Daftar Inventarisasi BHP </span>
		<div class="panel-heading-controls">
			<button class="btn btn-sm btn-flat btn-labeled btn-success" id="btn-tambah">
				<span class="btn-label-icon left fa fa-plus"></span> Tambah
			</button>
		</div>
	</div>
	<div class="panel-body">
		<div class="table-light table-primary" id="table-container">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table table-striped table-bordered" id="jq-datatables">
				<thead>
					<tr>
						<th width="35px" class="no-sort text-center">No</th>
						<th>Kode Barang</th>
						<th>Nama Barang</th>
						<th>Merek</th>
						<th>Jumlah Stok</th>
						<th>Stok Minimal</th>
						<th>Lokasi Gudang</th>
						<th>Unit Kerja</th>
						<th width="180px" class="no-sort text-center">Aksi</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$("select[name='status_stok']").select2({
			allowClear: true,
			placeholder: "Cari berdasarkan unit kerja"
		});
		var dtTable;
		$("#jq-datatables").tooltip({
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
			lastStage = '?gudang=' + $("input[name='gudang']").val() + '&kode_barang=' + $("input[name='kode_barang']").val() + '&status_stok=' + $("#status_stok option:selected").val();
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
		dtTable = $('#jq-datatables').DataTable({
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
					data.gudang = $("input[name='gudang']").val();
					data.kode_barang = $("input[name='kode_barang']").val();
					data.status_stok = $("#status_stok option:selected").val();
					console.log(data);
				},
				error: function() {
					$(".jq-datatables-error").html("");
					$("#jq-datatables").append('<tbody class="jq-datatables-error"><tr><th colspan="7">No data found in the server</th></tr></tbody>');
					$("#jq-datatables_processing").css("display", "hidden");
				}
			},
			"columnDefs": [{
					targets: [0],
					orderable: false,
					className: "text-center"
				},
				{
					targets: [8],
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
				$('td:eq(7)', row).css('min-width', '80px');
			}
		});

		$('#jq-datatables_wrapper .table-caption').text('List Data');
		$('#jq-datatables_wrapper .dataTables_filter input').attr('placeholder', 'Cari...');

	});
</script>