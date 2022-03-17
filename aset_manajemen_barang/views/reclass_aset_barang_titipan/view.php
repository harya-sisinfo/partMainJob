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
        <?php
            $date = date("Y-m-d"); //echo $date; //echo "<br>";
            $tahun = date("Y"); //echo $tahun;
            
            $periodeAwal = $tahun.'-01-01';
            $periodeAkhir = $date;
        ?>
        <div class="row form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">Periode Awal</label>
			<div class="col-sm-2">
				<div class="input-group date day">
					<input type="text" name="periode_awal" class="form-control" placeholder="Periode Awal" autocomplete="off" value="<?php echo $periodeAwal; ?>" />
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
				</div>
			</div>	
		</div>
        
        <div class="row form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">Periode Akhir</label>
			<div class="col-sm-2">
				<div class="input-group date day">
					<input type="text" name="periode_akhir" class="form-control" placeholder="Periode Akhir" autocomplete="off" value="<?php echo $periodeAkhir; ?>" />
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
				</div>
			</div>	
		</div>
        
		<div class="row form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">No. Referensi</label>
			<div class="col-sm-4">
				<input type="input" id="search" name="search" value="<?php echo (!empty($search)?$search:'')?>" class="form-control" placeholder="No. Referensi" />
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
	<div class="panel-body">
		<div class="table-primary" style="overflow: auto;">
			<table class="table table-bordered table-hover table-striped">
				<thead>
					<tr>
						<th style="text-align: center;">Nomor</th>
                        <th style="text-align: center;">Tgl Buku</th>
						<th style="text-align: center;">Nomor Referensi</th>
						<th style="text-align: center;">Keterangan</th>
						<th style="text-align: center;">Jumlah Aset</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center; width: 15%;">Aksi</th>
					</tr>
				</thead>
				<tbody>
                <?php //echo "<pre>"; print_r($content); echo "</pre>"; ?>
				<?php
                if (!empty($content)) {
					$i =0;
					foreach ($content as $key => $row) {
					$i++;
				?>
					<tr>
						<td style="text-align: center;"><?php echo ($i+$offset)?></td>
						<td style="text-align: center;"><?php echo $row['tgl_buku']; ?></td>
						<td><?php echo $row['no_ref']; ?></td>
						<td><?php echo $row['keterangan']; ?></td>
                        <td style="text-align: center;">1</td>
                        <td style="text-align: center;"><i><?php echo $row['status'];  ?></i></td>
                        <td>
                            <a href="<?php //echo $linkU.'/'.$row['id']; ?>" class="btn btn-flat btn-xs btn-labeled btn-warning xhr dest_subcontent-element" title="Ubah"><i class="fa fa-edit"></i></a>
                            <a href="<?php //echo $linkD.'/'.$row['id']; ?>" class="btn btn-flat btn-xs btn-labeled btn-info xhr dest_subcontent-element" title="Detail"><i class="fa fa-folder-open"></i></a>
                            <button class="btn btn-primary btn-xs"><i class="fa fa fa-print"></i></button>
                            <button class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></button>
                        </td>
					</tr>
					<?php 
                    }
			} else {
				echo "<tr><td colspan='7'><div class='alert alert-danger'>Data Reclass Aset Barang Titipan Tidak Ditemukan</div></td></tr>";
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
		$("div.input-group.date.day").datepicker({
			format: "yyyy-mm-dd",
			viewMode: "days",
			minViewMode: "days",
			autoclose: true
		});
	});
</script>