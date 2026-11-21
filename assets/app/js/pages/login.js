/**
 * Page: Login
 * - Show / hide password
 * - Remember NRP (never the password)
 * - Basic validation + loading state on submit
 */
(function () {
    'use strict';

    var STORAGE_KEY = 'mt_nrp';

    var form = document.getElementById('loginForm');
    if (!form) return;

    var nrpInput = document.getElementById('nrp');
    var passwordInput = document.getElementById('password');
    var rememberInput = document.getElementById('rememberNrp');
    var toggleButton = document.getElementById('togglePassword');
    var submitButton = document.getElementById('submitButton');

    function togglePasswordVisibility() {
        var isHidden = passwordInput.type === 'password';
        passwordInput.type = isHidden ? 'text' : 'password';
        toggleButton.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
    }

    function restoreRememberedNrp() {
        var saved = localStorage.getItem(STORAGE_KEY);
        if (!saved) return;
        nrpInput.value = saved;
        rememberInput.checked = true;
        passwordInput.focus();
    }

    function persistNrp() {
        if (rememberInput.checked) {
            localStorage.setItem(STORAGE_KEY, nrpInput.value.trim());
        } else {
            localStorage.removeItem(STORAGE_KEY);
        }
    }

    function setLoading() {
        submitButton.classList.add('is-loading');
        submitButton.disabled = true;
    }

    function handleSubmit(event) {
        if (!nrpInput.value.trim()) {
            event.preventDefault();
            nrpInput.focus();
            return;
        }
        if (!passwordInput.value) {
            event.preventDefault();
            passwordInput.focus();
            return;
        }
        persistNrp();
        setLoading();
    }

    toggleButton.addEventListener('click', togglePasswordVisibility);
    form.addEventListener('submit', handleSubmit);
    restoreRememberedNrp();
})();
