<?php echo Modules::run('breadcrump'); ?>

<?php //echo "<pre>"; print_r($detailTransaksi); echo "</pre>"; ?>

<?php //echo "<pre>"; print_r($kode_barang); echo "</pre>"; ?>
<?php //echo "<pre>"; print_r($kode_unit); echo "</pre>"; ?>
<div class="panel">
	<div class="panel-heading">
		<span class="panel-title">
        <b>
        <?php
            $judul_barang = substr($kode_barang,0,1).'.'.substr($kode_barang,1,2).'.'.substr($kode_barang,3,2).'.'.substr($kode_barang,5,2).'.'.substr($kode_barang,7,3).'.'.substr($kode_barang,10);
            echo $judul_barang;
        ?>
        </b>
        </span>
	</div>

    <div class="panel-body">
    	<div class="table-primary" style="overflow: auto;">
    		<table class="table table-bordered table-hover table-striped">
    			<thead>
                    <tr>
                        <th style="text-align: center;" colspan="2">Sub-Sub Kelompok Barang</th>
                        <th style="text-align: center; vertical-align: middle" rowspan="2">NUP</th>
                        <th style="text-align: center; vertical-align: middle" rowspan="2">Tgl Perolehan</th>
                        <th style="text-align: center; vertical-align: middle" rowspan="2">Tgl Buku</th>
                        <th style="text-align: center; vertical-align: middle" rowspan="2">Umur<br />Ekonomis</th>
                        <th style="text-align: center; vertical-align: middle" rowspan="2">Sisa<br />Umur</th>
    					<th style="text-align: center; vertical-align: middle" rowspan="2">Jenis<br />Transaksi</th>
    					<th style="text-align: center; vertical-align: middle" rowspan="2">Uraian Transaksi</th>
                        <th style="text-align: center; vertical-align: middle" rowspan="2">Nilai</th>
                        <th style="text-align: center; vertical-align: middle" rowspan="2">Saldo</th>
                    </tr>
    				<tr>
                        <th style="text-align: center; vertical-align: center">Kode</th>
    					<th style="text-align: center; vertical-align: center">Uraian</th>
    				</tr>
    			</thead>
                
    			<tbody>
    			<?php
                    if (!empty($detailTransaksi)) {
    					//$i =0;
                        $saldo = 0;
    					foreach ($detailTransaksi as $row) {
    					//$i++;
                        $saldo = $saldo + $row->nilai;
    				?>
    					<tr>
                            <td style="text-align: center;"><?php echo $row->kodeSubKel; ?></td>
                            <td style="text-align: center;"><?php echo $row->brgNama; ?></td>
                            <td style="text-align: center;"><?php echo $row->nup; ?></td>
                            <td style="text-align: center;"><?php echo $row->tglBeli; ?></td>
                            <td style="text-align: center;"><?php echo $row->tglBuku; ?></td>
                            <td style="text-align: center;"><?php echo $row->umurEko; ?></td>
                            <td style="text-align: center;"><?php echo $row->sisaUmur; ?></td>
                            <td style="text-align: center;"><?php echo $row->jnsTrn; ?></td>
                            <td style="text-align: center;"><?php echo $row->jnsUr; ?></td>
                            <td style="text-align: center;">
                                <?php echo number_format($row->nilai,2,',','.'); ?>
                            </td>
                            <td style="text-align: center;">
                                <?php echo number_format($saldo,2,',','.'); ?>
                            </td>
                        </tr>
                        
    			<?php 
                        }
                ?>
                        
                        <tr style="background-color: aliceblue;">
                            <td style="text-align: center;" colspan="9"><b>TOTAL (NILAI BUKU)</b></td>
                            <td style="text-align: center;">
                                <b><?php echo number_format($saldo,2,',','.'); ?><b>
                            </td>
                            <td style="text-align: center;"></td>
                        </tr>
                        
                
                <?php
    			} else {
    				echo "<tr><td colspan='17'><div class='alert alert-danger'>Detail Transaksi Tidak Ditemukan</div></td></tr>";
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