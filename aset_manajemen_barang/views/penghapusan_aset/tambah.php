<?php echo Modules::run('breadcrump'); ?>

<form method="post" action="<?php echo $form_action; ?>" class="panel form-horizontal xhr dest_subcontent-element" >
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	<input type="hidden" name="lastStage" value="<?php echo $this->security->get_csrf_hash(); ?>" />
    
   	<div class="panel-heading">
		<span class="panel-title"><b>Tambah</b> Penghapusan Aset</span>
	</div>

    <?php $date = date("Y-m-d");//echo $date; ?>
	<div class="panel-body">
        <div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Asal SK Penghapusan&nbsp;<font color="red">*</font></label>
					<div class="col-sm-3">
                        <div class="form-group">
                        	<div class="col-sm-10">
                        		<div class="radio">
                        			<label>
                        				<input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" class="px" checked="" />
                        				<span class="lbl">Lelang</span>
                        			</label>
                        		</div> <!-- / .radio -->
                        		<div class="radio">
                        			<label>
                        				<input type="radio" name="optionsRadios" id="optionsRadios2" value="option2" class="px" />
                        				<span class="lbl">Manual</span>
                        			</label>
                        		</div> <!-- / .radio -->
                        	</div> <!-- / .col-sm-10 -->
                        </div>
					</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Jenis Penghapusan&nbsp;<font color="red">*</font></label>
					<div class="col-sm-6">
                        <select class="form-control select-jenis-hapus" name="jenis_penghapusan" id="jenis_penghapusan" >
                            <option></option>
                        </select>
					</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Paket Penghapusan&nbsp;<font color="red">*</font></label>
					<div class="col-sm-6">
                        <select class="form-control select-paket-hapus" name="paket_penghapusan" id="paket_penghapusan" >
                            <option></option>
                        </select>
					</div>
				</div>
			</div>
		</div>
        
        <div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Tanggal Penghapusan&nbsp;<font color="red">*</font></label>
					<div class="col-sm-3">
						<div class="input-group date day">
							<input type="text" name="tanggal_penghapusan" class="form-control" placeholder="Tanggal Penghapusan" autocomplete="off" value="<?php echo $date; ?>" />
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						</div>
					</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>File BA/SK&nbsp;<font color="red">*</font></label>
					<div class="col-sm-6">
						<label class="custom-file px-file">
							<input type="file" class="custom-file-input" name="to_doc[]" onchange="checkFileUpload(this,['jpg','jpeg','pdf']);" multiple >
							<span class="custom-file-control form-control">

								<?php 
								if(!empty($data_doc['to_doc_nama'])){
									?><?php 
									echo (set_value('to_doc[]')) ? set_value('to_doc[]') : $data_doc['to_doc_nama']; ?>
									<?php 
								}else{
									echo "Pilih file ... ";
								}
							?></span>
							<div class="px-file-buttons">
								<button type="button" class="btn px-file-clear">Clear</button>
								<button type="button" class="btn btn-primary px-file-browse">Browse</button>
							</div>
						</label>

						<p class='help-block'><i>Format file <b>*.jpg|.jpeg|.pdf</b></i></p>
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
                <td>
                    <b>List Barang&nbsp;<font color="red">*</font></b>
                    <a href="<?php //echo $linkAdd ?>" class="btn btn-flat btn-sm btn-labeled btn-success xhr dest_subcontent-element pull-right"><span class="btn-label-icon left fa fa-plus"></span> Tambah</a>
                </td>
            </tr>
        </tbody>
    </table>
    
    <div class="panel-body">
    	<div class="table-primary" style="overflow: auto;">
    		<table class="table table-bordered table-hover table-striped">
    			<thead>
    				<tr>
    					<th style="text-align: center; vertical-align: center">No</th>
                        <th style="text-align: center; vertical-align: center">Kode Barang</th>
    					<th style="text-align: center; vertical-align: center">Nama</th>
    					<th style="text-align: center; vertical-align: center">Merk</th>
                        <th style="text-align: center; vertical-align: center">Tgl Perolehan</th>
    					<th style="text-align: center; vertical-align: center">Nilai Perolehan</th>
    					<th style="text-align: center; vertical-align: center">Unit Kerja</th>
                        <th style="text-align: center; vertical-align: center">Keterangan</th>
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
        $("select[name='jenis_penghapusan']").select2({
			allowClear: true,
			placeholder: "Pilih Jenis Penghapusan"
		});
        
        $("select[name='paket_penghapusan']").select2({
			allowClear: true,
			placeholder: "Pilih Paket Penghapusan"
		});

		$("div.input-group.date.day").datepicker({
			format: "yyyy-mm-dd",
			viewMode: "days",
			minViewMode: "days",
			autoclose: true
		});
	});
</script>