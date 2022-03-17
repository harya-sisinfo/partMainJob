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
								<th class="text-center">Kode Barang</th>
								<th class="text-center">Nama Barang</th>
								<th class="text-center">Merk</th>
								<th class="text-center">Nilai Perolehan</th>
								<th class="text-center">Tanggal Perolehan</th>
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
										<td><?php echo $row['kode_barang']?></td>
										<td><?php echo $row['nama_barang']?></td>
										<td><?php echo $row['merk']?></td>
										<td><?php echo $row['nilai_perolehan']?></td>
										<td><?php echo $row['tgl_perolehan']?></td>
										<td><a href="javascript:;" onclick="input_rincian('<?php echo $row['barang_id'] ?>', '<?php echo $row['kode_barang'] ?>','<?php echo $row['nama_barang'] ?>','<?php echo $row['merk'] ?>','<?php echo $row['spesifikasi'] ?>','<?php echo $row['tgl_perolehan'] ?>','<?php echo $row['nilai_perolehan'] ?>','<?php echo $row['satuan'] ?>','<?php echo $row['kondisi'] ?>','<?php echo $row['keterangan'] ?>')" class="btn btn-xs btn-primary" id="pilih_<?php echo $row['barang_id'] ?>">Pilih</a></td>
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

		<button id="test" onclick="test()">aaa</button>
	</form>
</div>

<script type="text/javascript">
	
	var listBarangId = $("input[name='barang_id[]']").map(function(){return $(this).val();}).get();
	for (i = 0; i < listBarangId.length; i++) {
		$('#pilih_'+listBarangId[i]).hide();
		console.log('#pilih_'+listBarangId[i]);
	}

	function add_rincian(barang_id,kode_barang,nama_barang,merk,spesifikasi,tgl_perolehan,nilai_perolehan,satuan,kondisi,keterangan) {
		if ($("#tbl-rincian tbody tr:first").find(":input").length == 0) {
			$("#tbl-rincian tbody").html("");
		}
		var values = $("input[name='barang_id[]']").map(function(){return $(this).val();}).get();
		
		//var arrayByBarangId = values.split(",");
		if(jQuery.inArray(barang_id, values) != -1) {
		    alert("Kode barang "+kode_barang+" sudah ada.");
		} else {
			$("#tbl-rincian tbody").append(
				'<tr>' +
				'<td class="text-center">'+
				'<input type="text">'+
				'</td>' +
				'<td>' + kode_barang + '</td>' +
				'<td>' + nama_barang + '</td>' +
				'<td>' + spesifikasi + '</td>' +
				'<td>' + tgl_perolehan + '</td>' +
				'<td>' + formatRupiah(nilai_perolehan,'Rp. ') + '</td>' +
				'<td>' + 
					'<select class="form-control" name="kondisi[]">'+ 
					'<option value="1">Baik</option>'+
					'<option value="2">Rusak Ringan</option>'+
					'<option value="3">Rusak Berat</option>'+
					'</select>'+
				'</td>' +
				'<td>' + keterangan + '</td>' +
				'<td>' +
					'<input type="hidden" name="barang_id[]"  value="' + barang_id + '">'+
					'<input type="hidden" name="kode_barang[]"  value="' + kode_barang + '">'+
					'<input type="hidden" name="nama_barang[]"  value="' + nama_barang + '">' +
					'<input type="hidden" name="merk[]"  value="' + merk + '">' +
					'<input type="hidden" name="spesifikasi[]"  value="' + spesifikasi + '">' +
					'<input type="hidden" name="tgl_perolehan[]"  value="' + tgl_perolehan + '">' +
					'<input type="hidden" name="nilai_perolehan[]"  value="' + nilai_perolehan + '">' +
					'<button type="button" class="btn btn-danger btn-sm btn-hapus" data-toggle="tooltip" data-placement="top" value="' + parseInt(barang_id) + '" title="hapus"><span class="icon fa fa-trash-o"></span></button>' +
				'</td>' +
				'</tr>'
			);
		}
		$('#pilih_'+barang_id).hide();
		$("input[name='detail_'").val('detail not empty');
	}
	function input_rincian(barang_id,kode_barang,nama_barang,merk,spesifikasi,tgl_perolehan,nilai_perolehan,satuan,kondisi,keterangan) {
		add_rincian(barang_id,kode_barang,nama_barang,merk,spesifikasi,tgl_perolehan,nilai_perolehan,satuan,kondisi,keterangan);
		urutkan_item();
		
	}
	
	function urutkan_item() {
		var no = 1;
		$('#tbl-rincian > tbody  > tr').each(function () {
			$(this).find("td:first").text(no);
			no++;
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