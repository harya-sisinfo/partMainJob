<?php echo Modules::run('breadcrump'); ?>
 <div class="row">
	<div class="col-sm-12">
		<div class="panel">
			<div class="panel-heading"><span class="panel-title">Filter</span></div>
			<div class="panel-body">
				<div id="form-filter" class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label" style="text-align:left;">No. BA</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="no_ba">	
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" style="text-align:left;">Unit / Gedung Asal</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="nama_asal">	
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" style="text-align:left;">Unit / Gedung Penerima</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="nama_penerima">	
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
		<span class="panel-title">Daftar Mutasi BHP </span>
		<div class="panel-heading-controls">
			<button class="btn btn-sm btn-flat btn-labeled btn-success" id="btn-tambah">
				<span class="btn-label-icon left fa fa-plus"></span> Tambah
			</button>
		</div>
	</div>
	<div class="panel-body">
		<div class="table-light table-primary" id="table-container">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table table-striped table-bordered" id="jq-datatables-mutasi-bhp">
				<thead>
					<tr>
						<th rowspan="2" width="35px" class="no-sort text-center">No</th>
						<th rowspan="2" class="text-center">Tanggal Mutasi</th>
						<th rowspan="2" class="text-center">No. BA</th>
						<th class="text-center"colspan="2">Asal</th>
						<th class="text-center"colspan="2">Penerima</th>
						<th rowspan="2" class="no-sort text-center">Jumlah</th>
						<th rowspan="2" class="no-sort text-center">PIC</th>
						<th rowspan="2" width="180px" class="no-sort text-center">Aksi</th>
					</tr>
					<tr>
						<th>Gudang</th>
						<th>Unit</th>
						<th>Gudang</th>
						<th>Unit</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
</div>
<span class="sr-only">Toggle Dropdown</span>
<script>
	$(document).ready(function () {
		
		$("#jq-datatables").tooltip({
			selector: '[data-toggle="tooltip"]'
		});
		$("#btn-filter").click(function(){
			dtTable.draw();
		});
		$("#btn-reset").click(function(){
			$("#form-filter select").each(function(){
				$(this).val("").trigger("change");
			});
			$("#form-filter input[type='text']").each(function(){
				$(this).val("");
			});
			dtTable.draw();
		});

		var dtTable;
		dtTable = $('#jq-datatables-mutasi-bhp').DataTable({
			"processing": false,
			"serverSide": true,
			"scrollCollapse": true,
			"order": [[ 1, "DESC" ]],
			"ajax": {
				url: "<?php echo $linkDataTable ?>",
				type: "get",
				data: function(data){
					data.no_ba 			= $("input[name='no_ba']").val();
					data.nama_asal 		= $("input[name='nama_asal']").val();
					data.nama_penerima 	= $("input[name='nama_penerima']").val();
					console.log(data);
				},
					error: function () {
						$(".jq-datatables-mutasi-bhp-error").html("");
						$("#jq-datatables-mutasi-bhp").append('<tbody class="jq-datatables-error"><tr><th colspan="7">No data found in the server</th></tr></tbody>');
						$("#jq-datatables-mutasi-bhp_processing").css("display", "hidden");
					}
				},
				"columnDefs": [
				{
					targets: [0],
					orderable: false,
					className: "text-center"
				},
				{
					targets: [9],
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
				"createdRow": function (row, data, dataIndex) {
					$('td:eq(9)', row).css('min-width', '80px');
				}
			});

			$('#jq-datatables-mutasi-bhp_wrapper .table-caption').text('List Data');
			$('#jq-datatables-mutasi-bhp_wrapper .dataTables_filter input').attr('placeholder', 'Cari...');
	});
</script>
