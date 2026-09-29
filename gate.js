/*
 * Shared captcha logic: picks the language and sends the visitor on once the
 * captcha is passed.
 *
 * Where the visitor goes after the captcha:
 *   1. ?to=<full URL> (or ?url=) in the captcha link, e.g.
 *      /variant-1?to=https://site.com/programs/some-guide
 *   2. otherwise a random page from config.js for this variant.
 * Language: ?lang=en|es|fr, otherwise the browser language.
 */
window.Gate = (function () {
    var qs = new URLSearchParams(location.search);
    var SUPPORTED = ['en', 'es', 'fr'];
    var TITLES = { en: 'Security check', es: 'Verificación de seguridad', fr: 'Vérification de sécurité' };

    var lang = (qs.get('lang') || '').toLowerCase().slice(0, 2);
    if (SUPPORTED.indexOf(lang) < 0) lang = (navigator.language || 'en').toLowerCase().slice(0, 2);
    if (SUPPORTED.indexOf(lang) < 0) lang = 'en';
    document.documentElement.lang = lang;
    document.title = TITLES[lang];

    var variant = location.pathname.split('/').pop().replace(/\.html$/, '') || 'variant-1';

    function target() {
        var to = qs.get('to') || qs.get('url');
        if (to) {
            try {
                var u = new URL(to);
                if (u.protocol === 'https:' || u.protocol === 'http:') return u.href;
            } catch (e) {}
        }
        var all = window.GATE_CONFIG || {}, c = all[variant] || all['default'];
        if (!c || !c.pages || !c.pages.length) return null;
        var page = c.pages[Math.floor(Math.random() * c.pages.length)];
        var prefix = lang !== 'en' && (c.languages || []).indexOf(lang) >= 0 ? '/' + lang : '';
        return c.site.replace(/\/+$/, '') + prefix + page;
    }

    return {
        lang: lang,
        go: function () {
            var url = target();
            if (url) location.replace(url);
        }
    };
})();
