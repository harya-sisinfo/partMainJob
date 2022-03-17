<div id='modal-data-basic'>
    <div class="alert alert-info">
        <!-- ====================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-2 control-label" style='text-align:left;'>Nomor Transaksi</label>
                    <div class="col-sm-6">
                        <?php echo $content['trans_nomor'] ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- ====================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-2 control-label" style='text-align:left;'>Tanggal</label>
                    <div class="col-sm-6">
                        <?php echo $content['trans_tanggal'] ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- ====================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-2 control-label" style='text-align:left;'>Nama</label>
                    <div class="col-sm-6">
                        <?php echo $content['trans_nama'] ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- ====================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-2 control-label" style='text-align:left;'>Diskripsi</label>
                    <div class="col-sm-6">
                        <?php echo $content['trans_keterangan'] ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- ====================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-2 control-label" style='text-align:left;'>Unit</label>
                    <div class="col-sm-6">
                        <?php echo $content['nama_unit'] ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title">
                List Item
            </span>
        </div>
        <div class="panel-body">
            <div class="table-light table-primary" style="overflow: auto;">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Kode Barang</th>
                            <th class="text-center">Nama</th>
                            <th class="text-center">Diskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><?php echo $content['barangKode'] ?></td>
                            <td><?php echo $content['barangNama'] ?></td>
                            <td><?php echo $content['barangDiskripsi'] ?></td>
                        </tr>
                    </tbody>
            </div>
        </div>
    </div>