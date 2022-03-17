<?php echo Modules::run('breadcrump'); ?>
<div class="row"><input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
	<div class="col-sm-12">
		<div class="panel">
			<div class="panel-heading"><span class="panel-title">Filter</span></div>
			<div class="panel-body">
				<div id="form-filter" class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label" style="text-align:left;">Kode</label>
						<div class="col-sm-4">
							<input type="input" name="kode" value="<?php echo (!empty($kode)?$kode:'')?>" class="form-control" placeholder="Cari berdasarkan kode" autocomplete="off">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" style="text-align:left;">Label</label>
						<div class="col-sm-4">
							<input type="input" name="label" value="<?php echo (!empty($label)?$label:'')?>" class="form-control" placeholder="Cari berdasarkan label" autocomplete="off">
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
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table table-striped table-bordered" id="jq-datatables-biaya-aset">
				<thead>
					<tr>
						<th width="35px" class="no-sort text-center">No</th>
						<td>Kode</td>
						<td>Kode Aset</td>
						<td>Label</td>
						<td>Biaya Sewa</td>
						<td>Biaya Kerusakan</td>
						<td>Biaya Penggantian</td>
						<th width="180px" class="no-sort text-center">Aksi</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		$("#btn-tambah").click(function(){
			lastStage = '?kode=' + $("input[name='kode']").val() + '&label=' + $("input[name='label']").val();
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
		$("#jq-datatables-biaya-aset").tooltip({
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
		dtTable = $('#jq-datatables-biaya-aset').DataTable({
			"processing": false,
			"serverSide": true,
			"scrollCollapse": true,
			"order": [[ 1, "DESC" ]],
			"ajax": {
				url: "<?php echo $linkDataTable ?>",
				type: "get",
				data: function(data){
					data.kode 		= $("input[name='kode']").val();				
					data.label 		= $("input[name='label']").val();
					console.log(data);
				},
				error: function () {
					$(".jq-datatables-biaya-aset-error").html("");
					$("#jq-datatables-biaya-aset").append('<tbody class="jq-datatables-biaya-aset-error"><tr><th colspan="9">No data found in the server</th></tr></tbody>');
					$("#jq-datatables-biaya-aset_processing-biaya-aset").css("display", "hidden");
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
				$('td:eq(8)', row).css('min-width', '80px');
			}
		});

		$('#jq-datatables-biaya-aset_wrapper .table-caption').text('List Data');
		$('#jq-datatables-biaya-aset_wrapper .dataTables_filter input').attr('placeholder', 'Cari...');
		$("#jq-datatables-biaya-aset").on("click", ".btn-hapus", function (e) {
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

</script>