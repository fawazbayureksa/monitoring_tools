/**
 * App Layout Behaviour:
 * - Desktop sidebar collapse (saved in localStorage)
 * - Mobile sidebar slide-over drawer + backdrop
 * - Accessible dropdown toggles (notifications & user menu)
 * - Dark mode toggle (syncs with body.able-dark-mode)
 */
(function () {
    'use strict';

    var SIDEBAR_STORAGE_KEY = 'mt_sidebar_collapsed';
    var THEME_STORAGE_KEY = 'theme';
    var DESKTOP_BREAKPOINT = 992;

    var sidebar = document.getElementById('appSidebar');
    if (!sidebar) return;

    var body = document.body;
    var toggleBtn = document.getElementById('sidebarToggle');
    var backdrop = document.getElementById('sidebarBackdrop');
    var themeToggle = document.getElementById('themeToggle');

    function isDesktop() {
        return window.innerWidth >= DESKTOP_BREAKPOINT;
    }

    function restoreSidebarState() {
        if (isDesktop() && localStorage.getItem(SIDEBAR_STORAGE_KEY) === '1') {
            body.classList.add('is-sidebar-collapsed');
        }
    }

    function closeMobileSidebar() {
        body.classList.remove('is-sidebar-open');
        if (backdrop) {
            backdrop.classList.remove('is-open');
        }
    }

    function handleSidebarToggle() {
        if (isDesktop()) {
            var collapsed = body.classList.toggle('is-sidebar-collapsed');
            localStorage.setItem(SIDEBAR_STORAGE_KEY, collapsed ? '1' : '0');
        } else {
            var open = body.classList.toggle('is-sidebar-open');
            if (backdrop) {
                backdrop.classList.toggle('is-open', open);
            }
        }
    }

    function closeAllDropdowns(exceptDropdown) {
        var openDropdowns = document.querySelectorAll('.app-dropdown.is-open');
        for (var i = 0; i < openDropdowns.length; i++) {
            var item = openDropdowns[i];
            if (item !== exceptDropdown) {
                item.classList.remove('is-open');
                var btn = item.querySelector('[aria-expanded]');
                if (btn) btn.setAttribute('aria-expanded', 'false');
            }
        }
    }

    function setupDropdown(buttonId, wrapperId) {
        var btn = document.getElementById(buttonId);
        var wrapper = document.getElementById(wrapperId);
        if (!btn || !wrapper) return;

        btn.addEventListener('click', function (event) {
            event.stopPropagation();
            var willOpen = !wrapper.classList.contains('is-open');
            closeAllDropdowns(wrapper);
            wrapper.classList.toggle('is-open', willOpen);
            btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        });
    }

    function setupThemeToggle() {
        if (!themeToggle) return;
        if (localStorage.getItem(THEME_STORAGE_KEY) === 'dark') {
            body.classList.add('able-dark-mode');
        }
        themeToggle.addEventListener('click', function () {
            var isDark = body.classList.toggle('able-dark-mode');
            localStorage.setItem(THEME_STORAGE_KEY, isDark ? 'dark' : 'light');
        });
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', handleSidebarToggle);
    }
    if (backdrop) {
        backdrop.addEventListener('click', closeMobileSidebar);
    }

    setupDropdown('notifToggle', 'notifDropdown');
    setupDropdown('userMenuToggle', 'userMenuDropdown');
    setupThemeToggle();
    restoreSidebarState();

    document.addEventListener('click', function () {
        closeAllDropdowns(null);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeAllDropdowns(null);
            closeMobileSidebar();
        }
    });
})();
