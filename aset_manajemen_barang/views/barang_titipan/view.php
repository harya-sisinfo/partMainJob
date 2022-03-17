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
<?php //echo "<pre>"; print_r($content); echo "</pre>"; ?>
<?php //echo "<pre>"; print_r($unit_kerja); echo "</pre>"; ?>
<?php //echo "<pre>"; print_r($kode_unit); echo "</pre>"; ?>

<form id="form-filter" rel="ajax" action="<?php echo $linkView; ?>" class="panel form-horizontal xhr dest_subcontent-element" method="post" >
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
	<div class="panel-heading">
        <span class="panel-title"><b>Pencarian</b></span>
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
			<label class="col-sm-3 control-label" style="text-align:left;">Lokasi</label>
			<div class="col-sm-3">
				<select class="form-control" name="lokasi" id="lokasi">
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
			<label class="col-sm-3 control-label" style="text-align:left;">Status Barang</label>
			<div class="col-sm-3">
				<select class="form-control" name="status" id="status">
                <option value="2" selected="selected">Aktif</option>
                </select>
			</div>	
		</div>
        
        <?php //echo "<pre>"; print_r($dataUnitKerja); echo "</pre>"; ?>
        <?php //echo "<pre>"; print_r($kode_unit); echo "</pre>"; ?>
		<div class="row form-group">
			<label class="col-sm-3 control-label" style="text-align:left;">Unit PJ</label>
			<div class="col-sm-6">
				<select class="form-control" name="unit_pj" id="unit_pj">
                <option></option>
                    <?php
                        if (!empty($dataUnitKerja)) {
                            foreach ($dataUnitKerja as $unit) {
                                $selected = '';
                                
                                if ($unit_kerja=="") {
                                    $unit_kerja=$kode_unit;
                                }
                                
                                if ($unit->kode == $unit_kerja)
                                $selected = ' selected="selected" ';
                                echo ' <option value="' . $unit->kode . '" '.$selected.' >' . $unit->nama . '</option>';
                            }
                        }
                    ?>
                </select>
			</div>	
		</div>
        
		<div class="row form-group">
			<label class="col-sm-3 control-label" style="text-align:left;">Kode / Nama Barang</label>
			<div class="col-sm-4">
				<input type="input" id="search" name="search" value="<?php echo (!empty($search)?$search:'')?>" class="form-control" placeholder="Kode / Nama Barang" />
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
				<a href="<?php echo $linkAdd ?>" class="btn btn-flat btn-sm btn-labeled btn-success xhr dest_subcontent-element"><span class="btn-label-icon left  fa fa-plus"></span> Tambah</a>
                <a href="" class="btn btn-flat btn-sm btn-labeled btn-info xhr dest_subcontent-element"><span class="btn-label-icon left fa fa-file-text-o"></span> Export Excel</a>
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
							<th style="text-align: center;">Label Barang</th>
							<th style="text-align: center;">Merk</th>
							<th style="text-align: center;">Spesifikasi</th>
							<th style="text-align: center;">Lokasi Barang</th>
							<th style="text-align: center;">Unit PJ</th>
                            <th style="text-align: center;">Tahun<br />Pengadaan</th>
                            <th style="text-align: center;">Nilai Perolehan<br />(Rp)</th>
                            <th style="text-align: center;">Tgl Buku</th>
                            <th style="text-align: center;">Kondisi</th>
                            <th style="text-align: center;">Status<br />Kepemilikan</th>
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
                                <a href="<?php echo $linkU.'/'.$row['id']; ?>" class="btn btn-flat btn-xs btn-labeled btn-warning xhr dest_subcontent-element" title="Update Barang Titipan"><i class="fa fa-edit"></i></a>
                                <a href="<?php echo $linkD.'/'.$row['id']; ?>" class="btn btn-flat btn-xs btn-labeled btn-info xhr dest_subcontent-element" title="Detail Barang Titipan"><i class="fa fa-folder-open"></i></a>
                                <?php
                                $golongan = $row['golongan'];
                                //echo $golongan; echo "<br>";
                                
                                if ($golongan == 2) {
                                ?>
                                <a href="<?php echo $linkK.'/'.$row['id']; ?>" class="btn btn-flat btn-xs btn-labeled btn-success xhr dest_subcontent-element" title="Atur Koordinat Barang Titipan"><i class="fa fa-globe"></i></a>
                                <?php
                                }
                                ?>
                                <button class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></button><br />
                            </td>
							<td><?php echo $row['KIB']; ?></td>
							<td><?php echo $row['nama_aset']; ?></td>
                            <td><?php echo $row['merek'];  ?></td>
                            <td><?php echo $row['spec'];  ?></td>
                            <td><?php echo $row['lokasi_aset'];?></td>
                            <td><?php echo $row['unit_pj'];  ?></td>
                            <td style="text-align: center;"><?php echo $row['thn_pengadaan'];  ?></td>
                            <td style="text-align: right;">
                                <?php echo number_format($row['nilai_perolehan'],2,",",".")?>
                            </td>
                            <td><?php echo $row['tgl_buku'];  ?></td>
                            <td><?php echo $row['kondisi'];  ?></td>
                            <td><?php echo $row['status_kepemilikan'];  ?></td>
                            <td><?php echo $row['no_ref'];  ?></td>
						</tr>
						<?php 
                        }
				} else {
					echo "<tr><td colspan='17'><div class='alert alert-danger'>Data Barang Titipan Tidak Ditemukan</div></td></tr>";
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
        $("select[name='lokasi']").select2({ placeholder: "Pilih Lokasi"});
        $("select[name='kondisi']").select2({ placeholder: "Pilih Kondisi"});
		$("select[name='status']").select2({ placeholder: "Pilih Status Aset"});
		$("select[name='unit_pj']").select2({ placeholder: "Pilih Unit PJ"});
	});
</script>