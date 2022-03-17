<div id='modal-data-basic'>
	<form rel="ajax" action="<?php echo $linkForm ?>" class="form-horizontal xhr dest_modal-data-basic" method="post">
		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>"/>
		<div class="panel">
			<div class="panel-body">
				<div class="form-group">
                    <div class="col-md-1"></div>
                    <label class="col-md-4">
                        Cari kode/nama
                    </label>
                    <div class="col-md-4">
                        <input class="form-control" type="text" value="<?php echo set_value('search', isset($search) ? $search : ''); ?>" name="search" id="search">
                    </div>
                    <div class="col-md-3">
                        <?php echo tampilkan_button(); ?>
                    </div>
                </div>
			</div>
			<div class="table-light table-primary" style="overflow: auto;">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Kode</th>
                                <th class="text-center">Nama</th>
                                <th class="text-center">Lokasi Aset</th>
                                <th class="text-center">Status Kepemilikan</th>
                                <th class="text-center">Kondisi</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <?php 
                        if(!empty($content)){

                            ?>
                            <tbody>
                                <?php 
                                $i=$offset + 1;
                                foreach ($content as $key => $row) {
                                    ?>
                                    <tr>
                                        <td><?php echo $i?></td>
                                        <td><?php echo $row['label_kode']?></td>
                                        <td><?php echo $row['label_nama']?></td>
                                        <td><?php echo $row['label_lokasi']?></td>
                                        <td><?php echo $row['label_pemilik']?></td>
                                        <td><?php echo $row['label_kondisi']?></td>
                                        <td>
                                            <a href="javascript:;" 
                                                onclick="pilih_('<?php echo $row['label_id'] ?>', '<?php echo $row['label_kode'] ?>','<?php echo $row['label_nama'] ?>','<?php echo $row['label_lokasi'] ?>','<?php echo $row['label_pemilik'] ?>','<?php echo $row['label_kondisi'] ?>')" class="btn btn-xs btn-primary">Pilih</a>
                                        </td>
                                    </tr>
                                    <?php 
                                    $i++;
                                } ?>
                            </tbody>
                        <?php  }?>
                    </table>
                    <?php if(!empty($content)){ ?>
                        <div class="row">
                            <div class="pull-right">
                                <?php echo $halaman; ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
		</div>
	</form>
</div>
<script type="text/javascript">
    function pilih_(label_id, label_kode, label_nama,label_lokasi,label_pemilik,label_kondisi) {
        $("input[name=label_id]").val(label_id);
        $("input[name=label_lokasi]").val(label_lokasi);
        $("input[name=label_pemilik]").val(label_pemilik);
        $("input[name=label_kondisi]").val(label_kondisi);

        $("input[name=biaya_sewa_label]").val(label_nama);
        $("input[name=biaya_sewa_kode_aset]").val(label_kode);
        
        $('#modal-data-basic').closest('.modal').modal('hide');
    }
</script>






