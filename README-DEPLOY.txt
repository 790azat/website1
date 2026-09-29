CAPTCHA DOMAIN / ДОМЕН КАПЧИ

Upload everything into the ROOT of the captcha domain (e.g. kapcha.com).
Залейте всё в КОРЕНЬ домена капчи (например kapcha.com).

Variants / Варианты:
  /variant-1  - 18+ age check (YES only)           / проверка 18+ (только YES)
  /variant-2  - slide to verify                   / слайдер
  /variant-3  - press and hold                    / нажать и удерживать
  /           - opens variant-1                   / открывает variant-1
  /terms, /privacy - Terms of Use and Privacy Policy of the captcha domain
                     (linked from the captcha footer) / Terms и Privacy домена капчи

Where the visitor goes after the captcha / Куда ведёт после капчи:
  1) ?to=<full URL> in the link:
     https://kapcha.com/variant-2?to=https://site.com/programs/sofi-loans
  2) without ?to= - a random page from config.js for that variant
     (edit config.js: real site domains and pages).
     без ?to= - случайная страница из config.js (впишите реальные домены).

Language / Язык: ?lang=en|es|fr, otherwise the browser language.

- Vercel / Netlify: upload the folder as is (vercel.json is included).
- Apache / cPanel: .htaccess is included (hidden file).
- Nginx: location / { try_files $uri $uri.html $uri/index.html =404; }
