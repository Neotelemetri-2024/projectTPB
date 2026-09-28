import test from 'node:test';
import assert from 'node:assert/strict';
import { onDomReady } from '../../resources/js/dom-ready.js';

test('waits for DOMContentLoaded when chart bundle loads while parsing', () => {
    let readyHandler;
    let calls = 0;
    const documentRef = {
        readyState: 'loading',
        addEventListener(eventName, callback, options) {
            assert.equal(eventName, 'DOMContentLoaded');
            assert.deepEqual(options, { once: true });
            readyHandler = callback;
        },
    };

    onDomReady(() => calls++, documentRef);
    assert.equal(calls, 0);
    readyHandler();
    assert.equal(calls, 1);
});

test('renders immediately when chart bundle loads after DOMContentLoaded', () => {
    let calls = 0;
    const documentRef = {
        readyState: 'interactive',
        addEventListener() {
            assert.fail('should not register a listener after DOM is ready');
        },
    };

    onDomReady(() => calls++, documentRef);
    assert.equal(calls, 1);
});
