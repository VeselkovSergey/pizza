<!DOCTYPE html>
<html lang="ru">

    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @yield('meta')

        <title>{{ isset($title_page) ? $title_page : env('APP_NAME') }}</title>

        <link href="{{asset('resources/css/reset.css')}}" rel="stylesheet">
        <link href="{{asset('resources/css/helpers.css')}}" rel="stylesheet">
        <link href="{{asset('resources/css/loaders.css')}}" rel="stylesheet">

        <link href="{{asset('resources/css/app.css')}}" rel="stylesheet">

        <link href="{{asset('resources/scss/app.scss')}}" rel="stylesheet">

        @yield('css')

        <script src="{{ asset('resources/js/add.prototypes.js') }}"></script>

    </head>

    <body>

        <header class="flex-wrap pos-sticky top-0 bg-white z-1">@include('layouts.header')</header>

        <main class="m-25">@yield('content')</main>

        <div class="pos-fix top-0 left-0 w-100 h-100vh bg-white flex-center z-1 pre-text">
            <div style="font-style: italic; font-size: 100px;">БРОПИЦЦА - НАСТОЯЩАЯ ПИЦЦА ДЛЯ ТЕБЯ</div>
        </div>

        <script>

            const routeOrderCreate = "{{route('order-create')}}";

        </script>

        <script src="{{ asset('resources/js/script.js') }}"></script>

        @yield('js')

    </body>

</html>
