<?php echo Modules::run('breadcrump'); ?>
<div class="row">
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
	<div class="col-sm-12">
		<div class="panel">
			<div class="panel-heading"><span class="panel-title">Filter</span></div>
			<div class="panel-body">
				<div id="form-filter" class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label" style="text-align:left;">Tanggal BA Mutasi</label>
						<div class="col-sm-4">
							<div class="input-daterange input-group" id="periode-range">
								<input type="text" class="form-control" name="tglAwal" placeholder="Tanggal Awal" value="<?php echo (!empty($tglAwal_))?date('d-m-Y', strtotime($tglAwal_)):date('d-m-Y', strtotime(date('Y-').'01-01')) ?>" autocomplete="off">
								<span class="input-group-addon">s.d</span>
								<input type="text" class="form-control" name="tglAkhir" placeholder="Tanggal Akhir" value="<?php echo (!empty($tglAkhir_))?date('d-m-Y', strtotime($tglAkhir_)):date('d-m-Y') ?>" autocomplete="off">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" style="text-align:left;">BA Mutasi</label>
						<div class="col-sm-4">
							<input type="input" name="ba_mutasi" value="<?php echo (!empty($ba_mutasi_)?$ba_mutasi_:'')?>" class="form-control" placeholder="Cari berdasarkan nomor BA mutasi" autocomplete="off">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" style="text-align:left;">Unit Tujuan</label>
						<div class="col-sm-4">
							<select class="form-control" name="unit_kerja" id="unit_kerja">
								<?php
								if(!empty($dataUnitKerja)){
									foreach($dataUnitKerja as $key_unit_kerja => $row_unit_kerja){
										if(!empty($unit_kerja_)){
											$select_unit_kerja = ($row_unit_kerja['id']==$unit_kerja_)?'selected="selected"':'';
										}else{
											$select_unit_kerja = '';
										}
										echo "<option ".$select_unit_kerja." value='".$row_unit_kerja['id']."' >".$row_unit_kerja['kode'].' - '.$row_unit_kerja['nama']."</option>";
									}
								}
								?>
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
		<span class="panel-title">Mutasi Antar Unit Kerja </span>
		<div class="panel-heading-controls">
			<button class="btn btn-sm btn-flat btn-labeled btn-success" id="btn-tambah">
				<span class="btn-label-icon left fa fa-plus"></span> Tambah
			</button>
		</div>
	</div>

	<div class="panel-body">
		<div class="table-light table-primary" id="table-container">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table table-striped table-bordered" id="jq-datatables-aset-mutasi-antar-unit">
				<thead>
					<tr>
						<th class="text-center">No</th>
						<th class="text-center">BA Mutasi</th>
						<th class="text-center">Tgl BA Mutasi</th>
						<th class="text-center">Unit Asal</th>
						<th class="text-center">Unit Tujuan</th>
						<th class="text-center">Jumlah Barang</th>
						<th class="text-center">PIC</th>
						<th class="text-center">Status</th>
						<th class="text-center">Aksi</th>
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
		$("select[name='unit_kerja']").select2({ placeholder: "Pilih unit kerja"});
		let options2 = {
			format: "dd-mm-yyyy",
			autoclose: true,
			orientation: $('body').hasClass('right-to-left') ? "auto right" : 'auto bottom'
		}
		$('#periode-range').datepicker(options2);

		var dtTable;

		$("#btn-tambah").click(function(){
			lastStage = '?tglAwal='+$("input[name='tglAwal']").val()+'&tglAkhir='+$("input[name='tglAkhir']").val()+'&ba_mutasi='+$("input[name='ba_mutasi']").val()+'&unit_kerja='+$("#unit_kerja option:selected").val();
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
		$("#jq-datatables-aset-mutasi-antar-unit").tooltip({
			selector: '[data-toggle="tooltip"]'
		});
		dtTable = $('#jq-datatables-aset-mutasi-antar-unit').DataTable({
			"processing": false,
			"serverSide": true,
			"scrollCollapse": true,
			"order": [[ 1, "DESC" ]],
			"ajax": {
				url: "<?php echo $linkDataTable ?>",
				type: "get",
				data: function(data){
					data.tglAwal 		= $("input[name='tglAwal']").val();				
					data.tglAkhir 		= $("input[name='tglAkhir']").val();				
					data.ba_mutasi 		= $("input[name='ba_mutasi']").val();				
					data.unit_kerja 	= $("#unit_kerja option:selected").val();				
					console.log(data);
				},
				error: function () {
					$(".jq-datatables-aset-mutasi-antar-unit-error").html("");
					$("#jq-datatables-aset-mutasi-antar-unit").append('<tbody class="jq-datatables-aset-mutasi-antar-unit-error"><tr><th colspan="9">No data found in the server</th></tr></tbody>');
					$("#jq-datatables-aset-mutasi_processing-antar-unit").css("display", "hidden");
				}
			},
			"columnDefs": [
			{
				targets: [0],
				orderable: false
			},
			{
				targets: [1],
				orderable: true,
				className: 'text-nowrap'
			},
			{
				targets: [2],
				orderable: true,
				className: 'text-center'
			},
			{
				targets: [5],
				orderable: true,
				className: 'text-center'
			},
			{
				targets: [7],
				orderable: false,
				className: 'text-left'
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
			"createdRow": function (row, data, dataIndex) {
				$('td:eq(8)', row).css('min-width', '180px');
			}
		});

		$('#jq-datatables-aset-mutasi_wrapper-antar-unit .table-caption').text('List Data');
		$('#jq-datatables-aset-mutasi_wrapper-antar-unit .dataTables_filter input').attr('placeholder', 'Cari...');
		$("#jq-datatables-aset-mutasi-antar-unit").on("click", ".btn-hapus", function (e) {
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
