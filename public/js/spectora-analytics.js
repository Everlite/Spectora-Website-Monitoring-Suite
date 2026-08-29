/**
 * Spectora Analytics - Alias to Spectora Core
 */
(function (window, document) {
    'use strict';

    var script = document.currentScript || document.querySelector('script[data-domain]');
    if (!script) {
        return;
    }

    var domainUuid = script.getAttribute('data-domain');
    var autoTrackSpa = script.getAttribute('data-spa') !== 'false';

    if (!domainUuid) {
        console.warn('[Spectora Analytics] Missing data-domain attribute.');
        return;
    }

    var endpoint;
    try {
        var srcUrl = new URL(script.src);
        endpoint = srcUrl.protocol + '//' + srcUrl.host + '/api/sync';
    } catch (e) {
        endpoint = '/api/sync';
    }

    function sendPulse(payload) {
        var bodyData = JSON.stringify(payload);

        if (navigator.sendBeacon) {
            var blob = new Blob([bodyData], { type: 'application/json' });
            if (navigator.sendBeacon(endpoint, blob)) {
                return;
            }
        }

        try {
            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: bodyData,
                keepalive: true,
                mode: 'cors'
            }).catch(function () {});
        } catch (e) {}
    }

    function trackPageview(customUrl, customReferrer) {
        var payload = {
            domain: domainUuid,
            url: customUrl || window.location.href,
            referrer: customReferrer || document.referrer || null,
            width: window.innerWidth,
            event_type: 'pageview'
        };
        sendPulse(payload);
    }

    function trackEvent(eventName, eventData) {
        if (!eventName) return;
        var payload = {
            domain: domainUuid,
            url: window.location.href,
            referrer: document.referrer || null,
            width: window.innerWidth,
            event_type: 'custom',
            event_name: String(eventName).substring(0, 64),
            event_data: eventData || {}
        };
        sendPulse(payload);
    }

    window.spectora = {
        track: trackEvent,
        pageview: trackPageview
    };

    trackPageview();

    if (autoTrackSpa && window.history && window.history.pushState) {
        var originalPushState = window.history.pushState;
        var originalReplaceState = window.history.replaceState;
        var lastUrl = window.location.href;

        function checkUrlChange() {
            var currentUrl = window.location.href;
            if (currentUrl !== lastUrl) {
                var prevUrl = lastUrl;
                lastUrl = currentUrl;
                trackPageview(currentUrl, prevUrl);
            }
        }

        window.history.pushState = function () {
            originalPushState.apply(this, arguments);
            checkUrlChange();
        };

        window.history.replaceState = function () {
            originalReplaceState.apply(this, arguments);
            checkUrlChange();
        };

        window.addEventListener('popstate', checkUrlChange);
    }

})(window, document);
