<div id="modal-data-basic">
	<div class="panel">
		<div class="panel-heading">
			<span class="panel-title">Data Pengembalian Aset</span>
		</div>
		<div class="panel-body">
			<!-- ====================================================================================================== -->
			<div class="row">
				<div class="col-sm-12">
					<div class="row form-group">
						<label class="col-sm-3 control-label" style='text-align:left;'>Nomor Pengembalian</label>
						<div class="col-sm-8">
							<?php echo $kembali_nomor?>
						</div>
					</div>
				</div>
			</div>
			<!-- ====================================================================================================== -->
			<div class="row">
				<div class="col-sm-12">
					<div class="row form-group">
						<label class="col-sm-3 control-label" style='text-align:left;'>Tanggal Pengembalian</label>
						<div class="col-sm-8">
							<?php echo $kembali_tanggal?>
						</div>
					</div>
				</div>
			</div>
			<!-- ====================================================================================================== -->
			<div class="row">
				<div class="col-sm-12">
					<div class="row form-group">
						<label class="col-sm-3 control-label" style='text-align:left;'>Keterangan</label>
						<div class="col-sm-8">
							<?php echo $kembali_keterangan?>
						</div>
					</div>
				</div>
			</div>
			<!-- ====================================================================================================== -->
			<div class="table-light table-primary" style="overflow: auto;">
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>No</th>
							<th>Kode Sewa</th>
							<th>Label</th>
							<th>Rusak (Rp)</th>
							<th>Ganti (Rp)</th>
							<th>Keterangan</th>
						</tr>
					</thead>
					<tbody>
					<?php
					if($content){
						foreach ($content as $key => $value) {
							?>
						<tr>
							<td><?php echo $key+1?></td>
							<td><?php echo $value['kembali_detail_kode']?></td>
							<td><?php echo $value['kembali_detail_barang']?></td>
							<td><?php echo number_format($value['kembali_detail_rusak'],0,",",".")?></td>
							<td><?php echo number_format($value['kembali_detail_ganti'],0,",",".")?></td>
							<td><?php echo $value['kembali_detail_keterangan']?></td>
						</tr>
							<?php
						}
					}
					?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>