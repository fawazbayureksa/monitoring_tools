<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Stock Tools</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>"><i class="feather icon-box"></i></a></li>
                    <li class="breadcrumb-item"><a href="#!">Stock Tools</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 col-md-4 col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h4 class="text-c-blue"><?= isset($total_stock) ? $total_stock : 0 ?> <span class="text-muted" style="font-size: 14px;">Item</span></h4>
                        <h6 class="text-muted m-b-0">Stock Tools</h6>
                    </div>
                    <div class="col-4 text-right">
                        <i class="feather icon-box f-28 text-c-blue"></i>
                    </div>
                </div>
            </div>
            <a href="<?= site_url('Stock_tools') ?>" class="card-footer bg-c-blue">
                <div class="row align-items-center">
                    <div class="col-9"><p class="text-white m-b-0">More info</p></div>
                    <div class="col-3 text-right"><i class="feather icon-arrow-right text-white f-16"></i></div>
                </div>
            </a>
        </div>
    </div>

    <div class="col-lg-4 col-md-4 col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h4 class="text-c-green"><?= isset($total_borrowed) ? $total_borrowed : 0 ?> <span class="text-muted" style="font-size: 14px;">Item</span></h4>
                        <h6 class="text-muted m-b-0">Request Tools</h6>
                    </div>
                    <div class="col-4 text-right">
                        <i class="feather icon-edit f-28 text-c-green"></i>
                    </div>
                </div>
            </div>
            <a href="<?= site_url('Request_tools') ?>" class="card-footer bg-c-green">
                <div class="row align-items-center">
                    <div class="col-9"><p class="text-white m-b-0">More info</p></div>
                    <div class="col-3 text-right"><i class="feather icon-arrow-right text-white f-16"></i></div>
                </div>
            </a>
        </div>
    </div>

    <div class="col-lg-4 col-md-4 col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h4 class="text-c-red"><?= isset($total_bakk) ? $total_bakk : 0 ?> <span class="text-muted" style="font-size: 14px;">Item</span></h4>
                        <h6 class="text-muted m-b-0">BAKK Tools</h6>
                    </div>
                    <div class="col-4 text-right">
                        <i class="feather icon-file-text f-28 text-c-red"></i>
                    </div>
                </div>
            </div>
            <a href="<?= site_url('bakk') ?>" class="card-footer bg-c-red">
                <div class="row align-items-center">
                    <div class="col-9"><p class="text-white m-b-0">More info</p></div>
                    <div class="col-3 text-right"><i class="feather icon-arrow-right text-white f-16"></i></div>
                </div>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card table-inverse">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="feather icon-tool mr-2"></i>Stock Tools Inventory</h5>
                <div>
                    <button type="button" class="btn btn-outline-success btn-sm mr-2" data-toggle="modal" data-target="#modal-import">
                        <i class="feather icon-download"></i> Import CSV
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-add-tool">
                        <i class="feather icon-plus"></i> Add New Tool
                    </button>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Tools Name</th>
                                <th>No Parts</th>
                                <th>Type</th>
                                <th>Rack Location</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center no-export">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tools as $t): ?>
                            <tr>
                                <td><strong><?= $t['tool_name'] ?></strong></td>
                                <td><?= $t['part_number'] ?></td>
                                <td><?= $t['type'] ?></td>
                                <td><?= $t['rack_location'] ?></td>
                                
                                <td class="text-center">
                                    <?php if ($t['quantity'] > 0): ?>
                                        <span class="badge badge-success">Available</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Out of Stock</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="text-center font-weight-bold"><?= $t['quantity'] ?></td>
                                
                                <td class="text-center">      
                                    <button class="btn btn-sm btn-info btn-edit mr-1" 
                                        data-toggle="modal" 
                                        data-target="#modal-edit-tool"
                                        data-id="<?= $t['id'] ?>"
                                        data-name="<?= htmlspecialchars($t['tool_name']) ?>"
                                        data-part="<?= htmlspecialchars($t['part_number']) ?>"
                                        data-type="<?= $t['type'] ?>"
                                        data-location="<?= htmlspecialchars($t['rack_location']) ?>"
                                        data-qty="<?= $t['quantity'] ?>"
                                        title="Edit Data">
                                        <i class="feather icon-edit-2"></i> 
                                    </button>
                                    
                                    <a href="<?= site_url('stock_tools/delete/'.$t['id']) ?>" class="btn btn-sm btn-danger" title="Hapus Data" onclick="return confirm('Yakin ingin menghapus alat ini?')">
                                        <i class="feather icon-trash-2"></i> 
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-add-tool">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title"><i class="feather icon-plus-circle mr-2"></i>Input New Tool</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= site_url('stock_tools/add') ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Tool Name</label>
                        <input type="text" name="tool_name" class="form-control" placeholder="Contoh: Torque Wrench" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">No Parts</label>
                        <input type="text" name="part_number" class="form-control" placeholder="123456">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Type</label>
                        <select name="type" class="form-control">
                            <option value="Common Tools">Common Tools</option>
                            <option value="Chassis Tools">Chassis Tools</option>
                            <option value="Power Tools">Power Tools</option>
                            <option value="Measuring Tools">Measuring Tools</option>
                            <option value="Diagnostic Tools">Diagnostic Tools</option>
                            <option value="Engine Tools">Engine Tools</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Rack Location</label>
                        <input type="text" name="location" class="form-control" placeholder="Contoh: D-05">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Quantity</label>
                        <input type="number" name="quantity" class="form-control" value="1" min="1">
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="feather icon-save mr-1"></i>Save Tool</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-import">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h4 class="modal-title"><i class="feather icon-download-cloud mr-2"></i>Import Data from CSV</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= site_url('stock_tools/import') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-success">
                        <p class="mb-0">Format kolom wajib berurutan: <strong>Name, Part Number, Type, Location, Qty.</strong></p>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Pilih File CSV</label>
                        <div class="custom-file">
                            <input type="file" name="file_csv" class="custom-file-input" id="customFile" accept=".csv" required>
                            <label class="custom-file-label" for="customFile">Choose file</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success"><i class="feather icon-upload mr-1"></i>Import Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-edit-tool">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h4 class="modal-title"><i class="feather icon-edit mr-2"></i>Edit Tool</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= site_url('stock_tools/update') ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="form-group">
                        <label class="font-weight-bold">Tool Name</label>
                        <input type="text" name="tool_name" id="edit_tool_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">No Parts</label>
                        <input type="text" name="part_number" id="edit_part_number" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Type</label>
                        <select name="type" id="edit_type" class="form-control">
                            <option value="Common Tools">Common Tools</option>
                            <option value="Chassis Tools">Chassis Tools</option>
                            <option value="Power Tools">Power Tools</option>
                            <option value="Measuring Tools">Measuring Tools</option>
                            <option value="Diagnostic Tools">Diagnostic Tools</option>
                            <option value="Engine Tools">Engine Tools</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Rack Location</label>
                        <input type="text" name="location" id="edit_location" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Quantity</label>
                        <input type="number" name="quantity" id="edit_quantity" class="form-control" min="0">
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info"><i class="feather icon-save mr-1"></i>Update Tool</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.btn-edit');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const part = this.getAttribute('data-part');
            const type = this.getAttribute('data-type');
            const location = this.getAttribute('data-location');
            const qty = this.getAttribute('data-qty');

            document.getElementById('edit_id').value = id;
            document.getElementById('edit_tool_name').value = name;
            document.getElementById('edit_part_number').value = part;
            document.getElementById('edit_type').value = type;
            document.getElementById('edit_location').value = location;
            document.getElementById('edit_quantity').value = qty;
        });
    });
});
</script>