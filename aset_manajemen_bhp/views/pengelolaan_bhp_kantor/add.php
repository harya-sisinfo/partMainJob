<?php echo Modules::run('breadcrump'); ?>

<form rel="ajax-file" enctype="multipart/form-data" id="form_add_pengelolaan" action="<?php echo $url_action ?>" class="panel form-horizontal xhr dest_subcontent-element" method="post">
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <div class="panel-heading">
        <span class="panel-title"><b><?php echo $form_label ?></b></span>
    </div>
    <div class="panel-body">
        <!-- Barcode Kuitansi (Keuangan) * ====================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-3 control-label" style='text-align:left;'>Barcode Kuitansi (Keuangan)&nbsp;
                        <font color="red">*</font>
                    </label>
                    <div class="col-sm-6">
                        <input type="text" autocomplete="off" placeholder="Masukan data barcode Kuitansi (Keuangan)" class="form-control" name="barcode_kuitansi" value="<?php echo  set_value('barcode_kuitansi') ?>">
                        <?php echo form_error('barcode_kuitansi', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Barcode Barang ===================================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-3 control-label" style='text-align:left;'>Barcode Barang</label>
                    <div class="col-sm-6">
                        <input type="text" autocomplete="off" placeholder="Masukan data barcode Barang" class="form-control" name="barcode_barang" value="<?php echo  set_value('barcode_barang') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Gudang * =========================================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-3 control-label" style='text-align:left;'>Gudang&nbsp;<font color="red">*
                        </font></label>
                    <div class="col-sm-4">
                        <input type="text" readonly placeholder="Masukan data gudang" class="form-control" name="nama_gudang">
                        <input type="hidden" class="form-control" name="id_gudang">
                        <?php echo form_error('id_gudang', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
                    </div>
                    <div class="col-sm-1">
                        <a rel='async' id="listGudang" ajaxify="<?php echo modal("Pilih Gudang&nbsp;", 'aset_manajemen_bhp', 'pengelolaan_bhp_kantor', 'list_gudang') ?>" href="" class="btn btn-sm btn-labeled btn-success"><i class="fa fa-plus btn-label-icon left"></i>Pilih</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kode Barang * ====================================================================================================== 
-->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-3 control-label" style='text-align:left;'>Kode Barang&nbsp;<font color="red">*
                        </font></label>
                    <div class="col-sm-4">
                        <input type="text" readonly placeholder="Masukan data kode barang" class="form-control" name="kode_barang">
                        <input type="hidden" class="form-control" name="id_barang">
                        <?php echo form_error('id_barang', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
                    </div>
                    <div class="col-sm-1">
                        <a rel='async' id="listGudang" ajaxify="<?php echo modal("Pilih Barang", 'aset_manajemen_bhp', 'pengelolaan_bhp_kantor', 'list_barang') ?>" href="" class="btn btn-sm btn-labeled btn-success"><i class="fa fa-plus btn-label-icon left"></i>Pilih</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nama Barang ======================================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-3 control-label" style='text-align:left;'>Nama Barang </label>
                    <div class="col-sm-6">
                        <input type="text" autocomplete="off" placeholder="Masukan nama barang" class="form-control" name="nama_barang" value="<?php echo  set_value('nama_barang') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Label Barang ======================================================================================================= -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-3 control-label" style='text-align:left;'>Label Barang </label>
                    <div class="col-sm-6">
                        <input type="text" autocomplete="off" placeholder="Masukan label barang" class="form-control" name="nama_barang" value="<?php echo  set_value('label_barang') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Merk Barang ======================================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-3 control-label" style='text-align:left;'>Merk Barang </label>
                    <div class="col-sm-6">
                        <input type="text" autocomplete="off" placeholder="Masukan merk barang" class="form-control" name="merk" value="<?php echo  set_value('merk') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tanggal Pembelian * ================================================================================================ -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-3 control-label" style='text-align:left;'>Tanggal Pembelian&nbsp;<font color="red">*</font></label>
                    <div class="col-sm-3">
                        <div class="input-group date day">
                            <input type="text" name="tanggal_pembelian" class="form-control" placeholder="Tanggal pembelian" autocomplete="off" value="<?php echo  set_value('tanggal_pembelian') ?>"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                        <?php echo form_error('tanggal_pembelian', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Jumlah Barang (Satuan) * =========================================================================================== -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-3 control-label" style='text-align:left;'>Jumlah Barang (Satuan)&nbsp;<font color="red">*</font></label>
                    <div class="col-sm-2">
                        <input type="text" autocomplete="off" placeholder="Masukan jumlah barang" class="form-control nominal" name="jumlah_barang" value="<?php echo  set_value('jumlah_barang') ?>">
                        <?php echo form_error('jumlah_barang', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
                    </div>
                    <div class="col-sm-4">
                        <select name="satuan" class="form-control">
                            <option></option>
                            <?php foreach ($satuan as $key_satuan => $row_satuan) {
                            ?>
                                <option value="<?php echo $row_satuan['id'] ?>"><?php echo $row_satuan['name'] ?></option>
                            <?php
                            } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <!-- Total Barang (Rp) * ================================================================================================ -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-3 control-label" style='text-align:left;'>Total Barang (Rp)&nbsp;<font color="red">*</font></label>
                    <div class="col-sm-6">
                        <input type="text" autocomplete="off" placeholder="Masukan total barang (Rp)" class="form-control nominal" name="total_barang" value="<?php echo  set_value('total_barang') ?>">
                        <?php echo form_error('total_barang', '<p class="help-block text-danger" ><i>', '</i></p>'); ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Spesifikasi ================================================================================================ -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-3 control-label" style='text-align:left;'>Spesifikasi</label>
                    <div class="col-sm-6">
                        <textarea name="spesifikasi" class="form-control" placeholder="Masukan spesifikasi barang"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <!-- Keterangan Tambahan  ================================================================================================ -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <label class="col-sm-3 control-label" style='text-align:left;'>Keterangan Tambahan</label>
                    <div class="col-sm-6">
                        <textarea name="keterangan" class="form-control" placeholder="Masukan spesifikasi barang"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <!-- ========================================================================================== -->

    </div>

    <div class="row text-right">
        <div class="panel-footer">
            <a href="<?php echo $url_back ?>" class="btn btn-warning xhr dest_subcontent-element"><i class="fa fa-arrow-left btn-label-icon left"></i>Kembali</a>
            <?php echo save_button() ?>
        </div>
    </div>

</form>
<script type="text/javascript">
    $(document).ready(function() {
        $("select[name='satuan']").select2({
            allowClear: true,
            placeholder: "Pilih satuan jumlah barang"
        });
        $("div.input-group.date.day").datepicker({
            format: "dd-mm-yyyy",
            viewMode: "days",
            minViewMode: "days",
            autoclose: true
        });
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