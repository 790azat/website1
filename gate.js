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

    var FOOT = {
        en: { foot: 'The following content is informational and educational and does not constitute financial, legal, medical, or professional advice. Results are not guaranteed; your experience may vary.', rights: 'All rights reserved.', agree: 'By continuing, you agree to our {t} and our {p}.', t: 'Terms of Use', p: 'Privacy Policy' },
        es: { foot: 'El siguiente contenido es informativo y educativo y no constituye asesoramiento financiero, legal, médico ni profesional. Los resultados no están garantizados; tu experiencia puede variar.', rights: 'Todos los derechos reservados.', agree: 'Al continuar, aceptas nuestros {t} y nuestra {p}.', t: 'Términos de uso', p: 'Política de privacidad' },
        fr: { foot: "Le contenu suivant est informatif et éducatif et ne constitue pas un conseil financier, juridique, médical ou professionnel. Les résultats ne sont pas garantis ; votre expérience peut varier.", rights: 'Tous droits réservés.', agree: 'En continuant, vous acceptez nos {t} et notre {p}.', t: "Conditions d'utilisation", p: 'Politique de confidentialité' }
    };

    // Terms and Privacy live on this captcha domain; "back" returns to this exact captcha link.
    function legal(page) {
        return page + '?lang=' + lang + '&back=' + encodeURIComponent(location.pathname + location.search);
    }

    return {
        lang: lang,
        footer: function () {
            var f = FOOT[lang];
            return f.foot + '<br>' + f.agree
                .replace('{t}', '<a href="' + legal('terms') + '">' + f.t + '</a>')
                .replace('{p}', '<a href="' + legal('privacy') + '">' + f.p + '</a>') +
                '<br>© ' + new Date().getFullYear() + ' - ' + f.rights;
        },
        go: function () {
            var url = target();
            if (url) location.replace(url);
        }
    };
})();
