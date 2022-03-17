<?php echo Modules::run('breadcrump'); ?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            <div class="panel-heading"><span class="panel-title">Filter</span></div>
            <div class="panel-body">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                <div id="form-filter" class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" style="text-align:left;">Bidang Barang</label>
                        <div class="col-sm-4">
                            <input type="text" readonly placeholder="Cari berdasarkan bidang" class="form-control" name="nama_bidang">
                            <input type="hidden" class="form-control" name="id_bidang">
                        </div>
                        <div class="col-sm-3">
                            <a rel='async' id="listBidang" ajaxify="<?php echo modal("Pilih Bidang", 'aset_manajemen_bhp', 'inventarisasi_bhp', 'list_bidang') ?>" href="" class="btn btn-sm btn-labeled btn-success"><i class="fa fa-search btn-label-icon left"></i>Cari Bidang&nbsp;&nbsp;&nbsp;</a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" style="text-align:left;">Kelompok Barang</label>
                        <div class="col-sm-4">
                            <input type="text" readonly placeholder="Cari berdasarkan kelompok" class="form-control" name="nama_kelompok">
                            <input type="hidden" class="form-control" name="id_kelompok">
                        </div>
                        <div class="col-sm-3">
                            <a rel='async' id="listKelompok" ajaxify="" href="<?php echo modal("Pilih Kelompok", 'aset_manajemen_bhp', 'inventarisasi_bhp', 'list_kelompok') ?>" class="btn btn-sm btn-labeled btn-success"><i class="fa fa-search btn-label-icon left"></i>Cari Kelompok</a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" style="text-align:left;">Sub Kelompok</label>
                        <div class="col-sm-4">
                            <input type="text" readonly placeholder="Cari berdasarkan sub kelompok" class="form-control" name="nama_sub_kelompok">
                            <input type="hidden" class="form-control" name="id_sub_kelompok">
                        </div>
                        <div class="col-sm-3">
                            <a rel='async' id="listSubKelompok" ajaxify="" href="<?php echo modal("Pilih Sub Kelompok", 'aset_manajemen_bhp', 'inventarisasi_bhp', 'list_sub_kelompok') ?>" class="btn btn-sm btn-labeled btn-success"><i class="fa fa-search btn-label-icon left"></i>Cari Sub Kelompok</a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" style="text-align:left;">Nama Barang</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="nama_barang" placeholder="Cari berdasarkan nama barang">
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <button id="btn-reset" class="btn btn-default"><span class="btn-label-icon left fa fa-refresh"></span>Reset</button>
                        <button id="btn-filter" class="btn btn-primary"><span class="btn-label-icon left fa fa-search"></span>Tampilkan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="panel">
    <div class="panel-heading">
        <span class="panel-title">Daftar Inventarisasi BHP </span>
        <div class="panel-heading-controls">
            <button class="btn btn-sm btn-flat btn-labeled btn-success" id="btn-tambah">
                <span class="btn-label-icon left fa fa-plus"></span> Tambah
            </button>
        </div>
    </div>
    <div class="panel-body">
        <div class="table-light table-primary" id="table-container">
            <table cellpadding="0" cellspacing="0" border="0" width="100%" class="table table-striped table-bordered" id="jq-datatables-inventarisasi-bhp">
                <thead>
                    <tr>
                        <th width="35px" class="no-sort text-center">No</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Akun COA</th>
                        <th class="no-sort text-center">Status</th>
                        <th width="180px" class="no-sort text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var dtTable;
        $("#jq-datatables-inventarisasi-bhp").tooltip({
            selector: '[data-toggle="tooltip"]'
        });
        $("#btn-filter").click(function() {
            dtTable.draw();
        });

        $("#listBidang").click(function() {
            $("input[name='id_kelompok']").val("");
            $("input[name='nama_kelompok']").val("");
            $("input[name='id_sub_kelompok']").val("");
            $("input[name='nama_sub_kelompok']").val("");
        });

        $("#listKelompok").on("click", function() {
            var id_bidang = $("input[name='id_bidang']").val();
            var ajaxify = $(this).attr('href') + '/0/' + id_bidang;
            $(this).attr('ajaxify', ajaxify);
            return true;
        });

        $("#listSubKelompok").on("click", function() {
            var id_bidang = $("input[name='id_bidang']").val();
            var id_kelompok = $("input[name='id_kelompok']").val();
            var ajaxify = $(this).attr('href') + '/0/' + id_bidang + '/' + id_kelompok;
            $(this).attr('ajaxify', ajaxify);
            return true;
        });

        $("#btn-reset").click(function() {
            $("#form-filter select").each(function() {
                $(this).val("").trigger("change");
            });
            $("#form-filter input[type='text']").each(function() {
                $(this).val("");
            });
            dtTable.draw();
        });

        $("#btn-tambah").click(function() {
            lastStage = '?id_bidang=' + $("input[name='id_bidang']").val() + '&id_kelompok=' + $(
                "input[name='id_kelompok']").val() + '&id_sub_kelompok=' + $(
                "input[name='id_sub_kelompok']").val() + '&nama_barang=' + $(
                "input[name='nama_barang']").val();
            $.ajax({
                url: "<?php echo $url_add ?>",
                type: "GET",
                success: function(output) {
                    top.location = '<?php echo $url_add ?>' + lastStage;
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + " " + thrownError);
                }
            });
        });
        dtTable = $('#jq-datatables-inventarisasi-bhp').DataTable({
            "processing": false,
            "serverSide": true,
            "scrollCollapse": true,
            "order": [
                [1, "DESC"]
            ],
            "ajax": {
                url: "<?php echo $linkDataTable ?>",
                type: "get",
                data: function(data) {
                    data.id_bidang = $("input[name='id_bidang']").val();
                    data.id_kelompok = $("input[name='id_kelompok']").val();
                    data.id_sub_kelompok = $("input[name='id_sub_kelompok']").val();
                    data.nama_barang = $("input[name='nama_barang']").val();
                    console.log(data);
                },
                error: function() {
                    $(".jq-datatables-inventarisasi-bhp-error").html("");
                    $("#jq-datatables-inventarisasi-bhp").append(
                        '<tbody class="jq-datatables-inventarisasi-bhp-error"><tr><th colspan="5">No data found in the server</th></tr></tbody>'
                    );
                    $("#jq-datatables-inventarisasi-bhp_processing").css("display", "hidden");
                }
            },
            "columnDefs": [{
                    targets: [0],
                    orderable: false,
                    className: "text-center"
                },
                {
                    targets: [1],
                    orderable: false
                },
                {
                    targets: [2],
                    orderable: false
                },
                {
                    targets: [3],
                    orderable: false
                },
                {
                    targets: [4],
                    orderable: false,
                    className: "text-center"
                },
                {
                    targets: [5],
                    orderable: false,
                    className: "text-center"
                }
            ],
            "language": {
                "lengthMenu": "Per halaman _MENU_",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Menampilkan _START_ s.d _END_ dari total _TOTAL_",
                "infoEmpty": "Menampilkan 0 s.d _END_ dari total _TOTAL_",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "&gt;",
                    "previous": "&lt;"
                }
            },
            "createdRow": function(row, data, dataIndex) {
                $('td:eq(5)', row).css('min-width', '80px');
            }
        });

        $('#jq-datatables-inventarisasi-bhp_wrapper .table-caption').text('List Data');
        $('#jq-datatables-inventarisasi-bhp_wrapper .dataTables_filter input').attr('placeholder', 'Cari...');

    });
</script>