@extends('servicesite::layouts.master')

@section('content')
    <h1 class="my-8 text-3xl">Служебный сайт сообщества "Анонимные Наркоманы" Самары</h1>

    <div class="flex flex-col w-full bg-white text-gray-800 rounded shadow-lg w-full md:w-1/2 my-4">
        <div class="flex flex-col w-full">
            <div class="p-4 font-normal text-gray-800">
                <h2 class="mb-4 text-2xl font-bold leading-none text-gray-800">
                    Группа: Штаб
                </h2>
                <p class="leading-normal">
                    13 свободных служений
                </p>
                <div class="flex flex-row items-center mt-4 text-gray-700 w-full">
                    <div class="w-1/2">
                        Каждый второй вторник
                    </div>
                    <div class="w-1/2 text-right">
                        Фрунзе 96е
                    </div>
                </div>
            </div>
            <button class="w-full mt-3 rounded-b h-12 md:h-8 border-2">
                Подробнее
            </button>
        </div>
    </div>
@endsection
