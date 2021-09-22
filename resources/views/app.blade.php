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

    </head>

    <body>

        <div class="flash-message-container pos-fix z-5 py-5"></div>

        <header>@include('layouts.header')</header>

        <main class="m-25">@yield('content')</main>

        <div class="hide csrf_token">{{csrf_token()}}</div>

        <script src="{{ asset('resources/js/script.js') }}"></script>

        @yield('js')

    </body>

</html>
