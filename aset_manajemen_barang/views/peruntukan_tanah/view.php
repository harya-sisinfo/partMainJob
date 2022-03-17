<?php echo Modules::run('breadcrump'); ?>
<div class="panel">
	<div class="panel-heading">
		<span class="panel-title">Daftar Peruntukan Tanah </span>
		<div class="panel-heading-controls">
			<a href="<?php echo $linkAdd ?>"  id="btn-tambah" class="btn btn-sm btn-flat btn-labeled btn-success">
				<span class="btn-label-icon left fa fa-plus"></span> Tambah
			</a>
		</div>
	</div>
	<div class="panel-body">
		<div class="table-light table-primary" id="table-container">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table table-striped table-bordered" id="jq-datatables">
				<thead>
					<tr>
						<th width="35px" class="no-sort">Nomor</th>
						<th width="100px">Kode</th>
						<th >Nama</th>
						<th >Peruntukan</th>
						<th >Status Penggunaan</th>
						<th >Luas&nbsp;(m<sup>2</sup>)</th>
						<th width="80px" class="no-sort dt-center">Aksi</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
</div>
<span class="sr-only">Toggle Dropdown</span>
<script>
	$(document).ready(function(){
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
					error: function () {
						$(".jq-datatables-error").html("");
						$("#jq-datatables").append('<tbody class="jq-datatables-error"><tr><th colspan="7">Data tidak ditemukan</th></tr></tbody>');
						$("#jq-datatables_processing").css("display", "hidden");
					}
				},
				"columnDefs": [
				{
					targets: [0],
					orderable: false
				},
				{
					targets: [5],
					className: 'text-right'
				},
				{
					targets: [6],
					className: 'text-center',
					orderable: false
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
					$('td:eq(6)', row).css('min-width', '180px');
				}
			});
			$('#jq-datatables_wrapper .table-caption').text('Peruntukan Tanah');
			$('#jq-datatables_wrapper .dataTables_filter input').attr('placeholder', 'Cari...');
			$("#jq-datatables").on("click", ".btn-hapus", function (e) {
            e.preventDefault();
            var self = $(this);
            bootbox.confirm({
                title: 'Konfirmasi',
                message: "Yakin data akan dihapus ?",
                className: "bootbox-lg",
                buttons: {
                    'cancel': {
                        label: '<i class="fa fa-times"></i> Tidak',
                        className: 'btn-danger'
                    },
                    'confirm': {
                        label: '<i class="fa fa-check"></i> Ya'
                    }
                },
                callback: function (result) {
                    if (result) {
                        var url = self.attr('href');
                        CIS.Ajax.request(url);
                        dtTable.ajax.reload();
                    }
                }
            });
            return false;
        });
		});
	});
</script>