import './bootstrap';

import Alpine from 'alpinejs';
import Toastify from 'toastify-js';
import 'flowbite';

window.Alpine = Alpine;
window.Toastify = Toastify;

Alpine.start();

const LOADING_SPINNER_HTML = `
<svg class="js-loading-spinner w-4 h-4 mr-2 inline-block animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
</svg>`;

/**
 * Toggle loading state on a button to prevent spam clicks.
 * @param {HTMLElement|null} button
 * @param {boolean} isLoading
 * @param {string|null} loadingText Optional text while loading
 */
window.setButtonLoading = function(button, isLoading, loadingText = null) {
    if (!button) return;

    if (isLoading) {
        if (button.dataset.loading === '1') return;

        button.dataset.loading = '1';
        if (!button.dataset.originalHtml) {
            button.dataset.originalHtml = button.innerHTML;
        }
        if (button.disabled) {
            button.dataset.wasDisabled = '1';
        }

        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        button.classList.add('cursor-not-allowed', 'opacity-75');

        const spinner = button.querySelector('[data-spinner]');
        const icon = button.querySelector('[data-icon]');
        const submitText = button.querySelector('[data-submit-text]');

        if (spinner) {
            spinner.classList.remove('hidden');
            if (icon) icon.classList.add('hidden');
            if (loadingText && submitText) {
                submitText.textContent = loadingText;
            }
            return;
        }

        if (loadingText) {
            button.innerHTML = LOADING_SPINNER_HTML + loadingText;
            return;
        }

        if (!button.querySelector('.js-loading-spinner')) {
            button.insertAdjacentHTML('afterbegin', LOADING_SPINNER_HTML);
        }
        return;
    }

    if (button.dataset.loading !== '1') return;

    delete button.dataset.loading;
    button.removeAttribute('aria-busy');
    button.classList.remove('cursor-not-allowed', 'opacity-75');

    if (button.dataset.originalHtml !== undefined) {
        button.innerHTML = button.dataset.originalHtml;
        delete button.dataset.originalHtml;
    } else {
        const spinner = button.querySelector('[data-spinner]');
        const icon = button.querySelector('[data-icon]');
        const injected = button.querySelector('.js-loading-spinner');
        if (spinner) spinner.classList.add('hidden');
        if (icon) icon.classList.remove('hidden');
        if (injected) injected.remove();
    }

    if (button.dataset.wasDisabled === '1') {
        button.disabled = true;
        delete button.dataset.wasDisabled;
    } else {
        button.disabled = false;
    }
};

/**
 * Run an async action while locking a button. Re-enables only on error
 * (success often navigates/reloads).
 */
window.withButtonLoading = async function(button, asyncFn, loadingText = null) {
    window.setButtonLoading(button, true, loadingText);
    try {
        return await asyncFn();
    } catch (error) {
        window.setButtonLoading(button, false);
        throw error;
    }
};

function getFormSubmitButtons(form) {
    return Array.from(form.querySelectorAll('button[type="submit"], input[type="submit"], button:not([type])'))
        .filter((btn) => {
            if (btn.tagName === 'BUTTON' && !btn.hasAttribute('type')) {
                return btn.closest('form') === form;
            }
            return true;
        });
}

function shouldSkipFormLoading(form) {
    if (!(form instanceof HTMLFormElement)) return true;
    if (form.hasAttribute('data-no-loading')) return true;
    const method = (form.getAttribute('method') || 'get').toLowerCase();
    return method === 'get';
}

// Global anti-spam guard for classic POST/PUT/PATCH/DELETE form submits
document.addEventListener('submit', function(e) {
    const form = e.target;
    if (shouldSkipFormLoading(form)) return;

    if (form.dataset.submitting === '1') {
        e.preventDefault();
        return;
    }

    form.dataset.submitting = '1';

    queueMicrotask(() => {
        // AJAX handlers that call preventDefault manage their own loading
        if (e.defaultPrevented) {
            delete form.dataset.submitting;
            return;
        }

        getFormSubmitButtons(form).forEach((btn) => {
            window.setButtonLoading(btn, true);
        });
    });
});

// Show toasts for Laravel flash messages
document.addEventListener('DOMContentLoaded', function() {
    const successMessage = document.querySelector('meta[name="success-message"]');
    if (successMessage) {
        Toastify({
            text: successMessage.getAttribute('content'),
            duration: 3000,
            gravity: "top",
            position: "right",
            style: {
                background: "linear-gradient(to right, #10b981, #059669)",
            }
        }).showToast();
    }

    const errorMessage = document.querySelector('meta[name="error-message"]');
    if (errorMessage) {
        Toastify({
            text: errorMessage.getAttribute('content'),
            duration: 3000,
            gravity: "top",
            position: "right",
            style: {
                background: "linear-gradient(to right, #ef4444, #dc2626)",
            }
        }).showToast();
    }
});

window.showToast = function(message, type = 'success') {
    const backgroundColor = type === 'success'
        ? "linear-gradient(to right, #10b981, #059669)"
        : "linear-gradient(to right, #ef4444, #dc2626)";

    Toastify({
        text: message,
        duration: 3000,
        gravity: "top",
        position: "right",
        style: {
            background: backgroundColor,
        }
    }).showToast();
};

// Load the chart bundle only on pages that request it.
window.__chartReadyQueue = window.__chartReadyQueue || [];
let chartModulePromise = null;
window.whenChartReady = function (callback) {
    const run = () => callback(window.ApexCharts);
    if (window.ApexCharts) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', run, { once: true });
        } else {
            run();
        }
        return;
    }

    window.__chartReadyQueue.push(callback);
    if (!chartModulePromise) {
        chartModulePromise = import('./charts.js').catch((error) => {
            chartModulePromise = null;
            console.error('Unable to load chart module', error);
        });
    }
};
