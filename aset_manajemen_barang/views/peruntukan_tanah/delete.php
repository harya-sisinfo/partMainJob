<div id="modal-data-basic" >
	<form method="post" rel="ajax" action="<?php echo site_url('aset_manajemen_barang/peruntukan_tanah/delete_proses'); ?>" class="form-horizontal xhr dest_modal-data-basic">
		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
		<div class="form-group">
			<div class="alert alert-danger alert-dark">
				Apakah anda yakin untuk menghapus data? Silahkan masukan alasan penghapusan data pada kolom dibawah ini.
			</div>
			<input type="hidden" name="id" value="<?php echo $id?>">
			<textarea class="form-control" required placeholder="Alasan penghapusan data" name="alasan"></textarea>
		</div>
		<div class="row text-right">
			<div class="panel-footer">
				<button class="btn btn-success" data-dismiss="modal">Batal</button>
				<button class="btn btn-danger" value="">Delete</button>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	$(document).ready(function () {

		$(".modal").on('show.bs.modal', function (e) {
			$(this).removeAttr('tabindex');
		});

	});
</script>