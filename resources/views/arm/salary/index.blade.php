@extends('app')

@section('content')

    <div>
        <div>Зарплата</div>
        <div>
            <a href="{{route('employees-page')}}">Сотрудники</a>
        </div>
        <div>
            <a href="{{route('calendar-page')}}">График работы</a>
        </div>
    </div>

@stop

@section('js')

    <script>

    </script>

@stop
