<?php echo Modules::run('breadcrump'); ?>
<input id="csrf_add_kode_penerimaan" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" >
<div class="row">
	<div class="col-sm-12">
		<div class="panel">
			<div class="panel-heading"><span class="panel-title">Filter</span></div>
			<div class="panel-body">
				<div id="form-filter" class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label" style="text-align:left;">Jenis KIB</label>
						<div class="col-sm-4">
							<select class="form-control" name="jenis_kib" id="jenis_kib">
								<?php
								if(!empty($dataJenisKIB)){
									foreach($dataJenisKIB as $key_jenis_kib => $row_jenis_kib){
										if($row_jenis_kib['id']==$jenis_kib_){
											$selected_jenis_kib = "selected='selected'";
										}else{
											$selected_jenis_kib = "";
										}
										echo "<option value='".$row_jenis_kib['id']."' ".$selected_jenis_kib.">".$row_jenis_kib['nama']."</option>";
									}
								}
								?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" style="text-align:left;">Kode Aset</label>
						<div class="col-sm-4">
							<input type="input" name="kode_aset" value="<?php echo (!empty($kode_aset)?$kode_aset:'')?>" class="form-control" placeholder="Cari berdasarkan nomor kode aset" autocomplete="off">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" style="text-align:left;">Unit PJ Barang</label>
						<div class="col-sm-4">
							<select class="form-control" name="unit_kerja" id="unit_kerja">
								<?php
								if(!empty($dataUnitKerja)){
									foreach($dataUnitKerja as $key_unit_kerja => $row_unit_kerja){
										if($row_unit_kerja['id']==$unit_kerja_){
											$selected_unit_kerja = "selected='selected'";
										}else{
											$selected_unit_kerja = "";
										}
										echo "<option value='".$row_unit_kerja['id']."' ".$selected_unit_kerja.">".$row_unit_kerja['kode'].' - '.$row_unit_kerja['nama']."</option>";
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
		<span class="panel-title">Daftar Penyusutan Aset </span>
		<div class="panel-heading-controls">
			<button class="btn btn-sm btn-flat btn-labeled btn-success" id="btn-tambah">
				<span class="btn-label-icon left fa fa-plus"></span> Tambah
			</button>
			

			<button class="btn btn-sm btn-flat btn-labeled btn-primary" id="btn-print-pdf"><span class="btn-label-icon left fa fa-print"></span> Cetak PDF</button>

			<button class="btn btn-sm btn-flat btn-labeled btn-primary" id="btn-print-excel"><span class="btn-label-icon left fa fa-table"></span> Cetak Excel</button>
		</div>
	</div>
	<div class="panel-body">		
		<div class="table-light table-primary" id="table-container">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table table-striped table-bordered" id="jq-datatables">
				<thead>
					<tr>
						<th width="35px" class="no-sort dt-center">No</th>
						<th>Kode Aset</th>
						<th>Nama Aset</th>
						<th>Unit PJ Barang</th>
						<th>Nilai Perolehan (Rp)</th>
						<th>Nilai Penyusutan (Rp)</th>
						<th>Akumulasi Penyusutan (Rp)</th>
						<th>Nilai Buku</th>
						<th width="30px" class="no-sort dt-center">Aksi</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
</div>
<span class="sr-only">Toggle Dropdown</span>
<script type="text/javascript">
	$(document).ready(function () {
		$("select[name='jenis_kib']").select2({ placeholder: "Pilih jenis KIB"});
		$("select[name='unit_kerja']").select2({ placeholder: "Pilih unit kerja"});
		$("#btn-tambah").click(function(){
			var  jenis_kib_ = !($("#jenis_kib option:selected").val())?$("#jenis_kib option:selected").val():'';
			var  kode_aset_ = !($("input[name='kode_aset']").val())?$("input[name='kode_aset']").val():'';
			var  unit_kerja_ = !($("#unit_kerja option:selected").val())?$("#unit_kerja option:selected").val():'';
			lastStage = '?jenis_kib=' + jenis_kib_ + '&kode_aset=' + kode_aset_ + '&unit_kerja=' + unit_kerja_;
			$.ajax({
				url:"<?php echo $linkAdd?>"+lastStage,
				type: "GET",
				success: function (output) {
					top.location = '<?php echo $linkAdd?>'+lastStage;
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(xhr.status + " " + thrownError);
				}
			});
		});

		$("#btn-print-pdf").click(function(){
			var  jenis_kib_ = ($("#jenis_kib option:selected").val())?$("#jenis_kib option:selected").val():'';
			var  kode_aset_ = ($("input[name='kode_aset']").val())?$("input[name='kode_aset']").val():'';
			var  search_ = ($("input[type='search']").val())?$("input[type='search']").val():'';
			var  unit_kerja_ = ($("#unit_kerja option:selected").val())?$("#unit_kerja option:selected").val():'';
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
			});
		});

		$("#btn-print-excel").click(function(){
			var  jenis_kib_ = ($("#jenis_kib option:selected").val())?$("#jenis_kib option:selected").val():'';
			var  kode_aset_ = ($("input[name='kode_aset']").val())?$("input[name='kode_aset']").val():'';
			var  search_ = ($("input[type='search']").val())?$("input[type='search']").val():'';
			var  unit_kerja_ = ($("#unit_kerja option:selected").val())?$("#unit_kerja option:selected").val():'';
			lastStage = '?jenis_kib=' + jenis_kib_ + '&kode_aset=' + kode_aset_ + '&unit_kerja=' + unit_kerja_ + '&search=' + search_;
			$.ajax({
				url:"<?php echo $linkPrintPdf?>"+lastStage,
				type: "GET",
				success: function (output) {
					window.location.href='<?php echo $linkPrintExcel?>'+lastStage; 
					return false;
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
						data.jenis_kib 		= $("#jenis_kib option:selected").val();				
						data.kode_aset 		= $("input[name='kode_aset']").val();				
						data.unit_kerja 	= $("#unit_kerja option:selected").val();				
						console.log(data);
					},
					error: function () {
						$(".jq-datatables-error").html("");
						$("#jq-datatables").append('<tbody class="jq-datatables-error"><tr><th colspan="9">No data found in the server</th></tr></tbody>');
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
					targets: [4],
					className: "text-right"
				},
				{
					targets: [5],
					className: "text-right"
				},
				{
					targets: [6],
					className: "text-right"
				},
				{
					targets: [7],
					className: "text-right"
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
					$('td:eq(8)', row).css('min-width', '80px');
				}
			});

			$('#jq-datatables_wrapper .table-caption').text('List Data');
			$('#jq-datatables_wrapper .dataTables_filter input').attr('placeholder', 'Cari...');
		});
	});
</script>

