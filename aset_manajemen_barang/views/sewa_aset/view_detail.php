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
						<label class="col-sm-3 control-label" style='text-align:left;'>Nomor Sewa</label>
						<div class="col-sm-8">
							<?php echo $data_sewa['sewa_nomor']?>
						</div>
					</div>
				</div>
			</div>
			<!-- ====================================================================================================== -->
			<div class="row">
				<div class="col-sm-12">
					<div class="row form-group">
						<label class="col-sm-3 control-label" style='text-align:left;'>Nama Penyewa</label>
						<div class="col-sm-8">
							<?php echo $data_sewa['sewa_penyewa']?>
						</div>
					</div>
				</div>
			</div>
			<!-- ====================================================================================================== -->
			<div class="row">
				<div class="col-sm-12">
					<div class="row form-group">
						<label class="col-sm-3 control-label" style='text-align:left;'>Telp</label>
						<div class="col-sm-8">
							<?php echo $data_sewa['sewa_telp']?>
						</div>
					</div>
				</div>
			</div>




			<!-- ====================================================================================================== -->
			<div class="row">
				<div class="col-sm-12">
					<div class="row form-group">
						<label class="col-sm-3 control-label" style='text-align:left;'>Tanggal Mulai Sewa</label>
						<div class="col-sm-8">
							<?php echo tgl_indo($data_sewa['sewa_tanggal_awal'])?>&nbsp;<?php echo $data_sewa['sewa_waktu_awal']?>
						</div>
					</div>
				</div>
			</div>
			<!-- ====================================================================================================== -->
			<div class="row">
				<div class="col-sm-12">
					<div class="row form-group">
						<label class="col-sm-3 control-label" style='text-align:left;'>Tanggal Selesai Sewa</label>
						<div class="col-sm-8">
							<?php echo tgl_indo($data_sewa['sewa_tanggal_akhir'])?>&nbsp;<?php echo $data_sewa['sewa_waktu_akhir']?>
						</div>
					</div>
				</div>
			</div>
			<!-- ====================================================================================================== -->
			<div class="row">
				<div class="col-sm-12">
					<div class="row form-group">
						<label class="col-sm-3 control-label" style='text-align:left;'>Unit Kerja</label>
						<div class="col-sm-8">
							<?php echo $data_sewa['unitkerja']?>
						</div>
					</div>
				</div>
			</div>
			<!-- ====================================================================================================== -->
			<div class="row">
				<div class="col-sm-12">
					<div class="row form-group">
						<label class="col-sm-3 control-label" style='text-align:left;'>Mitra</label>
						<div class="col-sm-8">
							<?php echo $data_sewa['mitra']?>
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
							<?php echo $data_sewa['sewa_keterangan']?>
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
							<th>Nilai Satuan (Rp)</th>
							<th>Potongan (Rp)</th>
							<th>Sub Total</th>
						</tr>
					</thead>
					<tbody>
					<?php
					if($data_sewa_detail){
						$sub_total=0;
						foreach ($data_sewa_detail as $key => $value) {
							$sub_total_n = $value['asetSewaDetValue'] - $value['asetSewaDetPotongan'];
							?>
						<tr>
							<td><?php echo $key+1?></td>
							<td><?php echo $value['invBiayaSewaKode']?></td>
							<td><?php echo $value['invDetMstLabel']?></td>
							<td><?php echo number_format($value['asetSewaDetValue'],0,",",".")?></td>
							<td><?php echo number_format($value['asetSewaDetPotongan'],0,",",".")?></td>
							<td><?php echo number_format($sub_total_n,0,",",".")?></td>
						</tr>
							<?php
							$sub_total = $sub_total+$sub_total_n;
						}
						?>
						<tr>
							<td colspan="5" align="right">Total</td>
							<td><?php echo number_format($sub_total,0,",",".")?></td>
						</tr>
						<?php
					}
					?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>