<?php echo Modules::run('breadcrump'); ?>

<form method="post" action="<?php echo $form_action; ?>" class="panel form-horizontal xhr dest_subcontent-element" >
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	<input type="hidden" name="lastStage" value="<?php echo $this->security->get_csrf_hash(); ?>" />
    
   	<div class="panel-heading">
		<span class="panel-title"><b>Tambah</b> Usulan Penghapusan Aset</span>
	</div>

    <?php $date = date("Y-m-d");//echo $date; ?>
	<div class="panel-body">
        <div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>No. Surat&nbsp;<font color="red">*</font></label>
					<div class="col-sm-4">
						<input type="text" autocomplete="off" placeholder="Nomor Surat" class="form-control" name="nomor_surat" />
					</div>
				</div>
			</div>
		</div>
        
        <div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Tanggal Surat&nbsp;<font color="red">*</font></label>
					<div class="col-sm-3">
						<div class="input-group date day">
							<input type="text" name="tanggal_surat" class="form-control" placeholder="Tanggal Surat" autocomplete="off" value="<?php echo $date; ?>" />
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						</div>
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
					<label class="col-sm-2 control-label" style='text-align:left;'>Latar Belakang Penghapusan&nbsp;<font color="red">*</font></label>
					<div class="col-sm-6">
                        <select class="form-control select-latar-belakang" name="latar_belakang" id="latar_belakang" >
                            <option></option>
                        </select>
					</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Unit Kerja Pengusul&nbsp;<font color="red">*</font></label>
					<div class="col-sm-6">
                        <select class="form-control select-unit-kerja" name="unit_kerja" id="unit_kerja" >
                            <option></option>
                        </select>
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
                <td><b>Penanggung Jawab</b></td>
            </tr>
        </tbody>
    </table>
    
   	<div class="panel-body">
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Nama&nbsp;<font color="red">*</font></label>
					<div class="col-sm-6">
						<input type="text" autocomplete="off" placeholder="Nama" class="form-control" name="nama" value="Prof. Dr. Ir. Bambang Agus Kironoto" />
					</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>NIP&nbsp;<font color="red">*</font></label>
					<div class="col-sm-6">
						<input type="text" autocomplete="off" placeholder="NIP" class="form-control" name="nip" value="196308131988031002" />
					</div>
				</div>
			</div>
		</div>
        
		<div class="row">
			<div class="col-sm-12">
				<div class="row form-group">
					<label class="col-sm-2 control-label" style='text-align:left;'>Jabatan&nbsp;<font color="red">*</font></label>
					<div class="col-sm-6">
						<input type="text" autocomplete="off" placeholder="Jabatan" class="form-control" name="jabatan" value="Wakil Rektor Bidang Sumber Daya Manusia dan Aset" />
					</div>
				</div>
			</div>
		</div>
    </div>
    
    <div class="panel">
    	<div class="panel-heading">
    		<span class="panel-title"><b>Daftar Barang&nbsp;<font color="red">*</font></b></span>
    		<div class="panel-heading-controls">
    			<a href="<?php //echo $linkMP ?>" class="btn btn-flat btn-sm btn-labeled btn-success xhr dest_subcontent-element"><span class="btn-label-icon left fa fa-plus"></span> Tambah BA</a>
                <a href="<?php //echo $linkMH ?>" class="btn btn-flat btn-sm btn-labeled btn-success xhr dest_subcontent-element"><span class="btn-label-icon left fa fa-plus"></span> Tambah Aset</a>
    		</div>
    	</div>
        
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
        					<th style="text-align: center; vertical-align: center">Unit</th>
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
        				echo "<tr><td colspan='17'><div class='alert alert-danger'>Daftar Barang Tidak Ditemukan</div></td></tr>";
        			}
        			?>
                    </tbody>
                </table>
            </div>
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
        $("select[name='paket_penghapusan']").select2({
			allowClear: true,
			placeholder: "Pilih Paket Penghapusan"
		});
        
        $("select[name='latar_belakang']").select2({
			allowClear: true,
			placeholder: "Pilih Latar Belakang Penghapusan"
		});
        
        $("select[name='unit_kerja']").select2({
			allowClear: true,
			placeholder: "Pilih Unit Kerja Pengusul"
		});

		$("div.input-group.date.day").datepicker({
			format: "yyyy-mm-dd",
			viewMode: "days",
			minViewMode: "days",
			autoclose: true
		});
	});
</script>