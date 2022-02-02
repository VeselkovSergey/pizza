@extends('app')

@section('content')

    <style>
        .profile-container {
            display: flex;
        }
        @media screen and (max-width: 540px) {
            .profile-container {
                flex-direction: column;
            }
            .label-flex-column {
                display: flex;
                flex-direction: column;
            }

            .orange-button {
                width: 100%;
            }
        }
    </style>

    <div class="flex pl-10">
        <h3 class="mr-25"><a class="clear-a {{request()->is('profile') ? 'color-orange' : ''}}" href="{{route('profile')}}">Профиль</a></h3>
        <h3><a class="clear-a {{request()->is('profile/orders') ? 'color-orange' : ''}}" href="{{route('profile-orders')}}">Заказы</a></h3>
    </div>
    <div class="profile-container">
        <div class="mr-a w-100">
            <div class="mb-10">
                <label class="label-flex-column">
                    <div>Номер телефона</div>
                    <input type="text" readonly value="{{\App\Helpers\StringHelper::PhoneBeautifulFormat(auth()->user()->phone)}}">
                </label>
            </div>
        </div>
        <div><button class="orange-button" onclick="LogoutAndHref()">Выйти</button></div>
    </div>

@stop

@section('js')
    <script>
        function LogoutAndHref() {
            Ajax("{{route('logout')}}").then(() => {
                location.href = "{{route('home-page')}}";
            });
        }
    </script>
@stop
