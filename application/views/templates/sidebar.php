<?php
$role    = strtolower((string) $this->session->userdata('role'));
$rawName = (string) $this->session->userdata('name');
$name    = $rawName !== '' ? $rawName : 'User';
$initial = strtoupper(substr(trim($name), 0, 1));
$segment = strtolower((string) $this->uri->segment(1));
$logo    = base_url('assets/dist/img/logo-ewako1.png');

$roleLabels = [
    'master'     => 'Master',
    'supervisor' => 'Supervisor',
    'admin'      => 'Toolkeeper',
    'leader'     => 'Leader Mechanic',
    'user'       => 'Mechanic',
];
$roleLabel = isset($roleLabels[$role]) ? $roleLabels[$role] : ucfirst($role);

// Fetch notifications
$CI =& get_instance();
$CI->load->model('M_Requests');
$notifications = $CI->M_Requests->get_notifications();
$notifCount    = isset($notifications['count']) ? (int) $notifications['count'] : 0;
$notifData     = isset($notifications['data']) && is_array($notifications['data']) ? $notifications['data'] : [];

// Declarative navigation structure with clean SVG icons & role permissions
$menuGroups = [
    [
        'caption' => 'Navigation',
        'items'   => [
            [
                'label' => 'Dashboard',
                'slug'  => 'dashboard',
                'url'   => site_url('dashboard'),
                'roles' => ['master', 'supervisor', 'admin', 'leader', 'user'],
                'icon'  => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
            ],
            [
                'label' => 'Request Tools',
                'slug'  => 'request_tools',
                'url'   => site_url('request_tools'),
                'roles' => ['master', 'supervisor', 'admin', 'leader'],
                'icon'  => '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>',
            ],
            [
                'label' => 'Stock Tools',
                'slug'  => 'stock_tools',
                'url'   => site_url('stock_tools'),
                'roles' => ['master', 'supervisor', 'admin'],
                'icon'  => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>',
            ],
            [
                'label' => 'BAKK Tools',
                'slug'  => 'bakk',
                'url'   => site_url('bakk'),
                'roles' => ['master', 'supervisor', 'admin'],
                'icon'  => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>',
            ],
            [
                'label' => 'History',
                'slug'  => 'history',
                'url'   => site_url('history'),
                'roles' => ['master', 'supervisor', 'admin', 'leader', 'user'],
                'icon'  => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
            ],
        ],
    ],
    [
        'caption' => 'Administration',
        'items'   => [
            [
                'label' => 'User Management',
                'slug'  => 'user_management',
                'url'   => site_url('user_management'),
                'roles' => ['master'],
                'icon'  => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
            ],
        ],
    ],
    [
        'caption' => 'Help & Info',
        'items'   => [
            [
                'label' => 'About',
                'slug'  => 'about',
                'url'   => site_url('about'),
                'roles' => ['master', 'supervisor', 'admin', 'leader', 'user'],
                'icon'  => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>',
            ],
            [
                'label'    => 'Help Support',
                'slug'     => 'help',
                'url'      => 'https://wa.me/6287771220505',
                'external' => true,
                'roles'    => ['master', 'supervisor', 'admin', 'leader', 'user'],
                'icon'     => '<circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
            ],
        ],
    ],
];

