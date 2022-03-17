<?php echo Modules::run('breadcrump'); ?>

<style type="text/css">
	th {
		text-align: center;
		vertical-align: middle !important;
	}
</style>

<?php if(validation_errors()){ ?>
	<div class="alert alert-danger">
		<?php echo validation_errors(); ?>
	</div>
<?php } 
if(!empty($error)): ?>
	<div class="alert alert-danger">
		<?php echo $error; ?>
	</div>
<?php endif; ?>

<?php $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"); ?>

<form id="form-filter" rel="ajax" action="<?php echo $linkView; ?>" class="panel form-horizontal xhr dest_subcontent-element" method="post" >
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
	<div class="panel-heading">
        <span class="panel-title">Filter Pencarian</span>
    </div>
    
	<div class="panel-body">
	   <div class="row form-group">
			<label class="col-sm-3 control-label" style="text-align:left;">Kelompok COA</label>
			<div class="col-sm-4">
				<select class="form-control" name="kel_coa" id="kel_coa">
                </select>
			</div>	
		</div>
        
        <div class="row form-group">
			<label class="col-sm-3 control-label" style="text-align:left;">Tahun Pengadaan</label>
			<div class="col-sm-3">
				<select class="form-control" name="tahun_pengadaan" id="tahun_pengadaan">
                </select>
			</div>	
		</div>
        
        <div class="row form-group">
			<label class="col-sm-3 control-label" style="text-align:left;">Tahun Pembukuan</label>
			<div class="col-sm-3">
				<select class="form-control" name="tahun_pembukuan" id="tahun_pembukuan">
                </select>
			</div>	
		</div>
        
        <div class="row form-group">
			<label class="col-sm-3 control-label" style="text-align:left;">Sumber Dana</label>
			<div class="col-sm-4">
				<select class="form-control" name="sumber_dana" id="sumber_dana">
                </select>
			</div>	
		</div>
        
        <div class="row form-group">
			<label class="col-sm-3 control-label" style="text-align:left;">Kepemilikan</label>
			<div class="col-sm-3">
				<select class="form-control" name="kepemilikan" id="kepemilikan">
                </select>
			</div>	
		</div>
        
        <div class="row form-group">
			<label class="col-sm-3 control-label" style="text-align:left;">Kondisi</label>
			<div class="col-sm-3">
				<select class="form-control" name="kondisi" id="kondisi">
                </select>
			</div>	
		</div>
        
		<div class="row form-group">
			<label class="col-sm-3 control-label" style="text-align:left;">Status Aset</label>
			<div class="col-sm-3">
				<select class="form-control" name="status" id="status">
                <option value="2" selected="selected">Aktif</option>
                </select>
			</div>	
		</div>
        
		<div class="row form-group">
			<label class="col-sm-3 control-label" style="text-align:left;">Unit PJ</label>
			<div class="col-sm-4">
				<select class="form-control" name="unit_pj" id="unit_pj">
                <option value="0000" selected="selected">Universitas</option>
                </select>
			</div>	
		</div>
        
		<div class="row form-group">
			<label class="col-sm-3 control-label" style="text-align:left;">Kode/Nama Aset</label>
			<div class="col-sm-4">
				<input type="input" id="search" name="search" value="<?php echo (!empty($search)?$search:'')?>" class="form-control" placeholder="Kode/SPM/SP2D/Barcode/Nama Aset" />
			</div>	
		</div>
	</div>
    
	<div class="panel-footer text-right">
		<?php echo tampilkan_button()?>
	</div>
    
