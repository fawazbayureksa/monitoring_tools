<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">BAKK Tools</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>"><i class="feather icon-file-text"></i></a></li>
                    <li class="breadcrumb-item"><a href="#!">BAKK</a></li>
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
    <div class="col-12">
        <div class="card table-card">
            <div class="card-header border-bottom">
                <h5><i class="feather icon-file-text mr-2 text-danger"></i>Daftar Alat Rusak / Hilang</h5>
            </div>
            
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Tools Name</th>
                                <th>No Parts</th>
                                <th>Type</th>
                                <th>Rack Location</th>
                                <th class="text-center">Condition</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center no-export">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bakk_list as $b): ?>
                            <tr>
                                <td><strong><?= $b['tool_name'] ?></strong></td>
                                <td><?= $b['part_number'] ?></td>
                                <td><?= $b['type'] ?></td>
                                <td><?= $b['rack_location'] ?></td>
                                <td class="text-center">
                                    <?php if($b['condition_status'] == 'HILANG'): ?>
                                        <span class="badge badge-danger">HILANG</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">RUSAK</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-secondary"><?= $b['upload_status'] ?></span>
                                </td>
                                <td class="text-center font-weight-bold">1</td>
                                <td class="text-center">  
                                <button type="button" class="btn btn-sm btn-danger btn-trigger-upload" data-toggle="modal" data-target="#modal-upload" data-id="<?= $b['id'] ?>"> <i class="feather icon-upload-cloud"></i>
                                </button>
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

<div class="modal fade" id="modal-upload">
    <div class="modal-dialog"> 
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h4 class="modal-title"><i class="feather icon-upload mr-2"></i> Upload Dokumen BAKK</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="<?= site_url('bakk/process_upload') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    
                    <div class="alert alert-info">
                        <h6 class="alert-heading font-weight-bold mb-1"><i class="feather icon-info mr-1"></i> Info</h6>
                        <p class="mb-0" style="font-size: 13px;">Pastikan file yang diupload sudah ditandatangani oleh atasan. Format yang diizinkan: <strong>PDF / JPG</strong>.</p>
                    </div>

                        <input type="hidden" name="bakk_id" id="upload_bakk_id">

                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="berkas_bakk" name="berkas_bakk" accept=".pdf,.jpg,.jpeg,.png" required>
                            <label class="custom-file-label" for="berkas_bakk">Choose file</label>
                        </div>
                    
                    <div class="form-group">
                        <label class="font-weight-bold">Remark (Opsional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Keterangan detail kerusakan atau hilangnya alat..."></textarea>
                    </div>
                </div>

                <div class="modal-footer justify-content-between bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="feather icon-upload-cloud mr-1"></i> Upload File
                    </button>
                </div>
            </form> 
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof bsCustomFileInput !== 'undefined') {
        bsCustomFileInput.init();
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. SCRIPT MENGUBAH "CHOOSE FILE" JADI NAMA FILE
    $('.custom-file-input').on('change', function() {
        // Ambil nama file dari komputer user
        let fileName = $(this).val().split('\\').pop(); 
        // Timpa tulisan "Choose file" dengan nama file tersebut
        $(this).next('.custom-file-label').addClass("selected").html(fileName); 
    });

    // 2. SCRIPT MENGIRIM ID KE DALAM MODAL SAAT TOMBOL DIKLIK
    $(document).on('click', '.btn-trigger-upload', function() {
        let idBakk = $(this).data('id');
        // Masukkan ID ke input hidden
        $('#upload_bakk_id').val(idBakk); 
        
        // Reset form agar bersih saat modal baru dibuka
        $('.custom-file-label').html('Choose file');
        $('.custom-file-input').val('');
        $('textarea[name="notes"]').val('');
    });

});
</script>