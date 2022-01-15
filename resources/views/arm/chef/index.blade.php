@extends('app')

@section('content')

    <div>
        <div>ARM повара</div>
        <div>
            <a href="{{route('chef-arm-orders-page')}}">Заказы</a>
        </div>
        <div>
            <a href="{{route('chef-arm-orders-kitchen-page')}}">Заказы интерфейс кухни</a>
        </div>
    </div>

@stop

@section('js')

    <script>

    </script>

@stop
