<?php
$logo  = base_url('assets/dist/img/logo-ewako1.png');
$error = $this->session->flashdata('error');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Tools | Sign in</title>
    <link rel="icon" type="image/png" href="<?= $logo ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">

    <link rel="stylesheet" href="<?= base_url('assets/app/css/tokens.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/app/css/base.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/app/css/components.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/app/css/pages/login.css') ?>">
</head>
<body>
<main class="login">

    <section class="login-hero" aria-hidden="true">
        <div class="login-hero__brand">
            <img class="login-hero__logo" src="<?= $logo ?>" alt="">
            <span>PT. United Tractors Tbk.</span>
        </div>

        <div>
            <h1 class="login-hero__title">Track every tool, from request to return.</h1>
            <p class="login-hero__lead">
                Monitoring Tools gives your team real-time visibility of stock,
                requests, and borrowing history — all in one place.
            </p>
            <ul class="login-hero__features">
                <li class="login-hero__feature"><strong>Real-time</strong><span>Stock monitoring</span></li>
                <li class="login-hero__feature"><strong>Fast</strong><span>Tool requests</span></li>
                <li class="login-hero__feature"><strong>Complete</strong><span>Usage history</span></li>
            </ul>
        </div>

        <small class="login-hero__copyright">&copy; <?= date('Y') ?> Monitoring Tools</small>
    </section>

    <section class="login-panel">
        <div class="login-card">
            <img class="login-card__logo" src="<?= $logo ?>" alt="Logo">
            <h2 class="login-card__title">Welcome back</h2>
            <p class="login-card__subtitle">Sign in with your NRP to continue.</p>

            <?php if ($error): ?>
                <div class="alert alert--danger" role="alert">
                    <svg class="alert__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span><?= html_escape($error) ?></span>
                </div>
            <?php endif; ?>

            <form id="loginForm" action="<?= site_url('auth/login') ?>" method="post" novalidate>
                <div class="field">
                    <label class="field__label" for="nrp">ID / NRP</label>
                    <div class="field__control">
                        <input class="field__input" type="text" id="nrp" name="nrp"
                               placeholder="e.g. 12345678" autocomplete="username" required autofocus>
                        <svg class="field__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                </div>

                <div class="field">
                    <label class="field__label" for="password">Password</label>
                    <div class="field__control">
                        <input class="field__input" type="password" id="password" name="password"
                               placeholder="••••••••" autocomplete="current-password" required>
                        <svg class="field__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <button class="field__action" type="button" id="togglePassword" aria-label="Show password">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <div class="login-card__options">
                    <label class="checkbox"><input type="checkbox" id="rememberNrp"> Remember my NRP</label>
                </div>

                <button class="btn btn--primary btn--block" type="submit" id="submitButton">
                    <span class="btn__spinner"></span>
                    <svg class="btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    <span>Sign in</span>
                </button>
            </form>

            <p class="login-card__note">Having trouble signing in? Contact your administrator.</p>
        </div>
    </section>

</main>

<script src="<?= base_url('assets/app/js/pages/login.js') ?>"></script>
</body>
</html>
