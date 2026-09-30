<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    
    <title>Monitoring Tools | <?= isset($title) ? $title : 'Dashboard' ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/dist/img/logo-ewako1.png') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/plugins/select2/css/select2.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/plugins/daterangepicker/daterangepicker.css') ?>">
    
    <style>
        .fixed-button, .buy-pro, a[href*="envato.market"] { display: none !important; }
    </style>
    <style>
        body.able-dark-mode { background-color: #232a3b; color: #d1d5db; }
        body.able-dark-mode .card, 
        body.able-dark-mode .pcoded-navbar, 
        body.able-dark-mode .page-header { background-color: #2c364c; box-shadow: none; border: 1px solid #3b465e; }
        body.able-dark-mode .card-header, 
        body.able-dark-mode .border-bottom { border-bottom-color: #3b465e !important; }
        body.able-dark-mode .table th, 
        body.able-dark-mode .table td { border-color: #3b465e; color: #d1d5db; }
        body.able-dark-mode .table-hover tbody tr:hover { background-color: #3b465e; }
        body.able-dark-mode .modal-content { background-color: #2c364c; color: #d1d5db; }
        body.able-dark-mode .form-control { background-color: #232a3b; border-color: #3b465e; color: #d1d5db; }
        body.able-dark-mode .text-muted { color: #a1aab7 !important; }
        body.able-dark-mode .pcoded-navbar.menu-light .pcoded-inner-navbar > li > a { color: #d1d5db; }
        body.able-dark-mode .pcoded-navbar.menu-light .pcoded-inner-navbar > li.active > a { background: #4680ff; color: #fff; }
        body.able-dark-mode .breadcrumb-item a { color: #a1aab7; }
    </style>

    
</head>

<body class=""> 
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>