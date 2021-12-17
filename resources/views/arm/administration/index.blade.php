@extends('app')

@section('content')

    <div>
        <div>ARM администратора</div>
        <div>
            <a class="color-white" href="{{route('administrator-arm-users-page')}}">Пользователи</a>
        </div>
        <div>
            <a class="color-white" href="{{route('administrator-arm-orders-page')}}">Заказы</a>
        </div>
        <div>
            <a class="color-white" href="{{route('administrator-arm-products-modifications-page')}}">Модификаторы товаров</a>
        </div>
    </div>

@stop

@section('js')

@stop
