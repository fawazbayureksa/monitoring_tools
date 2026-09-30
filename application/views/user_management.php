<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">User Management</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>"><i class="feather icon-users"></i></a></li>
                    <li class="breadcrumb-item"><a href="#!">User Management</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card table-card">
            <div class="card-header d-flex align-items-center justify-content-between border-bottom">
                <h5 class="mb-0"><i class="feather icon-users mr-2 text-primary"></i>Registered Accounts</h5>
                <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalAddUser">
                    <i class="feather icon-user-plus mr-1"></i> Add New Account
                </button>
            </div>
            
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Full Name</th>
                                <th>User Name</th>
                                <th>Password</th>
                                <th class="text-center">Role</th>
                                <th class="text-center no-export">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=1; foreach($users as $u): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= $u['name'] ?></strong></td>
                                <td><?= $u['nrp'] ?></td>
                                <td><code><?= $u['password'] ?></code></td>
                                <td class="text-center">
                                    <?php if($u['role'] == 'master'): ?>
                                        <span class="badge badge-danger">MASTER</span>
                                    <?php elseif($u['role'] == 'admin'): ?>
                                        <span class="badge badge-primary">ADMIN</span>
                                    <?php elseif($u['role'] == 'leader'): ?>
                                        <span class="badge badge-info">LEADER MECHANIC</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><?= strtoupper(str_replace('_', ' ', $u['role'])) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-info mr-1" data-toggle="modal" data-target="#modalEditUser<?= $u['id'] ?>" title="Edit Account">
                                        <i class="feather icon-edit-2"></i>
                                    </button>
                                    <a href="<?= site_url('user_management/delete/'.$u['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus akun ini?')" title="Delete Account">
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

<?php foreach($users as $u): ?>
<div class="modal fade" id="modalEditUser<?= $u['id'] ?>">
    <div class="modal-dialog">
        <form action="<?= site_url('user_management/edit') ?>" method="post" class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title text-white"><i class="feather icon-edit mr-2"></i>Edit Account</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-left">
                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                <div class="form-group">
                    <label class="font-weight-bold">Full Name</label>
                    <input type="text" name="name" class="form-control" value="<?= $u['name'] ?>" required>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">User Name</label>
                    <input type="text" name="nrp" class="form-control" value="<?= $u['nrp'] ?>" required>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Password</label>
                    <input type="text" name="password" class="form-control" value="<?= $u['password'] ?>" required>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Role</label>
                    <select name="role" class="form-control" required>
                        <option value="admin" <?= $u['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="master" <?= $u['role'] == 'master' ? 'selected' : '' ?>>Master</option>
                        <option value="leader" <?= $u['role'] == 'leader' ? 'selected' : '' ?>>Leader Mechanic</option>
                        <option value="user" <?= $u['role'] == 'user' ? 'selected' : '' ?>>Mechanic</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer justify-content-between bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-info"><i class="feather icon-save mr-1"></i>Save Changes</button>
            </div>
        </form>
    </div>
</div>
<?php endforeach; ?>

<div class="modal fade" id="modalAddUser">
    <div class="modal-dialog">
        <form action="<?= site_url('user_management/add') ?>" method="post" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white"><i class="feather icon-user-plus mr-2"></i>Add New Account</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-left">
                <div class="form-group">
                    <label class="font-weight-bold">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: John Doe" required>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">User Name</label>
                    <input type="text" name="nrp" class="form-control" placeholder="Contoh: leader_upg" required>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Password</label>
                    <input type="text" name="password" class="form-control" placeholder="Masukkan password..." required>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Role</label>
                    <select name="role" class="form-control" required>
                        <option value="leader">Leader Mechanic</option>
                        <option value="user">Mechanic</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer justify-content-between bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary"><i class="feather icon-save mr-1"></i>Save Account</button>
            </div>
        </form>
    </div>
</div>