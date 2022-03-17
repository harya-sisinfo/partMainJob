<div id='modal-data-basic'>
    <form rel="ajax" action="<?php echo $linkForm ?>" class="form-horizontal xhr dest_modal-data-basic" method="get">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
        <div class="alert alert-info">
            <!-- ====================================================================================== -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="row form-group">
                        <label class="col-sm-2 control-label" style='text-align:left;'>Gudang</label>
                        <div class="col-sm-6">
                            Gudang Dit. PUI
                        </div>
                    </div>
                </div>
            </div>
            <!-- ====================================================================================== -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="row form-group">
                        <label class="col-sm-2 control-label" style='text-align:left;'>Kode Barang</label>
                        <div class="col-sm-6">
                            Kode Barang
                        </div>
                    </div>
                </div>
            </div>
            <!-- ====================================================================================== -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="row form-group">
                        <label class="col-sm-2 control-label" style='text-align:left;'>Nama Barang</label>
                        <div class="col-sm-6">
                            Nama Barang
                        </div>
                    </div>
                </div>
            </div>
            <!-- ====================================================================================== -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="row form-group">
                        <label class="col-sm-2 control-label" style='text-align:left;'>Jumlah Total</label>
                        <div class="col-sm-6">
                            21
                        </div>
                    </div>
                </div>
            </div>
            <!-- ====================================================================================== -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="row form-group">
                        <label class="col-sm-2 control-label" style='text-align:left;'>Jumlah Sisa</label>
                        <div class="col-sm-6">
                            11
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">Cari data</span>
            </div>
            <div class="panel-body">
                <!-- ====================================================================================== -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row form-group">
                            <label class="col-sm-2 control-label" style='text-align:left;'>Jenis Sirkulasi</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="jenis_sirkulasi" placeholder="Cari berdasarkan jenis sirkulasi">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ====================================================================================== -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row form-group">
                            <label class="col-sm-2 control-label" style='text-align:left;'>Periode</label>
                            <div class="col-sm-6">
                                <div class="input-daterange input-group" id="periode-range">
                                    <input type="text" class="form-control" name="tanggal_mulai" placeholder="Tanggal Awal" value="<?php echo !empty(set_value('tanggal_mulai')) ? set_value('tanggal_mulai') : '' ?>" autocomplete="off">
                                    <span class="input-group-addon">s.d</span>
                                    <input type="text" class="form-control" name="tanggal_selesai" placeholder="Tanggal Akhir" value="<?php echo !empty(set_value('tanggal_selesai')) ? set_value('tanggal_selesai') : ''  ?>" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <div class="panel-footer">
                    <?php echo tampilkan_button() ?>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="table-light table-primary" style="overflow: auto;">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Barcode</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">Harga Satuan</th>
                            <th class="text-center">Tujuan Barang / Penerima</th>
                            <th class="text-center">Jenis Sirkulasi</th>
                            <th class="text-center">Barcode Keuangan</th>
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
                                    <td><?php echo $row['logAtkTgl'] ?></td>
                                    <td><?php echo $row['invAtkDetBarcode'] ?></td>
                                    <td><?php echo $row['logAtkJmlBrg'] ?></td>
                                    <td><?php echo $row['hrgsat'] ?></td>
                                    <td><?php echo $row['tujuanBarang'] ?></td>
                                    <td><?php echo $row['logAtkKeterangan'] ?></td>
                                    <td><?php echo $row['logAtkStatus'] ?></td>
                                    <td><?php echo $row['nospp'] ?></td>
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
                            <?php echo $halaman; 
                            ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </form>
</div>
<script>
    $(document).ready(function() {
        $(".modal").on('show.bs.modal', function(e) {
            $(this).removeAttr('tabindex');
        });
        
        let options2 = {
            format: "yyyy-mm-dd",
            autoclose: true,
            orientation: $('body').hasClass('right-to-left') ? "auto right" : 'auto bottom'
        }
        $('#periode-range').datepicker(options2);


    });
</script>