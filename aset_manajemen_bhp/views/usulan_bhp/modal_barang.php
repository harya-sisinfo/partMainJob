 <div id='modal-data-basic'>
 	<form rel="ajax" action="<?php echo $linkForm ?>" class="form-horizontal xhr dest_modal-data-basic" method="get">
 		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
 		<div class="panel">
 			<div class="panel-body">
 				<div class="form-group">
 					<label class="col-md-3">
 						Bidang
 					</label>
 					<div class="col-md-4">
 						<input class="form-control" type="text" value="" name="search" id="search">
 					</div>
 					<div class="col-md-2">
 						<a rel='async' id="listBidang" ajaxify="<?php echo modal("Pilih Bidang", 'aset_manajemen_bhp', 'inventarisasi_bhp', 'list_bidang') ?>" href="" class="btn btn-sm btn-labeled btn-info"><i class="fa fa-search btn-label-icon left"></i>Cari Bidang</a>
 					</div>
 				</div>
 				<div class="form-group">
 					<label class="col-md-3">
 						Kelompok
 					</label>
 					<div class="col-md-4">
 						<input class="form-control" type="text" value="" name="search" id="search">
 					</div>
 				</div>
 				<div class="form-group">
 					<label class="col-md-3">
 						Sub Kelompok
 					</label>
 					<div class="col-md-4">
 						<input class="form-control" type="text" value="" name="search" id="search">
 					</div>
 				</div>
 				<div class="form-group">
 					<label class="col-md-3">
 						Cari kode/nama
 					</label>
 					<div class="col-md-4">
 						<input class="form-control" type="text" value="" name="search" id="search">
 					</div>
 				</div>
 				<div class="form-group">
 					<div class="col-md-7 text-right">
 						<?php echo tampilkan_button(); ?>
 					</div>
 				</div>
 				<div class="table-light table-primary" style="overflow: auto;">
 					<table class="table table-bordered table-striped">
 						<thead>
 							<tr>
 								<th class="text-center">No</th>
 								<th class="text-center">Kode</th>
 								<th class="text-center">Nama</th>
 								<th class="text-center">Aksi</th>
 							</tr>
 						</thead>
 						<tbody>
 							<?php
								if (!empty($content)) {
									$i = $offset + 1;
									foreach ($content as $key => $row) {
								?>
 									<tr>
 										<td><?php echo $i ?></td>
 										<td><?php echo $row['kode'] ?></td>
 										<td><?php echo $row['nama'] ?></td>
 										<td>
 											<?php
												if ($row['aksi'] == 'show') {
												?>
 												<a href="javascript:;" onclick="input_rincian('<?php echo $row['id'] ?>','<?php echo $row['kode'] ?>','<?php echo $row['nama'] ?>','<?php echo $row['satuan'] ?>','<?php echo ($row['satuan_nama']) ? $row['satuan_nama'] : 'empty' ?>')" class="btn btn-xs btn-primary">Pilih</a>
 											<?php
												}
												?>
 										</td>
 									</tr>
 							<?php
										$i++;
									}
								}
								?>
 						</tbody>
 					</table>
 					<?php if (!empty($content)) { ?>
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
 	/*  	function pilih_rincian(id, label, kode, nama, satuan) {
 		$("input[name='id_barang']").val(id);
 		$("input[name='label_barang']").val(label);
 		$("input[name='kode_barang']").val(kode);
 		$("input[name='nama_barang']").val(nama);
 		$("input[name='satuan_barang']").val(satuan);
 		$('#modal-data-basic').closest('.modal').modal('hide');
 	} */
 	var listBarangId = $("input[name='id_barang[]']").map(function() {
 		return $(this).val();
 	}).get();
 	for (i = 0; i < listBarangId.length; i++) {
 		$('#pilih_' + listBarangId[i]).hide();
 		console.log('#pilih_' + listBarangId[i]);
 	}

 	function add_rincian(id_barang, kode_barang, nama_barang, satuan_barang, satuan_nama) {
 		if ($("#tbl-rincian tbody tr:first").find(":input").length == 0) {
 			$("#tbl-rincian tbody").html("");
 		}
 		var values = $("input[name='id_barang[]']").map(function() {
 			return $(this).val();
 		}).get();
 		if (jQuery.inArray(id_barang, values) != -1) {
 			alert("Kode barang " + kode_barang + " sudah ada.");
 		} else {
 			if (typeof satuan_nama == 'undefined') {
 				satuan_nama = '';
 			}
 			if (typeof satuan_barang == 'undefined') {
 				satuan_barang = '';
 			}

 			$("#tbl-rincian tbody").append(
 				'<tr>' +
 				'<td class="text-center">' +
 				'<input type="text">' +
 				'</td>' +
 				'<td>' + kode_barang + '</td>' +
 				'<td>' + nama_barang + '</td>' +
 				'<td><textarea name="spesifikasi_barang[]" class="form-control" placeholder="Masukan spesifikasi barang" ></textarea></td>' +
 				'<td><label class="custom-file px-file"><input type="file" class="custom-file-input" name="file_barang[]" onchange="checkFileUpload(this,[\'jpg\',\'jpeg\',\'pdf\']);" ><span class="custom-file-control form-control">Pilih file ...&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><div class="px-file-buttons"><button type = "button" class = "btn px-file-clear" > Clear < /button> <button type = "button" class = "btn btn-primary px-file-browse" > Browse </button> </div></label></td > ' +
 				'<td><div class="input-group date day"><input type="text" name="tanggal_barang" class="form-control" placeholder="Masukan Tanggal pakai" autocomplete="off" ><span class="input-group-addon"><i class="fa fa-calendar"></i></span></div></td>' +
 				'<td><input type="text" autocomplete="off" placeholder="Masukan jumlah barang " class="form-control" name="jumlah_barang"></td>' +
 				'<td>' + satuan_nama + '</td>' +
 				'<td><input type="text" autocomplete="off" placeholder="Masukan harga satuan barang " class="form-control" name="harga_barang"></td>' +
 				'<td></td>' +
 				'<td>' +
 				'<input type="hidden" name="idx[]"  value="' + $("#idx").val() + '">' +
 				'<input type="hidden" name="id_barang[]"  value="' + id_barang + '">' +
 				'<input type="hidden" name="kode_barang[]"  value="' + kode_barang + '">' +
 				'<input type="hidden" name="nama_barang[]"  value="' + nama_barang + '">' +
 				'<input type="hidden" name="satuan_barang[]"  value="' + satuan_barang + '">' +
 				'<button type="button" class="btn btn-danger btn-sm btn-hapus" data-toggle="tooltip" data-placement="top" value="' + $("#idx").val() + '" title="hapus"><span class="icon fa fa-trash-o"></span></button>' +
 				'</td>' +
 				'</tr>'
 			);
 		}
 	}

 	/* 
<input type="file" class="custom-file-input" name="ba_file[]" onchange="checkFileUpload(this,['jpg','jpeg','pdf']);" >
							<span class="custom-file-control form-control">
								<?php
								if (!empty(set_value('ba_file[]'))) {
									echo set_value('ba_file[]');
								} else {
									echo "Pilih file ... ";
								}

								?></span>
							<div class="px-file-buttons">
								<button type="button" class="btn px-file-clear">Clear</button>
								<button type="button" class="btn btn-primary px-file-browse">Browse</button>
							</div> */
 	function urutkan_item() {
 		var no = 1;
 		$('#tbl-rincian > tbody  > tr').each(function() {
 			$(this).find("td:first").text(no);
 			no++;
 			$('input[name="idx"]').val(no);
 		});

 	}

 	function input_rincian(id_barang, kode_barang, nama_barang, satuan_barang) {
 		add_rincian(id_barang, kode_barang, nama_barang, satuan_barang);
 		urutkan_item();

 	}

 	function formatRupiah(angka, prefix) {
 		var number_string = angka.replace(/[^,\d]/g, '').toString(),
 			split = number_string.split(','),
 			sisa = split[0].length % 3,
 			rupiah = split[0].substr(0, sisa),
 			ribuan = split[0].substr(sisa).match(/\d{3}/gi);

 		if (ribuan) {
 			separator = sisa ? '.' : '';
 			rupiah += separator + ribuan.join('.');
 		}

 		rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
 		return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
 	}
 </script>