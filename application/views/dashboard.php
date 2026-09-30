<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                   <h5 class="m-b-10">Dashboard</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="#!">Dashboard</a></li>
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

<?php if (($this->session->userdata('role') == 'leader' || $this->session->userdata('role') == 'user') && !empty($cancelled_alerts)): ?>
    <?php foreach ($cancelled_alerts as $alert): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="markRead(<?= $alert['id'] ?>)">
                <span aria-hidden="true">&times;</span>
            </button>
            <h5 class="alert-heading"><i class="feather icon-alert-circle mr-2"></i>Request Declined!</h5>
            <p class="mb-0">Permintaan <strong><?= $alert['request_number'] ?></strong> ditolak dengan alasan: <?= $alert['notes'] ?></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<div class="row">
    <?php
    $sla_hours = ((int)($this->config->item('delivery_sla_days') ?: 2)) * 24;
    $remind_days = (int)($this->config->item('return_reminder_days') ?: 3);
    ?>
    <div class="col-lg-6 col-md-12">
        <div class="card table-card">
            <div class="card-header">
                <h5><i class="feather icon-truck mr-2"></i>Delivery to Site</h5>
                <span class="badge badge-info"><?= count($in_delivery) ?> On The Way</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No Request</th>
                                <th>Tools Name</th>
                                <th>Location</th>
                                <th>Sent At</th>
                                <th class="text-center">In Transit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($in_delivery)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-3">No tool in delivery</td></tr>
                            <?php else: ?>
                                <?php foreach ($in_delivery as $d): ?>
                                <?php $late = $d['hours_in_delivery'] > $sla_hours; ?>
                                <tr>
                                    <td><?= $d['request_number'] ?></td>
                                    <td><strong><?= $d['tool_name'] ?></strong><br><small class="text-muted">(<?= $d['part_number'] ?>)</small></td>
                                    <td><?= $d['site_location'] ?><br><small class="text-muted"><?= $d['user_name'] ?> (<?= $d['nrp'] ?>)</small></td>
                                    <td><?= $d['shipped_at'] ? date('d M Y H:i', strtotime($d['shipped_at'])) : '<span class="text-muted">-</span>' ?></td>
                                    <td class="text-center">
                                        <?php if ($late): ?>
                                            <span class="badge badge-danger"><?= floor($d['hours_in_delivery'] / 24) ?> Hari (Telat)</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning"><?= floor($d['hours_in_delivery'] / 24) ?> Hari</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-12">
        <div class="card table-card">
            <div class="card-header">
                <h5><i class="feather icon-bell mr-2"></i>Need to Return</h5>
                <span class="badge badge-warning"><?= count($return_reminders) ?> Item</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No Request</th>
                                <th>Tools Name</th>
                                <th>Borrower</th>
                                <th>Est. Return</th>
                                <th class="text-center">Reminder</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($return_reminders)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-3">Nothing due, up to <?= $remind_days ?> days</td></tr>
                            <?php else: ?>
                                <?php foreach ($return_reminders as $r): ?>
                                <tr>
                                    <td><?= $r['request_number'] ?></td>
                                    <td><strong><?= $r['tool_name'] ?></strong><br><small class="text-muted">(<?= $r['part_number'] ?>)</small></td>
                                    <td><?= $r['borrower_name'] ?><br><small class="text-muted"><?= $r['site_location'] ?></small></td>
                                    <td><?= date('d M Y H:i', strtotime($r['return_date_est'])) ?></td>
                                    <td class="text-center">
                                        <?php if ($r['hours_left'] < 0): ?>
                                            <span class="badge badge-danger">Telat <?= ceil(abs($r['hours_left']) / 24) ?> Hari</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning"><?= ceil($r['hours_left'] / 24) ?> Hari Lagi</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card table-card">
            <div class="card-header">
                <h5><i class="feather icon-tool mr-2"></i>Currently Borrowed Tools</h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No Request</th>
                                <th>Dates</th>
                                <th>Tools Name</th>
                                <th>Borrower</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($recent_requests)): ?>
                                <?php foreach ($recent_requests as $req): ?>
                                <tr>
                                    <td><?= $req['request_number'] ?></td>
                                    <td><?= date('d F Y', strtotime($req['borrow_date'])) ?></td>
                                    <td><strong><?= $req['tool_name'] ?></strong><br><small class="text-muted">(<?= $req['part_number'] ?>)</small></td>
                                    <td><strong><?= $req['borrower_name'] ?></strong><br><small class="text-muted">(<?= $req['borrower_nrp'] ?>)</small></td>
                                    <td><?= $req['site_location'] ?></td>
                                    <td>
                                        <?php 
                                            if ($req['status'] == 'Submitted') echo '<span class="badge badge-secondary">Submitted</span>';
                                            elseif ($req['status'] == 'Waiting Approval') echo '<span class="badge badge-dark">Waiting SPV</span>';
                                            elseif ($req['status'] == 'Waiting Received') echo '<span class="badge badge-warning">Waiting Received</span>';
                                            elseif ($req['status'] == 'Validating') echo '<span class="badge badge-warning">Validating</span>';
                                            elseif ($req['status'] == 'Borrowed') echo '<span class="badge badge-primary">Borrowed</span>';
                                            elseif ($req['status'] == 'OTW Site') echo '<span class="badge badge-info">OTW Site</span>';
                                            elseif ($req['status'] == 'OTW Workshop') echo '<span class="badge badge-info">OTW Workshop</span>';
                                            elseif ($req['status'] == 'Returned') echo '<span class="badge badge-success">Returned</span>';
                                            elseif ($req['status'] == 'Declined') echo '<span class="badge badge-danger">Declined</span>';
                                            elseif ($req['status'] == 'BAKK') echo '<span class="badge badge-danger">BAKK</span>';
                                        ?>
                                    </td>
                    <td class="text-center">
    <?php $role = $this->session->userdata('role'); ?>

    <?php if ($role == 'user'): ?>
        <button class="btn btn-sm btn-secondary" disabled><i class="feather icon-eye"></i> View Only</button>

    <?php else: ?>
        
        <?php if ($req['status'] == 'Submitted'): ?>
            <?php if ($role == 'master' || $role == 'admin'): ?>
                <button class="btn btn-sm btn-primary btn-action" data-toggle="modal" data-target="#modal-submitted" 
                    data-id="<?= $req['id'] ?>" data-toolid="<?= $req['tool_id'] ?>" data-qty="<?= $req['qty_requested'] ?>" 
                    data-tool="<?= $req['tool_name'] ?> (<?= $req['part_number'] ?>)" 
                    data-user="<?= $req['borrower_name'] ?> - <?= $req['borrower_nrp'] ?>" 
                    data-loc="<?= $req['site_location'] ?>"
                    data-reqnum="<?= $req['request_number'] ?>">
                    <i class="feather icon-navigation"></i> Release
                </button>
            <?php else: ?>
                <button class="btn btn-sm btn-warning" disabled><i class="feather icon-loader"></i> Waiting Approval</button>
            <?php endif; ?>

