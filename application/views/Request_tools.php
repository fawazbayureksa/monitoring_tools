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
                                    <?php foreach (MECHANIC_LIST as $nrp => $name): ?>
                                        <option value="<?= $nrp ?>|<?= $name ?>"><?= $nrp ?> - <?= $name ?></option>
                                    <?php endforeach; ?>
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
                                    <?php foreach (SITE_LOCATION_LIST as $site): ?>
                                        <option value="<?= $site['value'] ?>"<?= !empty($site['selected']) ? ' selected' : '' ?>><?= $site['label'] ?></option>
                                    <?php endforeach; ?>
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