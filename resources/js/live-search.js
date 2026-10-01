const DEFAULT_DELAY = 400;
const PAGE_PARAMETERS = ['page', 'mahasiswa_page', 'dosen_page'];

function getSearchInput(form) {
    const configuredName = form.dataset.liveSearchInput;
    if (configuredName) {
        return form.querySelector(`[name="${CSS.escape(configuredName)}"]`);
    }

    return form.querySelector('input[name="search"], input[name="q"]');
}

function buildUrl(form, input) {
    const url = new URL(form.action || window.location.href, window.location.origin);
    const formData = new FormData(form);

    url.search = '';
    for (const [key, value] of formData.entries()) {
        if (typeof value === 'string' && value.trim() !== '') {
            url.searchParams.append(key, value);
        }
    }

    PAGE_PARAMETERS.forEach((parameter) => url.searchParams.delete(parameter));
    url.searchParams.delete('page');

    if (input && input.value.trim() === '') {
        url.searchParams.delete(input.name);
    }

    return url;
}

function setLoading(form, loading) {
    form.toggleAttribute('aria-busy', loading);
    form.classList.toggle('live-search-loading', loading);
}

function createLiveSearch(form) {
    const input = getSearchInput(form);
    const targetSelector = form.dataset.liveSearchTarget;
    const target = targetSelector ? document.querySelector(targetSelector) : null;

    if (!input || !target) return;

    let timer = null;
    let controller = null;
    let requestId = 0;

    const replaceResults = async (url, updateHistory = true) => {
        requestId += 1;
        const currentRequestId = requestId;

        if (controller) controller.abort();
        controller = new AbortController();
        setLoading(form, true);

        try {
            const response = await fetch(url.toString(), {
                credentials: 'same-origin',
                signal: controller.signal,
                headers: {
                    Accept: 'text/html',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-Live-Search': '1',
                },
            });

            if (!response.ok) throw new Error(`Live search failed (${response.status})`);

            const html = await response.text();
            if (currentRequestId !== requestId) return;

            const documentFragment = new DOMParser().parseFromString(html, 'text/html');
            const nextTarget = documentFragment.querySelector(targetSelector);
            // Some existing endpoints (notably Tahun Ajaran-Matkul) already
            // return the result partial for XMLHttpRequest callers.
            if (!nextTarget && (response.redirected || /<!doctype html|<html\b/i.test(html))) {
                window.location.assign(response.url || url.toString());
                return;
            }
            target.innerHTML = nextTarget ? nextTarget.innerHTML : html;
            target.dispatchEvent(new CustomEvent('live-search:updated', { bubbles: true }));
            if (updateHistory) window.history.replaceState({}, '', url.toString());
        } catch (error) {
            if (error.name !== 'AbortError') console.error('Live search error:', error);
        } finally {
            if (currentRequestId === requestId) {
                setLoading(form, false);
            }
        }
    };

    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => replaceResults(buildUrl(form, input)), Number(form.dataset.liveSearchDelay || DEFAULT_DELAY));
    });

    target.addEventListener('click', (event) => {
        const link = event.target.closest('a');
        if (!link || !link.href || link.target === '_blank') return;

        const linkUrl = new URL(link.href, window.location.origin);
        const formUrl = new URL(form.action || window.location.href, window.location.origin);
        if (linkUrl.origin !== window.location.origin || linkUrl.pathname !== formUrl.pathname) return;
        if (!PAGE_PARAMETERS.some((parameter) => linkUrl.searchParams.has(parameter))) return;

        event.preventDefault();
        replaceResults(linkUrl);
    });

    form.addEventListener('submit', () => {
        clearTimeout(timer);
        if (controller) controller.abort();
    });
}

function initLiveSearch() {
    document.querySelectorAll('form[data-live-search]').forEach(createLiveSearch);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLiveSearch, { once: true });
} else {
    initLiveSearch();
}