<!-- ========================================================= -->
        <!-- KAMAR BARU: KHUSUS UNTUK STATUS WAITING RECEIVED -->
        <!-- ========================================================= -->
        <?php elseif ($req['status'] == 'Waiting Received'): ?>
            <?php 
            // Ambil ID User yang sedang login saat ini
            $user_id_saat_ini = $this->session->userdata('user_id');
            
            // HANYA munculkan tombol jika ID peminjam sama dengan ID yang sedang login
            if ($req['user_id'] == $user_id_saat_ini): 
            ?>
                <form action="<?= site_url('dashboard/process_action') ?>" method="post" class="d-inline">
                    <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                    <input type="hidden" name="tool_id" value="<?= $req['tool_id'] ?>">
                    <input type="hidden" name="qty_requested" value="<?= $req['qty_requested'] ?>">
                    <input type="hidden" name="status_action" value="receive">
                    <button type="submit" class="btn btn-success btn-sm text-white shadow-sm" onclick="return confirm('Apakah Anda yakin telah menerima fisik alat ini?');">
                        <i class="fas fa-hand-holding-box mr-1"></i> Receive Tool
                    </button>
                </form>
            <?php else: ?>
                <!-- Jika yang login bukan si peminjam, tampilkan tombol kuning terkunci -->
                <button class="btn btn-sm btn-warning text-dark font-weight-bold shadow-sm" disabled>
                    <i class="feather icon-clock"></i> Waiting Receive
                </button>
            <?php endif; ?>
        <!-- ========================================================= -->

        <?php elseif ($req['status'] == 'Returned'): ?>
            <?php if ($role == 'master' || $role == 'admin'): ?>
                <button class="btn btn-sm btn-info btn-validate-return" data-toggle="modal" data-target="#modal-returned"
                    data-id="<?= $req['id'] ?>" data-toolid="<?= $req['tool_id'] ?>" data-qty="<?= $req['qty_requested'] ?>" 
                    data-tool="<?= $req['tool_name'] ?> (<?= $req['part_number'] ?>)" 
                    data-user="<?= $req['borrower_name'] ?> - <?= $req['borrower_nrp'] ?>"
                    data-reqnum="<?= $req['request_number'] ?>">
                    <i class="feather icon-check-circle mr-1"></i> Validate
                </button>
            <?php else: ?>
                <button class="btn btn-sm btn-secondary" disabled><i class="feather icon-loader"></i> Waiting Approval</button>
            <?php endif; ?>

        <?php elseif ($req['status'] == 'Borrowed' || $req['status'] == 'OTW Site' || $req['status'] == 'OTW Workshop'): ?>
            <?php if ($role == 'leader' || $role == 'admin' || $role == 'master'): ?>
                <form action="<?= site_url('dashboard/process_action') ?>" method="POST" class="d-inline">
                    <input type="hidden" name="status_action" value="return">
                    <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Kembalikan alat ini ke Toolkeeper?')">
                        <i class="feather icon-corner-down-left"></i> Return
                    </button>
                </form>
            <?php else: ?>
                <button class="btn btn-sm btn-secondary" disabled><i class="feather icon-user-check"></i> In Use</button>
            <?php endif; ?>

        <?php elseif ($req['status'] == 'Waiting Approval'): ?>
            <button class="btn btn-sm btn-dark" disabled><i class="feather icon-user"></i> Waiting SPV</button>
        <?php elseif ($req['status'] == 'Validating'): ?>
            <button class="btn btn-sm btn-warning" disabled><i class="feather icon-loader"></i> Processing</button>
        <?php elseif ($req['status'] == 'Declined'): ?>
            <button class="btn btn-sm btn-danger" disabled><i class="feather icon-slash"></i> Declined</button>
        <?php endif; ?>
        
    <?php endif; ?> 
