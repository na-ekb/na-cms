<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>БотАН</title>

       {{-- Laravel Mix - CSS File --}}
       {{-- <link rel="stylesheet" href="{{ mix('css/tgbot.css') }}"> --}}

    </head>
    <body>
        @yield('content')
        <script src="{{ mix('js/telegram-web-app.js') }}"></script>
    </body>
</html>
