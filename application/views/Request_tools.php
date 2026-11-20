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

                    <h6 class="text-primary mb-3"><i class="feather icon-tool mr-2"></i>Select & Add Tools</h6>
                    <div class="row align-items-end bg-light p-3 rounded mb-3 border">
                        <div class="col-md-5">
                            <div class="form-group mb-md-0">
                                <label class="font-weight-bold">SELECT TOOL</label>
                                <select class="form-control select2" id="toolSelect" style="width: 100%;">
                                    <option value="">-- Cari & Pilih Alat --</option>
                                    <?php foreach ($tools as $t): ?>
                                        <option value="<?= $t['id'] ?>" data-stock="<?= $t['quantity'] ?>" data-name="<?= htmlspecialchars($t['tool_name']) ?>" data-part="<?= htmlspecialchars($t['part_number']) ?>" data-rack="<?= htmlspecialchars($t['rack_location']) ?>">
                                            <?= $t['tool_name'] ?> (<?= $t['part_number'] ?>) — Stok: <?= $t['quantity'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="form-group mb-md-0">
                                <label class="font-weight-bold">STOCK</label>
                                <input type="text" id="stockAvailable" class="form-control text-center font-weight-bold" readonly value="0" style="background-color: #e9ecef;">
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="form-group mb-md-0">
                                <label class="font-weight-bold">QTY</label>
                                <input type="number" id="qtyRequest" class="form-control text-center font-weight-bold" min="1" max="1" value="1">
                            </div>
                        </div>
                        <div class="col-md-3 mt-2 mt-md-0">
                            <button type="button" class="btn btn-success btn-block" id="btnAddTool">
                                <i class="feather icon-plus mr-1"></i> Add Tool
                            </button>
                        </div>
                        <div class="col-12 mt-2">
                            <small class="text-muted"><i class="feather icon-info mr-1"></i>Rack Location: <span id="rackLocation" class="font-weight-bold">-</span></small>
                        </div>
                    </div>

                    <!-- Tabel Daftar Alat yang Dipilih -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-striped" id="tableSelectedTools">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Tool Name</th>
                                    <th>Part Number</th>
                                    <th>Rack Location</th>
                                    <th class="text-center" style="width: 120px;">Stock</th>
                                    <th class="text-center" style="width: 120px;">Qty Request</th>
                                    <th class="text-center" style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="selectedToolsBody">
                                <tr id="emptyToolsRow">
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="feather icon-alert-circle mr-1"></i>Belum ada alat yang ditambahkan. Silakan pilih alat di atas dan klik <strong>Add Tool</strong>.
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-light font-weight-bold" id="toolsTableFoot" style="display: none;">
                                <tr>
                                    <td colspan="5" class="text-right">TOTAL:</td>
                                    <td class="text-center text-primary" id="dispTotalQty">0</td>
                                    <td class="text-center"><span class="badge badge-info" id="dispTotalItems">0 Item</span></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Container untuk hidden inputs tool_ids[] dan qtys[] -->
                    <div id="hiddenInputsContainer"></div>

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
                    <button type="button" class="btn btn-secondary mr-2" id="btnResetForm">
                        <i class="feather icon-refresh-ccw mr-1"></i> Reset Form
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitRequest">
                        <i class="feather icon-send mr-1"></i> Submit Peminjaman
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var selectedTools = []; // Array of { id, name, part, rack, stock, qty }

        // Update info ketika alat dipilih di dropdown
        $('#toolSelect').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var toolId = $(this).val();

            if (toolId) {
                var stock = parseInt(selectedOption.data('stock')) || 0;
                var rack = selectedOption.data('rack') || '-';

                $('#stockAvailable').val(stock);
                $('#rackLocation').text(rack);
                $('#qtyRequest').attr('max', stock);
                $('#qtyRequest').val(1);
            } else {
                $('#stockAvailable').val(0);
                $('#rackLocation').text('-');
                $('#qtyRequest').attr('max', 1);
                $('#qtyRequest').val(1);
            }
        });

        // Tambah alat ke tabel
        $('#btnAddTool').on('click', function() {
            var toolId = $('#toolSelect').val();
            if (!toolId) {
                alert('Silakan pilih alat terlebih dahulu!');
                return;
            }

            // Validasi apakah alat sudah ditambahkan sebelumnya
            var exists = selectedTools.some(function(item) {
                return item.id == toolId;
            });

            if (exists) {
                alert('Alat ini sudah ada dalam daftar permohonan! Anda tidak dapat menambahkan alat yang sama.');
                return;
            }

            var selectedOption = $('#toolSelect').find('option:selected');
            var toolName = selectedOption.data('name') || '';
            var partNumber = selectedOption.data('part') || '-';
            var rackLocation = selectedOption.data('rack') || '-';
            var stock = parseInt(selectedOption.data('stock')) || 0;
            var qty = parseInt($('#qtyRequest').val()) || 1;

            if (qty <= 0) {
                alert('Kuantitas permohonan minimal 1!');
                return;
            }

            if (qty > stock) {
                alert('Kuantitas permohonan (' + qty + ') melebihi stok yang tersedia (' + stock + ')!');
                return;
            }

            selectedTools.push({
                id: toolId,
                name: toolName,
                part: partNumber,
                rack: rackLocation,
                stock: stock,
                qty: qty
            });

            renderToolsTable();

            // Reset input pemilihan alat
            $('#toolSelect').val('').trigger('change');
            $('#qtyRequest').val(1);
        });

        // Hapus alat dari tabel
        $(document).on('click', '.btn-remove-tool', function() {
            var index = $(this).data('index');
            if (index !== undefined && index >= 0 && index < selectedTools.length) {
                selectedTools.splice(index, 1);
                renderToolsTable();
            }
        });

        // Render tabel dan perbarui hidden inputs
        function renderToolsTable() {
            var tbody = $('#selectedToolsBody');
            var container = $('#hiddenInputsContainer');
            tbody.empty();
            container.empty();

            if (selectedTools.length === 0) {
                tbody.append('<tr id="emptyToolsRow"><td colspan="7" class="text-center text-muted py-4"><i class="feather icon-alert-circle mr-1"></i>Belum ada alat yang ditambahkan. Silakan pilih alat di atas dan klik <strong>Add Tool</strong>.</td></tr>');
                $('#toolsTableFoot').hide();
                return;
            }

            var totalQty = 0;
            selectedTools.forEach(function(item, idx) {
                totalQty += item.qty;
                var rowHtml = '<tr>' +
                    '<td class="text-center font-weight-bold">' + (idx + 1) + '</td>' +
                    '<td><strong>' + item.name + '</strong></td>' +
                    '<td><span class="badge badge-secondary">' + item.part + '</span></td>' +
                    '<td>' + item.rack + '</td>' +
                    '<td class="text-center"><span class="badge badge-success p-1">' + item.stock + '</span></td>' +
                    '<td class="text-center font-weight-bold text-primary">' + item.qty + '</td>' +
                    '<td class="text-center">' +
                        '<button type="button" class="btn btn-sm btn-danger btn-remove-tool" data-index="' + idx + '" title="Hapus Alat">' +
                            '<i class="feather icon-trash-2"></i>' +
                        '</button>' +
                    '</td>' +
                '</tr>';
                tbody.append(rowHtml);

                // Tambahkan hidden input untuk form submit
                container.append('<input type="hidden" name="tool_ids[]" value="' + item.id + '">');
                container.append('<input type="hidden" name="qtys[]" value="' + item.qty + '">');
            });

            $('#dispTotalQty').text(totalQty);
            $('#dispTotalItems').text(selectedTools.length + ' Item');
            $('#toolsTableFoot').show();
        }

        // Validasi sebelum form submit
        $('form').on('submit', function(e) {
            if (selectedTools.length === 0) {
                e.preventDefault();
                alert('Wajib menambahkan minimal 1 alat sebelum mengirim permohonan!');
                return false;
            }
            return true;
        });

        // Reset form
        $('#btnResetForm').on('click', function() {
            selectedTools = [];
            renderToolsTable();
            $('#toolSelect').val('').trigger('change');
            $('form')[0].reset();
        });
    });
</script>