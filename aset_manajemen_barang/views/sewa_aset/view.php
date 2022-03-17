<?php echo Modules::run('breadcrump'); ?>
<div class="row">
	<div class="col-sm-12">
		<div class="panel">
			<div class="panel-heading"><span class="panel-title">Filter</span></div>
			<div class="panel-body">
				<div id="form-filter" class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label" style="text-align:left;">Nomor Sewa</label>
						<div class="col-sm-4">
							<input type="input" name="kode_sewa" value="<?php echo (!empty($kode_sewa)?$kode_sewa:'')?>" class="form-control" placeholder="Cari berdasarkan Kode Sewa" autocomplete="off">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" style="text-align:left;">Status Sewa</label>
						<div class="col-sm-4">
							<select class="form-control" name="status_isi" id="status_isi">
								<option value=""></option>
								<option value="1">Kadaluarsa</option>
								<option value="0">Disewa</option>
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
		<span class="panel-title">Daftar Peminjaman Aset </span>
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
						<th rowspan="2" width="35px" class="no-sort text-center">No</th>
						<th rowspan="2" class="text-center">Nomor Sewa</th>
						<th rowspan="2" class="text-center">Unit Penyewa</th>
						<th colspan="2" class="no-sort text-center" >Mulai Sewa</th>
						<th colspan="2" class="no-sort text-center">Selesai Sewa</th>
						<th rowspan="2" width="180px" class="no-sort text-center">Aksi</th>
					</tr>
					<tr>
						<th class="text-center">Tanggal</th>
						<th class="text-center">Waktu</th>
						<th class="text-center">Tanggal</th>
						<th class="text-center">Waktu</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		$("select[name='status_isi']").select2({ placeholder: "Cari berdasarkan Status Sewa"});
		$("#btn-tambah").click(function(){
			lastStage = '?kode_sewa=' + $("input[name='kode_sewa']").val() + '&status_isi='+ $("#status_isi option:selected").val();
			$.ajax({
				url:"<?php echo $linkAdd?>",
				type: "GET",
				success: function (output) {
					top.location = '<?php echo $linkAdd?>'+lastStage;
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(xhr.status + " " + thrownError);
				}
			});
		});
		var dtTable;
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
			dtTable = $('#jq-datatables').DataTable({
				"processing": false,
				"serverSide": true,
	            "scrollCollapse": true,
				"order": [[ 1, "DESC" ]],
				"ajax": {
					url: "<?php echo $linkDataTable ?>",
					type: "get",
					data: function(data){
						data.kode_sewa 		= $("input[name='kode_sewa']").val();				
						data.status_isi 	= $("#status_isi option:selected").val();
						console.log(data);
					},
					error: function () {
						$(".jq-datatables-error").html("");
						$("#jq-datatables").append('<tbody class="jq-datatables-error"><tr><th colspan="7">No data found in the server</th></tr></tbody>');
						$("#jq-datatables_processing").css("display", "hidden");
					}
				},
				"columnDefs": [
				{
					targets: [0],
					orderable: false,
					className: "text-center"
				},
				{
					targets: [7],
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

			$('#jq-datatables_wrapper .table-caption').text('List Data');
			$('#jq-datatables_wrapper .dataTables_filter input').attr('placeholder', 'Cari...');
		});
	});
</script>