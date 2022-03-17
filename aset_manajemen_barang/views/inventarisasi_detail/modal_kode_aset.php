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
                        <input class="form-control" type="text" value="<?php echo set_value('search', isset($search) ? $search : ''); ?>" name="search" id="search" />
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
                        <?php //echo "<pre>"; print_r($content); echo "</pre>"; ?>
                        <?php 
                        if(!empty($content)){

                            ?>
                            <tbody>
                                <?php 
                                $i=$offset + 1;
                                foreach ($content as $key => $row) {
                                    ?>
                                    <tr>
                                        <td style="text-align: center;"><?php echo $i?></td>
                                        <td><?php echo $row->kode; ?></td>
                                        <td><?php echo $row->nama; ?></td>
                                        <td style="text-align: center;">
                                            <a href="javascript:;" 
                                                onclick="pilih_('<?php echo $row->id; ?>', '<?php echo $row->kode; ?>','<?php echo $row->nama; ?>')" class="btn btn-xs btn-primary">Pilih</a>
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
    function pilih_(id, kode, nama) {
        $("input[name=kode_aset]").val(id);
        $("input[name=kode_tampil]").val(kode);
        $("input[name=nama_aset]").val(nama);
        $("input[name=label_aset]").val(nama);
        
        $('#modal-data-basic').closest('.modal').modal('hide');
    }
</script>