<?php echo Modules::run('breadcrump'); ?>
<div class="panel">
	<div class="panel-heading">
		<span class="panel-title">Pengembalian Aset </span>
	</div>
	<div class="panel-body">
		<div class="table-light table-primary" id="table-container">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table table-striped table-bordered" id="jq-datatables">
				<thead>
					<tr>
						<th rowspan="2" width="35px" class="no-sort text-center">No</th>
						<th rowspan="2" class="text-center">Nomor Sewa</th>
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

		$("#btn-print-pdf").click(function(){
			
			
			alert("aaaa");exit();
			/*var  id_data = ($(this).val())?$(this).val():'';
			lastStage = '?jenis_kib=' + jenis_kib_ + '&kode_aset=' + kode_aset_ + '&unit_kerja=' + unit_kerja_ + '&search=' + search_;
			$.ajax({
				url:"<?php echo $linkPrintPdf?>"+lastStage,
				type: "GET",
				success: function (output) {
					MyWindow=window.open('<?php echo $linkPrintPdf?>'+lastStage,'MyWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=800,height=600'); return false;
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(xhr.status + " " + thrownError);
				}
			});*/
		});

		var dtTable;
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
				targets: [1],
				orderable: true,
				className: "text-center"
			},
			{
				targets: [2],
				orderable: true,
				className: "text-center"
			},
			{
				targets: [3],
				orderable: true,
				className: "text-center"
			},
			{
				targets: [4],
				orderable: true,
				className: "text-center"
			},
			{
				targets: [5],
				orderable: true,
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
			"createdRow": function (row, data, dataIndex) {
				$('td:eq(8)', row).css('min-width', '80px');
			}
		});

		$('#jq-datatables_wrapper .table-caption').text('List Data');
		$('#jq-datatables_wrapper .dataTables_filter input').attr('placeholder', 'Cari...');
	});
</script>