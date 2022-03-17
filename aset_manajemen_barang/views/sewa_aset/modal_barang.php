<div id='modal-data-basic'>
	<form rel="ajax" action="<?php echo $linkForm ?>" class="form-horizontal xhr dest_modal-data-basic" method="post">
		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>"/>
		<div class="panel">
			<div class="panel-body">
				<div class="form-group">
					<div class="col-md-1"></div>
					<label class="col-md-4">
						Cari kode/nama
					</label>
					<div class="col-md-4">
						<input class="form-control" type="text" value="<?php echo $search; ?>" name="search" id="search">
					</div>
					<div class="col-md-3">
						<?php echo tampilkan_button(); ?>
					</div>
				</div>
				<div class="table-light table-primary" style="overflow: auto;">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th class="text-center">No</th>
								<th class="text-center">Kode Sewa</th>
								<th class="text-center">Nama Barang</th>
								<th class="text-center">Harga Sewa</th>
								<th class="text-center">Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							if(!empty($content))
							{ 
								$i=$offset + 1;
								foreach ($content as $key => $row) 
								{
									?>
									<tr>					
										<td><?php echo $i?></td>
										<td><?php echo $row['aset_kode']?></td>
										<td><?php echo $row['aset_label']?></td>
										<td><?php echo $row['aset_harga_sewa']?></td>
										<td>
											<a href="javascript:;" onclick="input_rincian(
										'<?php echo $row['aset_id'] ?>', 
										'<?php echo str_replace("'","\'",$row['aset_kode']) ?>',
										'<?php echo str_replace("'","\'", $row['aset_label']) ?>',
										'<?php echo $row['aset_harga_sewa'] ?>',
										'<?php echo $row['status_id'] ?>')" class="btn btn-xs btn-primary" id="pilih_<?php echo $row['aset_id'] ?>">Pilih</a></td>
									</tr>
									<?php 
									$i++;
								}
							}
							?>
						</tbody>
					</table>
					<?php if(!empty($content)){ ?>
						<div class="row">
							<div class="pull-right">
								<?php echo $halaman; ?>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
	
	var listBarangId = $("input[name='aset_id[]']").map(function(){return $(this).val();}).get();
	for (i = 0; i < listBarangId.length; i++) {
		$('#pilih_'+listBarangId[i]).hide();
		console.log('#pilih_'+listBarangId[i]);
	}

	function add_rincian(aset_id,aset_kode,aset_label,aset_harga_sewa,status_id) {
		if ($("#tbl-rincian tbody tr:first").find(":input").length == 0) {
			$("#tbl-rincian tbody").html("");
		}
		var values = $("input[name='aset_id[]']").map(function(){return $(this).val();}).get();
		
		//var arrayByBarangId = values.split(",");
		if(jQuery.inArray(aset_id, values) != -1) {
		    alert("Kode barang "+aset_kode+" sudah ada.");
		} else {

			$("#tbl-rincian tbody").append(
				'<tr>' +
				'<td class="text-center">'+
				'<input type="text">'+
				'</td>' +
				'<td>' + aset_kode + '</td>' +
				'<td>' + aset_label + '</td>' +
				'<td><input class="form-control nominal" type="text" name="aset_harga_sewa[]" id="harga_sewa_' + $("#idx").val() + '"    value="' + formatInputToDisplay(aset_harga_sewa) + '" onChange="actionHargaSewa('+$("#idx").val()+')"></td>' +
				'<td> <input type="text" class="form-control nominal" name="aset_harga_potongan[]" id="harga_potongan_' + $("#idx").val() + '" value="0" onChange="actionHargaSewa('+$("#idx").val()+')"></td>' +
				'<td><input type="text" class="form-control nominal" name="aset_sub_total[]" id="aset_sub_total_' + $("#idx").val() + '"  value="' + formatInputToDisplay(aset_harga_sewa) + '"></td>' +
				'<td>' +
					'<input type="hidden" name="idx[]"  value="'+$("#idx").val()+'">'+
					'<input type="hidden" name="aset_id[]"  value="' + aset_id + '">'+
					'<input type="hidden" name="aset_kode[]"  value="' + aset_kode + '">'+
					'<input type="hidden" name="aset_label[]"  value="' + aset_label + '">'+
					'<button type="button" class="btn btn-danger btn-sm btn-hapus" data-toggle="tooltip" data-placement="top" value="' + $("#idx").val() + '" title="hapus"><span class="icon fa fa-trash-o"></span></button>' +
				'</td>' +
				'</tr>'
			);
			hitung_total('add',parseInt(aset_harga_sewa));
		}
		$('#pilih_'+aset_id).hide();
		$("input[name='detail_'").val('detail not empty');
	}
	function input_rincian(aset_id,aset_kode,aset_label,aset_harga_sewa,status_id) {
		add_rincian(aset_id,aset_kode,aset_label,aset_harga_sewa,status_id);
		urutkan_item();
		
	}
	
	function urutkan_item() {
		var no = 1;
		$('#tbl-rincian > tbody  > tr').each(function () {
			$(this).find("td:first").text(no);
			no++;
			$('input[name="idx"]').val(no);
		});

	}

	function formatRupiah(angka, prefix){
		var number_string = angka.replace(/[^,\d]/g, '').toString(),
		split   		= number_string.split(','),
		sisa     		= split[0].length % 3,
		rupiah     		= split[0].substr(0, sisa),
		ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);

			// tambahkan titik jika yang di input sudah menjadi angka ribuan
			if(ribuan){
				separator = sisa ? '.' : '';
				rupiah += separator + ribuan.join('.');
			}

			rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
			return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
		}
	</script>