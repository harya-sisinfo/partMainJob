<?php echo Modules::run('breadcrump'); ?>
<div class="row">
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
	<div class="col-sm-12">
		<div class="panel">
			<div class="panel-heading"><span class="panel-title">Filter</span></div>
			<div class="panel-body">
				<div id="form-filter" class="form-horizontal">
					<!-- Start No. SIP / Nama / NIP Pengguna -->
					<div class="form-group">
						<label class="col-sm-2 control-label" style="text-align:left;">No. SIP / Nama / NIP Pengguna</label>
						<div class="col-sm-4">
							<input type="input" name="noba" value="<?php echo (!empty($noba_)?$noba_:'')?>" class="form-control" placeholder="Cari berdasarkan No. SIP / Nama / NIP Pengguna" autocomplete="off">
						</div>
					</div>
					<!-- End No. SIP / Nama / NIP Pengguna -->
					<!-- Start Status -->
					<div class="form-group">
						<label class="col-sm-2 control-label" style="text-align:left;">Status</label>
						<div class="col-sm-4">
							<select name="stat" id="stat" class="form-control">
								<option value="Y" <?php echo (!empty($stat_)&&$stat_=="Y")?'selected="selected"':''?> >Aktif</option>
								<option value="N" <?php echo (!empty($stat_)&&$stat_=="N")?'selected="selected"':''?> >Expired</option>
							</select>
						</div>
					</div>
					<!-- End Status -->
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
		<span class="panel-title">Mutasi Antar Unit Kerja </span>
		<div class="panel-heading-controls">
			<button class="btn btn-sm btn-flat btn-labeled btn-success" id="btn-tambah">
				<span class="btn-label-icon left fa fa-plus"></span> Tambah
			</button>
		</div>
	</div>
	<div class="panel-body">
		<div class="table-light table-primary" id="table-container">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table table-striped table-bordered" id="jq-datatables-penggunaan-aset">
				<thead>
					<tr>
						<th class="text-center">No</th>
						<th class="text-center">No. SIP</th>
						<th class="text-center">Tgl SIP</th>
						<th class="text-center">Nama Pengguna</th>
						<th class="text-center">NIP</th>
						<th class="text-center">Telp</th>
						<th class="text-center">Jabatan</th>
						<th class="text-center">Unit Kerja</th>
						<th class="text-center">Jumlah Aset</th>
						<th class="text-center">Status</th>
						<th class="text-center">Aksi</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function () {
		$("select[name='stat']").select2({ placeholder: "Pilih Status"});
		
		var dtTable;

		$("#btn-tambah").click(function(){
			lastStage = '?noba='+$("input[name='noba']").val()+'&stat='+$("#stat option:selected").val();
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

		$("#jq-datatables-penggunaan-aset").tooltip({
			selector: '[data-toggle="tooltip"]'
		});

		dtTable = $('#jq-datatables-penggunaan-aset').DataTable({
			"processing": false,
			"serverSide": true,
			"scrollCollapse": true,
			"order": [[ 1, "DESC" ]],
			"ajax": {
				url: "<?php echo $linkDataTable ?>",
				type: "get",
				data: function(data){
					data.noba 		= $("input[name='noba']").val();				
					data.stat 		= $("input[name='stat']").val();				
					console.log(data);
				},
				error: function () {
					$(".jq-datatables-penggunaan-aset-error").html("");
					$("#jq-datatables-penggunaan-aset").append('<tbody class="jq-datatables-penggunaan-aset-error"><tr><th colspan="9">No data found in the server</th></tr></tbody>');
					$("#jq-datatables-penggunaan-aset_processing-penggunaan-aset").css("display", "hidden");
				}
			},
			"columnDefs": [
			{
				targets: [0],
				orderable: false
			},
			{
				targets: [1],
				orderable: true
			},
			{
				targets: [10],
				orderable: false,
				className: "text-center",
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
				$('td:eq(11)', row).css('min-width', '180px');
			}
		});
		$('#jq-datatables-penggunaan-aset_wrapper-penggunaan-aset .table-caption').text('List Data');
		$('#jq-datatables-penggunaan-aset_wrapper-penggunaan-aset .dataTables_filter input').attr('placeholder', 'Cari...');
		$("#jq-datatables-penggunaan-aset").on("click", ".btn-hapus", function (e) {
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