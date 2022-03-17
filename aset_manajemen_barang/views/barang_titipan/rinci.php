<?php echo Modules::run('breadcrump'); ?>

<table style="clear: both" class="table table-bordered table-striped">
    <tbody>
        <?php //echo "<pre>"; print_r($detailAset); echo "</pre>"; ?>
        <tr>
        	<td style="width: 25%;"><b>Jenis Barang</b></td>
        	<td><?php echo substr($detailAset->golbrgNama, 7); ?></td>
        </tr>
        
        <tr>
        	<td><b>Nomor Referensi / Kuitansi</b></td>
        	<td><?php echo $detailAset->noref; ?></td>
        </tr>
        
        <tr>
        	<td><b>Kode Barang</b></td>
        	<td><b><?php echo $detailAset->invDetKodeBarang; ?></b></td>
        </tr>
        
        <tr>
        	<td><b>Nama Barang</b></td>
        	<td><?php echo $detailAset->namaAset; ?></td>
        </tr>
    
        <tr>
        	<td><b>Label Barang</b></td>
        	<td><?php echo $detailAset->invDetMstLabel; ?></td>
        </tr>
        
        <tr>
        	<td><b>Merk</b></td>
        	<td><?php echo $detailAset->invDetMerek; ?></b>
            </td>
        </tr>
        
        <tr>
        	<td><b>Spesifikasi</b></td>
        	<td><?php echo $detailAset->invDetSpesifikasi; ?></td>
        </tr>
        
        <tr>
        	<td><b>Tanggal Pembelian</b></td>
        	<td><?php echo $detailAset->invDetTglPembelian; ?></td>
        </tr>
        
        <tr>
        	<td><b>Sumber Dana</b></td>
        	<td><?php echo $detailAset->sumberdanaNama; ?></td>
        </tr>
        
        <?php
        $golongan = $detailAset->golbrgId;
        
        if (($golongan==2) or ($golongan==5)) {
            $label_perolehan = 'Nilai Perolehan per Meter';
            $nilai_perolehan = $detailAset->invNilaiFaktur/$detailAset->invLuasTanah;
        ?>
        <tr>
        	<td><b>Luas Tanah</b></td>
        	<td><?php echo $detailAset->invLuasTanah; ?>&nbsp;&#13217;</td>
        </tr>
        <?php
        } else {
            $label_perolehan = 'Nilai Perolehan per Satuan';
            $nilai_perolehan = $detailAset->invDetNilaiPerolehanSatuan/1;
        ?>
        <tr>
        	<td><b>Jumlah Barang</b></td>
        	<td><?php echo '1 '.$detailAset->satuanbrgNama; ?></td>
        </tr>
        
        <tr>
        	<td><b>Umur Ekonomis</b></td>
        	<td><?php echo $detailAset->invDetUmurEkonomis; ?> bulan</td>
        </tr>
        <?php
        }

        if (($golongan==2) or ($golongan==5)) {
        ?>
        <tr>
        	<td><b>Nilai Kuitansi</b></td>
        	<td>Rp.<?php echo number_format($detailAset->invNilaiFaktur,2,',','.'); ?></td>
        </tr>
        <?php
        }
        ?>
        
        <tr>
        	<td><b><?php echo $label_perolehan; ?></b></td>
        	<td>Rp.<?php echo number_format(($nilai_perolehan),2,',','.'); ?></td>
        </tr>
        
        <tr>
        	<td><b>Total Perolehan</b></td>
        	<td><b>Rp.<?php echo number_format($nilai_perolehan,2,',','.'); ?></b></td>
        </tr>
        
        <?php
        if (($golongan==2) or ($golongan==4) or ($golongan==5)) {
        ?>
        <tr>
        	<td><b>Lokasi</b></td>
        	<td><?php echo $detailAset->invDetLokasi; ?></td>
        </tr>
        <?php
        }
        
        if (($golongan==2) or ($golongan==5)) {
        ?>
        <tr>
        	<td><b>Kegunaan</b></td>
        	<td><?php echo $detailAset->invDetKegunaan; ?></td>
        </tr>
        <?php
        }
        ?>
        
        <tr>
        	<td><b>Kepemilikan</b></td>
        	<td><?php echo $detailAset->kepemilikanbrgNama; ?></td>
        </tr>
        
        <tr>
        	<td><b>Penguasaan</b></td>
        	<td><?php echo $detailAset->statuspengbrgNama; ?></td>
        </tr>
        
        <?php
        if ($golongan!=5) {
        ?>
        <tr>
        	<td><b>Kondisi Barang</b></td>
        	<td><?php echo $detailAset->kondisi; ?></td>
        </tr>
        <?php
        }
        
        if (($golongan==3) or ($golongan==4) or ($golongan==6) or ($golongan==8)) {
        ?>
        <tr>
        	<td><b>Lokasi Kampus</b></td>
        	<td><?php echo $detailAset->kampusNama; ?></td>
        </tr>
        <?php
        }
        
        if (($golongan==3) or ($golongan==6) or ($golongan==8)) {
        ?>
        <tr>
        	<td><b>Lokasi Barang</b></td>
        	<td><?php echo $detailAset->lokasiAset; ?></td>
        </tr>
        <?php
        }
        ?>
        
        <!--<tr>
        	<td><b>Kartu Identitas Barang</b></td>
        	<td><?php echo $detailAset->invDetIdentitasBarang; ?></td>
        </tr>-->
        
        <tr>
        	<td><b>Status Barang</b></td>
        	<td><?php echo $detailAset->statusAset; ?></td>
        </tr>
        
        <tr>
        	<td><b>Keterangan Lain</b></td>
        	<td><?php echo $detailAset->invDetKeteranganLain; ?></td>
        </tr>
        
        <tr>
        	<td><b>Unit Penanggung Jawab Barang</b></td>
        	<td><?php echo $detailAset->unitkerjaNama; ?></td>
        </tr>
        
        <tr>
        	<td><b>Foto Barang</b></td>
        	<td></td>
        </tr>
    </tbody>
</table>

<table style="clear: both" class="table">
    <tbody>
        <tr>
        	<td>
                <a href="<?php echo $linkView ?>" class="btn btn-flat btn-default xhr dest_subcontent-element pull-right"><span class="btn-label-icon left fa fa-chevron-circle-left"></span>Kembali</a>
            </td>
        </tr>
    </tbody>
</table>