<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Transaction History</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>"><i class="feather icon-clock"></i></a></li>
                    <li class="breadcrumb-item"><a href="#!">Log History</a></li>
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
    <div class="col-sm-12">
        <div class="card table-card">
            <div class="card-header border-bottom">
                <h5><i class="feather icon-clock mr-2 text-primary"></i>Transaction History Log</h5>
            </div>
            
            <div class="card-body p-4">

                <form action="<?= site_url('history') ?>" method="GET">
                    <div class="row mb-4 align-items-end bg-light p-3 rounded">
                        <div class="col-md-5">
                            <label class="font-weight-bold">Date Range Filter (Borrow Date):</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white">
                                        <i class="feather icon-calendar text-primary"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control" id="reservation" name="date_range" value="<?= $this->input->get('date_range') ?>" placeholder="Pilih rentang tanggal...">
                            </div>
                        </div>
                        <div class="col-md-4 mt-3 mt-md-0">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="feather icon-filter mr-1"></i> Filter Data
                            </button>
                            <a href="<?= site_url('history') ?>" class="btn btn-secondary">
                                <i class="feather icon-refresh-cw mr-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>No Request</th>
                                <th>Tools (Part No)</th>
                                <th>Borrower (NRP)</th>
                                <th>Date Borrowed</th>
                                <th>Date Returned</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                            <tbody>
                            <?php $no=1; foreach ($history as $h): ?>
                            <tr>
                                <td><strong><?= $h['request_number'] ?></strong></td>
                                <td><?= $h['tool_name'] ?> <br><small class="text-muted">(<?= $h['part_number'] ?>)</small></td>
                                <td><?= $h['borrower_nrp'] ?> <br><small class="text-muted">(<?= $h['borrower_name'] ?>)</small></td>
                                <td><?= date('d M Y, H:i', strtotime($h['borrow_date'])) ?></td>
                                <td>
                                    <?php if(!empty($h['return_date_actual']) && $h['return_date_actual'] != '0000-00-00 00:00:00'): ?>
                                        <?= date('d M Y, H:i', strtotime($h['return_date_actual'])) ?>
                                    <?php else: ?> 
                                        <span class="text-muted">-</span> 
                                    <?php endif; ?>
                                </td>
                                
                                <td class="text-center">
                                    <?php 
                                        $current_status = trim($h['status']); 
                                    ?>

                                    <?php if ($current_status == 'Completed'): ?>
                                        <span class="badge badge-success">Completed</span>

                                    <?php elseif ($current_status == 'Declined'): ?>
                                        <span class="badge badge-danger">Declined</span>

                                <?php elseif (strpos(strtoupper($current_status), 'BAKK') !== false): ?>
                                        <span class="badge badge-danger"><?= $current_status ?></span>
                                        <br>
                                        
                                        <?php if (!empty($h['file_path'])): ?>
                                <?php 

                                    $clean_base = str_replace(array('index.php/', 'index.php'), '', base_url());
                                    $file_url = $clean_base . 'assets/uploads/bakk/' . trim($h['file_path']);
                                ?>
                                        <a href="javascript:void(0)" class="badge badge-info mt-1 btn-view-bakk" 
                                        data-toggle="modal" 
                                        data-target="#modal-bakk"
                                        data-file="<?= $file_url ?>"
                                        data-notes="<?= htmlspecialchars($h['bakk_notes']) ?>"
                                        data-reqnum="<?= $h['request_number'] ?>"
                                        data-tool="<?= $h['tool_name'] ?> (<?= $h['part_number'] ?>)"
                                        data-borrower="<?= $h['borrower_nrp'] ?> (<?= $h['borrower_name'] ?>)"
                                        data-dateborrow="<?= date('d M Y, H:i', strtotime($h['borrow_date'])) ?>"
                                        data-datereturn="<?= (!empty($h['return_date_actual']) && $h['return_date_actual'] != '0000-00-00 00:00:00') ? date('d M Y, H:i', strtotime($h['return_date_actual'])) : '-' ?>">
                                        <i class="feather icon-info"></i> Detail Info
                                        </a>
                                        <?php else: ?>
                                            <span class="badge badge-warning mt-1 text-dark">
                                                <i class="feather icon-clock"></i> Menunggu Upload
                                            </span>
                                        <?php endif; ?>

                                    <?php else: ?>
                                        <span class="badge badge-secondary"><?= $current_status ?></span>
                                    <?php endif; ?>
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
<div class="modal fade" id="modal-bakk" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title text-white"><i class="feather icon-file-text mr-2"></i>Detail Information BAKK</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <div class="p-3 mb-3 text-left" style="font-size: 13px; background: #f8f9fa; border-radius: 8px;">
                    <div class="row mb-2">
                        <div class="col-sm-4 col-12 font-weight-bold text-muted">No Request</div>
                        <div class="col-sm-8 col-12" style="word-wrap: break-word;">
                            <span class="d-none d-sm-inline">: </span><strong id="display-reqnum"></strong>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 col-12 font-weight-bold text-muted">Nama Tools</div>
                        <div class="col-sm-8 col-12" style="word-wrap: break-word;">
                            <span class="d-none d-sm-inline">: </span><span id="display-tool"></span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 col-12 font-weight-bold text-muted">Borrower (NRP)</div>
                        <div class="col-sm-8 col-12" style="word-wrap: break-word;">
                            <span class="d-none d-sm-inline">: </span><span id="display-borrower"></span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 col-12 font-weight-bold text-muted">Date Borrowed</div>
                        <div class="col-sm-8 col-12">
                            <span class="d-none d-sm-inline">: </span><span id="display-dateborrow"></span>
                        </div>
                    </div>
                    <div class="row mb-0">
                        <div class="col-sm-4 col-12 font-weight-bold text-muted">Date Returned</div>
                        <div class="col-sm-8 col-12">
                            <span class="d-none d-sm-inline">: </span><span id="display-datereturn"></span>
                        </div>
                    </div>


                        <div class="alert alert-secondary text-left mb-3">
                            <h6 class="font-weight-bold mb-1" style="font-size: 13px;">Remark:</h6>
                            <p id="bakk-notes-display" class="mb-0 text-dark" style="font-size: 13px;"></p>
                        </div>
                        
                        <hr>
                        <h6 class="font-weight-bold mb-2 text-center">Preview Dokumen:</h6>
                        <div id="bakk-file-preview" style="min-height: 300px; background: #f4f7fa; border-radius: 8px; padding: 10px;">
                        </div>
                
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                
                <?php if($this->session->userdata('role') == 'master' || $this->session->userdata('role') == 'admin'): ?>
                    <a href="#" id="btn-download-bakk" class="btn btn-success shadow-sm" download>
                        <i class="feather icon-download mr-1"></i> Download File
                    </a>
                <?php else: ?>
                    <div>  

                    </div> <?php endif; ?>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
