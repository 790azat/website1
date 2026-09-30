{{-- Captcha page (no site name or logo). Pass logic: window.Gate below; guard: partials/head. --}}
@verbatim
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<meta name="referrer" content="no-referrer-when-downgrade">
<title>Security check</title>
<link rel="icon" href="data:,">
<style>

    html, body { margin: 0; background: #07060d; }
    #tu-gate {
        visibility: visible; position: fixed; inset: 0; z-index: 2147483000;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        padding: 24px 16px; overflow-y: auto;
        background:
            radial-gradient(60rem 30rem at 15% -10%, rgba(56, 189, 248, .22), transparent 60%),
            radial-gradient(50rem 30rem at 110% 110%, rgba(139, 92, 246, .25), transparent 60%),
            #07060d;
        color: #f4f4f5; font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        transition: opacity .45s ease;
    }
    #tu-gate.is-leaving { opacity: 0; }
    #tu-gate * { box-sizing: border-box; }
    .tu-card {
        width: 100%; max-width: 440px; text-align: center; padding: 40px 28px 32px;
        border-radius: 28px; border: 1px solid rgba(255, 255, 255, .09);
        background: linear-gradient(180deg, rgba(255, 255, 255, .07), rgba(255, 255, 255, .02));
        box-shadow: 0 30px 80px -20px rgba(0, 0, 0, .7); backdrop-filter: blur(12px);
        animation: tu-rise .5s cubic-bezier(.2, .8, .2, 1) both;
    }
    @keyframes tu-rise { from { opacity: 0; transform: translateY(14px) scale(.98); } }
    .tu-badge {
        margin: 0 auto 18px; width: 84px; height: 84px; border-radius: 50%;
        display: grid; place-items: center; font-weight: 900; font-size: 30px; letter-spacing: -.04em;
        background: conic-gradient(from 210deg, #38bdf8, #8b5cf6, #f472b6, #38bdf8);
        box-shadow: 0 0 0 6px rgba(255, 255, 255, .04), 0 12px 40px -8px rgba(139, 92, 246, .7);
    }
    .tu-badge span { width: 72px; height: 72px; border-radius: 50%; display: grid; place-items: center; background: #0d0b16; }
    .tu-title { margin: 0 0 28px; font-size: clamp(26px, 6vw, 32px); line-height: 1.15; font-weight: 800; letter-spacing: -.02em; }
    .tu-sub { margin: 10px 0 28px; color: #a1a1aa; font-size: 15px; line-height: 1.5; }
    .tu-actions { display: grid; gap: 12px; }
    .tu-btn {
        position: relative; overflow: hidden; width: 100%; min-height: 56px; border: 0; cursor: pointer;
        border-radius: 999px; font: inherit; font-size: 16px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase;
        display: inline-flex; align-items: center; justify-content: center; gap: 10px;
        transition: transform .15s ease, box-shadow .2s ease, background-color .2s ease;
    }
    .tu-btn:focus-visible { outline: 3px solid #7dd3fc; outline-offset: 3px; }
    .tu-btn:active { transform: scale(.98); }
    .tu-yes {
        color: #fff; background: linear-gradient(100deg, #0ea5e9, #6366f1 55%, #a855f7);
        box-shadow: 0 10px 30px -6px rgba(99, 102, 241, .8), inset 0 1px 0 rgba(255, 255, 255, .35);
    }
    .tu-yes::after {
        content: ""; position: absolute; top: 0; left: -60%; width: 40%; height: 100%;
        background: linear-gradient(100deg, transparent, rgba(255, 255, 255, .45), transparent);
        transform: skewX(-20deg); animation: tu-shine 2.6s ease-in-out infinite;
    }
    @keyframes tu-shine { 0%, 55% { left: -60%; } 100% { left: 130%; } }
    .tu-yes:hover { box-shadow: 0 14px 38px -6px rgba(99, 102, 241, 1), inset 0 1px 0 rgba(255, 255, 255, .35); }
    .tu-yes svg { transition: transform .2s ease; }
    .tu-yes:hover svg { transform: translateX(4px); }
    .tu-spin { margin: 4px auto 22px; width: 72px; height: 72px; border-radius: 50%; position: relative; }
    .tu-spin::before { content: ""; position: absolute; inset: 0; border-radius: 50%; border: 5px solid rgba(255, 255, 255, .08); }
    .tu-spin::after {
        content: ""; position: absolute; inset: 0; border-radius: 50%; border: 5px solid transparent;
        border-top-color: #38bdf8; border-right-color: #a78bfa; animation: tu-rot .8s linear infinite;
    }
    @keyframes tu-rot { to { transform: rotate(360deg); } }
    .tu-wait { margin-top: 22px; font-size: 12px; letter-spacing: .2em; text-transform: uppercase; color: #71717a; }
    .tu-lang { display: flex; gap: 4px; margin-bottom: 18px; padding: 4px; border-radius: 999px; background: rgba(255, 255, 255, .05); border: 1px solid rgba(255, 255, 255, .09); }
    .tu-lang a {
        display: inline-flex; align-items: center; gap: 7px; padding: 6px 11px; border-radius: 999px;
        color: #a1a1aa; text-decoration: none; font-size: 12px; font-weight: 700; letter-spacing: .08em;
        transition: background-color .2s ease, color .2s ease;
    }
    .tu-lang a:hover { background: rgba(255, 255, 255, .06); color: #f4f4f5; }
    .tu-lang a[aria-current] { background: rgba(255, 255, 255, .12); color: #fff; }
    .tu-lang a:focus-visible { outline: 2px solid #7dd3fc; outline-offset: 2px; }
    .tu-lang svg { display: block; width: 20px; height: 14px; border-radius: 2px; box-shadow: 0 0 0 1px rgba(255, 255, 255, .2); }
    .tu-foot { max-width: 440px; margin-top: 22px; text-align: center; font-size: 12px; line-height: 1.55; color: #71717a; }
    .tu-foot a { color: #a1a1aa; text-decoration: underline; text-underline-offset: 2px; }
    @media (prefers-reduced-motion: reduce) { #tu-gate *, #tu-gate *::after { animation: none !important; } }
</style>
<script>
/*
 * Same-site captcha. Every page sends a visitor without a pass to
 * /captcha?next=<page>. Passing it stores a short-lived cookie (30 minutes)
 * and opens the page they asked for (the home page for site.com).
 */
window.Gate = (function () {
    var PASS_MINUTES = 30;
    var qs = new URLSearchParams(location.search);
    var next = qs.get('next') || '/';
    if (!/^\/(?![\/\\])/.test(next) || /^\/(?:(?:es|fr)\/)?captcha(?:[?#.]|$)/.test(next)) next = '/';

    var m = next.match(/^\/(es|fr)(?=[\/?#]|$)/) || next.match(/[?&]lang=(es|fr)\b/);
    var lang = m ? m[1] : 'en', pre = lang === 'en' ? '' : '/' + lang;
    var TITLES = { en: 'Security check', es: 'Verificación de seguridad', fr: 'Vérification de sécurité' };
    document.documentElement.lang = lang;
    document.title = TITLES[lang];

    var FOOT = {
        en: { foot: 'The following content is informational and educational and does not constitute financial, legal, medical, or professional advice. Results are not guaranteed; your experience may vary.', rights: 'All rights reserved.', agree: 'By continuing, you agree to our {t} and our {p}.', t: 'Terms of Use', p: 'Privacy Policy' },
        es: { foot: 'El siguiente contenido es informativo y educativo y no constituye asesoramiento financiero, legal, médico ni profesional. Los resultados no están garantizados; tu experiencia puede variar.', rights: 'Todos los derechos reservados.', agree: 'Al continuar, aceptas nuestros {t} y nuestra {p}.', t: 'Términos de uso', p: 'Política de privacidad' },
        fr: { foot: "Le contenu suivant est informatif et éducatif et ne constitue pas un conseil financier, juridique, médical ou professionnel. Les résultats ne sont pas garantis ; votre expérience peut varier.", rights: 'Tous droits réservés.', agree: 'En continuant, vous acceptez nos {t} et notre {p}.', t: "Conditions d'utilisation", p: 'Politique de confidentialité' }
    };

    var FLAGS = {
        en: '<svg viewBox="0 0 60 30" aria-hidden="true"><rect width="60" height="30" fill="#012169"/><path d="M0,0 L60,30 M60,0 L0,30" stroke="#fff" stroke-width="6"/><path d="M0,0 L30,15 M60,0 L30,15 M60,30 L30,15 M0,30 L30,15" stroke="#C8102E" stroke-width="2"/><path d="M30,0 v30 M0,15 h60" stroke="#fff" stroke-width="10"/><path d="M30,0 v30 M0,15 h60" stroke="#C8102E" stroke-width="6"/></svg>',
        es: '<svg viewBox="0 0 30 20" aria-hidden="true"><rect width="30" height="20" fill="#AA151B"/><rect y="5" width="30" height="10" fill="#F1BF00"/></svg>',
        fr: '<svg viewBox="0 0 30 20" aria-hidden="true"><rect width="30" height="20" fill="#FFFFFF"/><rect width="10" height="20" fill="#002654"/><rect x="20" width="10" height="20" fill="#CE1126"/></svg>'
    };
    var NAMES = { en: 'English', es: 'Español', fr: 'Français' };

    // The page they asked for, in language l: "?lang=" links keep that form, others get the /es or /fr prefix.
    function nextIn(l) {
        var cut = next.search(/#/), hash = cut < 0 ? '' : next.slice(cut), url = cut < 0 ? next : next.slice(0, cut);
        if (/[?&]lang=(?:en|es|fr)\b/.test(url)) return url.replace(/([?&]lang=)(?:en|es|fr)\b/, '$1' + l) + hash;
        url = url.replace(/^\/(?:es|fr)(?=[\/?#]|$)/, '');
        if (url === '' || url.charAt(0) === '?') url = '/' + url;
        if (l !== 'en') url = '/' + l + (url.charAt(1) === '?' || url === '/' ? url.slice(1) : url);
        return url + hash;
    }

    return {
        lang: lang,
        switcher: function (cls) {
            return '<nav class="' + cls + '" aria-label="Language">' + ['en', 'es', 'fr'].map(function (l) {
                return '<a href="' + location.pathname + '?next=' + encodeURIComponent(nextIn(l)) + '" hreflang="' + l + '" lang="' + l + '" title="' + NAMES[l] + '"' +
                    (l === lang ? ' aria-current="true"' : '') + '>' + FLAGS[l] + l.toUpperCase() + '</a>';
            }).join('') + '</nav>';
        },
        footer: function () {
            var f = FOOT[lang];
            return f.foot + '<br>' + f.agree
                .replace('{t}', '<a href="' + pre + '/terms-of-use">' + f.t + '</a>')
                .replace('{p}', '<a href="' + pre + '/privacy-policy">' + f.p + '</a>') +
                '<br>© ' + new Date().getFullYear() + ' - ' + f.rights;
        },
        go: function () {
            document.cookie = 'gate_pass=1; Max-Age=' + PASS_MINUTES * 60 + '; Path=/; SameSite=Lax' + (location.protocol === 'https:' ? '; Secure' : '');
            location.replace(next);
        }
    };
})();
</script>
</head>
<body>
<script>

(function () {
    var root = document.documentElement;

    var T = {
        en: { q: 'Are you 18 or older?', yes: 'Yes', v: 'Verifying you', vs: 'Just a moment while we unlock your access.', wait: 'Please wait' },
        es: { q: '¿Tienes 18 años o más?', yes: 'Sí', v: 'Verificando', vs: 'Un momento mientras desbloqueamos tu acceso.', wait: 'Espera por favor' },
        fr: { q: 'Avez-vous 18 ans ou plus ?', yes: 'Oui', v: 'Vérification', vs: 'Un instant, nous débloquons votre accès.', wait: 'Veuillez patienter' }
    };
    var lang = Gate.lang, t = T[lang] || T.en;

    function build() {
        var g = document.createElement('div');
        g.id = 'tu-gate';
        g.setAttribute('role', 'dialog');
        g.setAttribute('aria-modal', 'true');
        g.setAttribute('aria-labelledby', 'tu-q');
        g.innerHTML =
            Gate.switcher('tu-lang') +
            '<div class="tu-card">' +
                '<div class="tu-badge"><span>18+</span></div>' +
                '<h2 class="tu-title" id="tu-q">' + t.q + '</h2>' +
                '<div class="tu-actions">' +
                    '<button type="button" class="tu-btn tu-yes">' + t.yes + ' <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>' +
                '</div>' +
            '</div>' +
            '<p class="tu-foot">' + Gate.footer() + '</p>';
        document.body.appendChild(g);
        var card = g.querySelector('.tu-card');
        g.querySelector('.tu-yes').focus();

        g.querySelector('.tu-yes').addEventListener('click', function () {
            card.style.animation = 'none';
            card.offsetWidth;
            card.style.animation = '';
            card.innerHTML =
                '<div class="tu-spin" aria-hidden="true"></div>' +
                '<h2 class="tu-title" role="status">' + t.v + '</h2>' +
                '<p class="tu-sub" style="margin:-18px 0 0">' + t.vs + '</p>' +
                '<div class="tu-wait">' + t.wait + '</div>';
            setTimeout(function () {
                Gate.go();
            }, 1500);
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', build);
    else build();
})();
</script>
</body>
</html>
@endverbatim
