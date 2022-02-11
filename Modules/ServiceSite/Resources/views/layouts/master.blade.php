<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>Laravel</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0,viewport-fit=cover">
        <link rel="stylesheet" href="{{ mix('css/servicesite.css') }}">
    </head>
    <body class="antialiased text-gray-800 bg-gray-100 dark:bg-gray-600 dark:text-gray-100">
        <div>
            <nav class="bg-white dark:bg-gray-800 shadow-md py-2">
                <div class="max-w-7xl mx-auto px-5">
                    <div class="flex items-center justify-between h-14">
                        <div class="flex items-center">
                            <a class="flex-shrink-0" href="/">
                                <svg class="h-12 w-12" viewBox="0 0 2000 2000">
                                    <path class="fill-current text-gray-900 dark:text-gray-100" d="M1115 1777c17,-38 -5,-8 30,-53l295 -297c71,-75 269,-280 345,-330 -70,335 -303,666 -670,680zm-893 -678l676 686c-388,-65 -598,-308 -676,-686zm776 -712c71,38 576,546 613,616l-610 615 -610 -613c24,-56 543,-583 607,-618zm106 -172c349,25 638,344 679,685l-679 -685zm-883 673c46,-349 324,-639 675,-675 -24,60 -599,634 -675,675zm572 -835c-522,112 -859,623 -735,1162 120,518 618,847 1151,734 516,-109 853,-639 735,-1165 -119,-537 -615,-847 -1151,-731z">
                                </svg>
                            </a>
                            <div class="hidden md:block">
                                <div class="ml-1 flex items-baseline space-x-4">
                                    <span class="text-3xl transition-all text-gray-900 dark:text-gray-100  px-3 py-2">
                                        Самара
                                    </span>
                                </div>
                                <div class="ml-10 flex items-baseline space-x-4 hidden">
                                    <a class="transition-all text-gray-800 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-100  px-3 py-2 rounded-md text-md font-medium" href="">
                                        Главная
                                    </a>
                                    <a class="transition-all text-gray-400 dark:text-gray-100 hover:text-gray-800 dark:hover:text-gray-400 px-3 py-2 rounded-md text-md font-medium" href="">
                                        Группы
                                    </a>
                                    <a class="transition-all text-gray-400 dark:text-gray-100 hover:text-gray-800 dark:hover:text-gray-400 px-3 py-2 rounded-md text-md font-medium" href="">
                                        Чаты
                                    </a>
                                    <a class="transition-all text-gray-400 dark:text-gray-100 hover:text-gray-800 dark:hover:text-gray-400 px-3 py-2 rounded-md text-md font-medium" href="">
                                        Файлы
                                    </a>
                                </div>
                            </div>
                        </div>
                        <form class="w-3/4 md:w-2/5 lg:w-2/4" method="GET">
                            <div class="w-full relative text-gray-800 dark:text-gray-500 dark:focus-within:text-gray-400">
                                <span class="absolute inset-y-0 right-2 flex items-center pl-2">
                                    <button type="submit" class="p-1 focus:outline-none focus:shadow-outline">
                                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-6 h-6">
                                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </button>
                                </span>
                                <input type="text" name="q" class="transition-all py-3 text-sm dark:text-white bg-gray-200 dark:bg-gray-900 rounded-md pl-4 pr-10 focus:outline-none dark:focus:bg-white dark:focus:text-gray-900 w-full" placeholder="Поиск..." autocomplete="off">
                            </div>
                        </form>
                    </div>
                </div>
            </nav>
        </div>
        <div class="max-w-7xl mx-auto px-5 py-4">
            @yield('content')
        </div>
        <script src="{{ mix('js/servicesite.js') }}"></script>
    </body>
</html>
