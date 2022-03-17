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
			<label class="col-sm-3 control-label" style="text-align:left;">Nama Unit / Sub Unit</label>
			<div class="col-sm-4"><b>Universitas</b></div>	
		</div>
        
		<div class="row form-group">
			<label class="col-sm-3 control-label" style="text-align:left;">Tahun Pembukuan</label>
			<div class="col-sm-2">
				<select class="form-control" name="tahun" id="tahun">
    				<option value="2021">2021</option>
                    <option value="2020">2020</option>
                    <option value="2019">2019</option>
                    <option value="2018">2018</option>
                    <option value="2017">2017</option>
                    <option value="2016">2016</option>
                </select>
			</div>	
		</div>
        
		<div class="row form-group">
			<label class="col-sm-3 control-label" style="text-align:left;">No Referensi / Kode Barang / Nama Barang</label>
			<div class="col-sm-4">
				<input type="input" id="search" name="search" value="<?php echo (!empty($search)?$search:'')?>" class="form-control" placeholder="No Referensi / Kode Barang / Nama Barang" />
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
			</div>
		</div>
        <?php //echo "<pre>"; print_r($content); echo "</pre>"; ?>
		<div class="panel-body">
			<div class="table-primary" style="overflow: auto;">
				<table class="table table-bordered table-hover table-striped">
					<thead>
						<tr>
							<th style="text-align: center;">No.</th>
							<th style="text-align: center;">Tgl Buku</th>
							<th style="text-align: center;">Nomor Referensi</th>
							<th style="text-align: center;">Keterangan</th>
							<th style="text-align: center;">Jumlah Aset</th>
							<th style="text-align: center;">Total Kapitalisasi</th>
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
							<td><?php echo $row['nomor']; ?></td>
                            <td><?php echo $row['keterangan'];  ?></td>
                            <td style="text-align: center;"><?php echo $row['jml_aset'];  ?></td>
                            <td style="text-align: right;"><?php echo number_format($row['total'],2,",",".")?></td>
							<td style="width: 8%;">
                                <a href="<?php echo $linkDet.'/'.$row['id']; ?>" class="btn btn-flat btn-xs btn-labeled btn-info xhr dest_subcontent-element" title="Detail"><i class="fa fa-folder-open"></i></a>
                                <button class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></button>
                            </td>
						</tr>
						<?php 
                        }
				} else {
					echo "<tr><td colspan='8'><div class='alert alert-danger'>Data Pengembangan Aset Langsung Tidak Ditemukan</div></td></tr>";
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
		$("select[name='tahun']").select2({ placeholder: "Pilih Tahun Pembukuan"});
	});
</script>