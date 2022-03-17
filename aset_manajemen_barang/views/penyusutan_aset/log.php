<div class="panel">
	<div class="panel-heading">
		<span class="panel-title">Data Barang</span>
	</div>
	<div class="panel-body">
		<!-- ====================================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Kode Barang</label>
					<div class="col-sm-8">
						<?php echo $kode_aset?>
					</div>
				</div>
			</div>
		</div>
		<!-- ====================================================================================================== -->
		<!-- ====================================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Nama Barang</label>
					<div class="col-sm-8">
						<?php echo $barangNama?>
					</div>
				</div>
			</div>
		</div>
		<!-- ====================================================================================================== -->
		<!-- ====================================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Label Barang</label>
					<div class="col-sm-8">
						<?php echo $label_aset?>
					</div>
				</div>
			</div>
		</div>
		<!-- ====================================================================================================== -->
		<!-- ====================================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Merk</label>
					<div class="col-sm-8">
						<?php echo $invMerek?>
					</div>
				</div>
			</div>
		</div>
		<!-- ====================================================================================================== -->		<!-- ====================================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Spesifikasi</label>
					<div class="col-sm-8">
						<?php echo $invSpesifikasi?>
					</div>
				</div>
			</div>
		</div>
		<!-- ====================================================================================================== -->
		
	</div>
</div>
<div class="panel">
	<div class="panel-heading"><span class="panel-title">List Log Penyusutan</span></div>
	<div class="panel-body">
		<div class="table-light table-primary" style="overflow: auto;">
			<table class="table table-bordered table-striped">
				<thead>
					<th>Nomor</th>
					<th>Periode Penyusutan</th>
					<th>Berita Acara</th>
					<th>Nilai Penyusutan (Rp)</th>
					<th>Nilai Buku (Rp)</th>
				</thead>
				<?php
				if(!empty($content))
				{
					$i=$offset + 1;
					foreach ($content as $key => $row)
					{
					?>
				<tbody>
					<td><?php echo $i++?></td>
					<td><?php echo date('d-m-Y', strtotime($row['penyusutanMstPeriode']))?></td>
					<td><?php echo $row['penyusutanMstNoBA']?></td>
					<td align="right"><?php echo number_format($row['penyusutanDetNilaiPenyusutan'],0,",",".")?></td>
					<td align="right"><?php echo number_format($row['penyusutanDetNilaiAkhir'],0,",",".")?></td>
				</tbody>
			<?php } }?>
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