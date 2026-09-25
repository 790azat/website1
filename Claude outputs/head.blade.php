<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

{{-- Google Website Translator: powers the 🇪🇸 language toggle in the header. --}}
<style>
    .goog-te-banner-frame,
    #goog-gt-tt,
    .goog-te-balloon-frame,
    .skiptranslate > iframe {
        display: none !important;
    }
    body {
        top: 0 !important;
        position: static !important;
    }
    .goog-text-highlight {
        background: none !important;
        box-shadow: none !important;
    }
</style>
<div id="google_translate_element" class="hidden"></div>
<script>
    // Google's widget remembers the chosen language two ways: a "googtrans" cookie,
    // and a "#googtrans(en|es)" hash it appends to the URL. Both are checked again on
    // every load, so both have to be dealt with — clearing only the cookie left the
    // hash in place (or vice versa), which is why reloading kept snapping back to
    // Spanish instead of English. Deleting the cookie isn't always enough on its own
    // (some hosting/proxy setups keep re-asserting it), so on top of deleting it we
    // also write an explicit "#googtrans(en|en)" hash before reloading — Google's
    // widget reads that hash at init time and it wins over a stale cookie.
    function clearTranslateCookies() {
        var host = window.location.hostname;
        var names = ['googtrans'];
        document.cookie.split(';').forEach(function (c) {
            var name = c.split('=')[0].trim();
            if (name && name.toLowerCase().indexOf('googtrans') !== -1 && names.indexOf(name) === -1) {
                names.push(name);
            }
        });
        names.forEach(function (name) {
            var expired = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            document.cookie = expired;
            document.cookie = expired + ' domain=' + host + ';';
            document.cookie = expired + ' domain=.' + host + ';';
            document.cookie = name + '=; max-age=0; path=/;';
            document.cookie = name + '=; max-age=0; path=/; domain=' + host + ';';
            document.cookie = name + '=; max-age=0; path=/; domain=.' + host + ';';
        });
    }

    function stripTranslateHash() {
        if (window.location.hash.indexOf('googtrans') !== -1) {
            history.replaceState(null, '', window.location.pathname + window.location.search);
        }
    }

    // Forces the ORIGINAL (English) page on the next load, overriding any cookie
    // that keeps getting re-applied. Used instead of a plain reload() wherever we
    // actually need English to stick.
    function reloadForcingEnglish() {
        clearTranslateCookies();
        history.replaceState(null, '', window.location.pathname + window.location.search + '#googtrans(en|en)');
        window.location.reload();
    }

    clearTranslateCookies();
    stripTranslateHash();

    function googleTranslateElementInit() {
        new google.translate.TranslateElement(
            { pageLanguage: 'en', includedLanguages: 'es', autoDisplay: false },
            'google_translate_element'
        );
    }

    function isSiteCurrentlySpanish() {
        return document.documentElement.classList.contains('translated-ltr')
            || document.documentElement.classList.contains('translated-rtl');
    }

    function updateLangToggleUI(isSpanish) {
        document.querySelectorAll('.lang-icon-es').forEach(function (el) {
            el.classList.toggle('hidden', isSpanish);
        });
        document.querySelectorAll('.lang-icon-en').forEach(function (el) {
            el.classList.toggle('hidden', !isSpanish);
        });
        document.querySelectorAll('.lang-toggle-btn').forEach(function (btn) {
            btn.setAttribute('aria-label', isSpanish ? 'View site in English' : 'Ver el sitio en español');
            btn.setAttribute('title', isSpanish ? 'View in English' : 'Ver en español');
        });
        document.querySelectorAll('.lang-toggle-label').forEach(function (el) {
            el.textContent = isSpanish ? 'View in English' : 'Ver en español';
        });
    }

    // Set the instant a person actually clicks the flag to go to Spanish, so the
    // load-time enforcement below (which fights an unwanted auto-translate) never
    // fights a translation the person asked for.
    var userRequestedSpanish = false;

    function switchToSpanish() {
        userRequestedSpanish = true;
        try { sessionStorage.removeItem('te_revert_attempted'); } catch (e) {}
        var trySwitch = function () {
            var select = document.querySelector('select.goog-te-combo');
            if (select) {
                select.value = 'es';
                select.dispatchEvent(new Event('change'));
                return true;
            }
            return false;
        };
        if (!trySwitch()) {
            var attempts = 0;
            var interval = setInterval(function () {
                attempts++;
                if (trySwitch() || attempts > 20) {
                    clearInterval(interval);
                }
            }, 250);
        }
        // Optimistic UI update; the observer below corrects it once Google finishes.
        updateLangToggleUI(true);
    }

    // Something in the widget (a cached cookie/hash Google re-derives internally, or
    // a hosting-level quirk) can re-apply Spanish a moment after the page has already
    // rendered in English. The old fix for that — resetting the <select> to "" — does
    // NOT actually undo a translation once Google has rewritten the DOM (that trick
    // only ever worked for going TO a language, never back), so it silently failed
    // and the page just sat there mistranslated, which is what showed up as "switches
    // back / jitters a second after loading". The reliable fix is the same one the
    // manual toggle uses: clear the cookie, force the "original" hash, reload. Guard
    // it with a one-shot sessionStorage flag so a page that's stubbornly reasserting
    // Spanish can't reload itself in an infinite loop — it corrects once per tab
    // session and then leaves it alone.
    function enforceEnglishOnLoad() {
        [0, 400, 900, 1500, 2500].forEach(function (delay) {
            setTimeout(function () {
                if (userRequestedSpanish) return;
                if (!isSiteCurrentlySpanish()) return;
                var alreadyTried = false;
                try { alreadyTried = sessionStorage.getItem('te_revert_attempted') === '1'; } catch (e) {}
                if (alreadyTried) return;
                try { sessionStorage.setItem('te_revert_attempted', '1'); } catch (e) {}
                reloadForcingEnglish();
            }, delay);
        });
    }

    function toggleSiteLanguage() {
        if (isSiteCurrentlySpanish()) {
            reloadForcingEnglish();
            return;
        }
        switchToSpanish();
    }

    document.addEventListener('DOMContentLoaded', function () {
        updateLangToggleUI(false);
        if (!isSiteCurrentlySpanish()) {
            try { sessionStorage.removeItem('te_revert_attempted'); } catch (e) {}
        }
        enforceEnglishOnLoad();
        var observer = new MutationObserver(function () {
            updateLangToggleUI(isSiteCurrentlySpanish());
        });
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    });
</script>
<script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async></script>
