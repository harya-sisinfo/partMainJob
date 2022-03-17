<?php echo Modules::run('breadcrump'); ?>

<form method="post" action="<?php echo $form_action; ?>" class="panel form-horizontal xhr dest_subcontent-element" >
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	<input type="hidden" name="lastStage" value="<?php echo $this->security->get_csrf_hash(); ?>" />
    
   	<div class="panel-heading">
		<span class="panel-title"><b>Tambah</b> Pengembangan Aset Langsung</span>
	</div>

    <?php $date = date("Y-m-d");//echo $date; ?>
	<div class="panel-body">
        <div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Tanggal Pembukuan&nbsp;<font color="red">*</font></label>
					<div class="col-sm-3">
						<div class="input-group date day">
							<input type="text" name="tanggal_pembukuan" class="form-control" placeholder="Tanggal Pembukuan" autocomplete="off" value="<?php echo $date; ?>" />
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						</div>
					</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Keterangan</label>
					<div class="col-sm-6">
                        <textarea class="form-control" name="keterangan" rows="4" cols="60"></textarea>
					</div>
				</div>
			</div>
		</div>
    </div>
    
    <table style="clear: both" class="table table-bordered table-striped">
        <tbody>
            <tr>
                <td><b>RINCIAN PEROLEHAN</b></td>
            </tr>
        </tbody>
    </table>
    
   	<div class="panel-body">
        <div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Asal Perolehan&nbsp;<font color="red">*</font></label>
					<div class="col-sm-3">
                        <div class="form-group">
                        	<div class="col-sm-10">
                        		<div class="radio">
                        			<label>
                        				<input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" class="px" checked="" />
                        				<span class="lbl">Manual</span>
                        			</label>
                        		</div> <!-- / .radio -->
                        		<div class="radio">
                        			<label>
                        				<input type="radio" name="optionsRadios" id="optionsRadios2" value="option2" class="px" />
                        				<span class="lbl">Integrasi</span>
                        			</label>
                        		</div> <!-- / .radio -->
                        	</div> <!-- / .col-sm-10 -->
                        </div>
					</div>
				</div>
			</div>
		</div>
        
        <!-- / MANUAL -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Nomor Referensi&nbsp;<font color="red">*</font></label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="Nomor Referensi" class="form-control" name="nomor_referensi" />
					</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Nilai Perolehan</label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="Nilai Perolehan" class="form-control" name="nilai_perolehan" id="nilai_perolehan" value="0,00" style="text-align: right;" />
					</div>
				</div>
			</div>
		</div>
        
        <!-- / INTEGRASI -->
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Nomor Referensi Pengadaan</label>
					<div class="col-sm-4">
                        <select class="form-control select-noref" name="noref" id="noref" >
                            <option></option>
                        </select>
					</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Nama Paket</label>
					<div class="col-sm-4">Nama Paket</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Nilai Perolehan</label>
					<div class="col-sm-4">Nilai Perolehan</div>
				</div>
			</div>
		</div>
    </div>
    
    <table style="clear: both" class="table table-bordered table-striped">
        <tbody>
            <tr>
                <td><b>RINCIAN ASET</b></td>
            </tr>
            <tr>
                <td>
                    <b>Detail Barang</b>
                    <a href="<?php //echo $linkAdd ?>" class="btn btn-flat btn-sm btn-labeled btn-success xhr dest_subcontent-element pull-right"><span class="btn-label-icon left fa fa-plus"></span> Tambah Aset</a>
                </td>
            </tr>
        </tbody>
    </table>
    
    <div class="panel-body">
    	<div class="table-primary" style="overflow: auto;">
    		<table class="table table-bordered table-hover table-striped">
    			<thead>
    				<tr>
    					<th style="text-align: center; vertical-align: center">No.</th>
                        <th style="text-align: center; vertical-align: center">Kode Aset</th>
    					<th style="text-align: center; vertical-align: center">Nama Aset</th>
    					<th style="text-align: center; vertical-align: center">Merk</th>
                        <th style="text-align: center; vertical-align: center">Unit</th>
    					<th style="text-align: center; vertical-align: center">Tambah Umur Ekonomis (Bulan) *</th>
    					<th style="text-align: center; vertical-align: center">Nilai Kapitalisasi *</th>
                        <th style="text-align: center; vertical-align: center">Total</th>
                        <th style="text-align: center; vertical-align: center">Aksi</th>
    				</tr>
    			</thead>
                
    			<tbody>
    			<?php
                    $detailTransaksi = "";
                    if (!empty($detailTransaksi)) {
    					$i =0;
    					foreach ($detailTransaksi as $row) {
    					$i++;
    				?>
    					<tr>
    						<td style="text-align: center;"><?php echo $i; ?></td>
                            <td style="text-align: center;"><?php echo $row->kodeSubKel; ?></td>
                            <td style="text-align: center;"><?php echo $row->brgNama; ?></td>
                            <td style="text-align: center;"><?php echo $row->nup; ?></td>
                            <td style="text-align: center;"><?php echo $row->tglBuku; ?></td>
                            <td style="text-align: center;"><?php echo $row->jnsTrn; ?></td>
                            <td style="text-align: center;"><?php echo $row->jnsUr; ?></td>
                            <td style="text-align: center;"><?php echo number_format($row->nilai,2,',','.'); ?></td>
                        </tr>
    			<?php 
                        }
    			} else {
    				echo "<tr><td colspan='17'><div class='alert alert-danger'>Detail Barang Tidak Ditemukan</div></td></tr>";
    			}
    			?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="panel-body">
   	    <div class="row text-center">
            <div class="col-sm-8">
        		<div class="panel-footer">
                    <button class="btn btn-flat btn-primary" type="submit" name="tombol_proses" value="simpan"><span class="btn-label-icon left fa fa-save"></span><b>Simpan</b></button>
                    <button class="btn btn-flat btn-default" id=""><span class="btn-label-icon left fa fa-undo"></span><b>Batal</b></button>
        		</div>
            </div>
    	</div>
    </div>
</form>

<script type="text/javascript">
	$(document).ready(function () {
        $("select[name='noref']").select2({
			allowClear: true,
			placeholder: "Nomor Referensi Pengadaan"
		});

		$("div.input-group.date.day").datepicker({
			format: "yyyy-mm-dd",
			viewMode: "days",
			minViewMode: "days",
			autoclose: true
		});
	});
</script>