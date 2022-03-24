@php
        $isStaff = false;
        if (auth()->check() && auth()->user()->IsStaff()) {
            $isStaff = true;
        }

        $isARM = false;
        if(request()->is('arm', 'arm/*', 'settings', 'settings/*') && $isStaff) {
            $isARM = true;
        }

        $closedMessage = \App\Models\Settings::where('key', 'closedMessage')->first();
        if ($closedMessage) {
            $closedMessageTitle = json_decode($closedMessage->value)->closedMessageTitle;
            $closedMessageStart = json_decode($closedMessage->value)->start;
            $closedMessageStart = (time() - strtotime($closedMessageStart)) > 0 ? true : false;
        } else {
            $closedMessageTitle = '';
            $closedMessageStart = false;
        }
@endphp

<!DOCTYPE html>
<html lang="ru">

    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#0e0e0e">

        @yield('meta')

        <title>Пицца Дубна. БроПицца!</title>
        <meta name="description" content="Привет БРО! Это БРОпицца, доставка пиццы Дубна. Говорят в Дубне давненько никто не делал вкусную пиццу. Этому городу определенно нужен был спасатель! Классно что в Дубне открылись мы (БРОпицца)! Теперь вкусная пицца будет доставлена каждому на стол!">
        <meta name="keywords" content="Пицца, Дубна, Бропицца, BROпицца, bro пицца, бро пицца, вкусная пицца, пицца Дубна, новая пиццерия Дубна, доставка Дубна, доставка пиццы дубна">
        <link href="{{asset('resources/scss/reset.scss')}}" rel="stylesheet">
        <link href="{{asset('resources/scss/helpers.scss')}}" rel="stylesheet">
        <link href="{{asset('resources/scss/loaders.scss')}}" rel="stylesheet">
        <link href="{{asset('resources/scss/app.scss')}}" rel="stylesheet">
        <link href="{{asset('resources/scss/adaptive.scss')}}" rel="stylesheet">

        @if(auth()->check() && auth()->user()->IsAdmin())

            <meta name="mobile-web-app-capable" content="yes">
            <meta name="apple-mobile-web-app-capable" content="yes">
            <meta name="application-name" content="БроПицца">
            <meta name="apple-mobile-web-app-title" content="БроПицца">
            <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
            <meta name="msapplication-starturl" content="/">

            <link rel="apple-touch-icon" href="{{asset('logo-192x192.png')}}">
            <link rel="manifest" href="{{asset('manifest.json')}}">

            <script>
                if ('serviceWorker' in navigator) {
                    // Весь код регистрации у нас асинхронный.
                    window.addEventListener('load', function() {
                        navigator.serviceWorker.register('{{asset('service-worker.js')}}', {scope: '/'})
                            .then(() => navigator.serviceWorker.ready.then((worker) => {
                                worker.sync.register('syncdata');
                            }))
                            .catch((err) => console.log(err));
                    });
                }
            </script>

        @endif

        @if($isARM)

            <style>
                .flash-message-container .flash-message-text {
                    background-color: rgba(0, 0, 0, 0.9);
                    color: #ffffff;
                }
            </style>

        @else

            <style>

                .snow-blocks {
                    z-index: -100;
                    position: fixed;
                    height: 100vh;
                    width: 100vw;
                    overflow: hidden;
                    background-size:cover;
                }

                .snow1{
                    background-image: url({{asset('snow1.png')}});
                    position:absolute;
                    width: 100%;
                    height:100%;
                    animation: snow1 18s linear infinite;
                }

                .snow2{
                    background-image: url({{asset('snow2.png')}}),url({{asset('snow3.png')}});
                    animation: snow2 10s linear infinite;
                    width: 100%;
                    height:100%;
                    position:absolute;
                }

                @keyframes snow1{
                    from{background-position: 0 -300px;}
                    20% {background-position: 20% -100px;}
                    40% {background-position: 30% 100px;}
                    to{background-position: 20% 700px;}
                }

                @keyframes snow2{
                    0%{background-position: 0 0, 0 0;}
                    100%{background-position: 10% 100vh, 10% 100vw;}
                }
            </style>

        @endif

        <style>
            .spring {
                background-image: url("{{asset('grass2.png')}}");
                height: 140px;
                position: sticky;
                bottom: 0;
            }

            @media screen and (max-width: 720px) {
                .spring {
                    bottom: -50px;
                }
            }
        </style>

        @yield('css')

        <script src="{{ asset('resources/js/add.prototypes.js') }}"></script>

    </head>

    <body class="@if(!$isARM) bg-black-custom color-white @endif" @if(!$isARM)style="background-image: url('{{asset('bg-2.jpg')}}'); background-attachment: fixed;"@endif>

        {{-- Зима --}}
{{--    @if(!$isARM)--}}
{{--    <div class="snow-blocks">--}}
{{--        <div class="snow1"></div>--}}
{{--        <div class="snow2"></div>--}}
{{--    </div>--}}
{{--    @endif--}}

        @php
            $authCheck = auth()->check();
            $actionConditionAuth = !$authCheck ? 'LoginWindow()' : 'Profile()';
        @endphp

        <header class="flex-wrap pos-sticky top-0 bg-black-custom2 z-2">@include('layouts.header')</header>

        <nav class="left-menu hide z-3 pos-fix top-0 left-0 w-100 h-100">
            <div class="shadow-menu w-100 h-100 bg-black pos-abs" style="opacity: 0.5"></div>
            <div class="left-menu-content-container bg-white h-100 pos-rel w-fit">
                <div class="close-menu-button cp pos-abs top-0" style="right: -48px">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-x color-white" viewBox="0 0 16 16">
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>
                    </svg>
                </div>
                <div class="scroll-auto h-100">
                    <div class="fast-menu-in-left-menu">
                        <div class="flex-column p-10">
                            <div class="mb-5 pb-5 {{$authCheck ? 'color-green' : 'color-black'}}" style="border-bottom: 1px solid gray;" onclick="{{$actionConditionAuth}}">
                                @if($authCheck)
                                    Профиль
                                @else
                                    Вход
                                @endif
                            </div>
                            @if($isStaff)
                                @foreach(\App\Http\Controllers\ARM\ARMController::AllRoutes() as $route)
                                    @if(in_array(auth()->user()->role_id, (array)$route->role))
                                        <a class="mb-5 pb-5 color-black" style="text-decoration: none; border-bottom: 1px solid gray;" href="{{$route->link}}">{{$route->title}}</a>
                                    @endif
                                @endforeach
                            @endif
                            <a class="color-black" style="text-decoration: none;" href="tel:+7(926)585-36-21">+7(926)585-36-21</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        @if(isset($allCategory))
            <div class="fast-menu pos-sticky w-100 py-15 flex scroll-x-auto left-0 bg-black-custom z-1" style="top: 50px; box-shadow: 0 0 10px white;">
                @foreach($allCategory as $category)
                    <div class="clear-a color-orange px-15 navigation" data-anchor-id="{{$category->id}}">{{$category->title}}</div>
                @endforeach
            </div>
        @endif

        <main style=" @if(!$isARM) max-width: 1440px; @endif min-height: calc(100vh - 100px);">@yield('content')</main>

        {{-- Весна --}}
{{--        @if(!$isARM)--}}
{{--            <div class="spring"></div>--}}
{{--        @endif--}}

        @if(isset($footer) && $footer === true)
            <footer>@include('layouts.footer')</footer>
        @endif

        <script>
            const auth = {{auth()->check() ? 'true' : 'false'}};
            const admin = {{(auth()->check() && auth()->user()->IsManager()) ? 'true' : 'false'}};
            const userPhone = "{{auth()->check() ? auth()->user()->phone : ''}}";

            const routeOrderCreate = "{{route('order-create')}}";
            const routePhoneValidation = "{{route('phone-validation')}}";
            const routeCheckConfirmationCode = "{{route('check-confirmation-code')}}";
            const routeLogout = "{{route('logout')}}";
            const routeCheckPromoCodeRequest = "{{route('check-promo-code')}}";
            const routeProfile = "{{route('profile')}}";

            const routeClientLastAddress = "{{route('manager-arm-client-last-address')}}";
        </script>

        @if($isStaff)
        <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
        <script>

            // Enable pusher logging - don't include this in production
            // Pusher.logToConsole = true;

            const pusher = new Pusher("{{env('PUSHER_APP_KEY')}}", {
                cluster: 'eu'
            });

            const channel = pusher.subscribe('manager-channel');
            channel.bind('updateStatuses', function(data) {
                ManagerArmCheckOrderStatusChange(data);
            });
        </script>
        @endif

        @if(!$isStaff)
        {{--    <!-- Yandex.Metrika counter -->--}}
        <script>
            (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
                m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
            (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

            ym(86929115, "init", {
                clickmap:true,
                trackLinks:true,
                accurateTrackBounce:true,
                webvisor:true
            });
        </script>
        {{--    <!-- /Yandex.Metrika counter -->--}}
        @endif

        <script src="{{ asset('resources/js/smoothscroll-polyfill.js') }}"></script>
        <script src="{{ asset('resources/js/script.js') }}?123"></script>
        <script src="{{ asset('resources/js/app.js') }}?123"></script>

        @yield('js')

        <script>
            eval({{session('execFunction')}});
            if (localStorage.getItem('execFunction') !== null) {
                let execFunction = localStorage.getItem('execFunction');
                localStorage.removeItem('execFunction');
                eval(execFunction);
            }

            @if(!$isARM && $closedMessageStart)
            const closeMessage = "{{$closedMessageTitle}}";

            if (closeMessage) {
                ModalWindow(closeMessage);
            }
            @endif

            OpeningHours(11, 0, 22, 30);

            document.querySelectorAll('.table-sort > thead').forEach(tableTH => tableTH.addEventListener('click', () => getSort(event)));

            @if($isStaff)
                ManagerArmCheckOrderStatusChange();
            @endif

            @if($isARM)
            document.body.querySelectorAll('table').forEach((table) => {
                table.parentNode.style.overflow = 'auto';
                table.parentNode.style.width = '100%';
            });
            @endif

        </script>

    </body>

</html>
