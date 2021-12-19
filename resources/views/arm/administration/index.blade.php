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
            <a href="{{route('administrator-arm-products-page')}}">Продукты</a>
        </div>
        <div>
            <a href="{{route('administrator-arm-products-modifications-page')}}">Модификаторы товаров</a>
        </div>
        <div>
            <a href="{{route('administrator-arm-device-used-page')}}">Используемые устройства</a>
        </div>
        <div>
            <a href="{{route('administrator-arm-ingredients-page')}}">Ингредиенты</a>
        </div>
        <div>
            <a href="{{route('administrator-arm-spent-ingredients-page')}}">Расход ингредиентов</a>
        </div>
    </div>

@stop

@section('js')

@stop
