@php
    $siteName = config('app.name', 'Laravel');
    $title = __('Terms of Use').' — '.$siteName;

    $data = app(\App\Content\SiteContent::class)->localized();
    $categories = collect($data['sections'])
        ->map(fn ($meta, $key) => ['id' => $key, 'title' => $meta['title']])
        ->sortBy(fn ($c) => $data['sections'][$c['id']]['order'])
        ->values()
        ->all();

    $slug = fn (string $s) => strtolower(preg_replace('/[^a-z0-9]+/i', '', $s));
    $domainName = $slug($siteName).'.com';

    $lastUpdated = \Carbon\Carbon::parse('2026-09-01');

    $sections = [
        [
            'heading' => 'Acceptance of Terms',
            'body' => "These Terms of Use (\"Terms\") govern your access to and use of :site, including our website, articles, and any account features we offer. By accessing or using :site, you agree to be bound by these Terms. If you do not agree, please do not use the site.",
        ],
        [
            'heading' => 'Educational Use Only',
            'body' => ":site publishes content for general informational and educational purposes only. Nothing on this site constitutes personalized financial, investment, tax, or legal advice, and it should not be relied upon as such. You should consult a qualified professional before making decisions based on information found here.",
        ],
        [
            'heading' => 'Use of the Site',
            'body' => 'When using our site, you agree to:',
            'list' => [
                'Use the site only for lawful purposes and in a manner consistent with these Terms.',
                'Not attempt to gain unauthorized access to any part of the site, other accounts, or related systems.',
                'Not interfere with or disrupt the site, its servers, or its networks.',
                'Not scrape, copy, or republish substantial portions of our content without permission.',
                'Provide accurate information if you create an account with us.',
            ],
        ],
        [
            'heading' => 'Accounts',
            'body' => "If :site offers account or dashboard features, you are responsible for maintaining the confidentiality of your login credentials and for all activity that occurs under your account. Please notify us promptly if you suspect any unauthorized use of your account.",
        ],
        [
            'heading' => 'Intellectual Property',
            'body' => "Unless otherwise noted, all articles, graphics, logos, and other content on :site are the property of :site or its licensors and are protected by copyright and other intellectual property laws. You may view and share our content for personal, non-commercial use, but you may not reproduce, distribute, or create derivative works from it without our prior written consent.",
        ],
        [
            'heading' => 'Third-Party Links',
            'body' => "Our articles may reference or link to third-party websites, tools, or sources for further reading. These links are provided for convenience only. We do not control, endorse, or take responsibility for the content, accuracy, or practices of any third-party site.",
        ],
        [
            'heading' => 'Disclaimer of Warranties',
            'body' => ":site is provided on an \"as is\" and \"as available\" basis. While we aim to keep our content accurate and up to date, we make no warranties, express or implied, regarding the completeness, reliability, or accuracy of any information on the site.",
        ],
        [
            'heading' => 'Limitation of Liability',
            'body' => "To the fullest extent permitted by law, :site and its team members are not liable for any indirect, incidental, or consequential damages arising from your use of, or inability to use, the site or any content published on it.",
        ],
        [
            'heading' => 'Changes to These Terms',
            'body' => "We may update these Terms from time to time to reflect changes in our practices or for legal or operational reasons. When we do, we will revise the date at the top of this page. Continued use of the site after changes are posted constitutes your acceptance of the updated Terms.",
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <meta name="description" content="{{ __('Read the :site terms of use for the rules and guidelines that govern use of our site.', ['site' => $siteName]) }}" />
    </head>
    <body
        x-data="{ mobileOpen: false }"
        class="bg-white text-zinc-900 antialiased selection:bg-zinc-900 selection:text-white dark:bg-zinc-950 dark:text-zinc-100 dark:selection:bg-white dark:selection:text-zinc-900"
    >
        @include('partials.site-header', ['categories' => $categories, 'siteName' => $siteName])

        <main>
            <section class="relative overflow-hidden">
                <div class="pointer-events-none absolute inset-x-0 -top-40 -z-10 flex justify-center">
                    <div class="h-[26rem] w-[56rem] rounded-full bg-blue-200/40 blur-3xl dark:bg-blue-500/10"></div>
                </div>

                <div class="mx-auto max-w-2xl px-6 pt-16 pb-4 text-center lg:px-8 lg:pt-20">
                    <span class="inline-flex items-center rounded-full border border-zinc-200 bg-zinc-50 px-3 py-1 text-xs font-medium tracking-wide text-zinc-600 uppercase dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400">
                        {{ __('Legal') }}
                    </span>
                    <h1 class="mt-6 text-4xl font-semibold tracking-tight text-zinc-900 sm:text-5xl dark:text-white">
                        {{ __('Terms of Use') }}
                    </h1>
                    <p class="mt-6 text-sm text-zinc-500 dark:text-zinc-500">
                        {{ __('Last updated :date', ['date' => $lastUpdated->translatedFormat(__('F j, Y'))]) }}
                    </p>
                </div>
            </section>

            <section class="mx-auto max-w-3xl px-6 py-14 lg:px-8">
                <div class="space-y-10">
                    @foreach ($sections as $block)
                        <div>
                            <h2 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-white">
                                {{ __($block['heading']) }}
                            </h2>
                            <p class="mt-3 leading-relaxed text-zinc-600 dark:text-zinc-400">
                                {{ __($block['body'], ['site' => $siteName]) }}
                            </p>
                            @isset($block['list'])
                                <ul class="mt-4 space-y-2">
                                    @foreach ($block['list'] as $item)
                                        <li class="flex gap-3 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                                            <span class="mt-2 size-1.5 shrink-0 rounded-full bg-zinc-400 dark:bg-zinc-600"></span>
                                            <span>{{ __($item, ['site' => $siteName]) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endisset
                        </div>
                    @endforeach

                    <div>
                        <h2 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-white">
                            {{ __('Contact Us') }}
                        </h2>
                        <p class="mt-3 leading-relaxed text-zinc-600 dark:text-zinc-400">
                            {{ __('If you have any questions about these Terms of Use, please reach out to us at') }}
                            <a href="mailto:hello@{{ $domainName }}" class="font-medium text-zinc-900 hover:underline dark:text-white">hello@{{ $domainName }}</a>
                            {{ __('or visit our') }} <a href="{{ route('contact') }}" wire:navigate class="font-medium text-zinc-900 hover:underline dark:text-white">{{ __('Contact page') }}</a>.
                        </p>
                    </div>
                </div>

                <div class="mt-12 rounded-2xl border border-zinc-200 bg-zinc-50 p-6 text-sm leading-relaxed text-zinc-600 dark:border-zinc-800 dark:bg-zinc-900/40 dark:text-zinc-400">
                    <p>
                        {{ __(':site publishes educational and informational content only and is not a substitute for personalized financial, investment, tax, or legal advice. These Terms describe the rules for using our site and are not themselves legal advice.', ['site' => $siteName]) }}
                    </p>
                </div>
            </section>
        </main>

        @include('partials.site-footer', ['siteName' => $siteName])

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
