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
    // Google's widget persists the chosen language across page loads via a
    // "googtrans" cookie, and re-applies it on the very next load whenever
    // pageLanguage's default (English) init runs. That's normally convenient,
    // but on this site the toggle is a manual per-visit switch, not a
    // "remember forever" preference — and trying to delete that cookie after
    // the fact turned out to be unreliable (timing races against Google's own
    // script re-writing it), which is what showed up as the page switching to
    // Spanish and then snapping back / jittering a moment later.
    //
    // Rather than fight to delete a cookie Google keeps re-asserting, this
    // page simply hides the "googtrans" cookie from anyone reading
    // document.cookie (including Google's own init script) and refuses to let
    // anything write a real value into it. Every other cookie (session,
    // CSRF, etc.) passes through untouched. With the cookie invisible, the
    // widget's own default init never finds a reason to auto-translate, so
    // every fresh page load reliably starts in English — no more races, no
    // more reload loops needed to "win" against Google.
    (function () {
        var proto = Object.getPrototypeOf(document);
        var desc = Object.getOwnPropertyDescriptor(proto, 'cookie')
            || Object.getOwnPropertyDescriptor(Document.prototype, 'cookie');
        if (!desc || !desc.configurable || !desc.get || !desc.set) return;
        var nativeGet = desc.get;
        var nativeSet = desc.set;
        var isGoogtrans = function (pairOrAssignment) {
            return pairOrAssignment.split('=')[0].trim().toLowerCase() === 'googtrans';
        };
        Object.defineProperty(document, 'cookie', {
            configurable: true,
            get: function () {
                var raw = nativeGet.call(document);
                if (!raw || raw.toLowerCase().indexOf('googtrans') === -1) return raw;
                return raw.split('; ').filter(function (pair) { return !isGoogtrans(pair); }).join('; ');
            },
            set: function (v) {
                if (typeof v === 'string' && isGoogtrans(v)) {
                    // Still allow an actual deletion attempt through (harmless), just
                    // block anything trying to set a real translation value.
                    var isDeletion = /expires=Thu,?\s*01 Jan 1970|max-age=0/i.test(v);
                    if (!isDeletion) return;
                }
                return nativeSet.call(document, v);
            }
        });
    })();

    function stripTranslateHash() {
        if (window.location.hash.indexOf('googtrans') !== -1) {
            history.replaceState(null, '', window.location.pathname + window.location.search);
        }
    }

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

    function switchToSpanish() {
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

    // Going back to English is now just a plain reload: the cookie override
    // above means Google's init never sees a reason to re-translate, so a
    // reload reliably lands in English without any clear/retry dance.
    function toggleSiteLanguage() {
        if (isSiteCurrentlySpanish()) {
            stripTranslateHash();
            window.location.reload();
            return;
        }
        switchToSpanish();
    }

    document.addEventListener('DOMContentLoaded', function () {
        updateLangToggleUI(false);
        var observer = new MutationObserver(function () {
            updateLangToggleUI(isSiteCurrentlySpanish());
        });
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    });
</script>
<script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async></script>
