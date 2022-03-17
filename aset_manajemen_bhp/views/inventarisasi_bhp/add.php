<?php echo Modules::run('breadcrump'); ?>
<form rel="ajax-file" enctype="multipart/form-data" id="form_add" action="<?php echo $url_action ?>" class="panel form-horizontal xhr dest_subcontent-element" method="post">
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <div class="panel-heading">
        <span class="panel-title"><b><?php echo $form_label ?></b></span>
    </div>
    <div class="panel-body">
        <input type="hidden" name="lastStage" value="<?php echo $lastStage ?>">
        <!--==========================================================================================-->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-2 control-label" style='text-align:left;'>Kode Barang&nbsp;<font color="red">*
                        </font></label>
                    <div class="col-sm-5">
                        <input type="text" autocomplete="off" readonly placeholder="Masukan Kode Barang" class="form-control" name="label_barang">
                        <input type="hidden" name="kode_barang">
                        <input type="hidden" name="id_barang">

                        <?php echo form_error('id_barang', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
                    </div>
                    <div class="col-sm-3">
                        <a rel='async' id="listBidang" ajaxify="<?php echo modal("Pilih Barang", 'aset_manajemen_bhp', 'inventarisasi_bhp', 'list_barang') ?>" href="" class="btn btn-sm btn-labeled btn-info"><i class="fa fa-check btn-label-icon left"></i>Pilih Barang</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- ========================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-2 control-label" style='text-align:left;'>Nama Barang&nbsp;<font color="red">*
                        </font></label>
                    <div class="col-sm-5">
                        <input type="text" autocomplete="off" placeholder="Masukan Nama Barang " class="form-control" name="nama_barang" value="<?php echo  set_value('nama_barang') ?>">
                        <?php echo form_error('nama_barang', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- ========================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-2 control-label" style='text-align:left;'>Detail Barang&nbsp;<font color="red">
                            *</font></label>
                    <div class="col-sm-5">
                        <input type="text" autocomplete="off" placeholder="Masukan Detail Barang " class="form-control" name="detail_barang" value="<?php echo  set_value('detail_barang') ?>">
                        <?php echo form_error('detail_barang', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- ========================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-2 control-label" style='text-align:left;'>Aktif</label>
                    <div class="col-sm-5">
                        <input type="checkbox" name="stat" value="Y">
                    </div>
                </div>
            </div>
        </div>
        <div class="row text-right">
            <div class="panel-footer">
                <a href="<?php echo $url_back ?>" class="btn btn-warning xhr dest_subcontent-element"><i class="fa fa-arrow-left btn-label-icon left"></i>Kembali</a>
                <?= save_button() ?>
            </div>
        </div>
    </div>
</form>