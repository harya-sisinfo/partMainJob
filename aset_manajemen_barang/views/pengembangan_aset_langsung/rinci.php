<?php echo Modules::run('breadcrump'); ?>

<?php //echo "<pre>"; print_r($detail); echo "</pre>"; ?>
<div class="panel">
    <table style="clear: both" class="table table-bordered table-striped">
        <tbody>
            <tr>
           	    <td style="text-align:left;background-color:#bfbfbf;" colspan="2"><b>DETAIL PENGEMBANGAN ASET</b></td>
            </tr>
            <tr>
                <td style="width: 25%;">Tanggal Pembukuan</td>
                <td><?php echo $detail->tglBuku; ?></td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td><?php echo $detail->keterangan; ?></td>
            </tr>
            <tr>
           	    <td style="text-align:left;background-color:#bfbfbf;" colspan="2"><b>RINCIAN KAPITALISASI</b></td>
            </tr>
            <tr>
                <td>Asal Perolehan</td>
                <td>
                <?php
                    if ($detail->asal!="") {
                        echo '<span class="label label-info">Integrasi</span>';
                    } else {
                        echo '<span class="label label-info">Manual</span>';
                    }
                ?>
                </td>
            </tr>
            <tr>
                <td>Nomor Referensi</td>
                <td><?php echo $detail->noref; ?></td>
            </tr>
            <tr>
                <td><b>Nilai Perolehan</b></td>
                <td><b>Rp<?php echo number_format($detail->total,2,',','.'); ?></b></td>
            </tr>
            <tr>
           	    <td style="text-align:left;background-color:#bfbfbf;" colspan="2"><b>RINCIAN ASET</b></td>
            </tr>
        </tbody>
    </table>

    <div class="panel-body">
    	<div class="table-primary" style="overflow: auto;">
    		<table class="table table-bordered table-hover table-striped">
    			<thead>
    				<tr>
    					<th style="text-align: center; vertical-align: middle">No.</th>
                        <th style="text-align: center; vertical-align: middle">Kode Aset</th>
    					<th style="text-align: center; vertical-align: middle">Nama Aset</th>
    					<th style="text-align: center; vertical-align: middle">Merek</th>
                        <th style="text-align: center; vertical-align: middle">Unit</th>
    					<th style="text-align: center; vertical-align: middle">Tambah Umur<br />Ekonomis (Bulan)</th>
    					<th style="text-align: center; vertical-align: middle">Nilai<br />Kapitalisasi</th>
    				</tr>
    			</thead>
                <?php //echo "<pre>"; print_r($rincianAset); echo "</pre>"; ?>
    			<tbody>
    			<?php
                    if (!empty($rincianAset)) {
    					$i =0;
    					foreach ($rincianAset as $row) {
    					$i++;
    				?>
    					<tr>
    						<td style="text-align: center;"><?php echo $i; ?></td>
                            <td style="text-align: center;"><?php echo $row->kode; ?></td>
                            <td style="text-align: center;"><?php echo $row->nama; ?></td>
                            <td style="text-align: center;"><?php echo $row->merek; ?></td>
                            <td style="text-align: center;"><?php echo $row->unit; ?></td>
                            <td style="text-align: center;"><?php echo $row->umur; ?></td>
                            <td style="text-align: center;"><?php echo number_format($row->nilai,2,',','.'); ?></td>
                        </tr>
    			<?php 
                        }
    			} else {
    				echo "<tr><td colspan='17'><div class='alert alert-danger'>Rincian Aset Tidak Ditemukan</div></td></tr>";
    			}
    			?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<table style="clear: both" class="table">
    <tbody>
        <tr>
        	<td>
                <a href="<?php echo $linkView ?>" class="btn btn-flat btn-default xhr dest_subcontent-element pull-right"><span class="btn-label-icon left fa fa-chevron-circle-left"></span>Kembali</a>
            </td>
        </tr>
    </tbody>
</table>