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
			<label class="col-sm-3 control-label" style="text-align:left;">No. Ref Usulan Penghapusan</label>
			<div class="col-sm-4">
				<input type="input" id="search" name="search" value="<?php echo (!empty($search)?$search:'')?>" class="form-control" placeholder="No. Ref Usulan Penghapusan" />
			</div>	
		</div>

		<div class="row form-group">
			<label class="col-sm-3 control-label" style="text-align:left;">Jenis Penghapusan</label>
			<div class="col-sm-4">
				<select class="form-control" name="jenis" id="jenis">
                    <option value="" selected="selected">SEMUA</option>
    				<option value="7">Dihibahkan</option>
                    <option value="2">Dijual/Dilelang</option>
                    <option value="3">Dimusnahkan</option>
                    <option value="8">Hilang</option>
                    <option value="9">Kebakaran</option>
                </select>
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
				<a href="<?php echo $linkAdd; ?>" class="btn btn-flat btn-sm btn-labeled btn-success xhr dest_subcontent-element"><span class="btn-label-icon left  fa fa-plus"></span> Tambah</a>
			</div>
		</div>
		<div class="panel-body">
			<div class="table-primary" style="overflow: auto;">
				<table class="table table-bordered table-hover table-striped">
					<thead>
						<tr>
							<th style="text-align: center;">No.</th>
							<th style="text-align: center;">Tanggal</th>
							<th style="text-align: center;">Jenis</th>
							<th style="text-align: center;">No. Ref</th>
							<th style="text-align: center;">Keterangan</th>
							<th style="text-align: center;">Jumlah Aset</th>
							<th style="text-align: center;">EP</th>
							<th style="text-align: center;">Aksi</th>
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
							<td style="width: 12%;">
                                <?php
                                    list($thn, $bln, $tgl)=explode('-', $row['tanggal']); //memindah tanggal kedalam array
                                    $tgl_hapus = $tgl.' '.$namabulan[(int)$bln].' '.$thn;
                                        
                                    echo $tgl_hapus;
                                ?>
                            </td>
							<td><?php echo $row['jenis']; ?></td>
							<td><?php echo $row['nomor']; ?></td>
                            <td><?php echo $row['keterangan'];  ?></td>
                            <td style="text-align: center;"><?php echo $row['jml_aset'];  ?></td>
                            <td style="text-align: center;">
                                <?php
                                    if ($row['execHps']==1) {
                                        echo '<i class="fa fa-check-circle" title="Sudah"></i>';
                                    } else {
                                        echo '<i class="fa fa-circle-o" title="Belum"></i>';
                                    }
                                ?>
                            </td>
							<td style="width: 19%;">
                                <?php
                                if ($row['execHps']!=1) {
                                    echo '<button class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></button>';
                                }
                                ?>
                                <button class="btn btn-info btn-xs"><i class="fa fa-folder-open"></i></button>
                                <button class="btn btn-success btn-xs"><i class="fa fa-print"></i></button>
                                <button class="btn btn-primary btn-xs"><i class="fa fa-file"></i></button>
                                <?php
                                if ($row['execHps']!=1) {
                                    echo '<button class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></button>&nbsp';
                                    echo '<button class="btn btn-warning btn-xs"><i class="fa fa-arrow-circle-right"></i></button>';
                                }
                                ?>
                            </td>
						</tr>
						<?php 
                        }
				} else {
					echo "<tr><td colspan='8'><div class='alert alert-danger'>Data Penghapusan Aset Tidak Ditemukan</div></td></tr>";
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
		$("select[name='jenis']").select2({ placeholder: "Pilih Jenis Penghapusan"});
		$("select[name='unit_kerja']").select2({ placeholder: "Pilih unit kerja"});
	});
</script>