</form>
	<div class="panel">
		<div class="panel-heading">
			<span class="panel-title">&nbsp;</span>
			<div class="panel-heading-controls">
				<a href="<?php echo $linkForm ?>" class="btn btn-flat btn-sm btn-labeled btn-success xhr dest_subcontent-element"><span class="btn-label-icon left  fa fa-plus"></span> Tambah</a>
			</div>
		</div>
		<div class="panel-body">
			<div class="table-primary" style="overflow: auto;">
				<table class="table table-bordered table-hover table-striped">
					<thead>
						<tr>
							<th style="text-align: center;">No.</th>
                            <th style="text-align: center;">Aksi</th>
							<th style="text-align: center;">Kode Barang</th>
							<th style="text-align: center;">Label Aset</th>
							<th style="text-align: center;">Merk</th>
							<th style="text-align: center;">Spesifikasi</th>
							<th style="text-align: center;">Lokasi Aset</th>
							<th style="text-align: center;">Unit PJ</th>
                            <th style="text-align: center;">Tahun<br />Pengadaan</th>
                            <th style="text-align: center;">Nilai Perolehan<br />(Rp)</th>
                            <th style="text-align: center;">Tgl Buku</th>
                            <th style="text-align: center;">Nilai Buku<br />(Rp)</th>
                            <th style="text-align: center;">Kondisi</th>
                            <th style="text-align: center;">Status<br />Kepemilikan</th>
                            <th style="text-align: center;">No Barcode</th>
                            <th style="text-align: center;">No Surat</th>
                            <th style="text-align: center;">No Ref<br />(Manual)</th>
						</tr>
					</thead>
					<tbody>
					<?php
                    if (!empty($content)) {
						$i =0;
						foreach ($content as $key => $row) {
						$i++;
					?>
						<tr>
							<td style="text-align: center;"><?php echo ($i+$offset)?></td>
							<td>
                                <button class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></button><br />
                                <button class="btn btn-info btn-xs"><i class="fa fa-folder-open"></i></button><br />
                                <button class="btn btn-success btn-xs"><i class="fa fa-search-plus"></i></button><br />
                            </td>
							<td><?php echo $row['kode_barang']; ?></td>
							<td><?php echo $row['label_aset']; ?></td>
                            <td><?php echo $row['merk'];  ?></td>
                            <td><?php echo $row['spesifikasi'];  ?></td>
                            <td>
                                <?php
                                echo $row['gedung'].' // '.$row['ruang'];
                                ?>
                            </td>
                            <td><?php echo $row['unit_pj'];  ?></td>
                            <td style="text-align: center;"><?php echo $row['thn_pembelian'];  ?></td>
                            <td style="text-align: right;">
                                <?php echo number_format($row['nilai_perolehan'],2,",",".")?>
                            </td>
                            <td><?php echo $row['tgl_buku'];  ?></td>
                            <td>0,00</td>
                            <td><?php echo $row['kondisi'];  ?></td>
                            <td><?php echo $row['kepemilikan'];  ?></td>
                            <td><?php //echo $row['no_barcode'];  ?></td>
                            <td><?php //echo $row['no_surat'];  ?></td>
                            <td><?php echo $row['no_ref'];  ?></td>
						</tr>
						<?php 
                        }
				} else {
					echo "<tr><td colspan='17'><div class='alert alert-danger'>Data Inventarisasi Detail Tidak Ditemukan</div></td></tr>";
				}
				?>
                </tbody>
				</table>
			</div>
            <?php
            if (!empty($content)) {
            ?>
			<div class="row">
				<div class="pull-right">
					<?php echo $pagination; ?>
				</div>
			</div>
            <?php
            }
            ?>
		</div>
	</div>

<script type="text/javascript">
	$(document).ready(function () {
	    $("select[name='kel_coa']").select2({ placeholder: "Pilih Kelompok COA"});
        $("select[name='tahun_pengadaan']").select2({ placeholder: "Pilih Tahun Pengadaan"});
        $("select[name='tahun_pembukuan']").select2({ placeholder: "Pilih Tahun Pembukuan"});
        $("select[name='sumber_dana']").select2({ placeholder: "Pilih Sumber Dana"});
        $("select[name='kepemilikan']").select2({ placeholder: "Pilih Kepemilikan"});
        $("select[name='kondisi']").select2({ placeholder: "Pilih Kondisi"});
		$("select[name='status']").select2({ placeholder: "Pilih Status Aset"});
		$("select[name='unit_pj']").select2({ placeholder: "Pilih Unit PJ"});
	});
</script>