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
								<th class="text-center">Kode</th>
								<th class="text-center">Nama</th>
								<th class="text-center">Merk</th>
								<th class="text-center">Spesifiksi</th>
								<th class="text-center">Tanggal Perolehan</th>
								<th class="text-center">Nilai Perolehan</th>
								<th class="text-center">Unit PJ</th>
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
										<td><?php echo $row['label_kode']?></td>
										<td><?php echo $row['label_nama']?></td>
										<td><?php echo $row['merk']?></td>
										<td><?php echo $row['spec']?></td>
										<td><?php echo $row['tgl_beli']?></td>
										<td><?php echo $row['nilOleh']?></td>
										<td><?php echo $row['unit_nama']?></td>
										<td>
											<a href="javascript:;"
											onclick="input_rincian('<?php echo $row['label_id'] ?>',
												'<?php echo $row['label_kode'] ?>',
												'<?php echo $row['label_nama'] ?>',
												'<?php echo $row['merk'] ?>',
												'<?php echo $row['tgl_beli'] ?>',
												'<?php echo $row['nilOleh'] ?>',
												'<?php echo $row['unit_nama'] ?>',
												'<?php echo $row['unit_id'] ?>',
												'<?php echo $row['kondisi'] ?>'
												)"
												class="btn btn-xs btn-primary" id="pilih_<?php echo $row['label_id'] ?>">Pilih</a>
											</td>
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
		var listBarangId = $("input[name='inv_det_id[]']").map(function(){return $(this).val();}).get();
		for (i = 0; i < listBarangId.length; i++) {
			$('#pilih_'+listBarangId[i]).hide();
			console.log('#pilih_'+listBarangId[i]);
		}

		function add_rincian(inv_det_id,label_kode,label_nama,merk,tgl_beli,nilOleh,unit_nama,unit_id,kondisi) {
			if ($("#tbl-rincian tbody tr:first").find(":input").length == 0) {
				$("#tbl-rincian tbody").html("");
			}
			var values = $("input[name='inv_det_id[]']").map(function(){return $(this).val();}).get();


			if(jQuery.inArray(inv_det_id, values) != -1) {
				alert("Kode barang "+label_kode+" sudah ada.");
			} else {
				$("#tbl-rincian tbody").append(
					'<tr>' +
					'<td class="text-center">'+
					'<input type="text">'+
					'</td>' +
					'<td>' + label_kode + '</td>' +
					'<td>' + label_nama + '</td>' +
					'<td>' + merk + '</td>' +
					'<td>' + tgl_beli + '</td>' +
					'<td>' + formatRupiah(nilOleh,'Rp. ') + '</td>' +

					'<td>' + unit_nama + '</td>' +
					'<td><textarea class="form-control" placeholder="Masukan keterangan" name="keterangan[]"></textarea></td>' +
					'<td>' +
					'<input type="hidden" name="inv_det_id[]"  value="' + inv_det_id + '">'+
					'<input type="hidden" name="barang_kode[]"  value="' + label_kode + '">'+
					'<input type="hidden" name="kondisi[]"  value="' + kondisi + '">' +
					'<input type="hidden" name="unit_id[]"  value="' + unit_id + '">' +
					'<input type="hidden" name="nilOleh[]"  value="' + nilOleh + '">' +
					'<button type="button" class="btn btn-danger btn-sm btn-hapus" data-toggle="tooltip" data-placement="top" value="' + parseInt(inv_det_id) + '" title="hapus"><span class="icon fa fa-trash-o"></span></button>' +
					'</td>' +
					'</tr>'
					);
			}
			$('#pilih_'+inv_det_id).hide();
			$("input[name='detail_'").val('detail not empty');
		}

		function input_rincian(inv_det_id,label_kode,label_nama,merk,tgl_beli,nilOleh,unit_nama,unit_id,kondisi) {
			add_rincian(inv_det_id,label_kode,label_nama,merk,tgl_beli,nilOleh,unit_nama,unit_id,kondisi);
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