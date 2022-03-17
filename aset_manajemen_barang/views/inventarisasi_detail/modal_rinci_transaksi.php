<?php
if (isset($content)) {
    foreach ($content as $row) {
?>

<!-- Javascript -->
<script>
	init.push(function() {
		// Validation
		$("#validation-form-ubah").validate({
			ignore: '.ignore, .select2-input',
			focusInvalid: false,
			rules: {
                'denda': {
				  required: true,
				},
			},
            
            messages: {
                'denda': 'Nilai denda harus diisi!'
            }
		});
	});
</script>
<!-- / Javascript -->

<!-- Modal Detail Transaksi -->
<div id="modalDetailT<?php echo $row->id; ?>" class="modal fade" role="dialog" style="display: none; overflow:hidden;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><b>Detail Transaksi</b></h4>
                <b><i><?php //echo $detailTK->nama; ?></i></b>
            </div>
            
            <form method="post" action="<?php echo $form_action; ?>" class="form-horizontal" id="validation-form-ubah">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>"/>
                <input class="hidden" name="perseorangan_potongan_id" value="<?php //echo $dataDenda->id; ?>"/>
                <input class="hidden" name="id_perseorangan" value="<?php //echo $detailTK->id; ?>"/>
                <input class="hidden" name="periode_id" value="<?php //echo $dataDenda->periode; ?>"/>
                <div class="modal-body">
                    <?php //echo "<pre>"; print_r($detailTK); echo "</pre>"; ?>
                    <?php //echo "<pre>"; print_r($dataDenda); echo "</pre>"; ?>
                
                    <div class="form-group">
        				<label class="col-sm-3 control-label" style="text-align: left;">Nilai Denda<font color="red">&nbsp;*</font></label>
        				<div class="col-sm-9">
                            <input class="form-control" id="denda" name="denda" placeholder="Nilai Denda" type="text" autocomplete="off" value="<?php //echo number_format($dataDenda->nilai,0,'',''); ?>" />
        				</div>
        			</div>
                    
                </div> <!-- / .modal-body -->
                
                <div class="modal-footer">
                    <!--<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle"></i>&nbsp;<b>Tutup</b></button>-->
                    <button type="submit" name="tombol_proses" class="btn btn-primary" value="ubah_denda"><i class="fa fa-save"></i>&nbsp;<b>Simpan</b></button>
                </div>
            </form>
        </div> <!-- / .modal-content -->
    </div> <!-- / .modal-dialog -->
</div> <!-- /.modal -->

<?php
    }
}
?>