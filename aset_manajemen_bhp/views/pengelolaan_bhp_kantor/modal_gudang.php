<div id='modal-data-basic'>
    <form rel="ajax" action="<?php echo $linkForm ?>" class="form-horizontal xhr dest_modal-data-basic" method="get">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
            value="<?php echo $this->security->get_csrf_hash(); ?>" />
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row form-group">
                            <label class="col-sm-3 control-label" style='text-align:left;'>Kode</label>
                            <div class="col-sm-6">
                                <input type="text" autocomplete="off" placeholder="Masukan kode" class="form-control"
                                    name="kode" value="<?php echo  set_value('kode') ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row form-group">
                            <label class="col-sm-3 control-label" style='text-align:left;'>nama</label>
                            <div class="col-sm-6">
                                <input type="text" autocomplete="off" placeholder="Masukan nama" class="form-control"
                                    name="nama" value="<?php echo  set_value('nama') ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row text-right">
                <div class="panel-footer">
                    <?php echo tampilkan_button(); ?>
                </div>
            </div>
        </div>
        <div class="panel">
            <div class="panel-body">

                <div class="table-light table-primary" style="overflow: auto;">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Kode Ruang</th>
                                <th class="text-center">Nama Ruang</th>
                                <th class="text-center">Jenis Ruang</th>
                                <th class="text-center">Unit PJ</th>
                                <th class="text-center">Sediaan</th>
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
                                <td><?php echo $row['jenis'] ?></td>
                                <td><?php echo $row['unit'] ?></td>
                                <td><?php echo $row['jmlBhp'] ?></td>
                                <td><a href="javascript:;"
                                        onclick="pilih_rincian('<?php echo $row['id'] ?>','<?php echo $row['nama'] . ' (' . $row['kode'] . ' ) - ' . $row['unit']  ?>')"
                                        class="btn btn-xs btn-primary">Pilih</a></td>
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
function pilih_rincian(id, nama) {
    $("input[name='id_gudang']").val(id);
    $("input[name='nama_gudang']").val(nama);
    $('#modal-data-basic').closest('.modal').modal('hide');
}
</script>