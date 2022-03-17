<?php echo Modules::run('breadcrump'); ?>
<form rel="ajax-file" enctype="multipart/form-data" id="form_add_pengelolaan" action="<?php echo $url_action ?>" class="panel form-horizontal xhr dest_subcontent-element" method="post">
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="barang" value="<?php echo $barang; ?>">
    <input type="hidden" name="ruang" value="<?php echo $ruang; ?>">
    <input type="hidden" name="lastState" value="<?php echo $lastState; ?>">
    <div class="panel-heading">
        <span class="panel-title"><b><?php echo $form_label ?></b></span>
    </div>
    <div class="panel-body">
        <!-- Kode Barang * ====================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-2 control-label" style='text-align:left;'>Kode Barang</label>
                    <div class="col-sm-6">
                        <?php echo $kode_barang ?>
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
                        <?php echo $nama_barang ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Gudang * ====================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-2 control-label" style='text-align:left;'>Gudang</label>
                    <div class="col-sm-6">
                        <?php echo $gudang ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Stok Minimal * ====================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-2 control-label" style='text-align:left;'>Stok Minimal</label>
                    <div class="col-sm-4">
                        <input type="text" name="stok_minimal" placeholder="Masukan stok minimal" class="form-control nominal" value="<?php echo set_value('stok_minimal', $stok_minimal) ?>">
                    </div>
                </div>
            </div>
        </div>
        <!-- Keterangan * ====================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-2 control-label" style='text-align:left;'>Keterangan</label>
                    <div class="col-sm-6">
                        <textarea name="keterangan" placeholder="Masukan keterangan" class="form-control"><?php echo set_value('stok_minimal', !empty($keterangan) ? $keterangan : '') ?></textarea>
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