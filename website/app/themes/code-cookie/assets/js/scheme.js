(function () {
    'use strict';

    var COOKIE = 'cc_scheme';
    var YEAR = 60 * 60 * 24 * 365;

    function current() {
        return document.documentElement.getAttribute('data-scheme') === 'light' ? 'light' : 'dark';
    }

    function apply(scheme) {
        document.documentElement.setAttribute('data-scheme', scheme);
        document.cookie = COOKIE + '=' + scheme + ';path=/;max-age=' + YEAR + ';samesite=lax';
    }

    document.addEventListener('click', function (event) {
        var toggle = event.target.closest('[data-cc-scheme-toggle]');
        if (!toggle) {
            return;
        }
        apply(current() === 'dark' ? 'light' : 'dark');
    });
})();
