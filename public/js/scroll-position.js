(function() {
    'use strict';

    var STORAGE_KEY = 'scroll_positions';
    var DEBOUNCE_MS = 150;
    var MAX_RETRIES = 20;
    var RETRY_INTERVAL_MS = 200;

    // ── Storage helpers ──────────────────────────────────────────────
    function getPositions() {
        try {
            return JSON.parse(sessionStorage.getItem(STORAGE_KEY)) || {};
        } catch (e) {
            return {};
        }
    }

    function savePosition(url, y) {
        var positions = getPositions();
        positions[url] = y;
        try {
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify(positions));
        } catch (e) { /* quota exceeded — silently ignore */ }
    }

    function getSavedPosition(url) {
        var positions = getPositions();
        return positions.hasOwnProperty(url) ? positions[url] : null;
    }

    function removePosition(url) {
        var positions = getPositions();
        delete positions[url];
        try {
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify(positions));
        } catch (e) { /* ignore */ }
    }

    // ── Helpers ──────────────────────────────────────────────────────
    function currentUrl() {
        return window.location.pathname + window.location.search;
    }

    // Debounce: returns a function that delays invoking `fn` until
    // `wait` ms have elapsed since the last invocation.
    function debounce(fn, wait) {
        var timer;
        return function() {
            var context = this;
            var args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function() {
                fn.apply(context, args);
            }, wait);
        };
    }

    // ── Save current scroll position ─────────────────────────────────
    function captureScroll() {
        savePosition(currentUrl(), window.scrollY);
    }

    var debouncedCapture = debounce(captureScroll, DEBOUNCE_MS);

    // ── Restore scroll position on load ──────────────────────────────
    function tryRestore(url, retries) {
        var saved = getSavedPosition(url);
        if (saved === null) return;

        // If the page isn't tall enough yet, try again later.
        if (document.documentElement.scrollHeight <= saved && retries > 0) {
            setTimeout(function() {
                tryRestore(url, retries - 1);
            }, RETRY_INTERVAL_MS);
            return;
        }

        window.scrollTo(0, saved);

        // Remove the saved position so it doesn't re-apply on
        // subsequent in-page navigations (e.g. SPA-like behaviors).
        // The scroll listener will re-save if the user moves.
        removePosition(url);
    }

    // ── Hook: save on link clicks / form submissions ────────────────
    // Captures scroll just before navigating away (for same-origin links).
    document.addEventListener('click', function(e) {
        var target = e.target.closest('a, button, [type="submit"], .pagination a');
        if (!target) return;

        // For anchor links (<a>) only capture if they navigate
        // to another page (not hash-only, not download, not external).
        if (target.tagName === 'A') {
            var href = target.getAttribute('href');
            if (!href || href === '#' || href.startsWith('#') ||
                target.hasAttribute('download') ||
                target.getAttribute('target') === '_blank') {
                return;
            }
        }

        captureScroll();
    }, true);  // useCapture so it runs before navigation

    // ── Hook: save on scroll ─────────────────────────────────────────
    window.addEventListener('scroll', debouncedCapture, { passive: true });

    // ── Hook: save on beforeunload ───────────────────────────────────
    window.addEventListener('beforeunload', captureScroll);

    // ── Hook: save on history navigation ─────────────────────────────
    // When the user goes back/forward, save the current page's position
    // before the popstate fires.
    window.addEventListener('popstate', captureScroll);

    // ── Hook: restore on page load ───────────────────────────────────
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            tryRestore(currentUrl(), MAX_RETRIES);
        });
    } else {
        tryRestore(currentUrl(), MAX_RETRIES);
    }

    // ── Hook: re-check after dynamic content loads ───────────────────
    // Some pages (e.g. tabbed reports) may load content after DOMContentLoaded.
    // A single retry with a longer delay covers most cases.
    var domReady = document.readyState !== 'loading';
    if (!domReady) {
        document.addEventListener('readystatechange', function() {
            if (document.readyState === 'complete') {
                setTimeout(function() {
                    tryRestore(currentUrl(), MAX_RETRIES);
                }, 400);
            }
        });
    } else if (document.readyState === 'interactive' || document.readyState === 'complete') {
        setTimeout(function() {
            tryRestore(currentUrl(), MAX_RETRIES);
        }, 400);
    }

})();
