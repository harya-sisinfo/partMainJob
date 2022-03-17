<div id='modal-data-basic'>
    <form rel="ajax" action="<?php echo $linkForm ?>" class="form-horizontal xhr dest_modal-data-basic" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
        <div class="panel">
            <div class="panel-body">
                <!-- ======================== -->
                <div class="form-group">
                    <label class="col-md-3">
                        Bidang
                    </label>
                    <div class="col-md-4">
                        <input class="form-control" type="text" value="" name="nama_bidang" id="nama_bidang">
                        <input type="hidden" class="form-control" name="id_bidang">
                    </div>
                    <div class="col-md-2">
                        <a rel='async' id="listBidang" ajaxify="<?php echo modal("Pilih Bidang", 'aset_manajemen_bhp', 'inventarisasi_bhp', 'list_bidang') ?>" href="" class="btn btn-sm btn-labeled btn-info"><i class="fa fa-search btn-label-icon left"></i>Bidang</a>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3">
                        Kelompok
                    </label>
                    <div class="col-md-4">
                        <input class="form-control" type="text" value="" name="nama_kelompok" id="nama_kelompok">
                        <input type="hidden" class="form-control" name="id_kelompok">
                    </div>
                    <div class="col-md-2">
                        <a rel='async' id="listKelompok" ajaxify="<?php echo modal("Pilih Bidang", 'aset_manajemen_bhp', 'inventarisasi_bhp', 'list_kelompok') ?>" href="" class="btn btn-sm btn-labeled btn-info"><i class="fa fa-search btn-label-icon left"></i>Kelompok</a>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3">
                        Sub Kelompok
                    </label>
                    <div class="col-md-4">
                        <input class="form-control" type="text" value="" name="nama_sub_kelompok" id="nama_sub_kelompok">
                        <input type="hidden" class="form-control" name="id_sub_kelompok">
                    </div>
                    <div class="col-md-2">
                        <a rel='async' id="listSubKelompok" ajaxify="<?php echo modal("Pilih Bidang", 'aset_manajemen_bhp', 'inventarisasi_bhp', 'list_sub_kelompok') ?>" href="" class="btn btn-sm btn-labeled btn-info"><i class="fa fa-search btn-label-icon left"></i>Sub Kelompok &nbsp;</a>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3">
                        Cari kode/nama
                    </label>
                    <div class="col-md-4">
                        <input class="form-control" type="text" value="" name="search" id="search">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-7 text-right">
                        <?php echo tampilkan_button(); ?>
                    </div>
                </div>
                <!-- ======================== -->
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
                                        <td><?php echo $key ?></td>
                                        <td><?php echo $row['kode'] ?></td>
                                        <td><?php echo $row['nama'] ?></td>
                                        <td>
                                            <?php
                                            if ($row['aksi'] == 'show') {
                                            ?>
                                                <a href="javascript:;" onclick="pilih_rincian('<?php echo $row['id'] ?>','<?php echo $row['kode'] . ' - ' . $row['nama'] ?>','<?php echo $row['kode'] ?>','<?php echo $row['nama'] ?>','<?php echo $row['barang_merk'] ?>','<?php echo $row['stok'] ?>','<?php echo $row['ruang_id'] ?>')" class="btn btn-xs btn-primary">Pilih</a>
                                            <?php
                                            }
                                            ?>
                                        </td>
                                    </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php if (!empty($content)) { ?>
                    <div class="row">
                        <div class="pull-right">
                            <?php echo $halaman; ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
    </form>
</div>
<script type="text/javascript">
    function pilih_rincian(id, label, kode, nama, barang_merk, stok, ruang_id) {
        $("input[name='id_barang']").val(id);
        $("input[name='label_barang']").val(label);
        $("input[name='kode_barang']").val(kode);
        $("input[name='nama_barang']").val(nama);
        $("input[name='barang_merk']").val(barang_merk);
        $("input[name='stok']").val(stok);
        $("input[name='ruang_id']").val(ruang_id);
        $('#modal-data-basic').closest('.modal').modal('hide');
    };
</script>