$(document).on('click', '.btn-view-bakk', function() {
    let btn = $(this);
    let fileUrl = btn.attr('data-file');
    let notes = btn.attr('data-notes');

    let reqNum = btn.attr('data-reqnum');
    let tool = btn.attr('data-tool');
    let borrower = btn.attr('data-borrower');
    let dateBorrow = btn.attr('data-dateborrow');
    let dateReturn = btn.attr('data-datereturn');

    $('#display-reqnum').text(reqNum);
    $('#display-tool').text(tool);
    $('#display-borrower').text(borrower);
    $('#display-dateborrow').text(dateBorrow);
    $('#display-datereturn').text(dateReturn);

    if(notes && notes.trim() !== '') {
        $('#bakk-notes-display').text(notes);
    } else {
        $('#bakk-notes-display').text('-');
    }

        let previewUrl = fileUrl + '?view=bypass#toolbar=1';
        let ext = fileUrl.split('.').pop().toLowerCase();
        let previewContainer = $('#bakk-file-preview');
        
        if(ext === 'pdf') {
            previewContainer.html('<iframe src="'+previewUrl+'" width="100%" height="450px" style="border-radius: 5px; border: 1px solid #ddd;" frameborder="0"></iframe>');
        } else if(ext === 'jpg' || ext === 'jpeg' || ext === 'png') {
            previewContainer.html('<img src="'+fileUrl+'" class="img-fluid shadow-sm" style="max-height: 450px; border-radius: 5px; border: 1px solid #ddd;" alt="BAKK Document" />');
        } else {
            previewContainer.html('<p class="text-danger mt-4"><i class="feather icon-alert-circle"></i> Format file tidak dapat dipratinjau.</p>');
        }

        $('#btn-download-bakk').removeAttr('download').attr('href', '#').off('click').on('click', function(e) {
            e.preventDefault(); 
            
            let btn = $(this);
            let originalText = btn.html();
            btn.html('<i class="feather icon-loader"></i> Mendownload...');

            fetch(fileUrl)
                .then(response => response.blob())
                .then(blob => {
                    let blobUrl = window.URL.createObjectURL(blob);
                    let a = document.createElement('a');
                    a.href = blobUrl;
                    a.download = fileUrl.split('/').pop();
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(blobUrl);
                    
                    btn.html(originalText);
                })
                .catch(err => {
                    console.error('Download gagal:', err);
                    btn.html(originalText);
                    alert('Gagal mendownload file.');
                });
        });
    });

    $('#modal-bakk').on('hidden.bs.modal', function () {
        $('#bakk-file-preview').html('');
    });
});
</script>