</td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>                       
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <a href="<?= site_url('history') ?>" class="btn btn-sm btn-outline-primary"><i class="feather icon-clock mr-1"></i>View All History</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-returned">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h4 class="modal-title"><i class="feather icon-check-circle mr-2"></i>Return Validation</h4>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?= site_url('dashboard/process_action') ?>" method="POST">
        <div class="modal-body">
          <input type="hidden" name="status_action" value="validate_return">
          <input type="hidden" name="request_id" id="ret_req_id">
          <input type="hidden" name="tool_id" id="ret_tool_id">
          <input type="hidden" name="qty_requested" id="ret_qty">

          <div class="alert alert-info text-center">
            <p class="mb-1">Anda akan mem-validasi pengembalian alat:</p>
            <h6 class="font-weight-bold" id="disp-reqnum-return"></h6>
            <h4 class="font-weight-bold mt-2" id="disp-tool-return"></h4>
            <h5 id="val_tool_name" class="font-weight-bold">Nama Alat</h5>
            <p class="mb-0 mt-2">Borrower: <span id="val_user" class="font-weight-bold">Nama Karyawan</span></p>
          </div>

          <div class="form-group">
            <label>Condition of the Tool:</label>
            <select class="form-control" name="condition" id="toolCondition" onchange="checkCondition(this)" required>
              <option value="good">Good Condition</option>
              <option value="broken">Broken (Rusak)</option>
              <option value="lost">Lost (Hilang)</option>
            </select>
          </div>

          <div id="alert-bakk" class="alert alert-danger" style="display:none;">
            <i class="feather icon-alert-triangle"></i> Perhatian: Status akan diubah menjadi <strong>BAKK</strong>. Stok tidak akan dikembalikan.
          </div>

          <div class="form-group">
            <label>Additional Notes (Optional)</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Example: Tool received in dirty condition..."></textarea>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success"><i class="feather icon-check mr-1"></i>Validate Return</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-submitted">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-secondary text-white">
        <h4 class="modal-title"><i class="feather icon-inbox mr-2"></i>Validation</h4>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <form action="<?= site_url('dashboard/process_action') ?>" method="POST">
        <div class="modal-body">
          <input type="hidden" name="request_id" id="rel_req_id">
          <input type="hidden" name="tool_id" id="rel_tool_id">
          <input type="hidden" name="qty_requested" id="rel_qty">
          <input type="hidden" name="location" id="rel_loc_input">

          <div class="text-center mb-4">
            <i class="feather icon-refresh-cw f-40 text-success mb-3 d-block"></i>
            <p class="mb-1">Anda akan memproses permintaan alat berikut:</p>
            <h6 class="font-weight-bold" id="disp-reqnum-submit"></h6>
            <h4 class="font-weight-bold" id="disp-tool-submit"></h4>
            <h4 id="rel_tool_name" class="font-weight-bold text-primary mt-2">Nama Alat</h4>
            <p class="mt-2 mb-1">Oleh: <strong id="rel_user">Nama Karyawan</strong></p>
            <p class="mb-0">Lokasi: <strong id="rel_loc">Lokasi Proyek</strong></p>
          </div>
          
          <div class="form-group">
            <label>Catatan Admin (Opsional)</label>
            <input type="text" name="notes" class="form-control" placeholder="Misal: Sudah dicek bersama...">
          </div>
          
          <button type="button" class="btn btn-danger btn-block mb-3 btn-trigger-decline">
            <i class="feather icon-slash mr-1"></i> Decline Request
          </button>
          
          <div class="row" id="action-buttons">
              <div class="col-12">
                  <button type="submit" name="status_action" value="send_supervisor" class="btn btn-warning btn-block" id="btn-spv">
                    <i class="feather icon-arrow-right mr-1"></i> Send to Supervisor
                  </button>
                  <button type="submit" name="status_action" value="release" class="btn btn-primary btn-block" id="btn-release">
                    <i class="feather icon-navigation mr-1"></i> Release
                  </button>
              </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-declined">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h4 class="modal-title"><i class="feather icon-slash mr-2"></i>Decline Reason</h4>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <form action="<?= site_url('dashboard/process_action') ?>" method="POST">
        <div class="modal-body">
          <input type="hidden" name="status_action" value="cancel"> 
          <input type="hidden" name="request_id" id="dec_req_id">
          
          <div class="alert alert-danger">
            <p class="mb-0">Berikan alasan mengapa permintaan ini ditolak agar Leader mengetahui penyebabnya.</p>
          </div>
          
          <div class="form-group">
            <label>Alasan Penolakan <span class="text-danger">*</span></label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Contoh: Alat sedang dalam masa perbaikan / Stok fisik tidak sesuai dengan sistem..." required></textarea>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger"><i class="feather icon-navigation mr-1"></i>Submit Decline</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    document.body.addEventListener('click', function(e) {
        const btnRelease = e.target.closest('.btn-action');
        if (btnRelease) {
            const reqId = btnRelease.getAttribute('data-id');
            const toolId = btnRelease.getAttribute('data-toolid');
            const qty = btnRelease.getAttribute('data-qty');
            const toolName = btnRelease.getAttribute('data-tool');
            const user = btnRelease.getAttribute('data-user');
            const loc = btnRelease.getAttribute('data-loc');
            const reqNum = btnRelease.getAttribute('data-reqnum'); 

            if (document.getElementById('rel_req_id')) {
                document.getElementById('rel_req_id').value = reqId;
                document.getElementById('rel_tool_id').value = toolId;
                document.getElementById('rel_qty').value = qty;
                document.getElementById('rel_loc_input').value = loc;
                document.getElementById('rel_tool_name').innerText = toolName;
                document.getElementById('rel_user').innerText = user;
                document.getElementById('rel_loc').innerText = loc;

                if (document.getElementById('disp-reqnum-submit')) {
                    document.getElementById('disp-reqnum-submit').innerText = reqNum;
                }

                if (loc && loc.toUpperCase().includes('SITE')) {
                    document.getElementById('btn-spv').style.display = 'block';
                    document.getElementById('btn-release').style.display = 'none';
                } else {
                    document.getElementById('btn-spv').style.display = 'none';
                    document.getElementById('btn-release').style.display = 'block';
                }
            }
        }

        const btnTriggerDecline = e.target.closest('.btn-trigger-decline');
        if (btnTriggerDecline) {
            const reqId = document.getElementById('rel_req_id').value;
            
            if(reqId) {
                document.getElementById('dec_req_id').value = reqId;
                $('#modal-submitted').modal('hide');
                setTimeout(function() {
                    $('#modal-declined').modal('show');
                }, 400); 
            }
        }

        const btnValidate = e.target.closest('.btn-validate-return');
        if (btnValidate) {
            const reqId = btnValidate.getAttribute('data-id');
            const toolId = btnValidate.getAttribute('data-toolid');
            const qty = btnValidate.getAttribute('data-qty');
            const toolName = btnValidate.getAttribute('data-tool');
            const user = btnValidate.getAttribute('data-user');
            const reqNum = btnValidate.getAttribute('data-reqnum');

            if (document.getElementById('ret_req_id')) {
                document.getElementById('ret_req_id').value = reqId;
                document.getElementById('ret_tool_id').value = toolId;
                document.getElementById('ret_qty').value = qty;
                document.getElementById('val_tool_name').innerText = toolName;
                document.getElementById('val_user').innerText = user;
                
                if (document.getElementById('disp-reqnum-return')) {
                    document.getElementById('disp-reqnum-return').innerText = reqNum;
                }
            }
        }
    });
});

function checkCondition(select) {
    if (select.value === 'broken' || select.value === 'lost') {
        document.getElementById('alert-bakk').style.display = 'block';
    } else {
        document.getElementById('alert-bakk').style.display = 'none';
    }
}

function markRead(id) {
    fetch('<?= site_url("dashboard/mark_read/") ?>' + id);
}
</script>