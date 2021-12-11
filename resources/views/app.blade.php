<!DOCTYPE html>
<html lang="ru">

    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @yield('meta')

{{--        <title>{{ isset($title_page) ? $title_page : env('APP_NAME') }}</title>--}}
        <title>Пицца Дубна. БроПицца!</title>
        <meta name="description" content="Привет БРО! Говорят в Дубне давненько никто не делал вкусную пиццу. Этому городу определенно нужен был спасатель! Классно что в Дубне открылись мы (БРОпицца)! Теперь вкусная пицца будет у каждого на столе!">
        <meta name="keywords" content="Пицца, Дубна, Бропицца, BROпицца, bro пицца, бро пицца, вкусная пицца, пицца Дубна, новая пиццерия Дубна">
        <link href="{{asset('resources/scss/reset.scss')}}" rel="stylesheet">
        <link href="{{asset('resources/scss/helpers.scss')}}" rel="stylesheet">
        <link href="{{asset('resources/scss/loaders.scss')}}" rel="stylesheet">
        <link href="{{asset('resources/scss/app.scss')}}" rel="stylesheet">
        <link href="{{asset('resources/scss/adaptive.scss')}}" rel="stylesheet">

{{--        <link href="{{asset('fonts/fonts.css')}}" rel="stylesheet">--}}

        @yield('css')

        <script src="{{ asset('resources/js/add.prototypes.js') }}"></script>

    </head>

    <body class="bg-black-custom color-white">


        @php
            $authCheck = auth()->check();
            $actionConditionAuth = !$authCheck ? 'LoginWindow()' : 'Profile()';
        @endphp

        <header class="flex-wrap pos-sticky top-0 bg-black-custom2 z-1">@include('layouts.header')</header>

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
                            <div class="mb-10 {{$authCheck ? 'color-green' : 'color-black'}}" onclick="{{$actionConditionAuth}}">
                                @if($authCheck)
                                    Профиль
                                @else
                                    Вход
                                @endif
                            </div>
                            <a class="color-black text-center" style="text-decoration: none;" href="tel:+7(926)585-36-21">+7(926)585-36-21</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main>@yield('content')</main>

        @if(isset($footer) && $footer === true)
            <footer>@include('layouts.footer')</footer>
        @endif

        <script>
            const auth = {{auth()->check() ? 'true' : 'false'}};
            const admin = {{(auth()->check() && auth()->user()->IsAdmin()) ? 'true' : 'false'}};
            const userPhone = "{{auth()->check() ? auth()->user()->phone : ''}}";

            const routeOrderCreate = "{{route('order-create')}}";
            const routePhoneValidation = "{{route('phone-validation')}}";
            const routeCheckConfirmationCode = "{{route('check-confirmation-code')}}";
            const routeLogout = "{{route('logout')}}";

            const routeManagerArmCheckOrderStatusChange = "{{route('manager-arm-check-order-status-change')}}";
        </script>

        <script src="{{ asset('resources/js/script.js') }}"></script>

        @yield('js')

        <script>
            eval({{session('execFunction')}});
            if (localStorage.getItem('execFunction') !== null) {
                let execFunction = localStorage.getItem('execFunction');
                localStorage.removeItem('execFunction');
                eval(execFunction);
            }
        </script>

        <script>

            // function calcLostTime(container, startHour, startMints) {
            //     let timerId = setInterval(function() {
            //         let time = new Date();
            //         let hour = time.getUTCHours() + moskowUtc;
            //         container.innerHTML = ((hour > 24 ? "0" : "") + (((startHour - 1) - hour) < 10 ? '0' : '') + ((startHour - 1) - hour)) + ":" + (((60 - startMints) - time.getMinutes()) < 10 ? '0' : '') + ((60 - startMints) - time.getMinutes()) + ":" + ((60 - time.getSeconds()) < 10 ? '0' : '') + (60 - time.getSeconds());
            //     }, 1000);
            // }

            let startHour = 11;
            let startMints = 0;
            let endHour = 23;
            let endMints = 0;

            let moskowUtc = 3;
            let time = new Date();
            let hour = time.getUTCHours() + moskowUtc;
            if (hour < startHour || hour >= endHour) {
                ModalWindow('<div class="text-center mb-10">Часы работы с ' + startHour + ':'+ ((startMints < 10 ? '0' : '') + startMints) +' до ' + endHour + ':'+ ((endMints < 10 ? '0' : '') + endMints) +'</div></div>');
                // calcLostTime(modalTime.querySelector('.dynamic-time'), startHour, startMints)
            }

        </script>


    </body>

</html>
