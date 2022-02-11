<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>@yield('title')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Nunito&display=swap" rel="stylesheet">

        <!-- Styles -->
        <style>
            html {
                line-height: 1.15;
                    -ms-text-size-adjust: 100%;
                -webkit-text-size-adjust: 100%;
            }

            body {
                margin: 0;
            }

            header,
            nav,
            section {
                display: block;
            }

            figcaption,
            main {
                display: block;
            }

            a {
                background-color: transparent;
                -webkit-text-decoration-skip: objects;
            }

            strong {
                font-weight: inherit;
            }

            strong {
                font-weight: bolder;
            }

            code {
                font-family: monospace, monospace;
                font-size: 1em;
            }

            dfn {
                font-style: italic;
            }

            svg:not(:root) {
                overflow: hidden;
            }

            button,
            input {
                font-family: sans-serif;
                font-size: 100%;
                line-height: 1.15;
                margin: 0;
            }

            button,
            input {
                overflow: visible;
            }

            button {
                text-transform: none;
            }

            button,
            html [type="button"],
            [type="reset"],
            [type="submit"] {
                -webkit-appearance: button;
            }

            button::-moz-focus-inner,
            [type="button"]::-moz-focus-inner,
            [type="reset"]::-moz-focus-inner,
            [type="submit"]::-moz-focus-inner {
                border-style: none;
                padding: 0;
            }

            button:-moz-focusring,
            [type="button"]:-moz-focusring,
            [type="reset"]:-moz-focusring,
            [type="submit"]:-moz-focusring {
                outline: 1px dotted ButtonText;
            }

            legend {
                -webkit-box-sizing: border-box;
                        box-sizing: border-box;
                color: inherit;
                display: table;
                max-width: 100%;
                padding: 0;
                white-space: normal;
            }

            [type="checkbox"],
            [type="radio"] {
                -webkit-box-sizing: border-box;
                        box-sizing: border-box;
                padding: 0;
            }

            [type="number"]::-webkit-inner-spin-button,
            [type="number"]::-webkit-outer-spin-button {
                height: auto;
            }

            [type="search"] {
                -webkit-appearance: textfield;
                outline-offset: -2px;
            }

            [type="search"]::-webkit-search-cancel-button,
            [type="search"]::-webkit-search-decoration {
                -webkit-appearance: none;
            }

            ::-webkit-file-upload-button {
                -webkit-appearance: button;
                font: inherit;
            }

            menu {
                display: block;
            }

            canvas {
                display: inline-block;
            }

            template {
                display: none;
            }

            [hidden] {
                display: none;
            }

            html {
                -webkit-box-sizing: border-box;
                        box-sizing: border-box;
                font-family: sans-serif;
            }

            html,
            body {
                height: 100%;
            }

            *,
            *::before,
            *::after {
                -webkit-box-sizing: inherit;
                        box-sizing: inherit;
            }

            p {
                margin: 0;
            }

            button {
                background: transparent;
                padding: 0;
            }

            button:focus {
                outline: 1px dotted;
                outline: 5px auto -webkit-focus-ring-color;
            }

            *,
            *::before,
            *::after {
                border-width: 0;
                border-style: solid;
                border-color: #dae1e7;
            }

            button,
            [type="button"],
            [type="reset"],
            [type="submit"] {
                border-radius: 0;
            }

            button,
            input {
                font-family: inherit;
            }

            input::-webkit-input-placeholder {
                color: inherit;
                opacity: .5;
            }

            input:-ms-input-placeholder {
                color: inherit;
                opacity: .5;
            }

            input::-ms-input-placeholder {
                color: inherit;
                opacity: .5;
            }

            input::placeholder {
                color: inherit;
                opacity: .5;
            }

            button,
            [role=button] {
                cursor: pointer;
            }

            .bg-transparent {
                background-color: transparent;
            }

            .bg-white {
                background-color: #fff;
            }

            .bg-teal-light {
                background-color: #64d5ca;
            }

            .bg-blue-dark {
                background-color: #2779bd;
            }

            .bg-indigo-light {
                background-color: #7886d7;
            }

            .bg-purple-light {
                background-color: #a779e9;
            }

            .bg-no-repeat {
                background-repeat: no-repeat;
            }

            .bg-cover {
                background-size: cover;
            }

            .border-grey-light {
                border-color: #dae1e7;
            }

            .hover\:border-grey:hover {
                border-color: #b8c2cc;
            }

            .rounded-lg {
                border-radius: .5rem;
            }

            .border-2 {
                border-width: 2px;
            }

            .hidden {
                display: none;
            }

            .flex {
                display: -webkit-box;
                display: -ms-flexbox;
                display: flex;
            }

            .items-center {
                -webkit-box-align: center;
                    -ms-flex-align: center;
                        align-items: center;
            }

            .justify-center {
                -webkit-box-pack: center;
                    -ms-flex-pack: center;
                        justify-content: center;
            }

            .font-sans {
                font-family: Nunito, sans-serif;
            }

            .font-light {
                font-weight: 300;
            }

            .font-bold {
                font-weight: 700;
            }

            .font-black {
                font-weight: 900;
            }

            .h-1 {
                height: .25rem;
            }

            .leading-normal {
                line-height: 1.5;
            }

            .m-8 {
                margin: 2rem;
            }

            .my-3 {
                margin-top: .75rem;
                margin-bottom: .75rem;
            }

            .mb-8 {
                margin-bottom: 2rem;
            }

            .max-w-sm {
                max-width: 30rem;
            }

            .min-h-screen {
                min-height: 100vh;
            }

            .py-3 {
                padding-top: .75rem;
                padding-bottom: .75rem;
            }

            .px-6 {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }

            .pb-full {
                padding-bottom: 100%;
            }

            .absolute {
                position: absolute;
            }

            .relative {
                position: relative;
            }

            .pin {
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
            }

            .text-black {
                color: #22292f;
            }

            .text-white {
                color: #fff;
            }

            .text-grey-darkest {
                color: #3d4852;
            }

            .text-grey-darker {
                color: #606f7b;
            }

            .text-2xl {
                font-size: 1.5rem;
            }

            .text-5xl {
                font-size: 3rem;
            }

            .uppercase {
                text-transform: uppercase;
            }

            .antialiased {
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }

            .tracking-wide {
                letter-spacing: .05em;
            }

            .w-16 {
                width: 4rem;
            }

            .w-full {
                width: 100%;
            }

            .bg-custom {
                background-color: #495C9C;
            }

            .err {
                height: 50vh;
            }

            .err-img {
                display: flex;
                justify-content: center;
                align-items: center;
                align-content: center;
                height: 50vh;
            }

            .err-img img {
                width: 90%;
                max-height: 80%;
            }

            .glitch {
                position: relative;
            }

            .glitch:before,
            .glitch:after {
                 content: "@yield('code', __('Oh no'))";
                 color: #fff;
                 position: absolute;
                 top: 0;
                 overflow: hidden;
             }
            .glitch:before {
                 left: 3px;
                 text-shadow: -3px 0 red;
                 animation: glitch-before 3s linear 0s infinite alternate;
             }
            .glitch:after {
                 left: -3px;
                 text-shadow: -3px 0 blue;
                 animation: glitch-after 3s linear 0s infinite alternate;
             }

            @keyframes glitch-before {
                0% {
                    clip: rect(45px, 350px, 56px, 0);
                }
                5% {
                    clip: rect(102px, 350px, 52px, 0);
                }
                10% {
                    clip: rect(0, 350px, 93px, 0);
                }
                15% {
                    clip: rect(122px, 350px, 91px, 0);
                }
                20% {
                    clip: rect(52px, 350px, 69px, 0);
                }
                25% {
                    clip: rect(89px, 350px, 141px, 0);
                }
                30% {
                    clip: rect(80px, 350px, 24px, 0);
                }
                35% {
                    clip: rect(21px, 350px, 3px, 0);
                }
                40% {
                    clip: rect(87px, 350px, 13px, 0);
                }
                45% {
                    clip: rect(5px, 350px, 106px, 0);
                }
                50% {
                    clip: rect(138px, 350px, 115px, 0);
                }
                55% {
                    clip: rect(91px, 350px, 105px, 0);
                }
                60% {
                    clip: rect(92px, 350px, 25px, 0);
                }
                65% {
                    clip: rect(69px, 350px, 108px, 0);
                }
                70% {
                    clip: rect(67px, 350px, 20px, 0);
                }
                75% {
                    clip: rect(42px, 350px, 46px, 0);
                }
                80% {
                    clip: rect(94px, 350px, 48px, 0);
                }
                85% {
                    clip: rect(11px, 350px, 101px, 0);
                }
                90% {
                    clip: rect(135px, 350px, 104px, 0);
                }
                95% {
                    clip: rect(128px, 350px, 69px, 0);
                }
                100% {
                    clip: rect(26px, 350px, 116px, 0);
                }
            }
            @keyframes glitch-after {
                0% {
                    clip: rect(137px, 350px, 103px, 0);
                }
                5% {
                    clip: rect(29px, 350px, 77px, 0);
                }
                10% {
                    clip: rect(148px, 350px, 150px, 0);
                }
                15% {
                    clip: rect(60px, 350px, 65px, 0);
                }
                20% {
                    clip: rect(99px, 350px, 54px, 0);
                }
                25% {
                    clip: rect(104px, 350px, 11px, 0);
                }
                30% {
                    clip: rect(45px, 350px, 82px, 0);
                }
                35% {
                    clip: rect(34px, 350px, 10px, 0);
                }
                40% {
                    clip: rect(1px, 350px, 11px, 0);
                }
                45% {
                    clip: rect(119px, 350px, 93px, 0);
                }
                50% {
                    clip: rect(19px, 350px, 20px, 0);
                }
                55% {
                    clip: rect(26px, 350px, 84px, 0);
                }
                60% {
                    clip: rect(63px, 350px, 44px, 0);
                }
                65% {
                    clip: rect(21px, 350px, 143px, 0);
                }
                70% {
                    clip: rect(45px, 350px, 132px, 0);
                }
                75% {
                    clip: rect(65px, 350px, 105px, 0);
                }
                80% {
                    clip: rect(75px, 350px, 124px, 0);
                }
                85% {
                    clip: rect(101px, 350px, 16px, 0);
                }
                90% {
                    clip: rect(20px, 350px, 116px, 0);
                }
                95% {
                    clip: rect(133px, 350px, 102px, 0);
                }
                100% {
                    clip: rect(9px, 350px, 147px, 0);
                }
            }


            @media (min-width: 768px) {
                .md\:bg-left {
                    background-position: left;
                }

                .md\:bg-right {
                    background-position: right;
                }

                .md\:flex {
                    display: -webkit-box;
                    display: -ms-flexbox;
                    display: flex;
                }

                .md\:my-6 {
                    margin-top: 1.5rem;
                    margin-bottom: 1.5rem;
                }

                .md\:min-h-screen {
                    min-height: 100vh;
                }

                .md\:pb-0 {
                    padding-bottom: 0;
                }

                .md\:text-3xl {
                    font-size: 1.875rem;
                }

                .md\:text-15xl {
                    font-size: 9rem;
                }

                .md\:w-1\/2 {
                    width: 50%;
                }

                .err {
                    height: 100vh;
                }

                .err-img {
                    height: 100vh;
                }
            }

            @media (min-width: 992px) {
                .lg\:bg-center {
                    background-position: center;
                }
            }
        </style>
    </head>
    <body class="antialiased font-sans">
        <div class="md:flex min-h-screen">
            <div class="err w-full md:w-1/2 bg-custom flex items-center justify-center">
                <div class="max-w-sm m-8">
                    <div class="text-white glitch text-5xl md:text-15xl font-black">
                        @yield('code', __('Oh no'))
                    </div>

                    <div class="w-16 h-1 bg-white my-3 md:my-6"></div>

                    <p class="text-white text-2xl md:text-3xl font-light mb-8 leading-normal">
                        @yield('message')
                    </p>

                    <a href="{{ app('router')->has('home') ? route('home') : url('/') }}">
                        <button class="bg-transparent text-white font-bold uppercase tracking-wide py-3 px-6 border-2 border-grey-light hover:border-grey rounded-lg">
                            {{ __('errors.home') }}
                        </button>
                    </a>
                </div>
            </div>

            <div class="relative err-img md:flex md:pb-0 md:min-h-screen w-full md:w-1/2">
                <img src="{{ asset('img/error.svg') }}" alt="Ошибка">
            </div>
        </div>
    </body>
</html>
