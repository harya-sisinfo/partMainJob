<div id='modal-data-basic'>
	<div class="panel">
		<div class="panel-heading">
			<span class="panel-title">Detail Mutasi</span>
		</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="col-sm-3 control-label" style="text-align:left;">Unit Asal</label>
						<div class="col-sm-4">
							<?php echo $unit_asal?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="col-sm-3 control-label" style="text-align:left;">Unit Tujuan</label>
						<div class="col-sm-4">
							<?php echo $unit_tujuan?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="col-sm-3 control-label" style="text-align:left;">BA Mutasi</label>
						<div class="col-sm-4">
							<?php echo $ba_mutasi?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="col-sm-3 control-label" style="text-align:left;">Tanggal BA Mutasi</label>
						<div class="col-sm-4">
							<?php echo $tgl_mutasi?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="col-sm-3 control-label" style="text-align:left;">PIC</label>
						<div class="col-sm-4">
							<?php echo $pic?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">

					<div class="form-group">
						<label class="col-sm-3 control-label" style="text-align:left;">Keterangan Tambahan</label>
						<div class="col-sm-4">
							<?php echo $keterangan?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="col-sm-3 control-label" style="text-align:left;">File Dokumen BA Mutasi</label>
						<div class="col-sm-4">
							<?php 
							if(!empty($file_mutasi)){
							?>
							<a href="<?php echo $url_file?>" download class="btn btn-sm btn-flat btn-labeled btn-success" data-toggle="tooltip" data-placement="top" title="Download file" >
								<span class="btn-label-icon left fa fa-download"></span>&nbsp;Download
							</a>
							<?php 
							}else{
								?>
								<small>Belum terdapat file yang di unggah.</small>
								<?php
							}
							?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">

					<div class="form-group">
						<label class="col-sm-3 control-label" style="text-align:left;">Status Approval</label>
						<div class="col-sm-4">
							<span class="label <?php echo $label_color?>"><?php echo $approval?></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="table-light table-primary" style="overflow: auto;">
		<table class="table table-bordered table-striped">
			<thead>
				
				<tr>
					<th class="text-center" rowspan="2">No</th>
					<th class="text-center" colspan="2">Kode Barang</th>
					<th class="text-center" rowspan="2">Nama Barang</th>
					<th class="text-center" rowspan="2">Merk</th>
					<th class="text-center" rowspan="2">Spesifikasi</th>
					<th class="text-center" rowspan="2">Tanggal Perolehan</th>
					<th class="text-center" rowspan="2">Satuan</th>
					<th class="text-center" rowspan="2">harga Perolehan</th>
					<th class="text-center" rowspan="2">Nilai Buku</th>
					<th class="text-center" rowspan="2">Kondisi</th>
					<th class="text-center" rowspan="2">Keterangan</th>
				</tr>
				<tr>
					<th class="text-center">Baru</th>
					<th class="text-center">Lama</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$i=1;
				foreach ($list as $key => $row) {
					?>
					<tr>
						<td><?php echo $i++?></td>
						<td><?php echo (!empty($row['kodeLama']))?$row['kodeLama']:$row['kodeLama']?></td>
						<td><?php echo (!empty($row['kode_aset']))?$row['kode_aset']:$row['kode_barang']?></td>
						<td><?php echo $row['nama_aset']?></td>
						<td><?php echo $row['merk']?></td>
						<td><?php echo $row['spesifikasi']?></td>
						<td><?php echo $row['tgl_perolehan']?></td>
						<td><?php echo $row['satuan']?></td>
						<td><?php echo number_format($row['nilai_perolehan'],2,',','.')?></td>
						<td><?php echo $row['nilai_buku']?></td>
						<td><?php echo $row['kondisi']?></td>
						<td><?php echo $row['keteranganDet']?></td>
					</tr>
					<?php 
					
				}
				?>
			</tbody>
		</table>
	</div>
</div>