// Derive active page title for topbar
$activePageTitle = 'Dashboard';
foreach ($menuGroups as $group) {
    foreach ($group['items'] as $item) {
        if ($item['slug'] === $segment) {
            $activePageTitle = $item['label'];
            break 2;
        }
    }
}
?>
<aside class="app-sidebar" id="appSidebar" aria-label="Main Navigation">
    <div class="app-sidebar__header">
        <a href="<?= site_url('dashboard') ?>" class="app-sidebar__brand">
            <img src="<?= $logo ?>" alt="Logo" class="app-sidebar__logo">
            <div class="app-sidebar__brand-text">
                <span class="app-sidebar__brand-title">Monitoring Tools</span>
                <span class="app-sidebar__brand-sub">PT. United Tractors Tbk.</span>
            </div>
        </a>
    </div>

    <div class="app-sidebar__body">
        <?php foreach ($menuGroups as $group): ?>
            <?php
            $visibleItems = array_filter($group['items'], function ($item) use ($role) {
                return in_array($role, $item['roles'], true);
            });
            if (empty($visibleItems)) {
                continue;
            }
            ?>
            <div class="app-sidebar__group">
                <div class="app-sidebar__caption"><?= html_escape($group['caption']) ?></div>
                <ul class="app-sidebar__menu">
                    <?php foreach ($visibleItems as $item): ?>
                        <?php
                        $isActive   = ($segment === $item['slug']);
                        $isExternal = !empty($item['external']);
                        ?>
                        <li>
                            <a href="<?= html_escape($item['url']) ?>"
                               class="app-sidebar__link<?= $isActive ? ' is-active' : '' ?>"
                               title="<?= html_escape($item['label']) ?>"
                               <?= $isExternal ? 'target="_blank" rel="noopener noreferrer"' : '' ?>>
                                <svg class="app-sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><?= $item['icon'] ?></svg>
                                <span class="app-sidebar__label"><?= html_escape($item['label']) ?></span>
                                <?php if ($isExternal): ?>
                                    <svg class="app-sidebar__ext-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="app-sidebar__footer">
        <div class="app-sidebar__user">
            <div class="app-sidebar__avatar" aria-hidden="true"><?= html_escape($initial) ?></div>
            <div class="app-sidebar__user-info">
                <div class="app-sidebar__user-name"><?= html_escape($name) ?></div>
                <span class="app-sidebar__user-role"><?= html_escape($roleLabel) ?></span>
            </div>
            <a href="<?= site_url('auth/logout') ?>" class="app-sidebar__logout" title="Sign out" aria-label="Sign out">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </a>
        </div>
    </div>
</aside>

<div class="app-backdrop" id="sidebarBackdrop"></div>

<header class="app-topbar">
    <div class="app-topbar__left">
        <button type="button" class="app-topbar__toggle" id="sidebarToggle" aria-label="Toggle sidebar">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <h1 class="app-topbar__title"><?= html_escape($activePageTitle) ?></h1>
    </div>

    <div class="app-topbar__right">
        <button type="button" class="app-topbar__btn" id="themeToggle" title="Toggle Dark Mode" aria-label="Toggle Dark Mode">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
        </button>

        <div class="app-dropdown" id="notifDropdown">
            <button type="button" class="app-topbar__btn" id="notifToggle" aria-expanded="false" aria-label="Notifications" title="Notifications">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                <?php if ($notifCount > 0): ?>
                    <span class="app-topbar__badge"><?= $notifCount ?></span>
                <?php endif; ?>
            </button>

            <div class="app-dropdown__menu" role="menu">
                <div class="app-dropdown__head">
                    <h6 class="app-dropdown__title">Notifications</h6>
                    <span class="app-dropdown__count"><?= $notifCount ?> New</span>
                </div>
                <ul class="app-dropdown__list">
                    <?php if ($notifCount > 0): ?>
                        <?php foreach ($notifData as $n): ?>
                            <li>
                                <a href="<?= html_escape($n['link']) ?>" class="app-dropdown__item">
                                    <span class="app-dropdown__notif-dot"></span>
                                    <span class="app-dropdown__notif-text"><?= html_escape($n['text']) ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="app-dropdown__empty">No new notifications</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="app-dropdown" id="userMenuDropdown">
            <button type="button" class="app-topbar__user-btn" id="userMenuToggle" aria-expanded="false" aria-label="User menu">
                <span class="app-topbar__user-avatar"><?= html_escape($initial) ?></span>
                <span class="app-topbar__user-name"><?= html_escape($name) ?></span>
                <svg class="app-topbar__user-caret" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </button>

            <div class="app-dropdown__menu app-dropdown__menu--sm" role="menu">
                <div class="app-dropdown__head">
                    <div>
                        <h6 class="app-dropdown__title"><?= html_escape($name) ?></h6>
                        <span class="app-sidebar__user-role"><?= html_escape($roleLabel) ?></span>
                    </div>
                </div>
                <ul class="app-dropdown__list">
                    <li>
                        <a href="<?= site_url('auth/logout') ?>" class="app-dropdown__item app-dropdown__item--danger">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            <span>Sign out</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>

<div class="pcoded-main-container">
    <div class="pcoded-content">
