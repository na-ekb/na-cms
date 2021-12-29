<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\PageMeta;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $page = app('rinvex.pages.page')->create([
            'uri'   => 'home',
            'slug'  => 'home',
            'route' => 'site.pages.home',
            'title' => 'Анонимные Наркоманы',
            'view'  => 'home',
        ]);
        $meta = PageMeta::create([
            'small_desc'    => 'Сообщество «Анонимные Наркоманы», Екатеринбург',
            'description'   => 'Сообщество «Анонимные Наркоманы» – международное сообщество выздоравливающих зависимых. Екатеринбург и область.',
            'keywords'      => 'АН, Анонимные Наркоманы, Екатеринбург, Свердловская область',
            'meta_tags'     => '
                <script type="application/ld+json">
                {
                    "@context": "http://www.schema.org",
                    "@type": "NGO",
                    "name": "Сообщество «Анонимные Наркоманы» Екатеринбург",
                    "url": "' . route('pages') . '",
                    "logo": "' . asset('img/og-logo.png') . '",
                    "description": "Сообщество «Анонимные Наркоманы» – международное сообщество выздоравливающих зависимых. Екатеринбург и область.",
                    "address": {
                        "@type": "PostalAddress",
                        "addressLocality": "Екатеринбург",
                        "addressRegion": "Свердловская область",
                        "addressCountry": "Россия"
                    },
                    "contactPoint": {
                        "@type": "ContactPoint",
                        "telephone": " +7 (922) 296 12 12",
                        "contactType": "emergency",
                        "areaServed": "RU",
                        "availableLanguage": "Russian"
                    }
                }
                </script>',
            'scripts'       => '
                <!-- Yandex.Metrika counter -->
                <script type="text/javascript" >
                    (function(m, e, t, r, i, k, a) {
                        m[i] = m[i] || function() {
                            (m[i].a = m[i].a || []).push(arguments)
                        };
                        m[i].l = 1 * new Date();
                        k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
                    })(window, document, \'script\', \'https://mc.yandex.ru/metrika/tag.js\', \'ym\');
                    ym(36680270, \'init\', {
                        id: 36680270,
                        clickmap: true,
                        trackLinks: true,
                        accurateTrackBounce: true,
                        webvisor: true
                    });
                </script>
                <noscript>
                    <div>
                        <img src = "https://mc.yandex.ru/watch/36680270" style = "position:absolute; left:-9999px;" alt = "">
                    </div>
                </noscript >
                <!-- /Yandex.Metrika counter -->
                <!-- Google Analytics -->
                <script>
                    (function(i,s,o,g,r,a,m){
                        i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){
                            (i[r].q=i[r].q||[]).push(arguments)
                        }, i[r].l=1*new Date();a=s.createElement(o), m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                    })(window,document,\'script\',\'https://www.google-analytics.com/analytics.js\',\'ga\');
                    ga(\'create\', \'UA-98678826-1\', \'auto\');
                    ga(\'send\', \'pageview\');
                </script>
                <!-- /Google Analytics -->
            '
        ]);

        $page->meta = $meta;
        $page->save();



    }
}
