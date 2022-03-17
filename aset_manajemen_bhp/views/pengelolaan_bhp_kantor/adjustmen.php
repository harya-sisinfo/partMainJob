<?php echo Modules::run('breadcrump'); ?>
<form rel="ajax-file" enctype="multipart/form-data" id="form_add_pengelolaan" action="<?php echo $url_action ?>" class="panel form-horizontal xhr dest_subcontent-element" method="post">
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="barang" value="<?php echo $barang; ?>">
    <input type="hidden" name="ruang" value="<?php echo $ruang; ?>">
    <input type="hidden" name="lastState" value="<?php echo $lastState; ?>">
    <div class="panel-heading">
        <span class="panel-title"><b><?php echo $form_label ?></b></span>
    </div>
    <div class="panel-body">
        <!-- Barcode Barang * ====================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-2 control-label" style='text-align:left;'>Barcode Barang</label>
                    <div class="col-sm-6">
                        <?php echo $content['barcode'] ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Kode Barang * ====================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-2 control-label" style='text-align:left;'>Kode Barang</label>
                    <div class="col-sm-6">
                        <?php echo $content['kode_barang'] ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Nama Barang * ====================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-2 control-label" style='text-align:left;'>Nama Barang</label>
                    <div class="col-sm-6">
                        <?php echo $content['nama_barang'] ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Label Barang * ====================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-2 control-label" style='text-align:left;'>Label Barang</label>
                    <div class="col-sm-6">
                        <?php echo $content['label_barang'] ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Merk Barang * ====================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-2 control-label" style='text-align:left;'>Merk Barang</label>
                    <div class="col-sm-6">
                        <?php echo $content['merk_barang'] ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Jumlah Sisa Barang * ====================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-2 control-label" style='text-align:left;'>Jumlah Sisa Barang</label>
                    <div class="col-sm-3">
                        <input name="sisa_barang" placeholder="Masukan sisa barang" class="form-control" value="<?php echo $content['sisa_barang'] ?>">
                        <?php echo form_error('sisa_barang', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
                    </div>
                    <div class="col-sm-3"><?php echo $content['satuanbrgNama'] ?></div>
                </div>
            </div>
        </div>
        <!-- Total Sisa Barang (Rp) * ====================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-2 control-label" style='text-align:left;'>Total Sisa Barang </label>
                    <div class="col-sm-6">
                        <?php echo rupiah($content['invAtkBiayaNominal']) ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Spesifikasi * ====================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-2 control-label" style='text-align:left;'>Spesifikasi</label>
                    <div class="col-sm-6">
                        <?php echo $content['spesifikasi'] ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Keterangan Tambahan * ====================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-2 control-label" style='text-align:left;'>Keterangan Tambahan</label>
                    <div class="col-sm-4">
                        <textarea class="form-control" placeholder="Masukan keterangan tambahan"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-footer text-right">
        <a href="<?php echo $url_back ?>" class="btn btn-warning xhr dest_subcontent-element"><i class="fa fa-arrow-left btn-label-icon left"></i>Kembali</a>
        <?php echo save_button() ?>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function() {
        $('.nominal').on({
            focus: function() {
                this.value = formatDisplayToInput(this.value);
            },
            keypress: function(event) {
                return validasiInput(this.value, event);
            },
            blur: function() {
                if (this.value != "") {
                    this.value = formatInputToDisplay(this.value);
                }
            }
        });
    });
</script>