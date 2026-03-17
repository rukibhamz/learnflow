/**
 * LearnFlow Content Shield
 * Prevents copying, downloading, and unauthorized access to course content.
 */
(function () {
    'use strict';

    const SHIELD_SELECTOR = '[data-content-protected]';

    function init() {
        disableContextMenu();
        disableTextSelection();
        disableKeyboardShortcuts();
        disableDragDrop();
        disablePrintScreen();
        protectImages();
        monitorDevTools();
        injectPrintBlocker();
    }

    function disableContextMenu() {
        document.addEventListener('contextmenu', function (e) {
            if (e.target.closest(SHIELD_SELECTOR) || e.target.closest('.content-protected')) {
                e.preventDefault();
                return false;
            }
        }, true);
    }

    function disableTextSelection() {
        document.addEventListener('selectstart', function (e) {
            if (e.target.closest(SHIELD_SELECTOR) || e.target.closest('.content-protected')) {
                e.preventDefault();
                return false;
            }
        }, true);

        document.addEventListener('copy', function (e) {
            if (e.target.closest(SHIELD_SELECTOR) || e.target.closest('.content-protected')) {
                e.preventDefault();
                e.clipboardData?.setData('text/plain', '');
                return false;
            }
        }, true);

        document.addEventListener('cut', function (e) {
            if (e.target.closest(SHIELD_SELECTOR) || e.target.closest('.content-protected')) {
                e.preventDefault();
                return false;
            }
        }, true);
    }

    function disableKeyboardShortcuts() {
        document.addEventListener('keydown', function (e) {
            if (!document.querySelector(SHIELD_SELECTOR)) return;

            // Ctrl+S (Save), Ctrl+P (Print), Ctrl+Shift+I/J/C (DevTools), F12
            if (
                (e.ctrlKey && e.key === 's') ||
                (e.ctrlKey && e.key === 'p') ||
                (e.ctrlKey && e.key === 'u') ||
                (e.ctrlKey && e.shiftKey && ['i', 'j', 'c'].includes(e.key.toLowerCase())) ||
                e.key === 'F12'
            ) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }

            // Ctrl+A (Select All) on protected content
            if (e.ctrlKey && e.key === 'a') {
                const active = document.activeElement;
                if (active && (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA')) {
                    return; // Allow in form fields
                }
                e.preventDefault();
                return false;
            }

            // Ctrl+C (Copy) on protected content
            if (e.ctrlKey && e.key === 'c') {
                const sel = window.getSelection();
                if (sel && sel.rangeCount > 0) {
                    const container = sel.getRangeAt(0).commonAncestorContainer;
                    const el = container.nodeType === 3 ? container.parentElement : container;
                    if (el && (el.closest(SHIELD_SELECTOR) || el.closest('.content-protected'))) {
                        e.preventDefault();
                        return false;
                    }
                }
            }
        }, true);
    }

    function disableDragDrop() {
        document.addEventListener('dragstart', function (e) {
            if (e.target.closest(SHIELD_SELECTOR) || e.target.closest('.content-protected')) {
                e.preventDefault();
                return false;
            }
        }, true);

        document.addEventListener('drop', function (e) {
            if (e.target.closest(SHIELD_SELECTOR) || e.target.closest('.content-protected')) {
                e.preventDefault();
                return false;
            }
        }, true);
    }

    function disablePrintScreen() {
        document.addEventListener('keyup', function (e) {
            if (e.key === 'PrintScreen') {
                navigator.clipboard?.writeText('').catch(() => {});
            }
        }, true);
    }

    function protectImages() {
        const observer = new MutationObserver(function () {
            document.querySelectorAll(SHIELD_SELECTOR + ' img, .content-protected img').forEach(function (img) {
                if (!img.dataset.shielded) {
                    img.setAttribute('draggable', 'false');
                    img.addEventListener('contextmenu', function (e) { e.preventDefault(); });
                    img.dataset.shielded = '1';
                }
            });
        });

        observer.observe(document.body, { childList: true, subtree: true });

        document.querySelectorAll(SHIELD_SELECTOR + ' img, .content-protected img').forEach(function (img) {
            img.setAttribute('draggable', 'false');
            img.addEventListener('contextmenu', function (e) { e.preventDefault(); });
            img.dataset.shielded = '1';
        });
    }

    function monitorDevTools() {
        let threshold = 160;
        let devtoolsOpen = false;

        function check() {
            const widthDiff = window.outerWidth - window.innerWidth > threshold;
            const heightDiff = window.outerHeight - window.innerHeight > threshold;

            if (widthDiff || heightDiff) {
                if (!devtoolsOpen) {
                    devtoolsOpen = true;
                    onDevToolsOpen();
                }
            } else {
                devtoolsOpen = false;
            }
        }

        function onDevToolsOpen() {
            document.querySelectorAll(SHIELD_SELECTOR).forEach(function (el) {
                el.style.filter = 'blur(20px)';
                el.style.pointerEvents = 'none';
            });
        }

        setInterval(function () {
            check();
            if (!devtoolsOpen) {
                document.querySelectorAll(SHIELD_SELECTOR).forEach(function (el) {
                    el.style.filter = '';
                    el.style.pointerEvents = '';
                });
            }
        }, 1000);
    }

    function injectPrintBlocker() {
        if (document.getElementById('content-shield-print')) return;

        const style = document.createElement('style');
        style.id = 'content-shield-print';
        style.textContent = `
            @media print {
                [data-content-protected],
                .content-protected {
                    display: none !important;
                }
                body::after {
                    content: "Printing is disabled for protected content.";
                    display: block;
                    text-align: center;
                    font-size: 24px;
                    padding: 100px 20px;
                    color: #999;
                }
            }
        `;
        document.head.appendChild(style);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
