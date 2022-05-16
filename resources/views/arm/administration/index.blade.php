@extends('app')

@section('content')

    <div>
        <div>ARM администратора</div>
        <div>
            <a href="{{route('administrator-arm-users-page')}}">Пользователи</a>
        </div>
        <div>
            <a href="{{route('administrator-arm-orders-page')}}">Заказы</a>
        </div>
        <div>
            <a href="{{route('administrator-arm-orders-old-page')}}">Заказы old</a>
        </div>
        <div>
            <a href="{{route('administrator-arm-orders-addresses-page')}}">Заказы адреса</a>
        </div>
        <div>
            <a href="{{route('administrator-arm-products-page')}}">Продукты</a>
        </div>
        <div>
            <a href="{{route('administrator-arm-products-modifications-page')}}">Модификаторы товаров</a>
        </div>
        <div>
            <a href="{{route('administrator-arm-device-used-page')}}">Используемые устройства</a>
        </div>
        <div>
            <a href="{{route('administrator-arm-spent-ingredients-page')}}">Расход ингредиентов</a>
        </div>
        <div>
            <a href="{{route('all-promo-codes-page')}}">Промокоды</a>
        </div>
        <div>
            <a href="{{route('salary-page')}}">Зарплатный блок</a>
        </div>
        <div>
            <a href="{{route('send-sms-index-page')}}">Отправка СМС с номера 89151640548</a>
        </div>
        <div class="flex-center-vertical">
            <span>Выключить звук заказов</span>
            <label class="custom-checkbox-label" for="new-order-audio">
                <input type="checkbox" id="new-order-audio" name="new-order-audio"/>
                <div class="custom-checkbox-slider round"></div>
            </label>
        </div>
    </div>

@stop

@section('js')

    <script>
        document.getElementById('new-order-audio').addEventListener('change', (event) => {
            localStorage.setItem('newOrderAudioIsDisabled', event.target.checked);
        });

        if (localStorage.getItem('newOrderAudioIsDisabled') === 'true') {
            document.getElementById('new-order-audio').checked = true;
        }
    </script>

@stop
