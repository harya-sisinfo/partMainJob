<div id='modal-data-basic'>
    <form rel="ajax" action="<?php echo $linkForm ?>" class="form-horizontal xhr dest_modal-data-basic" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
        <div class="panel">
            <div class="panel-body">
                <div class="form-group">
                    <div class="col-md-1"></div>
                    <label class="col-md-4">
                        Cari kode/nama
                    </label>
                    <div class="col-md-4">
                        <input class="form-control" type="text" value="<?php echo $search; ?>" name="search" id="search">
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
                        <tbody>
                            <?php
                            if (!empty($content)) {
                                $i = $offset + 1;
                                foreach ($content as $key => $row) {
                            ?>
                                    <tr>
                                        <td><?php echo $i ?></td>
                                        <td><?php echo $row['kode'] ?></td>
                                        <td><?php echo $row['nama'] ?></td>
                                        <td><a href="javascript:;" onclick="pilih_rincian('<?php echo $row['id'] ?>','<?php echo $row['kode'] . ' - ' . $row['nama'] ?>','<?php echo $row['idBidang'] ?>','<?php echo $row['kodeBidang'] . ' - ' . $row['namaBidang'] ?>')" class="btn btn-xs btn-primary">Pilih</a></td>
                                    </tr>
                            <?php
                                    $i++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php if (!empty($content)) { ?>
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
    function pilih_rincian(id_kelompok, nama_kelompok, id_bidang, nama_bidang) {
        $("input[name='id_bidang']").val(id_bidang);
        $("input[name='nama_bidang']").val(nama_bidang);
        $("input[name='id_kelompok']").val(id_kelompok);
        $("input[name='nama_kelompok']").val(nama_kelompok);
        $("input[name='id_sub_kelompok']").val('');
        $("input[name='nama_sub_kelompok']").val('');
        $('#modal-data-basic').closest('.modal').modal('hide');
    }
</script>