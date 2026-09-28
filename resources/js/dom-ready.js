export function onDomReady(callback, documentRef = document) {
    if (documentRef.readyState === 'loading') {
        documentRef.addEventListener('DOMContentLoaded', callback, { once: true });
        return;
    }

    callback();
}
