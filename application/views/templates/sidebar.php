<?php $role = $this->session->userdata('role'); ?>
    <nav class="pcoded-navbar menu-light">
        <div class="navbar-wrapper">
            <div class="navbar-content scroll-div">

                <div class="">
                    <div class="main-menu-header">
                        <img src="<?= base_url('assets/dist/img/user.png') ?>" 
                            onerror="this.onerror=null; this.src='<?= base_url('assets/dist/img/user.png') ?>';" 
                            class="img-radius" 
                            alt="User-Profile" 
                            style="width: 60px; height: 60px; object-fit: cover; background-color: #fff;">
                        <div class="user-details">
                            <div id="more-details"><?= strtoupper($this->session->userdata('name')) ?> <i class="fa fa-caret-down"></i></div>
                        </div>
                    </div>
                    <div class="collapse" id="nav-user-link">
                        <ul class="list-unstyled">
                            <li class="list-group-item"><a href="<?= base_url('auth/logout') ?>" class="text-danger"><i class="feather icon-log-out m-r-5"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
                
                <ul class="nav pcoded-inner-navbar">
                    <li class="nav-item pcoded-menu-caption">
                        <label>Navigation</label>
                    </li>
                    <li class="nav-item <?= $this->uri->segment(1) == 'dashboard' ? 'active' : '' ?>">
                        <a href="<?= site_url('dashboard') ?>" class="nav-link "><span class="pcoded-micon"><i class="feather icon-home"></i></span><span class="pcoded-mtext">Dashboard</span></a>
                    </li>

                    <?php if($role == 'leader' || $role == 'master' || $role == 'admin'): ?>
                    <li class="nav-item <?= $this->uri->segment(1) == 'Request_tools' ? 'active' : '' ?>">
                        <a href="<?= site_url('request_tools') ?>" class="nav-link "><span class="pcoded-micon"><i class="feather icon-edit"></i></span><span class="pcoded-mtext">Request Tools</span></a>
                    </li>
                    <?php endif; ?>

                    <?php if($role == 'master' || $role == 'admin'): ?>
                    <li class="nav-item <?= $this->uri->segment(1) == 'stock_tools' ? 'active' : '' ?>">
                        <a href="<?= site_url('stock_tools') ?>" class="nav-link "><span class="pcoded-micon"><i class="feather icon-box"></i></span><span class="pcoded-mtext">Stock Tools</span></a>
                    </li>
                    <li class="nav-item <?= $this->uri->segment(1) == 'bakk' ? 'active' : '' ?>">
                        <a href="<?= site_url('bakk') ?>" class="nav-link "><span class="pcoded-micon"><i class="feather icon-file-text"></i></span><span class="pcoded-mtext">BAKK Tools</span></a>
                    </li>
                    <?php endif; ?>

                    <li class="nav-item <?= $this->uri->segment(1) == 'history' ? 'active' : '' ?>">
                        <a href="<?= site_url('history') ?>" class="nav-link "><span class="pcoded-micon"><i class="feather icon-clock"></i></span><span class="pcoded-mtext">History</span></a>
                    </li>

                    <?php if($role == 'master'): ?>
                    <li class="nav-item pcoded-menu-caption">
                        <label>Administration</label>
                    </li>
                    <li class="nav-item <?= $this->uri->segment(1) == 'user_management' ? 'active' : '' ?>">
                        <a href="<?= site_url('user_management') ?>" class="nav-link "><span class="pcoded-micon"><i class="feather icon-users"></i></span><span class="pcoded-mtext">User Management</span></a>
                    </li>
                    <?php endif; ?>

                    <li class="nav-item pcoded-menu-caption">
                        <label>Help & Info</label>
                    </li>
                    <li class="nav-item <?= $this->uri->segment(1) == 'about' ? 'active' : '' ?>">
                        <a href="<?= base_url('about') ?>" class="nav-link "><span class="pcoded-micon"><i class="feather icon-info"></i></span><span class="pcoded-mtext">About</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="https://wa.me/087771220505" class="nav-link "><span class="pcoded-micon"><i class="feather icon-help-circle"></i></span><span class="pcoded-mtext">Help</span></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="navbar pcoded-header navbar-expand-lg navbar-light header-blue">
        <div class="m-header">
            <a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
            <a href="<?= site_url('dashboard') ?>" class="b-brand">
                <img src="<?= base_url('assets/dist/img/logo-ewako1.png') ?>" alt="Logo" class="logo" style="height: 35px; width: auto; margin-right: 0px;">
                <span class="b-title text-white" style="white-space: nowrap; font-size: 12px; font-weight: bold; letter-spacing: 0.5px;">Monitoring Tools</span>
            </a>

            <a href="#!" class="mob-toggler">
            <i class="feather icon-more-vertical"></i>
        </a>
    </div>
        <div class="collapse navbar-collapse">
        <ul class="navbar-nav ml-auto">
            
            <li class="nav-item mr-3">
                <a href="#!" id="darkModeSwitch" class="nav-item" title="Toggle Dark Mode">
                    <i class="feather icon-moon"></i>
                </a>
            </li>

                <?php 
                    $CI =& get_instance();
                    $CI->load->model('M_Requests');
                    $notifications = $CI->M_Requests->get_notifications();
                    $notif_count = $notifications['count'];
                    $notif_data = $notifications['data'];
                ?>
                
                <li class="nav-item">
                        <div class="dropdown">
                            <a href="#!" class="dropdown-toggle" data-toggle="dropdown" title="Notifications" style="position: relative;">
                                <i class="feather icon-bell"></i>
                            <?php if($notif_count > 0): ?>
                                <span class="badge badge-danger"><?= $notif_count ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right notification">
                            <div class="noti-head">
                                <h6 class="d-inline-block m-b-0"><?= $notif_count ?> Notifikasi Baru</h6>
                            </div>
                            <ul class="noti-body">
                                <?php if($notif_count > 0): ?>
                                    <?php foreach($notif_data as $n): ?>
                                    <li class="notification">
                                        <div class="media">
                                            <div class="media-body">
                                                <a href="<?= $n['link'] ?>">
                                                    <p><strong><i class="<?= $n['icon'] ?> m-r-10"></i><?= $n['text'] ?></strong></p>
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="notification">
                                        <div class="media">
                                            <div class="media-body text-center">
                                                <p class="text-muted m-b-0">Tidak ada notifikasi/request.</p>
                                            </div>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </li>

                <li class="nav-item">
                    <div class="dropdown drp-user">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="feather icon-user"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right profile-notification">
                            <div class="pro-head">
                            <img src="<?= base_url('assets/dist/img/user.png') ?>" 
                            onerror="this.onerror=null; this.src='<?= base_url('assets/dist/img/icons8-avatars-50.png') ?>';" 
                            class="img-radius" 
                            alt="User-Profile" 
                            style="width: 50px; height: 50px; object-fit: cover; background-color: #fff;">
                                <span><?= strtoupper($this->session->userdata('name')) ?></span>
                                <a href="<?= base_url('auth/logout') ?>" class="dud-logout" title="Logout">
                                    <i class="feather icon-log-out text-danger"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </header>
<div class="pcoded-main-container">
    <div class="pcoded-content">