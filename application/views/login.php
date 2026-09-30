<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Monitoring Tools | Log in</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/dist/img/logo-ewako1.png') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <style>
        .fixed-button, .buy-pro, a[href*="envato.market"] { display: none !important; }
    </style>
</head>
<body class="">
<div class="auth-wrapper">
    <div class="auth-content text-center">
        <div class="card borderless shadow-sm">
            <div class="row align-items-center ">
                <div class="col-md-12">
                    <div class="card-body p-5">
                        <img src="<?= base_url('assets/dist/img/logo-ewako1.png') ?>" alt="Logo" class="img-fluid mb-4" style="max-height: 80px;">
                        <h4 class="mb-2 f-w-600 text-primary">Monitoring Tools</h4>
                        <p class="text-muted mb-4">PT. United Tractors Tbk.</p>
                        <?php if($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger text-center font-weight-bold" role="alert">
                                <i class="feather icon-alert-circle mr-2"></i><?= $this->session->flashdata('error') ?>
                            </div>
                        <?php endif; ?>
                        <form action="<?= site_url('auth/login') ?>" method="post">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="feather icon-user"></i></span>
                                </div>
                                <input type="text" name="nrp" class="form-control" placeholder="ID / NRP" required>
                            </div>
                            
                            <div class="input-group mb-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="feather icon-lock"></i></span>
                                </div>
                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-block btn-primary mb-4 shadow-sm font-weight-bold">
                                <i class="feather icon-log-in mr-2"></i>Sign In
                            </button>
                        </form>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/js/vendor-all.min.js') ?>"></script>
<script src="<?= base_url('assets/js/plugins/bootstrap.min.js') ?>"></script>
<script src="<?= base_url('assets/js/ripple.js') ?>"></script>
<script src="<?= base_url('assets/js/pcoded.min.js') ?>"></script>
<script>
$(function () {
    $('a, button').filter(function() { return $(this).text().trim().includes('Upgrade To Pro'); }).parent().remove();
});
</script>

</body>
</html>