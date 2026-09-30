<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Request Tools</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>"><i class="feather icon-edit"></i></a></li>
                    <li class="breadcrumb-item"><a href="#!">Request Tools</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header border-bottom">
                <h5><i class="feather icon-edit-1 mr-2 text-primary"></i>Request Tools Form</h5>
            </div>

            <form action="<?= site_url('request_tools/submit') ?>" method="post">
                <div class="card-body">

                    <h6 class="text-primary mb-3 mt-2"><i class="feather icon-user mr-2"></i>User Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">REQUEST NUMBER</label>
                                <input type="text" name="request_number" class="form-control" readonly value="<?= $req_number ?>" style="background-color: #f4f7fa;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">NAME & NRP</label>
                                <select name="mechanic_data" class="form-control select2" required>
                                    <option value="">Pilih Mekanik...</option>
                                    <option value="83101011|AHMAD DARMAWAN">83101011 - AHMAD DARMAWAN</option>
                                    <option value="83101013|ANDI USRIADI">83101013 - ANDI USRIADI</option>
                                    <option value="82111006|CHANDRA ALIM SAPUTRO">82111006 - CHANDRA ALIM SAPUTRO</option>
                                    <option value="80111243|DANI RAMDANI">80111243 - DANI RAMDANI</option>
                                    <option value="80114043|FAIZAL RASYID">80114043 - FAIZAL RASYID</option>
                                    <option value="83101016|HAMZAH">83101016 - HAMZAH</option>
                                    <option value="81112005|JOKO PANGESTU">81112005 - JOKO PANGESTU</option>
                                    <option value="81307020|M RIZAL MANSUR">81307020 - M RIZAL MANSUR</option>
                                    <option value="83107026|M. BASO IQBAL ZAKARIA">83107026 - M. BASO IQBAL ZAKARIA</option>
                                    <option value="83102009|MUH. ALWI">83102009 - MUH. ALWI</option>
                                    <option value="80112125|RIZAL PRISTANTO">80112125 - RIZAL PRISTANTO</option>
                                    <option value="82211002|RUDHI MARWANTO">82211002 - RUDHI MARWANTO</option>
                                    <option value="83110019|RUSDI">83110019 - RUSDI</option>
                                    <option value="83105006|SYAMSU ALAM">83105006 - SYAMSU ALAM</option>
                                    <option value="80105068|TULUS WAHYONO">80105068 - TULUS WAHYONO</option>
                                    <option value="83107021|ZULFIEKAR SURYADI">83107021 - ZULFIEKAR SURYADI</option>

                                    <option value="702210275|ALIF AKBAR NUR 'AZIZI">702210275 - ALIF AKBAR NUR 'AZIZI</option>
                                    <option value="702210276|AMRUL MU'MININ">702210276 - AMRUL MU'MININ</option>
                                    <option value="702211214|KIKI RUDIANSYAH">702211214 - KIKI RUDIANSYAH</option>
                                    <option value="70216365|MIFTAKHUR ROHMAN">70216365 - MIFTAKHUR ROHMAN</option>
                                    <option value="702211211|MUHAMMAD FICRI KABAN">702211211 - MUHAMMAD FICRI KABAN</option>
                                    <option value="702220380|NUGRI ARIS">702220380 - NUGRI ARIS</option>
                                    <option value="702220942|PRAYOGY">702220942 - PRAYOGY</option>
                                    <option value="702220941|NUR BAYU FIRMANSYAH">702220941 - NUR BAYU FIRMANSYAH</option>
                                    <option value="702221338|IHSAN MUNADI">702221338 - IHSAN MUNADI</option>
                                    <option value="702221331|AHMAD DHANI">702221331 - AHMAD DHANI</option>
                                    <option value="702221341|SUNANDAR">702221341 - SUNANDAR</option>
                                    <option value="702221335|ISWAN S">702221335 - ISWAN S</option>
                                    <option value="702221279|HASRUL">702221279 - HASRUL</option>
                                    <option value="702221278|DIRWAN WIRANTO H">702221278 - DIRWAN WIRANTO H</option>
                                    <option value="702240283|DODIK MELFIANTO">702240283 - DODIK MELFIANTO</option>
                                    <option value="702240282|GHAIRIL ZULTANI">702240282 - GHAIRIL ZULTANI</option>
                                    <option value="702210187|MOCH RIZAL FIRMANSYAH">702210187 - MOCH RIZAL FIRMANSYAH</option>
                                    <option value="702250517|MURDIANTO">702250517 - MURDIANTO</option>
                                    <option value="702240756|M IRSYAD">702240756 - M IRSYAD</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr class="mt-4 mb-4">

                    <h6 class="text-primary mb-3"><i class="feather icon-file mr-2"></i>Tools Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">SELECT TOOLS</label>
                                <select class="form-control select2" name="tool_id" id="toolSelect" style="width: 100%;" required>
                                    <option value="">Cari Alat...</option>
                                    <?php foreach ($tools as $t): ?>
                                        <option value="<?= $t['id'] ?>"><?= $t['tool_name'] ?> (<?= $t['part_number'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="font-weight-bold">STOCK AVAILABLE</label>
                                <input type="text" id="stockAvailable" class="form-control" readonly value="0" style="background-color: #f4f7fa; text-align: center;">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="font-weight-bold">QUANTITY REQUEST</label>
                                <input type="number" name="qty" id="qtyRequest" class="form-control text-center" min="1" max="1" value="1" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-bold">RACK LOCATION (INFO)</label>
                                <input type="text" id="rackLocation" class="form-control" readonly value="-" style="background-color: #f4f7fa;">
                            </div>
                        </div>
                    </div>
                    <hr class="mt-4 mb-4">

                    <h6 class="text-primary mb-3"><i class="feather icon-calendar mr-2"></i>Borrowing Details</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">LOCATION / SITE</label>
                                <select class="form-control select2" name="site_location" style="width: 100%;">
                                    <option value="WORKSHOP - UPG" selected>WORKSHOP</option>
                                    <option value="SITE - KONUT">FIELD UPG</option>
                                    <option value="SITE - MATARAPE">SITE - POMALAA</option>
                                    <option value="SITE - ROUTA">SITE - LANGGIKIMA</option>
                                    <option value="SITE - KONUT">SITE - ROUTA</option>
                                    <option value="SITE - MATARAPE">SITE - MOROWALI</option>
                                    <option value="SITE - ROUTA">SITE - KENDARI</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="font-weight-bold">BORROW DATE & TIME</label>
                                <input type="datetime-local" name="borrow_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="font-weight-bold">EST. RETURN DATE & TIME</label>
                                <input type="datetime-local" name="return_date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-right bg-light">
                    <button type="reset" class="btn btn-secondary mr-2">
                        <i class="feather icon-refresh-ccw mr-1"></i> Reset Form
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="feather icon-send mr-1"></i> Submit Peminjaman
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        $('#toolSelect').on('change', function() {
            var toolId = $(this).val();
            if (toolId) {
                $.ajax({
                    url: '<?= base_url("request_tools/get_tool_info") ?>',
                    type: 'POST',
                    data: {
                        tool_id: toolId
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data) {
                            $('#stockAvailable').val(data.quantity);
                            $('#rackLocation').val(data.rack_location);
                            $('#qtyRequest').attr('max', data.quantity);
                            $('#qtyRequest').val(1);
                        }
                    }
                });
            } else {
                $('#stockAvailable').val(0);
                $('#rackLocation').val('-');
                $('#qtyRequest').attr('max', 1);
            }
        });

        const btnDelete = document.getElementById('btnDeleteMechanic');
        if (btnDelete) {
            btnDelete.addEventListener('click', function() {
                var userSelect = document.getElementById('userSelect');
                var userId = userSelect.value;
                var userName = userSelect.options[userSelect.selectedIndex].text;

                if (!userId) {
                    alert('Silakan pilih nama karyawan di dropdown terlebih dahulu sebelum menekan tombol hapus!');
                    return;
                }

                if (confirm('Yakin ingin menghapus "' + userName + '" dari daftar?')) {
                    window.location.href = '<?= site_url("request_tools/delete_mechanic/") ?>' + userId;
                }
            });
        }
    });
</script>