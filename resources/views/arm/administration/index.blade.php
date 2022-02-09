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
    </div>

@stop

@section('js')

@stop
