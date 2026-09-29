{{--
    Entry gate (age check) shown over the site until the visitor confirms.
    The pass is kept in localStorage for 24 hours; add ?gate=1 to any URL to
    show it again. Legal pages stay open so the gate's own links work.
--}}
@verbatim
<style>
    html.gate-on { background: #07060d; }
    html.gate-on body { visibility: hidden; overflow: hidden; }
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
    .tu-brand { font-weight: 800; letter-spacing: -.02em; font-size: 15px; color: #a1a1aa; }
    .tu-brand b { background: linear-gradient(90deg, #38bdf8, #a78bfa); -webkit-background-clip: text; background-clip: text; color: transparent; }
    .tu-badge {
        margin: 26px auto 18px; width: 84px; height: 84px; border-radius: 50%;
        display: grid; place-items: center; font-weight: 900; font-size: 30px; letter-spacing: -.04em;
        background: conic-gradient(from 210deg, #38bdf8, #8b5cf6, #f472b6, #38bdf8);
        box-shadow: 0 0 0 6px rgba(255, 255, 255, .04), 0 12px 40px -8px rgba(139, 92, 246, .7);
    }
    .tu-badge span { width: 72px; height: 72px; border-radius: 50%; display: grid; place-items: center; background: #0d0b16; }
    .tu-title { margin: 0; font-size: clamp(26px, 6vw, 32px); line-height: 1.15; font-weight: 800; letter-spacing: -.02em; }
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
    .tu-no { color: #d4d4d8; background: transparent; box-shadow: inset 0 0 0 1.5px rgba(255, 255, 255, .18); }
    .tu-no:hover { background: rgba(255, 255, 255, .06); }
    .tu-deny { margin: 18px 0 0; padding: 12px 14px; border-radius: 14px; font-size: 14px; color: #fecaca; background: rgba(239, 68, 68, .12); border: 1px solid rgba(239, 68, 68, .3); }
    .tu-spin { margin: 30px auto 22px; width: 72px; height: 72px; border-radius: 50%; position: relative; }
    .tu-spin::before { content: ""; position: absolute; inset: 0; border-radius: 50%; border: 5px solid rgba(255, 255, 255, .08); }
    .tu-spin::after {
        content: ""; position: absolute; inset: 0; border-radius: 50%; border: 5px solid transparent;
        border-top-color: #38bdf8; border-right-color: #a78bfa; animation: tu-rot .8s linear infinite;
    }
    @keyframes tu-rot { to { transform: rotate(360deg); } }
    .tu-wait { margin-top: 22px; font-size: 12px; letter-spacing: .2em; text-transform: uppercase; color: #71717a; }
    .tu-foot { max-width: 440px; margin-top: 22px; text-align: center; font-size: 12px; line-height: 1.55; color: #71717a; }
    .tu-foot a { color: #a1a1aa; text-decoration: underline; text-underline-offset: 2px; }
    @media (prefers-reduced-motion: reduce) { #tu-gate *, #tu-gate *::after { animation: none !important; } }
</style>
<script>
(function () {
    var KEY = 'tu_gate_pass', TTL = 864e5, root = document.documentElement;
    try { if (/[?&]gate=1\b/.test(location.search)) localStorage.removeItem(KEY); } catch (e) {}
    var passed = false;
    try { passed = Number(localStorage.getItem(KEY)) > Date.now() - TTL; } catch (e) {}
    if (passed || /\/(terms-of-use|privacy-policy)(\.html)?$/.test(location.pathname)) return;
    root.classList.add('gate-on');

    var T = {
        en: { q: 'Are you 18 or older?', sub: 'This website is intended for adults. Please confirm your age to continue.', yes: 'Yes, I am 18+', no: 'No', deny: 'Sorry, you must be at least 18 years old to enter this website.', v: 'Verifying you', vs: 'Just a moment while we unlock your access.', wait: 'Please wait', foot: 'The following content is informational and educational and does not constitute financial, legal or professional advice.', agree: 'By continuing, you agree to our {t} and our {p}.', t: 'Terms of Use', p: 'Privacy Policy' },
        es: { q: '¿Tienes 18 años o más?', sub: 'Este sitio web está dirigido a adultos. Confirma tu edad para continuar.', yes: 'Sí, tengo 18+', no: 'No', deny: 'Lo sentimos, debes tener al menos 18 años para entrar en este sitio.', v: 'Verificando', vs: 'Un momento mientras desbloqueamos tu acceso.', wait: 'Espera por favor', foot: 'El siguiente contenido es informativo y educativo y no constituye asesoramiento financiero, legal ni profesional.', agree: 'Al continuar, aceptas nuestros {t} y nuestra {p}.', t: 'Términos de uso', p: 'Política de privacidad' },
        fr: { q: 'Avez-vous 18 ans ou plus ?', sub: 'Ce site est destiné aux adultes. Veuillez confirmer votre âge pour continuer.', yes: "Oui, j'ai 18+", no: 'Non', deny: 'Désolé, vous devez avoir au moins 18 ans pour accéder à ce site.', v: 'Vérification', vs: 'Un instant, nous débloquons votre accès.', wait: 'Veuillez patienter', foot: "Le contenu suivant est informatif et éducatif et ne constitue pas un conseil financier, juridique ou professionnel.", agree: 'En continuant, vous acceptez nos {t} et notre {p}.', t: "Conditions d'utilisation", p: 'Politique de confidentialité' }
    };
    var lang = (root.lang || 'en').slice(0, 2), t = T[lang] || T.en, pre = T[lang] && lang !== 'en' ? '/' + lang : '';

    function build() {
        var g = document.createElement('div');
        g.id = 'tu-gate';
        g.setAttribute('role', 'dialog');
        g.setAttribute('aria-modal', 'true');
        g.setAttribute('aria-labelledby', 'tu-q');
        var agree = t.agree.replace('{t}', '<a href="' + pre + '/terms-of-use">' + t.t + '</a>').replace('{p}', '<a href="' + pre + '/privacy-policy">' + t.p + '</a>');
        g.innerHTML =
            '<div class="tu-card">' +
                '<div class="tu-brand">ThumbsUp<b>TechCo</b></div>' +
                '<div class="tu-badge"><span>18+</span></div>' +
                '<h2 class="tu-title" id="tu-q">' + t.q + '</h2>' +
                '<p class="tu-sub">' + t.sub + '</p>' +
                '<div class="tu-actions">' +
                    '<button type="button" class="tu-btn tu-yes">' + t.yes + ' <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>' +
                    '<button type="button" class="tu-btn tu-no">' + t.no + '</button>' +
                '</div>' +
            '</div>' +
            '<p class="tu-foot">' + t.foot + '<br>' + agree + '</p>';
        document.body.appendChild(g);
        var card = g.querySelector('.tu-card');
        g.querySelector('.tu-yes').focus();

        g.querySelector('.tu-no').addEventListener('click', function () {
            if (!card.querySelector('.tu-deny')) {
                var p = document.createElement('p');
                p.className = 'tu-deny';
                p.setAttribute('role', 'alert');
                p.textContent = t.deny;
                card.appendChild(p);
            }
        });
        g.querySelector('.tu-yes').addEventListener('click', function () {
            try { localStorage.setItem(KEY, String(Date.now())); } catch (e) {}
            card.style.animation = 'none';
            card.offsetWidth;
            card.style.animation = '';
            card.innerHTML =
                '<div class="tu-brand">ThumbsUp<b>TechCo</b></div>' +
                '<div class="tu-spin" aria-hidden="true"></div>' +
                '<h2 class="tu-title" role="status">' + t.v + '</h2>' +
                '<p class="tu-sub" style="margin-bottom:0">' + t.vs + '</p>' +
                '<div class="tu-wait">' + t.wait + '</div>';
            setTimeout(function () {
                g.classList.add('is-leaving');
                root.classList.remove('gate-on');
                setTimeout(function () { g.remove(); }, 460);
            }, 1500);
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', build);
    else build();
})();
</script>
@endverbatim
