@php
    $isARM = false;
    if(str_contains(url()->current(), 'arm') && auth()->check() && auth()->user()->IsManager()) {
        $isARM = true;
    }
@endphp

<!DOCTYPE html>
<html lang="ru">

    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
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

        @if($isARM)

            <style>
                .flash-message-container .flash-message-text {
                    background-color: rgba(0, 0, 0, 0.9);
                    color: #ffffff;
                }
            </style>

        @endif

        @yield('css')

        <script src="{{ asset('resources/js/add.prototypes.js') }}"></script>

    </head>

    <body class="@if(!$isARM) bg-black-custom color-white @endif">


        @php
            $authCheck = auth()->check();
            $actionConditionAuth = !$authCheck ? 'LoginWindow()' : 'Profile()';
        @endphp

        <header class="flex-wrap pos-sticky top-0 bg-black-custom2 z-1">@include('layouts.header')</header>

        <nav class="left-menu hide z-2 pos-fix top-0 left-0 w-100 h-100">
            <div class="shadow-menu w-100 h-100 bg-black pos-abs" style="opacity: 0.5"></div>
            <div class="left-menu-content-container bg-white h-100 pos-rel w-fit">
                <div class="close-menu-button cp pos-abs top-0" style="right: -48px">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
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
                            @if($authCheck && auth()->user()->IsStaff())
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

        <main @if(!$isARM) style="max-width: 1440px;"@endif>@yield('content')</main>

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

            const routeManagerArmCheckOrderStatusChange = "{{route('manager-arm-check-order-status-change')}}";
        </script>

        @if(auth()->check() && auth()->user()->IsManager())
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

        <script src="{{ asset('resources/js/smoothscroll-polyfill.js') }}"></script>
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

            //toDo сделать редактирование через интерфейс
            const closeMessage = 'По техническим причинам мы закрыты. Приносим свои извинения.';

            if (closeMessage) {
                ModalWindow(closeMessage);
            }

            // function calcLostTime(container, startHour, startMints) {
            //     let timerId = setInterval(function() {
            //         let time = new Date();
            //         let hour = time.getUTCHours() + moskowUtc;
            //         container.innerHTML = ((hour > 24 ? "0" : "") + (((startHour - 1) - hour) < 10 ? '0' : '') + ((startHour - 1) - hour)) + ":" + (((60 - startMints) - time.getMinutes()) < 10 ? '0' : '') + ((60 - startMints) - time.getMinutes()) + ":" + ((60 - time.getSeconds()) < 10 ? '0' : '') + (60 - time.getSeconds());
            //     }, 1000);
            // }

            let startHour = 11;
            let startMints = 0;
            let endHour = 22;
            let endMints = 45;

            let moskowUtc = 3;
            let time = new Date();
            let hour = time.getUTCHours() + moskowUtc;
            let mints = time.getMinutes() + moskowUtc;

            if ((hour === startHour && mints >= startMints) || (startHour < hour && hour < endHour) || (hour === endHour && mints <= endMints)) {
                //
            } else {
                if (!admin) {
                    ModalWindow('<div class="text-center">Часы работы с ' + startHour + ':'+ ((startMints < 10 ? '0' : '') + startMints) +' до ' + endHour + ':'+ ((endMints < 10 ? '0' : '') + endMints) +'</div></div>');
                }
            }

            document.querySelectorAll('.table-sort > thead').forEach(tableTH => tableTH.addEventListener('click', () => getSort(event)));

            ManagerArmCheckOrderStatusChange();

            @if($isARM)
            document.body.querySelectorAll('table').forEach((table) => {
                table.parentNode.style.overflow = 'auto';
                table.parentNode.style.width = '100%';
            });
            @endif

            if (localStorage.getItem('cookiesAccepted') === null) {
                const cookiesInfo = '<div class="pos-fix bottom-0 bg-black w-100 shadow-white"><div class="flex-space-between flex-wrap p-25"><div class="text-center py-10"> Мы тоже используем куки, потому что без них вообще ничего не работает</div><button class="cookies-accept-button orange-button">Ничего, я привык</button></div></div>';
                let cookiesInfoElement = CreateElement('div', {content: cookiesInfo}, document.body);
                let cookiesAcceptButton = document.body.querySelector('.cookies-accept-button');
                if (cookiesAcceptButton) {
                    cookiesAcceptButton.addEventListener('click', () => {
                        localStorage.setItem('cookiesAccepted', Date.now().toString());
                        cookiesInfoElement.remove();
                    });
                }
            }



        </script>


    </body>

</html>
