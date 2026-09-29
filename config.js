/*
 * Where each captcha variant sends the visitor when the link has no ?to=.
 * site:      the site address (change to the real domain when it is live)
 * languages: languages the site has under /es, /fr (English lives at the root)
 * pages:     one of these opens at random
 */
window.GATE_CONFIG = {
    'variant-1': {
        site: 'https://website1-pink-delta.vercel.app',
        languages: ['en', 'es', 'fr'],
        pages: [
            '/programs/ama-digital-marketing-certification',
            '/programs/lsu-online-mba-in-marketing'
        ]
    },
    'variant-2': {
        site: 'https://website2-eight-tan.vercel.app',
        languages: ['en', 'es', 'fr'],
        pages: [
            '/programs/ford-auto-financing',
            '/programs/santander-auto-financing',
            '/programs/sofi-loans',
            '/programs/truist-auto-financing'
        ]
    },
    'variant-3': {
        site: 'https://website3-bay-kappa.vercel.app',
        languages: ['en', 'es', 'fr'],
        pages: [
            '/programs/andersen-replacement-windows',
            '/programs/gaf-roof-replacement',
            '/programs/home-repair-financing',
            '/programs/metronet-construction'
        ]
    }
};
