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
                <div class="table-light table-primary" style="overflow: auto;">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Kode</th>
                                <th class="text-center">Nama</th>
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
                                        <td><?php echo $row['KIB']?></td>
                                        <td><?php echo $row['nama_aset']?></td>
                                        <td>
                                            <a href="javascript:;" 
                                                onclick="pilih_('<?php echo $row['id'] ?>', '<?php echo $row['KIB'] ?>','<?php echo $row['nama_aset'] ?>','<?php echo $row['luas'] ?>','<?php echo $row['keterangan'] ?>','<?php echo $row['penggunaan'] ?>','<?php echo $row['unit_pj'] ?>')" class="btn btn-xs btn-primary">Pilih</a>
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
    function pilih_(id, kib, nama_aset,luas,keterangan,penggunaan,unit_pj) {

        $("input[name=invdet]").val(id);
        $("input[name=nama]").val(nama_aset);
        $("input[name=kode]").val(kib);
        $("input[name=panjang]").val('');
        $("input[name=lebar]").val('');
        $("input[name=luas]").val(luas);
        $("input[name=keterangan]").val(keterangan);
        $("select[name='penggunaan']").val(penggunaan).trigger('change');
        $("input[name=unitnama]").val(unit_pj);
        
        $('#modal-data-basic').closest('.modal').modal('hide');
    }
</script>