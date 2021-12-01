<div class="logo-container flex-center" style="max-width: 100px;">
    <a class="flex-center w-100" href="{{route('home-page')}}">
        <div class="logo w-100 h-100">
            <img class="w-100" src="{{url('logo-new.png')}}" alt="">
        </div>
    </a>
</div>
{{--<div class="menu flex-center p-5 cp">--}}
{{--    <div class="flex-center border px-10 border-radius-5">--}}
{{--        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">--}}
{{--            <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>--}}
{{--        </svg>--}}
{{--    </div>--}}
{{--</div>--}}
{{--<div class="search-container-header p-5 flex-center" style="flex: 1;">--}}
{{--    <div class="pos-rel w-100">--}}
{{--        <div class="pos-abs" style="top: 11px; left: 10px; color: grey;">--}}
{{--            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">--}}
{{--                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>--}}
{{--            </svg>--}}
{{--        </div>--}}
{{--        <input class="main-search-input w-100 p-10 border-radius-5" style="border: 2px solid #2e3192; text-indent: 30px;" type="text" placeholder="Поиск" value="">--}}
{{--        <div class="delete-value-search-input hide-el pos-abs cp" style="top: 9px; right: 10px; color: grey;">--}}
{{--            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">--}}
{{--                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>--}}
{{--            </svg>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
<a class="flex-center m-a clear-a" href="{{route('home-page')}}">
    <div style="font-style: italic;font-weight: bold;font-size: large;">
        BROпицца
    </div>
</a>

<div class="button-menu flex-center cp mr-10">
    <div class="flex-center border-radius-5 px-10 border">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
        </svg>
    </div>
</div>

<div class="container-profile-and-basket flex-center p-5">
    <div onclick="{{$actionConditionAuth}}" class="container-profile flex-column-center text-center cp p-5 {{$authCheck ? 'color-green' : ''}}">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
            </svg>
        </div>
        <div class="text-center">
            @if($authCheck)
                Профиль
            @else
                Вход
            @endif
        </div>
    </div>
    <div class="button-basket flex-column-center text-center cp p-5">
        <div class="pos-rel">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-basket" viewBox="0 0 16 16">
                <path d="M5.757 1.071a.5.5 0 0 1 .172.686L3.383 6h9.234L10.07 1.757a.5.5 0 1 1 .858-.514L13.783 6H15a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1v4.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 1 13.5V9a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h1.217L5.07 1.243a.5.5 0 0 1 .686-.172zM2 9v4.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V9H2zM1 7v1h14V7H1zm3 3a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3A.5.5 0 0 1 4 10zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3A.5.5 0 0 1 6 10zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3A.5.5 0 0 1 8 10zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 1 .5-.5zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 1 .5-.5z"/>
            </svg>
            <div class="amount-item-in-basket hide-el pos-abs right-0 p-5 bg-black color-white" style="border-radius: 100px; top: -20px; right: -10px; min-width: 20px;">
                0
            </div>
        </div>
        <div class="text-center">
            Корзина
        </div>
    </div>
</div>
<div class="phone-container-header flex-center p-5">
    <a class="color-black text-center" style="text-decoration: none;" href="tel:{{env('PHONE_COMPANY')}}">{{env('PHONE_COMPANY')}}</a>
</div>
