@extends('app')

@section('content')

    <div>
        <div>ARM менеджера</div>
        <div>
            <a class="color-white" href="{{route('manager-arm-orders-page')}}">Заказы</a>
        </div>
        <div>
            <a class="color-white" href="{{route('manager-arm-products-modifications-page')}}">Редактирование модификаций</a>
        </div>
    </div>

@stop

@section('js')

    <script>

    </script>

@stop
