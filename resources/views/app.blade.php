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

        <nav class="left-menu hide z-2 pos-fix top-0 left-0 w-100 h-100">
            <div class="shadow-menu w-100 h-100 bg-black pos-abs" style="opacity: 0.5"></div>
            <div class="bg-white h-100 pos-rel" style="width: fit-content; max-width: calc(100% - 96px);">
                <div class="close-menu-button cp pos-abs top-0" style="right: -48px">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>
                    </svg>
                </div>
                <div class="scroll-auto pr-25 h-100">
                    <div class="fast-menu-in-left-menu">
                        <div class="flex-column p-10">
                            <div class="mb-10">
                                @if(auth()->check())
                                    Профиль
                                @else
                                    Вход
                                @endif
                            </div>
                            <a class="color-black text-center" style="text-decoration: none;" href="tel:{{env('PHONE_COMPANY')}}">{{env('PHONE_COMPANY')}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main>@yield('content')</main>

        <script>
            const auth = {{auth()->check() ? 'true' : 'false'}};
            const userPhone = "{{auth()->check() ? auth()->user()->phone : ''}}";

            const routeOrderCreate = "{{route('order-create')}}";
            const routePhoneValidation = "{{route('phone-validation')}}";
            const routeCheckConfirmationCode = "{{route('check-confirmation-code')}}";
            const routeLogout = "{{route('logout')}}";
        </script>

        <script src="{{ asset('resources/js/script.js') }}"></script>

        @yield('js')

        <script>
            eval({{session('execFunction')}});
        </script>

    </body>

</html>
