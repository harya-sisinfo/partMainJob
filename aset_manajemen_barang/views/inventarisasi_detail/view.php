<?php echo Modules::run('breadcrump'); ?>
<?php //$this->load->view('modal_rinci_transaksi'); ?>

<script type="text/javascript">
	$(document).ready(function () {
	    $("select[name='kel_coa']").select2({
			allowClear: true,
			placeholder: "Pilih Kelompok COA"
		});
        
   	    $("select[name='tahun_pengadaan']").select2({
			allowClear: true,
			placeholder: "Pilih Tahun Pengadaan"
		});
        
   	    $("select[name='tahun_pembukuan']").select2({
			allowClear: true,
			placeholder: "Pilih Tahun Pembukuan"
		});
        
        $("select[name='sumber_dana']").select2({
			allowClear: true,
			placeholder: "Pilih Sumber Dana"
		});
        
        $("select[name='kepemilikan']").select2({
			allowClear: true,
			placeholder: "Pilih Kepemilikan"
		});
        
        $("select[name='kondisi']").select2({
			allowClear: true,
			placeholder: "Pilih Kondisi"
		});
        
        $("select[name='status']").select2({
			allowClear: true,
			placeholder: "Pilih Status Aset"
		});
        
        $("select[name='unit_pj']").select2({
			allowClear: true,
			placeholder: "Pilih Unit PJ"
		});
        
        $("#checkAll").click(function(){
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
	});

</script>

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
<?php //echo "<pre>"; print_r($kel_coa); echo "</pre>"; ?>
<?php //echo "<pre>"; print_r($tahun_pengadaan); echo "</pre>"; ?>
<?php //echo "<pre>"; print_r($tahun_pembukuan); echo "</pre>"; ?>
<?php //echo "<pre>"; print_r($sumber_dana); echo "</pre>"; ?>
<?php //echo "<pre>"; print_r($kepemilikan); echo "</pre>"; ?>
<?php //echo "<pre>"; print_r($kondisi); echo "</pre>"; ?>
<?php //echo "<pre>"; print_r($status); echo "</pre>"; ?>
<?php //echo "<pre>"; print_r($unit_kerja); echo "</pre>"; ?>
<?php //echo "<pre>"; print_r($tampilkan); echo "</pre>"; ?>
<?php //echo "<pre>"; print_r($unit_kerja_sistem); echo "</pre>"; ?>
<?php //echo "<pre>"; print_r($kode_unit); echo "</pre>"; ?>
<?php //echo "<pre>"; print_r($unit_kerja_pilih); echo "</pre>"; ?>

<form id="form-filter" rel="ajax" action="<?php echo $linkView; ?>" class="panel form-horizontal xhr dest_subcontent-element" method="post" >
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	<div class="panel-heading">
        <span class="panel-title"><b>Pencarian</b></span>
    </div>
    
    <?php //echo "<pre>"; print_r($dataKelCOA); echo "</pre>"; ?>
	<div class="panel-body">
	   <div class="row form-group">
			<label class="col-sm-3 control-label" style="text-align:left;">Kelompok COA</label>
			<div class="col-sm-4">
				<select class="form-control" name="kel_coa" id="kel_coa">
                <option></option>
                <?php
                    if (!empty($dataKelCOA)) {
                        foreach ($dataKelCOA as $kcoa) {
                            $selected = '';
                            
                            if ($kcoa->id == $kel_coa)
                            $selected = ' selected="selected" ';
                            echo ' <option value="' . $kcoa->id . '" '.$selected.' >' . $kcoa->nama . '</option>';
                        }
                    }
                ?>
                </select>
			</div>	
		</div>
        
        <div class="row form-group">
			<label class="col-sm-3 control-label" style="text-align:left;">Tahun Pengadaan</label>
			<div class="col-sm-3">
                <select class="form-control" name="tahun_pengadaan" id="tahun_pengadaan">
                <option></option>
                <?php
                    for ($i=date("Y"); $i >=1940 ; $i--) {
                        $selected = '';
                        
                        if ($i == $tahun_pengadaan)
                        $selected = ' selected="selected" ';
                        echo '<option value="' . $i . '" '.$selected.' >' . $i . '</option>';
                    }
                ?>
                </select>
			</div>	
		</div>
        
        <div class="row form-group">
			<label class="col-sm-3 control-label" style="text-align:left;">Tahun Pembukuan</label>
			<div class="col-sm-3">
				<select class="form-control" name="tahun_pembukuan" id="tahun_pembukuan">
                <option></option>
                <?php
                    for ($j=date("Y"); $j >=1940 ; $j--) {
                        $selected = '';
                        
                        if ($j == $tahun_pembukuan)
                        $selected = ' selected="selected" ';
                        echo '<option value="' . $j . '" '.$selected.' >' . $j . '</option>';
                    }
                ?>
                </select>
			</div>	
		</div>
        
        <?php //echo "<pre>"; print_r($dataSumberDana); echo "</pre>"; ?>
        <div class="row form-group">
			<label class="col-sm-3 control-label" style="text-align:left;">Sumber Dana</label>
			<div class="col-sm-4">
				<select class="form-control" name="sumber_dana" id="sumber_dana">
                <option></option>
                    <?php
                        if (!empty($dataSumberDana)) {
                            foreach ($dataSumberDana as $sdana) {
                                $selected = '';
                                
                                if ($sdana->id == $sumber_dana)
                                $selected = ' selected="selected" ';
                                echo ' <option value="' . $sdana->id . '" '.$selected.' >' . $sdana->nama . '</option>';
                            }
                        }
                    ?>
                </select>
			</div>	
		</div>
        
        <?php //echo "<pre>"; print_r($dataKepemilikan); echo "</pre>"; ?>
        <div class="row form-group">
			<label class="col-sm-3 control-label" style="text-align:left;">Kepemilikan</label>
			<div class="col-sm-3">
				<select class="form-control" name="kepemilikan" id="kepemilikan">
                <option></option>
                    <?php
                        if (!empty($dataKepemilikan)) {
                            foreach ($dataKepemilikan as $milik) {
                                $selected = '';
                                
                                if ($milik->id == $kepemilikan)
                                $selected = ' selected="selected" ';
                                echo ' <option value="' . $milik->id . '" '.$selected.' >' . $milik->nama . '</option>';
                            }
                        }
                    ?>
                </select>
			</div>	
		</div>
        
        <?php //echo "<pre>"; print_r($dataKondisi); echo "</pre>"; ?>
        <div class="row form-group">
			<label class="col-sm-3 control-label" style="text-align:left;">Kondisi</label>
			<div class="col-sm-3">
				<select class="form-control" name="kondisi" id="kondisi">
                <option></option>
                <?php
                    if (!empty($dataKondisi)) {
                        foreach ($dataKondisi as $kond) {
                            $selected = '';
                            
                            if ($kond->id == $kondisi)
                            $selected = ' selected="selected" ';
                            echo ' <option value="' . $kond->id . '" '.$selected.' >' . $kond->nama . '</option>';
                        }
                    }
                ?>
                </select>
			</div>	
		</div>
        
        <?php //echo "<pre>"; print_r($dataStatusAset); echo "</pre>"; ?>
		<div class="row form-group">
			<label class="col-sm-3 control-label" style="text-align:left;">Status Aset</label>
			<div class="col-sm-3">
				<select class="form-control" name="status" id="status">
                <option></option>
                <?php
                    if (!empty($dataStatusAset)) {
                        foreach ($dataStatusAset as $status_aset) {
                            $selected = '';
                            
                            if ($status_aset->id == $status)
                            $selected = ' selected="selected" ';
                            echo ' <option value="' . $status_aset->id . '" '.$selected.' >' . $status_aset->nama . '</option>';
                        }
                    }
                ?>
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
                <br/>
                <?php
                    if ($tampilkan==1) {
                        $centang = 'checked="checked"';
                    } else {
                        $centang = '';
                    }
                ?>
                <label class="custom-control custom-checkbox" for="tampilkan">
                <input type="checkbox" id="tampilkan" name="tampilkan" class="custom-control-input" <?php echo $centang; ?> value="1" />
                <span class="custom-control-indicator"></span>
                Tampilkan Sub Unit
                </label>
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
			<a href="<?php echo $linkMP ?>" class="btn btn-flat btn-sm btn-labeled btn-success xhr dest_subcontent-element"><span class="btn-label-icon left fa fa-plus"></span> Mutasi Pembelian</a>
            <a href="<?php echo $linkMH ?>" class="btn btn-flat btn-sm btn-labeled btn-success xhr dest_subcontent-element"><span class="btn-label-icon left fa fa-plus"></span> Mutasi Hibah</a>
            <a href="" class="btn btn-flat btn-sm btn-labeled btn-danger xhr dest_subcontent-element"><span class="btn-label-icon left fa fa-trash"></span> Hapus</a>
            <a href="" class="btn btn-flat btn-sm btn-labeled btn-info xhr dest_subcontent-element"><span class="btn-label-icon left fa fa-print"></span> Cetak Barcode</a>
            <a href="" class="btn btn-flat btn-sm btn-labeled btn-info xhr dest_subcontent-element"><span class="btn-label-icon left fa fa-file-text-o"></span> Export Excel</a>
		</div>
	</div>
	<div class="panel-body">
		<div class="table-primary" style="overflow: auto;">
			<table class="table table-bordered table-hover table-striped">
				<thead>
					<tr>
						<th style="text-align: center;">No.</th>
                        <th style="text-align: center;">
    						<div class="checkbox" style="margin: 0;">
    							<label>
    								<input type="checkbox" name="checkAll" id="checkAll" value="1" class="px" />
    							</label>
    						</div> <!-- / .checkbox -->
                        </th>
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
                        <th style="text-align: center;">Henti Ops</th>
					</tr>
				</thead>
				<tbody>
				<?php
                if (!empty($content)) {
					$i =0;
					foreach ($content as $row) {
					$i++;
                    
                    //echo $row->kode_barang; echo "<br>";
                    $kode_barang = str_replace(".","", $row->kode_barang);
                    //echo $kode_barang; echo "<br>";

                    if ($row->isHentiOps==0) {
                        $henti_ops = 'Tidak';
                        $style_bg = '';
                        
                    } else if ($row->isHentiOps==1) {
                        $henti_ops = '<b>Ya</b>';
                        $style_bg = 'style="background-color: LightBlue;"';
                        
                    } else {
                        $henti_ops = '-';
                        $style_bg = '';
                    }

				?>
					<tr <?php echo $style_bg; ?>>
						<td style="text-align: center;"><?php echo ($i+$offset)?></td>
                        <td style="text-align: center;">
    						<div class="checkbox" style="margin: 0;">
    							<label>
    								<input type="checkbox" name="pilih[<?php echo $row->id; ?>]" value="1" class="px" />
    							</label>
    						</div> <!-- / .checkbox -->
                        </td>
						<td>
                            <a href="<?php echo $linkUP.'/'.$row->id; ?>" class="btn btn-flat btn-xs btn-labeled btn-warning xhr dest_subcontent-element" title="Update Inventarisasi"><i class="fa fa-edit"></i></a>
                            <a href="<?php echo $linkDA.'/'.$row->id; ?>" class="btn btn-flat btn-xs btn-labeled btn-info xhr dest_subcontent-element" title="Detail Aset"><i class="fa fa-folder-open"></i></a>
                            <a href="<?php echo $linkDT.'/'.$kode_barang.'/'.$row->unitkerjaId; ?>" target="_blank" class="btn btn-flat btn-xs btn-labeled btn-primary xhr dest_subcontent-element" title="Detail Transaksi"><i class="fa fa-search-plus"></i></a>
                            <!--<a href="#modalDetailT<?php echo $row->id; ?>" class="btn btn-primary btn-xs" title="Detail Transaksi" data-toggle="modal"><i class="fa fa-search-plus"></i></a><br />-->
                            <?php
                            $jenis_aset = substr($row->kode_barang,0,1);
                            if ($jenis_aset == 2) {
                            ?>
                            <a href="<?php echo $linkKA.'/'.$row->id; ?>" class="btn btn-flat btn-xs btn-labeled btn-success xhr dest_subcontent-element" title="Atur Koordinat Aset"><i class="fa fa-globe"></i></a>
                            <?php
                            }
                            ?>
                        </td>
						<td>
                            <u>
                            <?php
                                if ($row->ruangKode!="") {
                                    $ruang = substr($row->ruangKode,0,5);
                                } else {
                                    $ruang = '999999';
                                }
                                echo $row->unitkerjaKode.'.'.$ruang.'.'.$row->thn_pembelian;
                            ?></u><br />
                            <?php echo $row->kode_barang; ?>
                        </td>
						<td><?php echo $row->label_aset; ?></td>
                        <td><?php echo $row->merk;  ?></td>
                        <td><?php echo $row->spesifikasi;  ?></td>
                        <td>
                            <?php
                                if ($row->lokasi!="") {
                                    echo $row->lokasi;
                                } else {
                                    echo $row->gedung.' &raquo; '.$row->ruang;
                                }
                            ?>
                        </td>
                        <td><?php echo $row->unit_pj;  ?></td>
                        <td style="text-align: center;"><?php echo $row->thn_pembelian;  ?></td>
                        <td style="text-align: right;">
                            <?php echo number_format($row->nilai_perolehan,2,",",".")?>
                        </td>
                        <td><?php echo $row->tgl_buku;  ?></td>
                        <td>0,00</td>
                        <td style="text-align: center;">
                        <?php
                            if ($row->kondisi==1) {
                                echo $row->kondisi;
                            } else {
                                echo $row->kondisi;
                                echo '<br>';
                                echo '<i>('.$row->status_aset.')</i>';
                            }
                             
                        ?>
                        </td>
                        <td><?php echo $row->kepemilikan;  ?></td>
                        <td><?php echo $row->nomor_spp;  ?></td>
                        <td><?php echo $row->nomor_surat;  ?></td>
                        <td><?php echo $row->no_ref;  ?></td>
                        <td style="text-align: center;"><?php echo $henti_ops; ?></td>
					</tr>
					<?php 
                    }
			} else {
				echo "<tr><td colspan='19'><div class='alert alert-danger'>Data Inventarisasi Detail Tidak Ditemukan</div></td></tr>";
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